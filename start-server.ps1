param(
  [string] $HostAddress = "127.0.0.1",
  [int] $Port = 8000
)

$ErrorActionPreference = "Stop"

try {
  $phpVersion = & php -v 2>$null | Select-Object -First 1
} catch {
  Write-Host "PHP was not found on PATH. Install PHP 8+ or add php.exe to PATH, then re-run." -ForegroundColor Red
  exit 1
}

Write-Host ("Using: " + $phpVersion) -ForegroundColor DarkGray
Write-Host ("Starting server at http://{0}:{1}" -f $HostAddress, $Port) -ForegroundColor Green
Write-Host "Press Ctrl+C to stop." -ForegroundColor DarkGray

& php -S "$HostAddress`:$Port" server.php

