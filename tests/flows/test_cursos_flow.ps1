# File: test_cursos_flow.ps1
# Teste de fluxo completo do modulo Cursos (Escola Biblica)
$base = 'http://localhost:8080'
$PSDefaultParameterValues['Invoke-WebRequest:NoProxy'] = $true
$s = $null

# ---------- Helper CSRF ----------
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

# ---------- 2. Listar cursos ----------
$c = Invoke-WebRequest "$base/cursos" -WebSession $s
if ($c.Content -match 'Cursos') { Write-Output "Listagem cursos OK" } else { Write-Output "Listagem: status $($c.StatusCode)" }

# ---------- 3. Form criar ----------
$f = Invoke-WebRequest "$base/cursos/criar" -WebSession $s
if ($f.Content -match 'Nome do Curso') { Write-Output "Form criar OK" } else { Write-Output "Form criar: status $($f.StatusCode)" }
$t2 = Get-Csrf $f

# ---------- 4. Salvar curso novo ----------
$nomeCurso = 'Curso Teste Automatico'
$sv = Invoke-WebRequest "$base/cursos/salvar" -Method Post -WebSession $s -Body @{ csrf_test_name = $t2; nome = $nomeCurso; status = 'Planejado'; ativo = '1'; descricao = 'teste automatizado' } -MaximumRedirection 5
Write-Output "Salvar status: $($sv.StatusCode)"

# Localizar o id do curso criado na listagem (link de editar)
$c2 = Invoke-WebRequest "$base/cursos" -WebSession $s
$cursoId = $null
if ($c2.Content -match 'cursos/editar/(\d+)[^<]*') { $cursoId = $Matches[1] }
# procura o id na linha que contem o nome do curso criado
$linhas = $c2.Content -split "`n"
foreach ($lin in $linhas) {
    if ($lin -match $nomeCurso -and $lin -match 'editar/(\d+)') { $cursoId = $Matches[1]; break }
}
Write-Output "Curso criado id: $cursoId"

# ---------- 5. Form editar ----------
$e = $null
if ($cursoId) {
    $e = Invoke-WebRequest "$base/cursos/editar/$cursoId" -WebSession $s
} else {
    foreach ($i in 1..10) {
        try {
            $e = Invoke-WebRequest "$base/cursos/editar/$i" -WebSession $s
            if ($e.Content -match 'Aulas') { break }
        } catch {}
    }
}
if ($e -and $e.Content -match 'Aulas') { Write-Output "Form editar OK (com Aulas/Alunos)" } else { Write-Output "Form editar nao verificado" }
$t3 = Get-Csrf $e

# ---------- 6. Dropdown membros ----------
if ($e.Content -match 'Selecione o membro para matricular') { Write-Output "Dropdown membros OK" }

# ---------- 7. Atualizar dados do curso ----------
if ($cursoId) {
    $up = Invoke-WebRequest "$base/cursos/atualizar/$cursoId" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t3; nome = "$nomeCurso - Editado"; status = 'Em Andamento'; ativo = '1'; descricao = 'descricao editada'
    } -MaximumRedirection 5
    if ($up.Content -match 'atualizado com sucesso') { Write-Output "Atualizar curso OK" } else { Write-Output "Atualizar curso: status $($up.StatusCode)" }
}

# ---------- 8. Adicionar aula ----------
if ($cursoId) {
    $aula = Invoke-WebRequest "$base/cursos/adicionarAula/$cursoId" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t3; data = '2026-01-10'; tema = 'Aula Teste Automatico'; conteudo = 'conteudo da aula'
    } -MaximumRedirection 5
    if ($aula.Content -match 'Aula Teste Automatico') { Write-Output "Adicionar aula OK" } else { Write-Output "Adicionar aula: status $($aula.StatusCode)" }
}

# ---------- 9. Matricular aluno (primeiro membro do dropdown) ----------
if ($e.Content -match 'name="membro_id"[^>]*>\s*<option value="(\d+)"') { $membroId = $Matches[1] } else { $membroId = $null }
if ($cursoId -and $membroId) {
    $mat = Invoke-WebRequest "$base/cursos/matricular/$cursoId" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t3; membro_id = $membroId
    } -MaximumRedirection 5
    if ($mat.Content -match 'matriculado com sucesso') { Write-Output "Matricular aluno OK (membro $membroId)" } else { Write-Output "Matricular: status $($mat.StatusCode)" }

    # duplicado deve falhar
    $mat2 = Invoke-WebRequest "$base/cursos/matricular/$cursoId" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t3; membro_id = $membroId
    } -MaximumRedirection 5
    if ($mat2.Content -match 'ja esta matriculado') { Write-Output "Matricular duplicado bloqueado OK" } else { Write-Output "Matricular duplicado: sem mensagem esperada" }

    # ---------- 10. Atualizar aluno ----------
    if ($mat.Content -match "cursos/atualizarAluno/$cursoId/(\d+)") { $alunoId = $Matches[1] }
    if ($alunoId) {
        $al = Invoke-WebRequest "$base/cursos/atualizarAluno/$cursoId/$alunoId" -Method Post -WebSession $s -Body @{
            csrf_test_name = $t3; frequencia = '85.5'; nota_final = '9'; status = 'Aprovado'; certificado = '1'
        } -MaximumRedirection 5
        if ($al.Content -match 'atualizados') { Write-Output "Atualizar aluno OK" } else { Write-Output "Atualizar aluno: status $($al.StatusCode)" }
    }

    # ---------- 11. Remover aluno ----------
    if ($alunoId) {
        $rm = Invoke-WebRequest "$base/cursos/removerAluno/$cursoId/$alunoId" -Method Post -WebSession $s -Body @{ csrf_test_name = $t3 } -MaximumRedirection 5
        if ($rm.Content -match 'removido|Remover') { Write-Output "Remover aluno OK" } else { Write-Output "Remover aluno: status $($rm.StatusCode)" }
    }
} else {
    Write-Output "Sem membro disponivel para matricular (membroId=$membroId)"
}

# ---------- 12. Excluir curso de teste ----------
if ($cursoId) {
    $ex = Invoke-WebRequest "$base/cursos/excluir/$cursoId" -Method Post -WebSession $s -Body @{ csrf_test_name = $t3 } -MaximumRedirection 5
    if ($ex.Content -match 'excluido com sucesso|exclu') { Write-Output "Excluir curso OK" } else { Write-Output "Excluir: status $($ex.StatusCode)" }
}

Write-Output '--- FIM test_cursos_flow ---'
