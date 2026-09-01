try {
  $r = Invoke-WebRequest 'http://localhost:8080/login' -SessionVariable s -UseBasicParsing
  if ($r.Content -match 'name="csrf_test_name"[^>]*value="([^"]+)"') { $t = $Matches[1] } else { $t = '' }
  Write-Output "CSRF len: $($t.Length)"
  $post = Invoke-WebRequest 'http://localhost:8080/login' -Method Post -WebSession $s -Body @{ csrf_test_name = $t; email = 'admin@igreja.com'; senha = 'alterar-na-primeira-utilizacao' } -UseBasicParsing
  Write-Output "Login status: $($post.StatusCode)"
  $c = Invoke-WebRequest 'http://localhost:8080/cursos' -WebSession $s -UseBasicParsing
  if ($c.Content -match 'Cursos') { Write-Output 'Cursos OK' } else { Write-Output "Cursos: status $($c.StatusCode) len $($c.Content.Length)" }
  if ($c.Content -match 'Curso Teste Automatico') { Write-Output 'Curso criado anteriormente aparece na listagem' }
} catch {
  Write-Output "ERRO: $($_.Exception.Message)"
}
