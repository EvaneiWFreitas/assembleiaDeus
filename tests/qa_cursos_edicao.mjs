export default async function run(page, ui) {
  const r = {};
  await page.goto('http://localhost:8080/login');
  await page.fill('input[name="email"]', 'admin@igreja.com');
  await page.fill('input[name="senha"]', 'alterar-na-primeira-utilizacao');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');

  // Abre edição do curso de teste
  await page.goto('http://localhost:8080/cursos');
  await page.waitForLoadState('networkidle');
  const link = page.locator('a[href*="/cursos/editar/"]').first();
  await link.click();
  await page.waitForLoadState('networkidle');
  r.urlEdicao = page.url();
  r.titulo = (await page.locator('h4').first().textContent().catch(() => null))?.trim();
  r.valorNome = await page.inputValue('input[name="nome"]');
  r.secaoAulas = await page.locator('text=Aulas').count();
  r.secaoAlunos = await page.locator('text=Alunos Matriculados').count();

  // Adiciona uma aula
  await page.fill('input[name="data"]', '2026-01-15');
  await page.fill('input[name="tema"]', 'Aula Teste QA');
  await page.locator('form[action*="adicionarAula"] button').first().click();
  await page.waitForLoadState('networkidle');
  r.aulaCriada = await page.locator('text=Aula Teste QA').count();

  // Matricula um aluno
  const select = page.locator('select[name="membro_id"]');
  await select.selectOption({ index: 1 });
  await page.locator('form[action*="matricular"] button').first().click();
  await page.waitForLoadState('networkidle');
  r.alunosTabela = await page.locator('table tbody tr').count();

  await page.screenshot({ path: 'tests/cursos_edicao.png', fullPage: true });
  return r;
}
