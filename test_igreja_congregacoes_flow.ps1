# File: test_igreja_congregacoes_flow.ps1
# Testes de fluxo: Dados da Igreja (editar/salvar) + Congregacoes
$base = 'http://localhost:8080'
$ProgressPreference = 'SilentlyContinue'
$s = $null

function Get-Csrff($resp) {
    if ($resp.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { return $Matches[1] }
    return ''
}

# 1. Login + CSRF
$r = Invoke-WebRequest "$base/login" -SessionVariable sess -UseBasicParsing
$s = $sess
$t = Get-Csrff $r
$post = Invoke-WebRequest "$base/login" -Method Post -WebSession $s -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } -UseBasicParsing
Write-Output "Login status: $($post.StatusCode)"

# ============================================================
# MODULO: DADOS DA IGREJA
# ============================================================
Write-Output "`n--- Igreja ---"

# Form
$f = Invoke-WebRequest "$base/igreja" -WebSession $s -UseBasicParsing
if ($f.Content -match 'Dados da Igreja') { Write-Output "Form igreja OK" } else { Write-Output "Form igreja: FALHOU ($($f.StatusCode))" }

# Campos presentes
if ($f.Content -match 'name="cnpj"') { Write-Output "Campo CNPJ OK" }
if ($f.Content -match 'btn-editar-igreja') { Write-Output "Botao Editar presente OK" }
if ($f.Content -match 'btn-salvar-igreja') { Write-Output "Botao Salvar presente OK" }
if ($f.Content -match 'name="logo"') { Write-Output "Upload logo presente OK" }

# Salvar dados editados
$t2 = Get-Csrff $f
$nomeNovo = 'Igreja Teste Automatica ' + (Get-Date -Format 'HHmmss')
$sv = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $s -Body @{
    csrf_test_name = $t2
    razao_social = 'Igreja Teste LTDA'
    nome = $nomeNovo
    cnpj = '12.345.678/0001-90'
    telefone = '(11) 3333-3333'
    whatsapp = '(11) 99999-9999'
    email = 'teste@igreja.com'
    site = 'https://teste.com'
    cep = '01234-567'
    logradouro = 'Rua Teste'
    numero = '123'
    complemento = 'Sala 1'
    bairro = 'Centro'
    cidade = 'Sao Paulo'
    estado = 'SP'
    pais = 'Brasil'
    pastor_responsavel = 'Pastor Teste'
    data_fundacao = '1990-01-01'
} -MaximumRedirection 5
Write-Output "Salvar igreja status: $($sv.StatusCode)"
if ($sv.Content -match 'atualizados com sucesso') { Write-Output "Mensagem de sucesso OK" }

# Confere persistencia (recarrega form e ve o nome salvo)
$f2 = Invoke-WebRequest "$base/igreja" -WebSession $s
if ($f2.Content -match [regex]::Escape($nomeNovo)) { Write-Output "Persistencia do nome editado OK" } else { Write-Output "Persistencia: nome editado NAO encontrado" }

# Validacao: CNPJ curto deve rejeitar
$t3 = Get-Csrff $f2
$inv = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $s -Body @{
    csrf_test_name = $t3; nome = 'Igreja Valida'; cnpj = '123'
} -MaximumRedirection 5
if ($inv.Content -match 'alert-warning|CNPJ') { Write-Output "Validacao CNPJ invalido OK (rejeitado)" } else { Write-Output "Validacao CNPJ: VERIFICAR" }

# ============================================================
# MODULO: CONGREGACOES
# ============================================================
Write-Output "`n--- Congregacoes ---"

$c = Invoke-WebRequest "$base/congregacoes" -WebSession $s
if ($c.Content -match 'Congrega') { Write-Output "Listagem congregacoes OK" } else { Write-Output "Listagem: status $($c.StatusCode)" }

$fc = Invoke-WebRequest "$base/congregacoes/novo" -WebSession $s
if ($fc.Content -match 'name="nome"') { Write-Output "Form criar congregacao OK" } else { Write-Output "Form criar: status $($fc.StatusCode)" }

$t4 = Get-Csrff $fc
$sv2 = Invoke-WebRequest "$base/congregacoes/salvar" -Method Post -WebSession $s -Body @{
    csrf_test_name = $t4
    nome = 'Congregacao Teste Automatica'
    endereco = 'Rua das Testes, 100'
    responsavel = 'Lider Teste'
    telefone = '(11) 2222-2222'
    ativo = '1'
} -MaximumRedirection 5
Write-Output "Salvar congregacao status: $($sv2.StatusCode)"

# Editar: procura id
$editOk = $false
$eid = $null
foreach ($i in 1..20) {
    try {
        $e = Invoke-WebRequest "$base/congregacoes/editar/$i" -WebSession $s
        if ($e.Content -match 'name="nome"') { Write-Output "Form editar/$i OK"; $editOk = $true; $eid = $i; break }
    } catch {}
}
if (-not $editOk) { Write-Output "Form editar congregacao nao verificado" }

if ($eid) {
    $t5 = Get-Csrff $e
    $sv3 = Invoke-WebRequest "$base/congregacoes/atualizar/$eid" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t5
        nome = 'Congregacao Teste Editada'
        endereco = 'Rua Editada, 200'
        responsavel = 'Lider Editado'
        telefone = '(11) 2222-3333'
        ativo = '1'
    } -MaximumRedirection 5
    Write-Output "Atualizar congregacao/$eid status: $($sv3.StatusCode)"
}

Write-Output "`nFim dos testes."
