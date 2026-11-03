# Starts the local MySQL 8.4 instance used for UB Shield development.
# No Windows service is registered (that needs an elevated shell), so run this
# after a reboot, or see README.md for the one-line service install.
$BASE = "C:\Program Files\MySQL\MySQL Server 8.4"
$INI  = "C:\Users\HP\AppData\Local\UBShield-MySQL\my.ini"
if ((Test-NetConnection 127.0.0.1 -Port 3306 -WarningAction SilentlyContinue).TcpTestSucceeded) {
    Write-Host "MySQL already listening on 3306."; exit 0
}
Start-Process -FilePath "$BASE\bin\mysqld.exe" -ArgumentList "--defaults-file=`"$INI`"" -WindowStyle Hidden
for ($i = 0; $i -lt 30; $i++) {
    Start-Sleep -Milliseconds 500
    if ((Test-NetConnection 127.0.0.1 -Port 3306 -WarningAction SilentlyContinue).TcpTestSucceeded) {
        Write-Host "MySQL started on 127.0.0.1:3306."; exit 0
    }
}
Write-Error "MySQL did not start. See C:\Users\HP\AppData\Local\UBShield-MySQL\mysqld.log"
exit 1
