@echo off
set "PHP_BIN=C:\xampp\php\php.exe"
if not exist "%PHP_BIN%" (
    echo PHP CLI not found: %PHP_BIN% 1>&2
    exit /b 1
)
"%PHP_BIN%" "%~dp0cleanup_orders.php"
exit /b %errorlevel%
