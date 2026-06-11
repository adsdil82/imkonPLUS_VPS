@echo off
chcp 65001 > nul
setlocal

REM =====================================================
REM NasiyaPro local backup
REM Loyiha + MySQL baza + Apache config
REM =====================================================

set BACKUP_ROOT=C:\NasiyaPro_Backup
set PROJECT_DIR=C:\laragon\www\nasiyapro

set MYSQLDUMP_EXE=C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe
set MYSQL_EXE=C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe

set DB_NAME=nasiyapro
set DB_USER=root
set DB_PASS=

REM === Sana vaqt nomi ===
for /f "tokens=1-3 delims=." %%a in ("%date%") do (
    set DD=%%a
    set MM=%%b
    set YYYY=%%c
)

set HH=%time:~0,2%
set MN=%time:~3,2%
set SS=%time:~6,2%
set HH=%HH: =0%

set BACKUP_NAME=NasiyaPro_%YYYY%-%MM%-%DD%_%HH%-%MN%-%SS%
set BACKUP_DIR=%BACKUP_ROOT%\%BACKUP_NAME%

echo.
echo ==============================================
echo NasiyaPro LOCAL BACKUP boshlanmoqda
echo Backup joyi: %BACKUP_DIR%
echo ==============================================
echo.

REM === Backup papkalar yaratish ===
mkdir "%BACKUP_ROOT%" 2>nul
mkdir "%BACKUP_DIR%" 2>nul
mkdir "%BACKUP_DIR%\project" 2>nul
mkdir "%BACKUP_DIR%\database" 2>nul
mkdir "%BACKUP_DIR%\config" 2>nul

REM === MySQL borligini tekshirish ===
if not exist "%MYSQLDUMP_EXE%" (
    echo XATO: mysqldump topilmadi:
    echo %MYSQLDUMP_EXE%
    pause
    exit /b 1
)

REM === Project borligini tekshirish ===
if not exist "%PROJECT_DIR%\artisan" (
    echo XATO: Laravel project topilmadi:
    echo %PROJECT_DIR%
    pause
    exit /b 1
)

REM === 1. Database backup ===
echo [1/5] MySQL baza backup qilinyapti...

"%MYSQLDUMP_EXE%" -u %DB_USER% %DB_NAME% --single-transaction --routines --triggers --events --default-character-set=utf8mb4 > "%BACKUP_DIR%\database\nasiyapro.sql"

if errorlevel 1 (
    echo.
    echo XATO: MySQL baza backup olinmadi.
    echo Laragon MySQL ishga tushganini tekshiring.
    pause
    exit /b 1
)

echo Database backup OK.

REM === 2. Project fayllar backup ===
echo.
echo [2/5] Loyiha fayllari nusxalanyapti...

robocopy "%PROJECT_DIR%" "%BACKUP_DIR%\project\nasiyapro" /E /XD storage\framework\cache storage\framework\sessions storage\framework\views /XF *.log

REM Robocopy 0-7 normal hisoblanadi
if %ERRORLEVEL% GEQ 8 (
    echo.
    echo XATO: Project fayllarni nusxalashda muammo.
    pause
    exit /b 1
)

echo Project backup OK.

REM === 3. Apache config backup ===
echo.
echo [3/5] Apache config backup qilinyapti...

if exist "C:\laragon\etc\apache2\sites-enabled" (
    xcopy "C:\laragon\etc\apache2\sites-enabled" "%BACKUP_DIR%\config\sites-enabled\" /E /I /Y > nul
)

REM === 4. hosts backup ===
echo.
echo [4/5] Windows hosts backup qilinyapti...

copy "C:\Windows\System32\drivers\etc\hosts" "%BACKUP_DIR%\config\hosts_backup.txt" > nul

REM === 5. Restore qo'llanma ===
echo.
echo [5/5] Restore qo'llanma yozilyapti...

(
echo NasiyaPro RESTORE QO'LLANMA
echo ===========================
echo.
echo Backup nomi:
echo %BACKUP_NAME%
echo.
echo 1. Laragon o'rnatiladi:
echo    C:\laragon
echo.
echo 2. Project qaytariladi:
echo    %BACKUP_DIR%\project\nasiyapro
echo    shu joyga ko'chiriladi:
echo    C:\laragon\www\nasiyapro
echo.
echo 3. MySQL baza yaratiladi:
echo    C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe -u root -e "CREATE DATABASE nasiyapro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo.
echo 4. SQL import qilinadi:
echo    C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe -u root nasiyapro ^< C:\laragon\www\nasiyapro\nasiyapro.sql
echo.
echo 5. Apache config:
echo    config\sites-enabled ichidagi .conf fayllar
echo    C:\laragon\etc\apache2\sites-enabled ichiga ko'chiriladi.
echo.
echo 6. hosts fayl:
echo    config\hosts_backup.txt ichidagi kerakli qatorlar
echo    C:\Windows\System32\drivers\etc\hosts ga qo'shiladi.
echo.
echo 7. Laravel cache tozalanadi:
echo    cd /d C:\laragon\www\nasiyapro
echo    php artisan optimize:clear
echo    php artisan cache:clear
echo.
echo 8. Laragon:
echo    Stop All
echo    Start All
echo.
echo Backup sana:
echo %date% %time%
) > "%BACKUP_DIR%\RESTORE_QOLLANMA.txt"

REM === ZIP arxiv yaratish ===
echo.
echo ZIP arxiv yaratilmoqda...

powershell -NoProfile -ExecutionPolicy Bypass -Command "Compress-Archive -Path '%BACKUP_DIR%\*' -DestinationPath '%BACKUP_ROOT%\%BACKUP_NAME%.zip' -Force"

if errorlevel 1 (
    echo.
    echo DIQQAT: ZIP yaratilmadi. Lekin oddiy papka backup bor:
    echo %BACKUP_DIR%
) else (
    echo ZIP backup OK:
    echo %BACKUP_ROOT%\%BACKUP_NAME%.zip
)

echo.
echo ==============================================
echo BACKUP TAYYOR!
echo.
echo Papka:
echo %BACKUP_DIR%
echo.
echo ZIP:
echo %BACKUP_ROOT%\%BACKUP_NAME%.zip
echo ==============================================
echo.

pause
endlocal