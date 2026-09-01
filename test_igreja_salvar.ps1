# File: test_igreja_salvar.ps1
# Testa o fluxo de EDICAO + SALVAR dos Dados da Igreja (POST /igreja)
$base = 'http://localhost:8080'
$ProgressPreference = 'SilentlyContinue'

# 1. Login + CSRF
$r = Invoke-WebRequest "$base/login" -SessionVariable sess -UseBasicParsing
$html = $r.Content
if ($html -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t = $Matches[1] }
$login = Invoke-WebRequest "$base/login" -Method Post -WebSession $sess -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } -UseBasicParsing
Write-Output "Login status: $($login.StatusCode)"

# 2. Form da igreja
$f = Invoke-WebRequest "$base/igreja" -WebSession $sess -UseBasicParsing
if ($f.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t2 = $Matches[1] } else { $t2 = '' }
Write-Output "CSRF form: $($t2.Length) chars"

# 3. Verifica botao editar/salvar
if ($f.Content -match '<button[^>]*>.*?Editar') { Write-Output "Botao Editar presente" }
if ($f.Content -match '<button[^>]*type="submit"') { Write-Output "Botao Salvar presente" }

# 4. POST salvando dados editados
$sv = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $sess -Body @{
    csrf_test_name = $t2
    razao_social = 'Igreja Teste LTDA'
    nome = 'Igreja Teste Editada'
    cnpj = '12345678000199'
    telefone = '(11) 1111-1111'
    whatsapp = '(11) 91111-1111'
    email = 'contato@igrejateste.com'
    site = 'https://igrejateste.com'
    cep = '01234567'
    logradouro = 'Rua Teste'
    numero = '123'
    complemento = 'Sala 1'
    bairro = 'Centro'
    cidade = 'Sao Paulo'
    estado = 'SP'
    pais = 'Brasil'
    pastor_responsavel = 'Pastor Teste'
    data_fundacao = '1980-01-01'
} -UseBasicParsing
Write-Output "Salvar status: $($sv.StatusCode)"

if ($sv.Content -match 'Dados da igreja atualizados') { Write-Output "Mensagem de sucesso OK" } else { Write-Output "SEM mensagem de sucesso" }
if ($sv.Content -match 'Igreja Teste Editada') { Write-Output "Nome editado visivel na pagina" } else { Write-Output "Nome editado NAO visivel" }
if ($sv.Content -match 'Igreja Teste LTDA') { Write-Output "Razao social persistida OK" }

# 5. Valores nos inputs (recarrega o form e verifica value preenchido)
$f2 = Invoke-WebRequest "$base/igreja" -WebSession $sess -UseBasicParsing
if ($f2.Content -match 'value="Pastor Teste"') { Write-Output "Campos persistidos no formulario OK" } else { Write-Output "Campos NAO persistidos" }
if ($f2.Content -match 'value="Pastor Teste"') { Write-Output "Pastor persistido" }
