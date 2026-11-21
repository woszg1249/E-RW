#!/usr/bin/env python3
"""
Script Otomatisasi Migrasi Data ke VPS E-RW
Website: https://e-rw.unique-monsters.my.id/
Host: 43.157.230.29
"""

import os
import sys
import argparse
import paramiko

DEFAULT_HOST = "43.157.230.29"
DEFAULT_PORT = 22
DEFAULT_USER = "ubuntu"
DEFAULT_PASS = "comet-92$-galaxy"
DEFAULT_APP_DIR = "/home/unique-monsters-erw/htdocs/e-rw.unique-monsters.my.id"
DEFAULT_OWNER = "unique-monsters-erw"
DEFAULT_DB_USER = "erw_user"
DEFAULT_DB_PASS = "comet-92$-galaxy"
DEFAULT_DB_NAME = "keuangan_rt"

def run_migration(host, port, username, password, sql_file, app_dir, owner, db_user, db_pass, db_name):
    if not os.path.exists(sql_file):
        print(f"❌ File SQL tidak ditemukan di: {sql_file}")
        sys.exit(1)

    print("=======================================================")
    print("🚀 MEMULAI MIGRASI DATA KE VPS E-RW")
    print(f"📡 Target VPS : {username}@{host}:{port}")
    print(f"📁 Target Dir : {app_dir}")
    print(f"🗄️ Database   : {db_name} (User: {db_user})")
    print(f"📄 SQL Dump   : {sql_file}")
    print("=======================================================\n")

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())

    try:
        print("🔗 Menghubungkan ke VPS melalui SSH...")
        ssh.connect(host, port=port, username=username, password=password, timeout=20)
        print("✅ Terhubung ke VPS dengan sukses!\n")

        # 1. Upload SQL Dump ke /tmp
        remote_sql_path = "/tmp/keuangan_rt_vps.sql"
        print(f"📤 Mengunggah file SQL ke {remote_sql_path} via SFTP...")
        sftp = ssh.open_sftp()
        sftp.put(sql_file, remote_sql_path)
        sftp.close()
        print("✅ Berhasil mengunggah file SQL dump!\n")

        # 2. Skrip Import Python di Sisi Server (Aman terhadap karakter khusus password)
        server_import_script = f"""
import subprocess

db_user = {repr(db_user)}
db_pass = {repr(db_pass)}
db_name = {repr(db_name)}
sql_file = {repr(remote_sql_path)}

print("🧹 Mengosongkan tabel lama agar struktur baru sinkron...")
res = subprocess.run(
    ['mysql', '-u', db_user, f'-p{{db_pass}}', '-N', '-e', f'SHOW TABLES IN {{db_name}};'],
    capture_output=True, text=True
)
tables = [line.strip() for line in res.stdout.splitlines() if line.strip()]
print(f"   Ditemukan {{len(tables)}} tabel lama.")

if tables:
    drop_sql = "SET FOREIGN_KEY_CHECKS = 0; "
    for t in tables:
        drop_sql += f"DROP TABLE IF EXISTS `{{t}}`; "
    drop_sql += "SET FOREIGN_KEY_CHECKS = 1;"

    p_drop = subprocess.run(
        ['mysql', '-u', db_user, f'-p{{db_pass}}', db_name, '-e', drop_sql],
        capture_output=True, text=True
    )
    if p_drop.returncode != 0:
        print("⚠️ Warning saat drop tabel:", p_drop.stderr)

print("📥 Mengimpor data ke database {{db_name}}...")
with open(sql_file, 'r', encoding='utf-8', errors='ignore') as f:
    p_import = subprocess.run(
        ['mysql', '-u', db_user, f'-p{{db_pass}}', db_name],
        stdin=f,
        capture_output=True,
        text=True
    )

if p_import.returncode != 0:
    print("❌ Gagal mengimpor SQL:", p_import.stderr)
    exit(1)
else:
    print("✅ Import SQL berhasil tanpa error!")
"""

        sftp = ssh.open_sftp()
        with sftp.file('/tmp/vps_import_runner.py', 'w') as f:
            f.write(server_import_script)
        sftp.close()

        print("⚙️ Menjalankan pembersihan tabel dan impor data di VPS...")
        stdin, stdout, stderr = ssh.exec_command("python3 /tmp/vps_import_runner.py")
        output = stdout.read().decode('utf-8')
        err_output = stderr.read().decode('utf-8')
        print(output.strip())
        if err_output and "Warning" not in err_output:
            print("STDERR:", err_output.strip())

        # 3. Jalankan artisan optimize:clear
        print("\n🧹 Membersihkan cache Laravel di VPS...")
        cmd_clear = f"sudo -u {owner} php {app_dir}/artisan optimize:clear"
        stdin, stdout, stderr = ssh.exec_command(f"echo {password} | sudo -S {cmd_clear}", get_pty=True)
        clear_out = stdout.read().decode('utf-8')
        print(clear_out.strip())

        # 4. Verifikasi Jumlah Data Tiap Tabel
        print("\n📊 Memeriksa status tabel dan jumlah baris data di VPS:")
        verify_php = f"""<?php
require '{app_dir}/vendor/autoload.php';
$app = require_once '{app_dir}/bootstrap/app.php';
$kernel = $app->make(\\Illuminate\\Contracts\\Console\\Kernel::class);
$kernel->bootstrap();

use Illuminate\\Support\\Facades\\DB;
$tables = DB::select('SHOW TABLES');
echo sprintf("%-25s | %-10s\\n", "Nama Tabel", "Total Baris");
echo str_repeat("-", 40) . "\\n";
foreach ($tables as $t) {{
    $tblName = array_values((array)$t)[0];
    $cnt = DB::table($tblName)->count();
    echo sprintf("%-25s | %d\\n", $tblName, $cnt);
}}
"""
        sftp = ssh.open_sftp()
        with sftp.file('/tmp/verify_counts.php', 'w') as f:
            f.write(verify_php)
        sftp.close()

        stdin, stdout, stderr = ssh.exec_command(f"echo {password} | sudo -S sudo -u {owner} php /tmp/verify_counts.php", get_pty=True)
        print(stdout.read().decode('utf-8').strip())

        print("\n=======================================================")
        print("🎉 MIGRASI DATA KE VPS BERHASIL SELESAI!")
        print("🌐 Portal: https://e-rw.unique-monsters.my.id/")
        print("=======================================================")

    except Exception as e:
        print(f"❌ Terjadi kesalahan: {e}")
        sys.exit(1)
    finally:
        ssh.close()

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Migrasi Data ke VPS E-RW")
    parser.add_argument("--host", default=DEFAULT_HOST, help="VPS IP/Hostname")
    parser.add_argument("--port", type=int, default=DEFAULT_PORT, help="SSH Port")
    parser.add_argument("--user", default=DEFAULT_USER, help="SSH Username")
    parser.add_argument("--password", default=DEFAULT_PASS, help="SSH Password")
    parser.add_argument("--sql", default=os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "database", "keuangan_rt_vps.sql"), help="Path file SQL dump")
    parser.add_argument("--app-dir", default=DEFAULT_APP_DIR, help="Path project Laravel di VPS")
    parser.add_argument("--owner", default=DEFAULT_OWNER, help="Linux user owner di VPS")
    parser.add_argument("--db-user", default=DEFAULT_DB_USER, help="Database username di VPS")
    parser.add_argument("--db-pass", default=DEFAULT_DB_PASS, help="Database password di VPS")
    parser.add_argument("--db-name", default=DEFAULT_DB_NAME, help="Database name di VPS")

    args = parser.parse_args()
    run_migration(
        host=args.host,
        port=args.port,
        username=args.user,
        password=args.password,
        sql_file=args.sql,
        app_dir=args.app_dir,
        owner=args.owner,
        db_user=args.db_user,
        db_pass=args.db_pass,
        db_name=args.db_name
    )
