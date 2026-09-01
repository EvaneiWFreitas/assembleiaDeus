export default async function run(page, ui) {
  const snap = await ui.snapshot()
  const email = snap.match(/@(e\d+) textbox "email"/)?.[1]
  const senha = snap.match(/@(e\d+) textbox "senha"/)?.[1]
  const botao = snap.match(/@(e\d+) button "Entrar"/)?.[1]
  if (!email || !senha || !botao) return { error: 'refs não encontrados', snap }
  await ui.fill(email, 'admin@igreja.com')
  await ui.fill(senha, 'alterar-na-primeira-utilizacao')
  await ui.click(botao)
  await page.waitForLoadState('networkidle').catch(() => { })
  await page.waitForTimeout(1500)
  return { url: page.url(), snapshot: await ui.snapshot() }
}
