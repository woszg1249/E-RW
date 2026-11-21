@echo off
echo ===================================================
echo   Migrasi Data E-RW ke VPS
echo   Target: https://e-rw.unique-monsters.my.id/
echo ===================================================
python "%~dp0migrate_vps.py" %*
pause
