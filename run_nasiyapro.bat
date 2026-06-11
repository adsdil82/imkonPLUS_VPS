@echo off
timeout /t 30 /nobreak

cd /d C:\laragon\www\nasiyapro

C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan optimize:clear

C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan serve --host=0.0.0.0 --port=8000 >> C:\laragon\www\nasiyapro\storage\logs\nasiyapro_service.log 2>&1