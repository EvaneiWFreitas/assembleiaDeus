$ProgressPreference = 'SilentlyContinue'
$base = 'http://localhost:8080'
try {
    $r = Invoke-WebRequest "$base/login" -SessionVariable sess -UseBasicParsing -TimeoutSec 15
    $t = [regex]::Match($r.Content, 'name="csrf_test_name"[^>]*value="([^"]+)"').Groups[1].Value
    Write-Output ("csrf len " + $t.Length)
    $p = Invoke-WebRequest "$base/login" -Method Post -WebSession $sess -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } -TimeoutSec 20
    Write-Output ("login status " + $p.StatusCode)
} catch {
    Write-Output ("ERRO: " + $_.Exception.Message)
}
