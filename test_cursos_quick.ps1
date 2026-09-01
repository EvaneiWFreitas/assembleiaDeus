$base = 'http://localhost:8080'
$ErrorActionPreference = 'Stop'
$sw = [Diagnostics.Stopwatch]::StartNew()
function Mark($m){ Write-Output ("[{0,6} ms] {1}" -f $sw.ElapsedMilliseconds, $m) }

$r = Invoke-WebRequest "$base/login" -SessionVariable s -UseBasicParsing -TimeoutSec 90
$t = ([regex]::Match($r.Content, 'csrf_test_name"\s*value="([^"]+)')).Groups[1].Value
Mark 'login page'
$p = Invoke-WebRequest "$base/login" -Method Post -WebSession $s -Body @{csrf_test_name=$t; email='admin@igreja.com'; senha='alterar-na-primeira-utilizacao'} -UseBasicParsing -TimeoutSec 90
Mark 'login post'
try {
    $c = Invoke-WebRequest "$base/cursos" -WebSession $s -UseBasicParsing -TimeoutSec 90
    Write-Output "cursos: $($c.StatusCode) len=$($c.Content.Length)"
    if ($c.Content -match 'Cursos') { Write-Output 'Listagem cursos OK' }
} catch { Write-Output "cursos ERRO: $($_.Exception.Message)" }
Mark 'fim'
