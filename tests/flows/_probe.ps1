$base = 'http://localhost:8080'
try {
    $r = Invoke-WebRequest "$base/login" -TimeoutSec 20
    Write-Output "login page: $($r.StatusCode)"
} catch { Write-Output "ERRO login: $($_.Exception.Message)" }
