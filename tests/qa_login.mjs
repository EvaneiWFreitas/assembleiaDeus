export default async function run(page, ui) {
  await page.goto('http://localhost:8080/login');
  await page.fill('input[name="email"]', 'admin@igreja.com');
  await page.fill('input[name="senha"]', 'alterar-na-primeira-utilizacao');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  const url = page.url();
  const flash = await page.locator('.alert').first().textContent().catch(() => null);

  // Tenta acessar usuários após login
  await page.goto('http://localhost:8080/usuarios');
  await page.waitForLoadState('networkidle');
  const tituloUsuarios = await page.locator('h4').first().textContent().catch(() => null);
  const linhas = await page.locator('tbody tr').count();

  return { urlAposLogin: url, flash: flash?.trim(), tituloUsuarios: tituloUsuarios?.trim(), linhasUsuarios: linhas };
}
