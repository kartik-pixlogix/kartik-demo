<#
.SYNOPSIS
    Puts the local site on a public HTTPS URL so the team can review it.

.DESCRIPTION
    Opens a Cloudflare Quick Tunnel to the local dev server and prints a
    https://<something>.trycloudflare.com address that anyone can open.

    Before it does, it turns APP_DEBUG off. That matters: with debug on,
    any error page shows a stack trace carrying database credentials and the
    app key, and this URL is reachable by anyone who has it. The original
    .env is restored when you stop the tunnel with Ctrl-C.

    The URL lives only as long as this window stays open, and it changes
    every run. For a link that survives a reboot, see docs/preview-url.md.

.EXAMPLE
    # terminal 1
    .\bin\php.cmd artisan serve
    # terminal 2
    .\scripts\share-preview.ps1
#>

$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$envFile = Join-Path $root '.env'
$backup = Join-Path $root '.env.preview-backup'
$cloudflared = 'C:\Program Files (x86)\cloudflared\cloudflared.exe'

if (-not (Test-Path $cloudflared)) {
    Write-Host "cloudflared not found at $cloudflared" -ForegroundColor Red
    Write-Host "Install it with:  winget install --id Cloudflare.cloudflared"
    exit 1
}

# The dev server has to be up first — the tunnel only forwards to it.
try {
    $null = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/' -UseBasicParsing -TimeoutSec 5
} catch {
    Write-Host "Nothing is answering on http://127.0.0.1:8000" -ForegroundColor Red
    Write-Host "Start it in another terminal first:  .\bin\php.cmd artisan serve"
    exit 1
}

Copy-Item $envFile $backup -Force

try {
    # Debug off: an error page would otherwise print the database password
    # and APP_KEY to whoever opens the link.
    (Get-Content $envFile) -replace '^APP_DEBUG=true', 'APP_DEBUG=false' |
        Set-Content $envFile -Encoding utf8
    & (Join-Path $root 'bin\php.cmd') artisan config:clear | Out-Null

    Write-Host ''
    Write-Host 'APP_DEBUG is off for the life of this tunnel.' -ForegroundColor DarkGray
    Write-Host 'Starting the tunnel — the https://...trycloudflare.com line below is the URL to share.' -ForegroundColor Cyan
    Write-Host 'Leave this window open. Ctrl-C closes the tunnel and restores your .env.' -ForegroundColor DarkGray
    Write-Host ''

    & $cloudflared tunnel --url http://127.0.0.1:8000 --no-autoupdate
} finally {
    Move-Item $backup $envFile -Force
    & (Join-Path $root 'bin\php.cmd') artisan config:clear | Out-Null
    Write-Host ''
    Write-Host 'Tunnel closed, .env restored.' -ForegroundColor DarkGray
}
