cls
@echo off
.php\php "%~dp0\bin\composer.phar" install --no-ansi
start /b .redis\redis-server.exe .redis.conf
timeout 2 /nobreak > nul
cls
cd "%~dp0.apache\bin"
echo We're happily online!
httpd.exe
