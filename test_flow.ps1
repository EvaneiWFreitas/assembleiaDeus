# test_flow.ps1 - smoke test completo: Login -> Dados da Igreja (editar/salvar) -> Cursos -> Membros
$base   = 'http://localhost:8080'
$cookie = Join-Path $env:TEMP 'ci_cookie.txt'
Remove-Item $cookie -ErrorAction SilentlyContinue

function Get-Csrf($url) {
    $r = Invoke-WebRequest "$base$url" -WebSession $s -TimeoutSec 15
    if ($r.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { return $Matches[1] }
    return ''
}

function Step($msg) { Write-Output "== $msg" }

# ---------- 1. Login ----------
Step 'Login'
$r = Invoke-WebRequest "$base/login" -SessionVariable sess
$s = $sess
$t = ''
if ($r.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t = $Matches[1] }
$post = Invoke-WebRequest "$base/login" -Method Post -WebSession $s -TimeoutSec 15 -Body @{
    csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao'
}
Write-Output ("Login status: " + $post.StatusCode)

# ---------- 2. Dados da Igreja ----------
Step 'Dados da Igreja'
$t = Get-Csrf '/igreja'
Write-Output ("Form igreja carregado, CSRF: " + $t.Length + " chars")

$sv = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $s -TimeoutSec 15 -Body @{
    csrf_test_name     = $t
    razao_social       = 'Igreja Teste Ltda'
    nome               = 'Igreja Teste Automatico'
    cnpj               = '11.222.333/0001-81'
    telefone           = '(11) 3333-3333'
    whatsapp           = '(11) 99999-9999'
    email              = 'contato@igrejateste.com.br'
    site               = 'https://igrejateste.com.br'
    cep                = '01310-100'
    logradouro         = 'Av. Paulista'
    numero             = '1000'
    complemento        = 'Sala 1'
    bairro             = 'Bela Vista'
    cidade             = 'Sao Paulo'
    estado             = 'SP'
    pais               = 'Brasil'
    pastor_responsavel = 'Pastor Teste'
    data_fundacao      = '1950-01-01'
}
Write-Output ("Salvar igreja status: " + $sv.StatusCode)
if ($sv.Content -match 'Dados da igreja atualizados com sucesso') { Write-Output 'MSG sucesso igreja OK' }
else { Write-Output 'MSG sucesso igreja NAO encontrada' }

# Verificar persistencia
$v = Invoke-WebRequest "$base/igreja" -WebSession $s
if ($v.Content -match 'Igreja Teste Automatico') { Write-Output 'Dado gravado no banco: OK' }
else { Write-Output 'Dado gravado no banco: NAO ENCONTRADO' }

# ---------- 3. Cursos ----------
Step 'Cursos'
$c = Invoke-WebRequest "$base/cursos" -WebSession $s
if ($c.Content -match 'Cursos') { Write-Output 'Listagem cursos OK' }

$t = Get-Csrf '/cursos/criar'
$sv = Invoke-WebRequest "$base/cursos/salvar" -Method Post -WebSession $s -TimeoutSec 15 -Body @{
    csrf_test_name = $t
    nome           = 'Curso Teste Automatico'
    professor_id   = ''
    status         = 'Planejado'
    ativo          = '1'
    descricao      = 'Curso criado pelo smoke test'
}
Write-Output ("Salvar curso status: " + $sv.StatusCode)

$editOk = $false
foreach ($i in 1..10) {
    try {
        $e = Invoke-WebRequest "$base/cursos/editar/$i" -WebSession $s
        if ($e.Content -match 'Aulas') { Write-Output ("Form editar curso/$i OK"); $editOk = $true; break }
    } catch {}
}
if (-not $editOk) { Write-Output 'Form editar curso nao verificado' }
if ($e.Content -match 'Selecione o membro para matricular') { Write-Output 'Dropdown membros OK' }

# Atualizar o curso criado
$cid = $null
$list = Invoke-WebRequest "$base/cursos?q=Curso+Teste+Automatico" -WebSession $s
if ($list.Content -match '/cursos/editar/(\d+)') { $cid = $Matches[1] }
if ($cid) {
    $t = Get-Csrf "/cursos/editar/$cid"
    $up = Invoke-WebRequest "$base/cursos/atualizar/$cid" -Method Post -WebSession $s -TimeoutSec 15 -Body @{
        csrf_test_name = $t
        nome           = 'Curso Teste Automatico Editado'
        professor_id   = ''
        status         = 'Em andamento'
        ativo          = '1'
        descricao      = 'Editado pelo smoke test'
    }
    Write-Output ("Atualizar curso status: " + $up.StatusCode)
} else { Write-Output 'Curso criado nao localizado na listagem' }

# ---------- 4. Membros ----------
Step 'Membros'
$m = Invoke-WebRequest "$base/membros" -WebSession $s
if ($m.Content -match 'Membros') { Write-Output 'Listagem membros OK' }

$t = Get-Csrf '/membros/novo'
$sv = Invoke-WebRequest "$base/membros/salvar" -Method Post -WebSession $s -TimeoutSec 15 -Body @{
    csrf_test_name    = $t
    nome              = 'Membro Teste Automatico'
    nome_social       = ''
    cpf               = ''
    rg                = ''
    data_nascimento   = '1990-05-10'
    sexo              = 'M'
    estado_civil      = 'Casado'
    telefone          = '(11) 98888-7777'
    email             = 'membroteste@teste.com'
    cep               = ''
    logradouro        = 'Rua Teste'
    numero            = '10'
    bairro            = 'Centro'
    cidade            = 'Sao Paulo'
    estado            = 'SP'
    congregacao_id    = ''
    tipo_membro       = 'Batizado'
    cargo             = ''
    data_conversao    = ''
    data_batismo      = ''
    data_recebimento  = ''
    status            = 'Ativo'
    observacoes       = 'Criado pelo smoke test'
}
Write-Output ("Salvar membro status: " + $sv.StatusCode)
if ($sv.Content -match 'Membro cadastrado com sucesso') { Write-Output 'MSG sucesso membro OK' }

# Buscar o membro criado e abrir edicao
$mid = $null
$busca = Invoke-WebRequest "$base/membros?q=Membro+Teste+Automatico" -WebSession $s
if ($busca.Content -match '/membros/editar/(\d+)') { $mid = $Matches[1] }
if ($mid) {
    $e = Invoke-WebRequest "$base/membros/editar/$mid" -WebSession $s
    if ($e.Content -match 'Editar Membro') { Write-Output ("Form editar membro/$mid OK") }
    $t = Get-Csrf "/membros/editar/$mid"
    $up = Invoke-WebRequest "$base/membros/atualizar/$mid" -Method Post -WebSession $s -TimeoutSec 15 -Body @{
        csrf_test_name    = $t
        nome              = 'Membro Teste Automatico Editado'
        nome_social       = ''
        cpf               = ''
        rg                = ''
        data_nascimento   = '1990-05-10'
        sexo              = 'F'
        estado_civil      = 'Casado'
        telefone          = '(11) 98888-7777'
        email             = 'membroteste@teste.com'
        cep               = ''
        logradouro        = 'Rua Teste'
        numero            = '10'
        bairro            = 'Centro'
        cidade            = 'Sao Paulo'
        estado            = 'SP'
        congregacao_id    = ''
        tipo_membro       = 'Batizado'
        cargo             = ''
        data_conversao    = ''
        data_batismo      = ''
        data_recebimento  = ''
        status            = 'Ativo'
        observacoes       = 'Editado pelo smoke test'
    }
    Write-Output ("Atualizar membro status: " + $up.StatusCode)
    if ($up.Content -match 'Membro atualizado com sucesso') { Write-Output 'MSG update membro OK' }

    # Ficha
    $f = Invoke-WebRequest "$base/membros/ficha/$mid" -WebSession $s
    if ($f.Content -match 'Ficha do Membro') { Write-Output 'Ficha membro OK' }
} else {
    Write-Output 'Membro criado nao localizado na listagem'
}

Write-Output 'FIM'
