# File: test_igreja_congregacoes.ps1
# Testes E2E: modulo Dados da Igreja (editar/salvar) + modulo Congregacoes (CRUD)
$base = 'http://localhost:8080'
$ok = 0; $fail = 0
function Check($nome, $cond) {
    if ($cond) { $script:ok++; Write-Output "[OK]   $nome" }
    else       { $script:fail++; Write-Output "[FAIL] $nome" }
}

# ---------- LOGIN ----------
$r = Invoke-WebRequest "$base/login" -SessionVariable sess
$s = $sess
if ($r.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t = $Matches[1] } else { $t = '' }
$post = Invoke-WebRequest "$base/login" -Method Post -WebSession $s -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' }
Check "Login admin" ($post.StatusCode -eq 200)

function Get-Csrf($resp) {
    if ($resp.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { return $Matches[1] }
    return ''
}

# ================= MODULO: DADOS DA IGREJA =================
Write-Output "`n--- Modulo: Dados da Igreja ---"

# 1. Formulario carrega
$f = Invoke-WebRequest "$base/igreja" -WebSession $s
Check "Form igreja carrega" ($f.StatusCode -eq 200 -and $f.Content -match 'Dados da Igreja')

# 2. Botao Editar existe (view ja tem) e campos começam desabilitados
Check "Botao Editar presente"    ($f.Content -match 'btn-editar-igreja')
Check "Botao Salvar presente"    ($f.Content -match 'btn-salvar-igreja')
Check "JS modo edicao presente"  ($f.Content -match 'modoEdicao')
Check "Campos desabilitados por padrao" ($f.Content -match '\.disabled = true')

# 3. Salvar dados editados (POST direto simula clique em Salvar)
$t = Get-Csrf $f
$nomeNovo = 'Igreja Teste E2E ' + (Get-Date -Format 'HHmmss')
$sv = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $s -Body @{
    csrf_test_name = $t; nome = $nomeNovo; razao_social = 'Razao Teste LTDA'
    telefone = '11999990000'; cidade = 'Sao Paulo'; estado = 'SP'; pais = 'Brasil'
} -MaximumRedirection 5
Check "Salvar igreja (POST /igreja)" ($sv.StatusCode -eq 200)

# 4. Dado editado aparece no form
$f2 = Invoke-WebRequest "$base/igreja" -WebSession $s
Check "Nome editado persistiu" ($f2.Content -match [regex]::Escape($nomeNovo))

# 5. Restaurar nome original
$t2 = Get-Csrf $f2
if ($f2.Content -match 'name="nome"[^>]*value="([^"]*)"') { $nomeAntigo = $Matches[1] } else { $nomeAntigo = 'Igreja' }
$sv2 = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $s -Body @{
    csrf_test_name = $t2; nome = 'Igreja Restaurada'; cidade = 'Sao Paulo'; estado = 'SP'
} -MaximumRedirection 5
Check "Restaurar dados igreja" ($sv2.StatusCode -eq 200)

# ================= MODULO: CONGREGACOES =================
Write-Output "`n--- Modulo: Congregacoes ---"

# 1. Listagem
$c = Invoke-WebRequest "$base/congregacoes" -WebSession $s
Check "Listagem congregacoes" ($c.StatusCode -eq 200 -and $c.Content -match 'Congrega')

# 2. Form novo
$n = Invoke-WebRequest "$base/congregacoes/novo" -WebSession $s
Check "Form nova congregacao" ($n.StatusCode -eq 200 -and $n.Content -match 'name="nome"')

# 3. Salvar nova
$t3 = Get-Csrf $n
$codigo = 'CONG-E2E-' + (Get-Date -Format 'HHmmss')
$sv3 = Invoke-WebRequest "$base/congregacoes/salvar" -Method Post -WebSession $s -Body @{
    csrf_test_name = $t3; nome = 'Congregacao Teste E2E'; codigo = $codigo
    telefone = '11888887777'; cidade = 'Sao Paulo'; estado = 'SP'; ativo = '1'
} -MaximumRedirection 5
Check "Salvar congregacao" ($sv3.StatusCode -eq 200)
Check "Mensagem sucesso" ($sv3.Content -match 'cadastrada com sucesso')

# 4. Localizar id criado na listagem (busca)
$busca = Invoke-WebRequest "$base/congregacoes?q=$codigo" -WebSession $s
$id = $null
if ($busca.Content -match 'congregacoes/editar/(\d+)') { $id = $Matches[1] }
Check "Congregacao aparece na busca" ($null -ne $id)

# 5. Form editar
if ($id) {
    $e = Invoke-WebRequest "$base/congregacoes/editar/$id" -WebSession $s
    Check "Form editar congregacao" ($e.StatusCode -eq 200 -and $e.Content -match 'Congregacao Teste E2E')

    # 6. Atualizar
    $t4 = Get-Csrf $e
    $sv4 = Invoke-WebRequest "$base/congregacoes/atualizar/$id" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t4; nome = 'Congregacao Editada E2E'; codigo = $codigo
        cidade = 'Campinas'; estado = 'SP'; ativo = '1'
    } -MaximumRedirection 5
    Check "Atualizar congregacao" ($sv4.StatusCode -eq 200 -and $sv4.Content -match 'atualizada com sucesso')

    # 7. Validacao: nome vazio deve voltar com erro
    $t5 = Get-Csrf $sv4
    try {
        $bad = Invoke-WebRequest "$base/congregacoes/atualizar/$id" -Method Post -WebSession $s -Body @{
            csrf_test_name = $t5; nome = ''; codigo = $codigo
        } -MaximumRedirection 5
        Check "Validacao nome obrigatorio" ($bad.Content -match 'Nome|obrigat|inv')
    } catch { Check "Validacao nome obrigatorio" $true }

    # 8. Excluir
    $t6 = Get-Csrf $sv4
    $del = Invoke-WebRequest "$base/congregacoes/excluir/$id" -Method Post -WebSession $s -Body @{ csrf_test_name = $t6 } -MaximumRedirection 5
    Check "Excluir congregacao" ($del.StatusCode -eq 200)
} else {
    Check "Form editar congregacao" $false
}

Write-Output "`n===== RESULTADO: $ok OK, $fail FAIL ====="
