export default async function run(page, ui) {
  await page.goto('http://localhost:8080/login');
  await page.waitForTimeout(1500);
  const inputs = await page.locator('input').all();
  const info = [];
  for (const i of inputs) info.push(await i.getAttribute('name'));
  await page.locator("input[type='email']").first().fill('admin@admin.com');
  await page.locator("input[type='password']").first().fill('admin123');
  await page.locator("button[type='submit']").first().click();
  await page.waitForTimeout(3000);
  await page.goto('http://localhost:8080/dashboard');
  const links = await page.evaluate(() =>
    [...document.querySelectorAll('a[href]')].map(a => a.getAttribute('href') + ' | ' + (a.textContent||'').trim().slice(0,40))
      .filter(s => /depart|minister|celula|disc/i.test(s))
  );
  return { url: page.url(), links };
}
