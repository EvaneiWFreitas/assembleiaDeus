export default async function run(page, ui) {
  await page.goto('http://localhost:8080/login');
  await page.fill('input[name="email"]', 'admin@igreja.com');
  await page.fill('input[name="senha"]', 'alterar-na-primeira-utilizacao');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  await page.goto('http://localhost:8080/usuarios');
  await page.waitForLoadState('networkidle');
  const trs = await page.locator('table tbody tr').count();
  const nome = await page.locator('tbody tr td').nth(1).textContent().catch(() => null);
  await page.screenshot({ path: 'tests/usuarios.png', fullPage: false });
  await page.goto('http://localhost:8080/dashboard');
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: 'tests/dashboard.png', fullPage: false });
  return { trs, primeiroUsuario: nome?.trim() };
}
