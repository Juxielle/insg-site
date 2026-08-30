$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$env:PHPRC = $projectRoot

Set-Location (Join-Path $projectRoot 'public')
php -c ..\php.ini -S 127.0.0.1:8001 -t . ..\vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php
