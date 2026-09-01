# File: test_igreja_flow.ps1
# Teste de fluxo completo do modulo Dados da Igreja
$base = 'http://localhost:8080'
$PSDefaultParameterValues['Invoke-WebRequest:NoProxy'] = $true

function Get-Csrf($resp) {
    if ($resp.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { return $Matches[1] }
    return ''
}

# ---------- 1. Login ----------
$r = Invoke-WebRequest "$base/login" -SessionVariable sess
$s = $sess
$t = Get-Csrf $r
Write-Output "CSRF obtido: $($t.Length) chars"

$post = Invoke-WebRequest "$base/login" -Method Post -WebSession $s -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' }
Write-Output "Login status: $($post.StatusCode)"

# ---------- 2. Form Dados da Igreja ----------
$f = Invoke-WebRequest "$base/igreja" -WebSession $s
if ($f.Content -match 'Dados da Igreja') { Write-Output "Form igreja OK" } else { Write-Output "Form igreja: status $($f.StatusCode)" }

# ---------- 3. Form alternativo /igreja/editar ----------
$f2 = Invoke-WebRequest "$base/igreja/editar" -WebSession $s
if ($f2.Content -match 'Dados da Igreja') { Write-Output "Rota /igreja/editar OK" } else { Write-Output "Rota /igreja/editar: status $($f2.StatusCode)" }

# ---------- 4. Campos em modo leitura + botoes ----------
if ($f.Content -match 'btn-editar-igreja') { Write-Output "Botao Editar presente OK" } else { Write-Output "Botao Editar ausente" }
if ($f.Content -match 'btn-salvar-igreja') { Write-Output "Botao Salvar presente OK" } else { Write-Output "Botao Salvar ausente" }
if ($f.Content -match "name=`"nome`"[^>]*disabled") { Write-Output "Campos desabilitados (modo leitura) OK" } else { Write-Output "Campos nao estao desabilitados" }

# Guardar valores originais (para restaurar depois)
$nomeOriginal = $null
if ($f.Content -match 'name="nome"[^>]*value="([^"]*)"') { $nomeOriginal = $Matches[1] }
Write-Output "Nome atual da igreja: $nomeOriginal"
$t2 = Get-Csrf $f

# ---------- 5. Salvar edicao (POST /igreja/atualizar) ----------
$nomeNovo = "$nomeOriginal - Editado"
$sv = Invoke-WebRequest "$base/igreja/atualizar" -Method Post -WebSession $s -Body @{
    csrf_test_name = $t2
    razao_social   = 'Igreja Teste LTDA'
    nome           = $nomeNovo
    cnpj           = '12.345.678/0001-90'
    telefone       = '(11) 99999-0000'
    whatsapp       = '(11) 99999-0000'
    email          = 'contato@teste.com'
    site           = 'https://teste.com'
    cep            = '01234-567'
    logradouro     = 'Rua Teste'
    numero         = '100'
    complemento    = 'Sala 1'
    bairro         = 'Centro'
    cidade         = 'Sao Paulo'
    estado         = 'SP'
    pais           = 'Brasil'
    pastor_responsavel = 'Pastor Teste'
    data_fundacao  = '1990-01-01'
} -MaximumRedirection 5
if ($sv.Content -match 'atualizados com sucesso') { Write-Output "Salvar edicao OK" } else { Write-Output "Salvar edicao: status $($sv.StatusCode)" }

# ---------- 6. Validar persistencia ----------
$v = Invoke-WebRequest "$base/igreja" -WebSession $s
if ($v.Content -match [regex]::Escape($nomeNovo)) { Write-Output "Persistencia do nome editado OK" } else { Write-Output "Nome editado NAO apareceu no form" }
if ($v.Content -match 'Sao Paulo') { Write-Output "Persistencia endereco OK" } else { Write-Output "Endereco NAO persistiu" }

# ---------- 7. Validacao: nome vazio deve falhar ----------
$inv = Invoke-WebRequest "$base/igreja/atualizar" -Method Post -WebSession $s -Body @{
    csrf_test_name = (Get-Csrf $v); nome = ''; cnpj = ''
} -MaximumRedirection 5
if ($inv.Content -match 'obrigat|nome|invalid') { Write-Output "Validacao nome vazio OK (voltou com erro)" } else { Write-Output "Validacao nome vazio: sem mensagem visivel" }

# ---------- 8. Restaurar dados originais ----------
$re = Invoke-WebRequest "$base/igreja/atualizar" -Method Post -WebSession $s -Body @{
    csrf_test_name = (Get-Csrf $v)
    razao_social   = ''; nome = $nomeOriginal; cnpj = ''; telefone = ''; whatsapp = ''
    email = ''; site = ''; cep = ''; logradouro = ''; numero = ''; complemento = ''
    bairro = ''; cidade = ''; estado = ''; pais = 'Brasil'; pastor_responsavel = ''; data_fundacao = ''
} -MaximumRedirection 5
if ($re.Content -match 'atualizados com sucesso') { Write-Output "Restauracao OK" } else { Write-Output "Restauracao: status $($re.StatusCode)" }

Write-Output '--- FIM test_igreja_flow ---'
