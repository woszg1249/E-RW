<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MigrateDataToVps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vps:migrate-data
                            {--host=43.157.230.29 : Alamat IP atau hostname VPS}
                            {--port=22 : Port SSH VPS}
                            {--user=ubuntu : User SSH VPS}
                            {--password=comet-92$-galaxy : Password SSH VPS}
                            {--sql= : Path file SQL dump (default: database/keuangan_rt_vps.sql)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasikan seluruh skema dan data database ke VPS produksi (https://e-rw.unique-monsters.my.id)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Memulai proses migrasi data ke VPS...');
        $host = $this->option('host');
        $port = $this->option('port');
        $user = $this->option('user');
        $password = $this->option('password');
        $sqlPath = $this->option('sql') ?: base_path('database/keuangan_rt_vps.sql');

        if (!file_exists($sqlPath)) {
            $this->error("❌ File SQL dump tidak ditemukan di: {$sqlPath}");
            return Command::FAILURE;
        }

        $scriptPath = base_path('scripts/migrate_vps.py');
        if (!file_exists($scriptPath)) {
            $this->error("❌ Skrip python tidak ditemukan di: {$scriptPath}");
            return Command::FAILURE;
        }

        $cmd = [
            'python',
            $scriptPath,
            '--host', $host,
            '--port', (string) $port,
            '--user', $user,
            '--password', $password,
            '--sql', $sqlPath,
        ];

        $process = new Process($cmd);
        $process->setTimeout(300);

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $this->error('❌ Proses migrasi data ke VPS gagal.');
            return Command::FAILURE;
        }

        $this->info('🎉 Migrasi data ke VPS berhasil diselesaikan!');
        return Command::SUCCESS;
    }
}

