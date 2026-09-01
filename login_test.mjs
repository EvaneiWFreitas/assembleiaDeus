export default async function run(page, ui) {
  const inputs = await page.$$eval('input', els => els.map(e => ({ name: e.name, type: e.type })));
  await page.fill('input[name=email]', 'admin@admin.com');
  await page.fill('input[name=senha]', 'admin123');
  await page.click('button[type=submit]');
  await page.waitForLoadState('networkidle');
  const body = await page.evaluate(() => document.body.innerText.slice(0, 400));
  return { inputs, url: page.url(), body };
}
