# File: test_igreja_flow.ps1
$base = 'http://localhost:8080'

# 1. Login + CSRF
$r = Invoke-WebRequest "$base/login" -SessionVariable sess -UseBasicParsing
$s = $sess
$html = $r.Content
if ($html -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t = $Matches[1] } else { $t = '' }
Write-Output "CSRF obtido: $($t.Length) chars"

$post = Invoke-WebRequest "$base/login" -Method Post -WebSession $s -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } -UseBasicParsing -MaximumRedirection 5
Write-Output "Login status: $($post.StatusCode)"

# 2. Form Dados da Igreja
$f = Invoke-WebRequest "$base/igreja" -WebSession $s -UseBasicParsing
if ($f.Content -match 'Dados da Igreja') { Write-Output "Form igreja OK" } else { Write-Output "Form igreja: status $($f.StatusCode)" }

# 3. Botões Editar / Salvar presentes
if ($f.Content -match 'btn-editar-igreja') { Write-Output "Botao Editar presente" }
if ($f.Content -match 'btn-salvar-igreja') { Write-Output "Botao Salvar presente" }

# 4. Pega valor atual do nome (para comparar depois)
$nomeAtual = $null
if ($f.Content -match 'name="nome"[^>]*value="([^"]*)"') { $nomeAtual = $Matches[1] }
Write-Output "Nome atual: $nomeAtual"

# 5. Captura CSRF do form
if ($f.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t2 = $Matches[1] }

# 6. Salva com nome alterado (simula o POST do botao Salvar apos Editar)
$novoNome = if ($nomeAtual -like '* (teste)') { $nomeAtual -replace ' \(teste\)$','' } else { "$nomeAtual (teste)" }
$sv = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $s -UseBasicParsing -Body @{
    csrf_test_name = $t2
    nome = $novoNome; razao_social = ''; cnpj = ''; data_fundacao = ''; pastor_responsavel = ''
    telefone = ''; whatsapp = ''; email = ''; site = ''
    cep = ''; logradouro = ''; numero = ''; complemento = ''; bairro = ''
    cidade = ''; estado = ''; pais = 'Brasil'
} -MaximumRedirection 5
Write-Output "Salvar igreja status: $($sv.StatusCode)"

# 7. Recarrega e confere se o nome foi persistido
$c = Invoke-WebRequest "$base/igreja" -WebSession $s -UseBasicParsing
if ($c.Content -match [regex]::Escape($novoNome)) {
    Write-Output "Nome persistido OK: $novoNome"
} else {
    Write-Output "ERRO: nome alterado nao encontrado apos salvar"
}

# 8. Mensagem de sucesso
if ($c.Content -match 'atualizados com sucesso' -or $sv.Content -match 'atualizados com sucesso') { Write-Output "Mensagem de sucesso OK" }
