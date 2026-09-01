export default async function run(page, ui) {
  await page.fill('input[name="nome"]', 'Curso Teste QA')
  await page.selectOption('select[name="professor_id"]', { label: 'Ana Oliveira' })
  await page.selectOption('select[name="status"]', 'Planejado')
  await page.fill('input[name="data_inicio"]', '2026-03-01')
  await page.fill('input[name="data_fim"]', '2026-06-30')
  await page.fill('input[name="vagas"]', '30')
  await page.fill('textarea[name="descricao"]', 'Descrição de teste automatizado')
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'load' }).catch(() => { }),
    page.click('button[type="submit"]')
  ])
  await page.waitForTimeout(1500)
  return { url: page.url(), snapshot: await ui.snapshot() }
}
