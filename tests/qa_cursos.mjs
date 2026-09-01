export default async function run(page, ui) {
  const r = {};

  // Login
  await page.goto('http://localhost:8080/login');
  await page.fill('input[name="email"]', 'admin@igreja.com');
  await page.fill('input[name="senha"]', 'alterar-na-primeira-utilizacao');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');

  // Lista de cursos
  await page.goto('http://localhost:8080/cursos');
  await page.waitForLoadState('networkidle');
  r.listaTitulo = (await page.locator('h4').first().textContent().catch(() => null))?.trim();
  r.listaLinhas = await page.locator('table tbody tr').count();

  // Formulário de novo curso
  await page.goto('http://localhost:8080/cursos/novo');
  await page.waitForLoadState('networkidle');
  r.formTitulo = (await page.locator('h4').first().textContent().catch(() => null))?.trim();
  r.campos = {
    nome: await page.locator('input[name="nome"]').count(),
    professor: await page.locator('select[name="professor_id"]').count(),
    status: await page.locator('select[name="status"]').count(),
    dataInicio: await page.locator('input[name="data_inicio"]').count(),
    dataFim: await page.locator('input[name="data_fim"]').count(),
    vagas: await page.locator('input[name="vagas"]').count(),
    ativo: await page.locator('select[name="ativo"]').count(),
    descricao: await page.locator('textarea[name="descricao"]').count(),
  };

  // CRUD real: cria um curso
  await page.fill('input[name="nome"]', 'Curso Teste QA');
  await page.fill('input[name="data_inicio"]', '2026-01-10');
  await page.fill('input[name="data_fim"]', '2026-03-10');
  await page.fill('input[name="vagas"]', '20');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  r.flashCriacao = (await page.locator('.alert-success').first().textContent().catch(() => null))?.trim();
  r.urlAposCriar = page.url();

  // Validação HTML: submit sem nome
  await page.goto('http://localhost:8080/cursos/novo');
  await page.click('button[type="submit"]');
  r.validacaoHtml = (await page.locator('input[name="nome"]:invalid').count()) > 0;

  // Screenshot da lista
  await page.goto('http://localhost:8080/cursos');
  await page.waitForLoadState('networkidle');
  await page.setViewportSize({ width: 1400, height: 900 });
  await page.screenshot({ path: 'tests/cursos.png' });

  return r;
}
