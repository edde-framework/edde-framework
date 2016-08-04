.php\php %~dp0..\bin\composer.phar update
start /b .php\php-cgi.exe -b 127.0.0.1:9080
start /b bin\caddy.exe
start /b explorer http://127.0.0.1
