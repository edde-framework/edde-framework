cls
title Edde Runtime
@echo off
"%~dp0.php\php" "%~dp0\bin\composer.phar" self-update --no-ansi
"%~dp0.php\php" "%~dp0\bin\composer.phar" install --no-ansi
cls
start /b "" "%~dp0.redis\redis-server.exe" "%~dp0.redis.conf"
cls
echo Redis is with us!
timeout 1 /nobreak > nul
pushd
chdir "%~dp0.apache"
echo ...and we're happily online!
title Edde Runtime - Yapee!
"%~dp0.apache\bin\httpd.exe"
popd
pause
