# File: test_congregacoes_flow.ps1
# Teste de fluxo do modulo Congregacoes + Dados da Igreja
$base = 'http://localhost:8080'

function Get-Csrf($resp) {
    if ($resp.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { return $Matches[1] }
    return ''
}

# ---------- Login ----------
$r = Invoke-WebRequest "$base/login" -SessionVariable sess
$s = $sess
$t = Get-Csrf $r
$post = Invoke-WebRequest "$base/login" -Method Post -WebSession $s -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' }
Write-Output "Login status: $($post.StatusCode)"

# ---------- Congregacoes: listagem ----------
$c = Invoke-WebRequest "$base/congregacoes" -WebSession $s
if ($c.Content -match 'Congrega') { Write-Output "Listagem congregacoes OK" } else { Write-Output "Listagem: status $($c.StatusCode)" }

# ---------- Form criar ----------
$f = Invoke-WebRequest "$base/congregacoes/novo" -WebSession $s
if ($f.Content -match 'name="codigo"') { Write-Output "Form criar congregacao OK" } else { Write-Output "Form criar: status $($f.StatusCode)" }
$t2 = Get-Csrf $f

# ---------- Salvar ----------
$nomeCong = 'Congregacao Teste Automatico'
$codigo = 'TST-' + (Get-Random -Maximum 9999)
$sv = Invoke-WebRequest "$base/congregacoes/salvar" -Method Post -WebSession $s -Body @{
    csrf_test_name = $t2; nome = $nomeCong; codigo = $codigo; ativo = '1'; telefone = '(11) 99999-0000'; cidade = 'Sao Paulo'; estado = 'SP'
} -MaximumRedirection 5
if ($sv.Content -match 'cadastrada com sucesso') { Write-Output "Salvar congregacao OK" } else { Write-Output "Salvar: status $($sv.StatusCode)" }

# ---------- Localizar id criado ----------
$c2 = Invoke-WebRequest "$base/congregacoes?q=$codigo" -WebSession $s
$congId = $null
$linhas = $c2.Content -split "`n"
foreach ($lin in $linhas) {
    if ($lin -match $codigo -and $lin -match 'editar/(\d+)') { $congId = $Matches[1]; break }
}
Write-Output "Congregacao criada id: $congId"

# ---------- Form editar + atualizar ----------
if ($congId) {
    $e = Invoke-WebRequest "$base/congregacoes/editar/$congId" -WebSession $s
    if ($e.Content -match 'Editar Congrega') { Write-Output "Form editar congregacao OK" }
    $t3 = Get-Csrf $e
    $up = Invoke-WebRequest "$base/congregacoes/atualizar/$congId" -Method Post -WebSession $s -Body @{
        csrf_test_name = $t3; nome = "$nomeCong Editada"; codigo = $codigo; ativo = '1'; cidade = 'Guarulhos'; estado = 'SP'
    } -MaximumRedirection 5
    if ($up.Content -match 'atualizada com sucesso|sucesso') { Write-Output "Atualizar congregacao OK" } else { Write-Output "Atualizar: status $($up.StatusCode)" }

    # Excluir
    $ex = Invoke-WebRequest "$base/congregacoes/excluir/$congId" -Method Post -WebSession $s -Body @{ csrf_test_name = $t3 } -MaximumRedirection 5
    if ($ex.Content -match 'exclu') { Write-Output "Excluir congregacao OK" } else { Write-Output "Excluir: status $($ex.StatusCode)" }
}

# ---------- Dados da Igreja ----------
$g = Invoke-WebRequest "$base/igreja" -WebSession $s
if ($g.Content -match 'Dados da Igreja') { Write-Output "Pagina igreja OK" } else { Write-Output "Pagina igreja: status $($g.StatusCode)" }

# Verifica botoes editar/salvar e campos desabilitados ate clicar em Editar
if ($g.Content -match 'id="btn-editar-igreja"') { Write-Output "Botao Editar presente" } else { Write-Output "Botao Editar NAO encontrado" }
if ($g.Content -match 'id="btn-salvar-igreja"') { Write-Output "Botao Salvar presente" } else { Write-Output "Botao Salvar NAO encontrado" }
if ($g.Content -match "action=['\`"]?[^'\`"]*igreja['\`"]") { Write-Output "Form action igreja presente" } else { Write-Output "Form salvar da igreja NAO encontrado" }
if ($g.Content -match 'name="razao_social"') { Write-Output "Campo razao_social presente" }

# ---------- Atualizar dados da igreja ----------
$t4 = Get-Csrf $g
$up2 = Invoke-WebRequest "$base/igreja" -Method Post -WebSession $s -Body @{
    csrf_test_name  = $t4
    razao_social    = 'Igreja Teste LTDA'
    nome            = 'Igreja Teste'
    cnpj            = '12.345.678/0001-90'
    telefone        = '(11) 3333-3333'
    email           = 'teste@igreja.com'
    cidade          = 'Sao Paulo'
    estado          = 'SP'
    pais            = 'Brasil'
    pastor_responsavel = 'Pastor Teste'
} -MaximumRedirection 5
if ($up2.Content -match 'atualizados com sucesso') { Write-Output "Atualizar dados igreja OK" }
# Confere persistencia dos campos editados
if ($up2.Content -match 'Igreja Teste LTDA') { Write-Output "Razao social editada visivel apos salvar OK" } else { Write-Output "Atualizar igreja: status $($up2.StatusCode)" }

# Confere persistencia
$g2 = Invoke-WebRequest "$base/igreja" -WebSession $s
if ($g2.Content -match 'Igreja Teste') { Write-Output "Dados da igreja persistidos OK" } else { Write-Output "Dados da igreja NAO persistidos" }

Write-Output '--- FIM test_congregacoes_flow ---'
