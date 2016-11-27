cls
@echo off
"%~dp0.php\php" "%~dp0\bin\composer.phar" install --no-ansi
cls
start /b "" "%~dp0.redis\redis-server.exe" "%~dp0.redis.conf"
cls
echo Redis is with us!
timeout 2 /nobreak > nul
echo ...and we're happily online!
"%~dp0.apache\bin\httpd.exe"
pause