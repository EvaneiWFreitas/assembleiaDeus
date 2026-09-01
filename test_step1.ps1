$base = 'http://localhost:8080'
$sw = [Diagnostics.Stopwatch]::StartNew()
function Mark($msg){ Write-Output ("[{0,6:N0} ms] {1}" -f $sw.ElapsedMilliseconds, $msg) }
try {
    $r = Invoke-WebRequest "$base/login" -SessionVariable sess -UseBasicParsing -TimeoutSec 15
    Write-Output "login page: $($r.StatusCode)"
} catch {
    Write-Output "login page ERRO: $($_.Exception.Message)"
    exit 1
}
$s = $sess
$t = ''
if ($r.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t = $Matches[1] }
Write-Output "csrf len: $($t.Length)"
try {
    $p = Invoke-WebRequest "$base/login" -Method Post -WebSession $s -TimeoutSec 15 -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } -UseBasicParsing
    Write-Output "login post: $($p.StatusCode)"; Mark 'login ok'
    $dash = Invoke-WebRequest "$base/" -WebSession $s -UseBasicParsing -TimeoutSec 60
    if ($dash.Content -match 'Dashboard') { Write-Output 'Dashboard: OK' } else { Write-Output 'Dashboard: status '+$dash.StatusCode }
    Mark 'dash'
} catch {
    Write-Output "login/dash ERRO: $($_.Exception.Message)"
}
try {
    $h = Invoke-WebRequest "$base/" -WebSession $s -UseBasicParsing -TimeoutSec 60
    Write-Output "home: $($h.StatusCode)"
} catch {
    Write-Output "home ERRO: $($_.Exception.Message)"
}
