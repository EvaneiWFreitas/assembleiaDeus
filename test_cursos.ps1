$base = 'http://localhost/assembleiaDeus/public'
$s = New-Object Microsoft.PowerShell.Commands.WebRequestSession
$c = Invoke-WebRequest -Session $s -Uri "$base/login" -UseBasicParsing
if ($c.Content -match 'name="[^"]*csrf[^"]*"\s+value="([^"]+)"') { $t = $Matches[1] } else { $t = '' }
Write-Output "csrf: $t"
$r = Invoke-WebRequest -Session $s -Uri "$base/login" -Method Post -Body @{email='admin@igreja.com'; senha='alterar-na-primeira-utilizacao'; csrf_test_name=$t} -UseBasicParsing -MaximumRedirection 5 -ErrorAction SilentlyContinue
Write-Output "login status: $($r.StatusCode)"
$r2 = Invoke-WebRequest -Session $s -Uri "$base/cursos" -UseBasicParsing
Write-Output "cursos status: $($r2.StatusCode)"
$r3 = Invoke-WebRequest -Session $s -Uri "$base/cursos/novo" -UseBasicParsing
Write-Output "form status: $($r3.StatusCode)"
if ($r3.Content -match 'Nome do Curso') { Write-Output 'form OK: campo Nome do Curso presente' } else { Write-Output 'form: campo NAO encontrado' }
if ($r3.Content -match 'Login|Entrar') { Write-Output 'parece tela de login' }
if ($r2.Content -match '<title>(.*?)</title>') { Write-Output "cursos title: $($Matches[1])" }
if ($r3.Content -match '<title>(.*?)</title>') { Write-Output "form title: $($Matches[1])" }
$r3.Content.Substring(0, [Math]::Min(600, $r3.Content.Length)) | Write-Output
