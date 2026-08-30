export default async function run(page, ui) {
  const resultados = {};

  // Login
  await page.goto('http://localhost:8080/login');
  await page.fill('input[name="email"]', 'admin@igreja.com');
  await page.fill('input[name="senha"]', 'alterar-na-primeira-utilizacao');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');

  // Verifica cada módulo
  for (const modulo of ['igreja', 'congregacoes', 'membros', 'visitantes', 'obreiros']) {
    await page.goto('http://localhost:8080/' + modulo);
    await page.waitForLoadState('networkidle');
    const titulo = await page.locator('h4').first().textContent().catch(() => null);
    const linhas = await page.locator('table tbody tr').count();
    resultados[modulo] = { titulo: titulo?.trim(), linhas };
  }

  // Ficha do membro #1
  await page.goto('http://localhost:8080/membros/ficha/1');
  await page.waitForLoadState('networkidle');
  resultados.ficha = (await page.locator('h5').first().textContent().catch(() => null))?.trim();

  // Cria um membro novo via formulário (teste de CRUD real)
  await page.goto('http://localhost:8080/membros/novo');
  await page.fill('input[name="nome"]', 'Teste CRUD Silva');
  await page.selectOption('select[name="status"]', 'Ativo');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  resultados.flashCriacao = (await page.locator('.alert-success').first().textContent().catch(() => null))?.trim();

  // Validação: tenta salvar sem nome
  await page.goto('http://localhost:8080/membros/novo');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  resultados.validacaoHtml = await page.locator('input[name="nome"]:invalid').count() > 0;

  // Screenshots
  await page.goto('http://localhost:8080/membros');
  await page.waitForLoadState('networkidle');
  await page.setViewportSize({ width: 1400, height: 900 });
  await page.screenshot({ path: 'tests/membros.png' });
  await page.goto('http://localhost:8080/dashboard');
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: 'tests/dashboard_e2.png' });

  return resultados;
}
