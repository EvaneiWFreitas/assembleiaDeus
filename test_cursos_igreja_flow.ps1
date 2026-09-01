# ============================================================
# Teste automatizado: Cursos (completo) + Dados da Igreja
# Uso: powershell -ExecutionPolicy Bypass -File test_cursos_igreja_flow.ps1
# Requer: servidor rodando em http://localhost:8080 e usuário admin
# ============================================================
$base = 'http://localhost:8080'
$ok = 0; $falha = 0
function Assert($nome, $cond) {
    if ($cond) { $script:ok++;   Write-Output "[OK]    $nome" }
    else       { $script:falha++; Write-Output "[FALHA] $nome" }
}
function Get-Csrf($html) {
    if ($html -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { return $Matches[1] }
    if ($html -match 'name="csrf_test_name"\s+content="([^"]+)"') { return $Matches[1] }
    return ''
}

# ------------------------------------------------------------
# 0. Login
# ------------------------------------------------------------
$r = Invoke-WebRequest "$base/login" -SessionVariable sess
$s = $sess
$t = Get-Csrf $r.Content
Assert "Login: pagina carregada" ($r.StatusCode -eq 200)
Assert "Login: CSRF presente" ($t.Length -gt 0)

$post = Invoke-WebRequest "$base/login" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' }
Assert "Login: autenticado" ($post.StatusCode -eq 200)

# ============================================================
# PARTE 1 - MODULO CURSOS
# ============================================================
Write-Output "`n===== CURSOS ====="

# 1.1 Listagem
$c = Invoke-WebRequest "$base/cursos" -WebSession $s -UseBasicParsing -TimeoutSec 30
Assert "Cursos: listagem" ($c.Content -match 'Cursos')

# 1.2 Form criar
$f = Invoke-WebRequest "$base/cursos/novo" -WebSession $s -UseBasicParsing -TimeoutSec 30
Assert "Cursos: form criar" ($f.Content -match 'Nome do Curso')
$t2 = Get-Csrf $f.Content

# 1.3 Salvar curso novo
$nomeCurso = 'Curso Teste Automatico ' + (Get-Date -Format 'yyyyMMddHHmmss')
$sv = Invoke-WebRequest "$base/cursos/salvar" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body @{ csrf_test_name = $t2; nome = $nomeCurso; status = 'Planejado'; ativo = '1'; descricao = 'curso de teste automatizado' } -MaximumRedirection 5
Assert "Cursos: salvar novo curso" ($sv.StatusCode -eq 200 -and $sv.Content -match 'cadastrado com sucesso')

# 1.4 Localizar id do curso criado na listagem
$c = Invoke-WebRequest "$base/cursos" -WebSession $s -UseBasicParsing -TimeoutSec 30
$cursoId = $null
if ($c.Content -match "/cursos/editar/(\d+)[^>]*>\s*[^<]*$nomeCurso") { $cursoId = $Matches[1] }
if (-not $cursoId) {
    # fallback: procurar qualquer id de edicao e checar pelo nome
    $ms = [regex]::Matches($c.Content, '/cursos/editar/(\d+)')
    foreach ($m in $ms) {
        $e = Invoke-WebRequest "$base/cursos/editar/$($m.Groups[1].Value)" -WebSession $s -UseBasicParsing -TimeoutSec 30
        if ($e.Content -match [regex]::Escape($nomeCurso)) { $cursoId = $m.Groups[1].Value; break }
    }
}
Assert "Cursos: curso criado localizado (id=$cursoId)" ($null -ne $cursoId)

# 1.5 Form editar (com Aulas e Alunos)
$e = Invoke-WebRequest "$base/cursos/editar/$cursoId" -WebSession $s -UseBasicParsing -TimeoutSec 30
Assert "Cursos: form editar tem secao Aulas" ($e.Content -match 'Aulas')
Assert "Cursos: form editar tem secao Alunos" ($e.Content -match 'Alunos')
$t3 = Get-Csrf $e.Content

# 1.6 Atualizar curso
$sv2 = Invoke-WebRequest "$base/cursos/atualizar/$cursoId" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body @{ csrf_test_name = $t3; nome = $nomeCurso + ' EDITADO'; status = 'Em Andamento'; ativo = '1'; descricao = 'editado via teste' } -MaximumRedirection 5
Assert "Cursos: atualizar curso" ($sv2.StatusCode -eq 200 -and $sv2.Content -match 'atualizado com sucesso')

# 1.7 Adicionar aula
$sv3 = Invoke-WebRequest "$base/cursos/adicionarAula/$cursoId" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body @{ csrf_test_name = $t3; data = (Get-Date -Format 'yyyy-MM-dd'); tema = 'Aula Teste Automatico'; conteudo = 'conteudo teste' } -MaximumRedirection 5
Assert "Cursos: adicionar aula" ($sv3.StatusCode -eq 200 -and $sv3.Content -match 'Aula adicionada')

# 1.8 Dropdown de membros para matricula
$e2 = Invoke-WebRequest "$base/cursos/editar/$cursoId" -WebSession $s -UseBasicParsing -TimeoutSec 30
$temDropdown = $e2.Content -match 'Selecione o membro para matricular|membro_id'
$temMembros  = $e2.Content -match '<option value="(\d+)"'
Assert "Cursos: dropdown de membros presente" $temDropdown

# 1.9 Matricular membro (pega primeiro membro do dropdown)
$membroId = $null
if ($e2.Content -match 'name="membro_id"[\s\S]{0,2000}?<option value="(\d+)"') { $membroId = $Matches[1] }
if ($membroId) {
    $sv4 = Invoke-WebRequest "$base/cursos/matricular/$cursoId" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body @{ csrf_test_name = $t3; membro_id = $membroId } -MaximumRedirection 5
    Assert "Cursos: matricular membro" ($sv4.StatusCode -eq 200 -and ($sv4.Content -match 'matriculado|j\u00e1 est\u00e1 matriculado'))

    # 1.10 Atualizar dados do aluno (localizar aluno_id)
    if ($sv4.Content -match "atualizarAluno/$cursoId/(\d+)") {
        $alunoId = $Matches[1]
        $sv5 = Invoke-WebRequest "$base/cursos/atualizarAluno/$cursoId/$alunoId" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body @{ csrf_test_name = $t3; frequencia = '85.5'; nota_final = '9'; status = 'Concluido'; certificado = '1' } -MaximumRedirection 5
        Assert "Cursos: atualizar aluno (frequencia/nota/status)" ($sv5.StatusCode -eq 200 -and $sv5.Content -match 'Dados do aluno atualizados')

        # 1.11 Remover aluno
        $sv6 = Invoke-WebRequest "$base/cursos/removerAluno/$cursoId/$alunoId" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body @{ csrf_test_name = $t3 } -MaximumRedirection 5
        Assert "Cursos: remover aluno" ($sv6.StatusCode -eq 200 -and $sv6.Content -match 'removido do curso')
    } else {
        Assert "Cursos: aluno localizado para atualizar" $false
    }
} else {
    Write-Output "[AVISO] Sem membros no dropdown - matricula/alunos nao testados"
}

# 1.12 Excluir curso criado
$sv7 = Invoke-WebRequest "$base/cursos/excluir/$cursoId" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body @{ csrf_test_name = $t3 } -MaximumRedirection 5
Assert "Cursos: excluir curso" ($sv7.StatusCode -eq 200 -and $sv7.Content -match 'exclu\u00eddo com sucesso|excluido com sucesso')

# ============================================================
# PARTE 2 - DADOS DA IGREJA
# ============================================================
Write-Output "`n===== DADOS DA IGREJA ====="

# 2.1 Form (rota GET /igreja)
$g = Invoke-WebRequest "$base/igreja" -WebSession $s -UseBasicParsing -TimeoutSec 30
Assert "Igreja: formulario carregado" ($g.StatusCode -eq 200 -and $g.Content -match 'Dados da Igreja')
Assert "Igreja: campo Nome da Igreja presente" ($g.Content -match 'name="nome"')
Assert "Igreja: botao Editar presente" ($g.Content -match 'btn-editar-igreja')
Assert "Igreja: botao Salvar presente (hidden ate editar)" ($g.Content -match 'btn-salvar-igreja')
Assert "Igreja: campos iniciam desabilitados (modo leitura)" ($g.Content -match 'el\.disabled = true')
$tg = Get-Csrf $g.Content

# 2.2 Salvar dados editados (POST /igreja) - simula o envio apos clicar em Editar > Salvar
$dadosIgreja = @{
    csrf_test_name     = $tg
    nome               = 'Assembleia de Deus Teste'
    razao_social       = 'AD Teste Editada'
    cnpj               = '12.345.678/0001-90'
    data_fundacao      = '1985-05-10'
    pastor_responsavel = 'Pastor Teste'
    telefone           = '(11) 3333-4444'
    whatsapp           = '(11) 95555-6666'
    email              = 'contato@igrejateste.com'
    site               = 'https://igrejateste.com'
    cep                = '01234-567'
    logradouro         = 'Rua Teste'
    numero             = '123'
    complemento        = 'Sala 1'
    bairro             = 'Centro'
    cidade             = 'Sao Paulo'
    estado             = 'SP'
    pais               = 'Brasil'
}
$svi = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body $dadosIgreja -MaximumRedirection 5
Assert "Igreja: salvar dados editados" ($svi.StatusCode -eq 200 -and $svi.Content -match 'atualizados com sucesso')

# 2.3 Confirmar persistencia
$g2 = Invoke-WebRequest "$base/igreja" -WebSession $s -UseBasicParsing -TimeoutSec 30
Assert "Igreja: dados persistidos apos salvar" ($g2.Content -match 'AD Teste Editada')

# 2.4 Validação: nome vazio deve ser rejeitado
$svi2 = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $s -UseBasicParsing -TimeoutSec 30 -UseBasicParsing -TimeoutSec 30 -Body (@{ csrf_test_name = (Get-Csrf $g2.Content); nome = '' } ) -MaximumRedirection 5
Assert "Igreja: validacao rejeita nome vazio" ($svi2.Content -match 'alert-warning|obrigat\u00f3rio|required')

# ------------------------------------------------------------
Write-Output "`n===== RESUMO ====="
Write-Output "Sucessos: $ok | Falhas: $falha"
if ($falha -gt 0) { exit 1 }
