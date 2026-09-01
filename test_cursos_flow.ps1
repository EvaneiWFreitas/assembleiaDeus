$base = 'http://localhost/assembleiaDeus/public'
$T = "$env:TEMP\cj_cursos.txt"
Remove-Item $T -ErrorAction SilentlyContinue

# 1. Pegar login + CSRF
$login = (curl.exe -s -m 10 -c $T "$base/login") -join ' '
$t = ''
if ($login -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t = $Matches[1] }
Write-Output "CSRF obtido: $($t.Length) chars"

# 2. Login
$code = curl.exe -s -m 10 -b $T -c $T -o NUL -w "%{http_code}" -X POST `
  --data-urlencode "csrf_test_name=$t" `
  --data-urlencode "email=admin@igreja.com" `
  --data-urlencode "senha=alterar-na-primeira-utilizacao" `
  "$base/login"
Write-Output "Login status: $code"

# 3. Listar cursos
$c = (curl.exe -s -m 10 -b $T -c $T "$base/cursos") -join ' '
if ($c -match 'Cursos') { Write-Output "Listagem cursos OK" } else { Write-Output "Listagem cursos FALHOU" }

# 4. Form criar
$f = (curl.exe -s -m 10 -b $T -c $T "$base/cursos/novo") -join ' '
if ($f -match 'Nome do Curso') { Write-Output "Form criar OK" } else { Write-Output "Form criar FALHOU" }

# 5. Salvar curso novo
$t2 = ''
if ($f -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t2 = $Matches[1] }
$sv = curl.exe -s -m 10 -b $T -c $T -o NUL -w "%{http_code}" -X POST `
  --data-urlencode "csrf_test_name=$t2" `
  --data-urlencode "nome=Curso Teste Automatico" `
  --data-urlencode "status=Planejado" `
  --data-urlencode "ativo=1" `
  --data-urlencode "descricao=teste" `
  "$base/cursos/salvar"
Write-Output "Salvar status: $sv"

# 6. Form editar (pega id 1..10)
$editOk = $false
$e = ''
foreach ($i in 1..10) {
    $e = (curl.exe -s -m 10 -b $T -c $T "$base/cursos/editar/$i") -join ' '
    if ($e -match 'Aulas') { Write-Output "Form editar/$i OK (com Aulas/Alunos)"; $editOk = $true; break }
}
if (-not $editOk) { Write-Output "Form editar nao verificado" }

# 7. Membros dropdown
if ($e -match 'Selecione o membro para matricular') { Write-Output "Dropdown membros OK" } else { Write-Output "Dropdown membros FALHOU" }

# ============================================================
# MODULO: Dados da Igreja
# ============================================================
Write-Output "--- Dados da Igreja ---"

# 8. Pagina Dados da Igreja
$ig = (curl.exe -s -m 10 -b $T -c $T "$base/igreja") -join ' '
if ($ig -match 'Dados da Igreja') { Write-Output "Pagina igreja OK" } else { Write-Output "Pagina igreja FALHOU" }

# 9. Botao Editar presente e campos disabled por padrao
if ($ig -match 'id="btn-editar-igreja"') { Write-Output "Botao Editar OK" } else { Write-Output "Botao Editar FALHOU" }
if ($ig -match 'btnSalvar|btn-salvar-igreja') { Write-Output "Botao Salvar presente OK" } else { Write-Output "Botao Salvar FALHOU" }
if ($ig -match '\.disabled = true') { Write-Output "Campos readonly por padrao OK" } else { Write-Output "Campos readonly FALHOU" }

# 10. Salvar dados editados (POST /igreja)
$tig = ''
if ($ig -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $tig = $Matches[1] }
$svig = curl.exe -s -m 10 -b $T -c $T -o NUL -w "%{http_code}" -X POST `
  --data-urlencode "csrf_test_name=$tig" `
  --data-urlencode "nome=Igreja Teste Editada" `
  --data-urlencode "razao_social=" `
  --data-urlencode "cnpj=" `
  --data-urlencode "data_fundacao=" `
  --data-urlencode "pastor_responsavel=Pastor Teste" `
  --data-urlencode "telefone=" `
  --data-urlencode "whatsapp=" `
  --data-urlencode "email=" `
  --data-urlencode "site=" `
  --data-urlencode "cep=" `
  --data-urlencode "logradouro=" `
  --data-urlencode "numero=" `
  --data-urlencode "complemento=" `
  --data-urlencode "bairro=" `
  --data-urlencode "cidade=" `
  --data-urlencode "estado=" `
  --data-urlencode "pais=Brasil" `
  "$base/igreja"
Write-Output "Salvar igreja status: $svig"

# 11. Confere se o nome editado persistiu
$ig2 = (curl.exe -s -m 10 -b $T -c $T "$base/igreja") -join ' '
if ($ig2 -match 'Igreja Teste Editada') { Write-Output "Edicao persistida OK" } else { Write-Output "Edicao persistida FALHOU" }

# --- Congregacoes ---
Write-Output "--- Congregacoes ---"

# 12. Listagem
$c = (curl.exe -s -m 10 -b $T -c $T "$base/congregacoes") -join ' '
if ($c -match 'Congrega') { Write-Output "Listagem congregacoes OK" } else { Write-Output "Listagem congregacoes FALHOU" }

# 13. Form criar
$f = (curl.exe -s -m 10 -b $T -c $T "$base/congregacoes/novo") -join ' '
if ($f -match 'name="codigo"') { Write-Output "Form criar congregacao OK" } else { Write-Output "Form criar congregacao FALHOU" }

# 14. Salvar nova congregacao
$t3 = ''
if ($f -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t3 = $Matches[1] }
$sv = curl.exe -s -m 10 -b $T -c $T -o NUL -w "%{http_code}" -X POST `
  --data-urlencode "csrf_test_name=$t3" `
  --data-urlencode "nome=Congregacao Teste Automatica" `
  --data-urlencode "codigo=CONG-T1" `
  --data-urlencode "ativo=1" `
  --data-urlencode "cidade=Teste" `
  "$base/congregacoes/salvar"
Write-Output "Salvar congregacao status: $sv"

# 15. Form editar (procura id existente)
$editOk = $false
$e = ''
foreach ($i in 1..15) {
    $e = (curl.exe -s -m 10 -b $T -c $T "$base/congregacoes/editar/$i") -join ' '
    if ($e -match 'name="codigo"') { Write-Output "Form editar congregacao/$i OK"; $editOk = $true; break }
}
if (-not $editOk) { Write-Output "Form editar congregacao nao verificado" }

# 16. Atualizar a congregacao editada
$t4 = ''
if ($e -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t4 = $Matches[1] }
$sv2 = curl.exe -s -m 10 -b $T -c $T -o NUL -w "%{http_code}" -X POST `
  --data-urlencode "csrf_test_name=$t4" `
  --data-urlencode "nome=Congregacao Teste Editada" `
  --data-urlencode "codigo=CONG-T1" `
  --data-urlencode "ativo=1" `
  --data-urlencode "cidade=Teste Editado" `
  "$base/congregacoes/atualizar/$i"
Write-Output "Atualizar congregacao status: $sv2"

# 17. Confirma persistencia da edicao
$v = (curl.exe -s -m 10 -b $T -c $T "$base/congregacoes") -join ' '
if ($v -match 'Congregacao Teste Editada') { Write-Output "Edicao congregacao persistida OK" } else { Write-Output "Edicao congregacao NAO persistida" }

# 18. Excluir
$ex = curl.exe -s -m 10 -b $T -c $T -o NUL -w "%{http_code}" -X POST `
  --data-urlencode "csrf_test_name=$t4" `
  "$base/congregacoes/excluir/$i"
Write-Output "Excluir congregacao status: $ex"

# 19. Confirma exclusao
$v2 = (curl.exe -s -m 10 -b $T -c $T "$base/congregacoes") -join ' '
if ($v2 -notmatch 'Congregacao Teste Editada') { Write-Output "Exclusao confirmada OK" } else { Write-Output "Exclusao FALHOU (ainda listada)" }


# 8. Atualizar curso (POST /cursos/atualizar/{id})
# usa o id achado no passo 6 ($e) ou varre de novo
$updId = $null
foreach ($i in 1..10) {
    try {
        $e2 = Invoke-WebRequest "$base/cursos/editar/$i" -WebSession $s
        if ($e2.Content -match 'Aulas') { $updId = $i; $e = $e2; break }
    } catch {}
}
if ($updId) {
    if ($e.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t3 = $Matches[1] }
    $up = Invoke-WebRequest "$base/cursos/atualizar/$updId" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t3
        nome = 'Curso Teste Automatico Editado'
        status = 'Em Andamento'
        ativo = '1'
        descricao = 'descricao editada'
    } -MaximumRedirection 5
    Write-Output "Atualizar curso/$updId status: $($up.StatusCode)"
    if ($up.Content -match 'sucesso') { Write-Output "Mensagem de sucesso OK" }

    # Confirma persistencia
    $v = Invoke-WebRequest "$base/cursos/editar/$updId" -WebSession $s
    if ($v.Content -match 'Curso Teste Automatico Editado') { Write-Output "Persistencia do curso editado OK" } else { Write-Output "Persistencia: NAO confirmada" }
} else {
    Write-Output "Atualizar curso: nenhum curso disponivel"
}

# 9. Aula/Alunos - cria aula se o form de edicao tiver
if ($e.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t4 = $Matches[1] }
if ($e.Content -match 'cursos/(\d+)/aulas/salvar') {
    $cid = $Matches[1]
    $al = Invoke-WebRequest "$base/cursos/$cid/aulas/salvar" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t4
        titulo = 'Aula Teste'
        ordem = '1'
    } -MaximumRedirection 5
    Write-Output "Salvar aula status: $($al.StatusCode)"
} else {
    Write-Output "Rota de aulas nao identificada no form (verificar manualmente)"
}

# 8. Salvar edicao do curso (POST cursos/atualizar/id)
if ($editOk) {
    if ($e.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t3 = $Matches[1] }
    $up = Invoke-WebRequest "$base/cursos/atualizar/$i" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t3
        nome = 'Curso Teste Automatico (editado)'; status = 'Em Andamento'; ativo = '1'; descricao = 'teste editado'
    } -MaximumRedirection 5
    Write-Output "Atualizar status: $($up.StatusCode)"

    # 9. Confere persistencia
    $c2 = Invoke-WebRequest "$base/cursos" -WebSession $s
    if ($c2.Content -match 'Curso Teste Automatico \(editado\)') { Write-Output "Edicao persistida OK" } else { Write-Output "ERRO: edicao nao persistida" }
}

# 10. Adicionar aula
if ($editOk) {
    if ($e.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t4 = $Matches[1] }
    $aula = Invoke-WebRequest "$base/cursos/adicionarAula/$i" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t4
        titulo = 'Aula Teste Automatico'; descricao = 'aula de teste'; ordem = '1'
    } -MaximumRedirection 5
    Write-Output "AdicionarAula status: $($aula.StatusCode)"
}

# 11. Matricular membro (pega primeiro id do dropdown)
if ($editOk -and $e.Content -match '<option value="(\d+)">') {
    $mid = $Matches[1]
    if ($e.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t5 = $Matches[1] }
    try {
        $mat = Invoke-WebRequest "$base/cursos/matricular/$i" -Method Post -WebSession $s -Body @{
            csrf_test_name = $t5; membro_id = $mid; status = 'Cursando'
        } -MaximumRedirection 5
        Write-Output "Matricular status: $($mat.StatusCode)"
    } catch { Write-Output "Matricular falhou (membro $mid): $($_.Exception.Message)" }
}
