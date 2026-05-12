/**
 * Assistente Cultural UDI — Widget Flutuante
 * Versão sem IA — consulta direto a API do Mapa Cultural
 */
(function () {
  'use strict';

  const MAPA_BASE = window.location.origin;
  const WIDGET_TITLE = 'MônicaIA';
  const WIDGET_SUBTITLE = 'Sua Assistente do Mapa Cultural de Uberlândia';
  const AVATAR_URL = '/assets/img/monicaia.png';

  const css = `
   #mc-chat-btn {
      position: fixed; bottom: 28px; right: 28px;
      width: 200px; height: 200px; border-radius: 50%;
      background: transparent;
      border: none; cursor: pointer;
      box-shadow: none;
      overflow: visible;
    }
    #mc-chat-btn:hover { transform: scale(1.08); }
    #mc-chat-btn.open svg.icon-chat { display: none; }
    #mc-chat-btn.open svg.icon-close { display: block !important; }
    #mc-badge {
      position: absolute; top: -2px; right: -2px;
      width: 18px; height: 18px; background: #F2A900;
      border-radius: 50%; font-size: 10px; color: #fff;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; border: 2px solid #fff; pointer-events: none;
    }
    #mc-badge.hidden { display: none; }
    #mc-chat-panel {
      position: fixed; bottom: 100px; right: 28px;
      width: 370px; height: 580px;
      background: #0d0f14; border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.5);
      display: flex; flex-direction: column; overflow: hidden;
      z-index: 99997; opacity: 0;
      transform: translateY(20px) scale(0.96); pointer-events: none;
      transition: opacity 0.25s ease, transform 0.25s ease;
      font-family: 'Segoe UI', sans-serif;
    }
    #mc-chat-panel.open { opacity: 1; transform: translateY(0) scale(1); pointer-events: all; }
    @media (max-width: 480px) {
      #mc-chat-panel { width: calc(100vw - 20px); right: 10px; bottom: 90px; height: 70vh; }
      #mc-chat-btn { bottom: 16px; right: 16px; }
    }
    .mc-header {
      padding: 14px 16px; background: #161920;
      border-bottom: 1px solid rgba(255,255,255,0.07);
      display: flex; align-items: center; gap: 10px; flex-shrink: 0;
    }
    .mc-avatar {
      width: 52px; height: 52px; border-radius: 50%;
      background: linear-gradient(135deg, #0055A5, #0099D6);
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0; overflow: hidden;
    }
    .mc-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .mc-header-info { flex: 1; }
    .mc-header-info strong { display: block; font-size: 14px; color: #eef0f8; }
    .mc-header-info span { font-size: 11px; color: #7a7f9a; }
    .mc-status { width: 8px; height: 8px; background: #4ade80; border-radius: 50%; box-shadow: 0 0 5px #4ade80; animation: mc-pulse 2s infinite; flex-shrink: 0; }
    @keyframes mc-pulse { 0%,100%{opacity:1} 50%{opacity:.35} }
    .mc-messages { flex: 1; overflow-y: auto; padding: 12px; display: flex; flex-direction: column; gap: 10px; scroll-behavior: smooth; }
    .mc-messages::-webkit-scrollbar { width: 3px; }
    .mc-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 3px; }
    .mc-msg { display: flex; align-items: flex-end; gap: 6px; animation: mc-fadein 0.25s ease; }
    .mc-msg.bot { flex-direction: row; }
    .mc-msg.user { flex-direction: row-reverse; }
    @keyframes mc-fadein { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
    .mc-msg-av { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg,#0055A5,#0099D6); flex-shrink: 0; font-size: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .mc-msg-av img { width: 100%; height: 100%; object-fit: cover; }
    .mc-bubble { max-width: 82%; padding: 8px 12px; border-radius: 16px; font-size: 13px; line-height: 1.5; }
    .mc-msg.bot .mc-bubble { background: #1e2230; border: 1px solid rgba(255,255,255,0.07); border-bottom-left-radius: 4px; color: #eef0f8; }
    .mc-msg.user .mc-bubble { background: #0055A5; border-bottom-right-radius: 4px; color: #fff; }
    .mc-ev-card { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.09); border-radius: 8px; padding: 8px 10px; margin-top: 6px; font-size: 12px; cursor: pointer; transition: background 0.2s; }
    .mc-ev-card:hover { background: rgba(255,255,255,0.09); }
    .mc-ev-title { font-weight: 600; color: #7ab8f5; margin-bottom: 2px; }
    .mc-ev-meta { color: #7a7f9a; font-size: 11px; }
    .mc-ev-desc { color: #aaa; font-size: 11px; margin-top: 3px; }
    .mc-typing { display: flex; gap: 4px; padding: 8px 12px; background: #1e2230; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; border-bottom-left-radius: 4px; width: fit-content; }
    .mc-dot { width: 6px; height: 6px; background: #7a7f9a; border-radius: 50%; animation: mc-bounce 1.2s infinite; }
    .mc-dot:nth-child(2){animation-delay:.2s} .mc-dot:nth-child(3){animation-delay:.4s}
    @keyframes mc-bounce{0%,80%,100%{transform:translateY(0)}40%{transform:translateY(-5px)}}
    .mc-qr { padding: 0 10px 8px; display: flex; flex-wrap: wrap; gap: 6px; flex-shrink: 0; }
    .mc-qr-btn { background: transparent; border: 1px solid #0055A5; color: #7ab8f5; border-radius: 999px; padding: 5px 12px; font-size: 12px; cursor: pointer; font-family: inherit; transition: all 0.2s; white-space: nowrap; }
    .mc-qr-btn:hover { background: #0055A5; color: #fff; }
    .mc-input-area { padding: 10px; border-top: 1px solid rgba(255,255,255,0.07); display: flex; gap: 8px; align-items: center; background: #161920; flex-shrink: 0; }
    .mc-input { flex: 1; background: #0d0f14; border: 1px solid rgba(255,255,255,0.1); border-radius: 999px; padding: 7px 14px; color: #eef0f8; font-size: 13px; font-family: inherit; outline: none; transition: border-color 0.2s; }
    .mc-input::placeholder { color: #7a7f9a; }
    .mc-input:focus { border-color: #0055A5; }
    .mc-send { width: 36px; height: 36px; border-radius: 50%; background: #0055A5; border: none; color: #fff; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.15s; flex-shrink: 0; }
    .mc-send:hover { background: #003F7D; transform: scale(1.05); }
    .mc-send:disabled { background: #2a2a3a; cursor: not-allowed; }
    .mc-footer { text-align: center; font-size: 10px; color: #4a4f66; padding: 4px 0 6px; flex-shrink: 0; }
  `;

  function buildHTML() {
    return `
      <button id="mc-chat-btn" aria-label="Abrir assistente cultural" title="Assistente Cultural UDI">
        <div id="mc-badge" class="hidden">1</div>
        <img class="icon-chat" src="${AVATAR_URL}" style="width:110%;height:110%;object-fit:contain;border-radius:0;" alt="Assistente" />
        <svg class="icon-close" style="display:none" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round">
          <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
      <div id="mc-chat-panel" role="dialog" aria-label="Assistente Cultural UDI">
        <div class="mc-header">
          <div class="mc-avatar" id="mc-avatar-hdr">🎭</div>
          <div class="mc-header-info">
            <strong>${WIDGET_TITLE}</strong>
            <span>${WIDGET_SUBTITLE}</span>
          </div>
          <div class="mc-status"></div>
        </div>
        <div class="mc-messages" id="mc-messages"></div>
        <div class="mc-qr" id="mc-qr"></div>
        <div class="mc-input-area">
          <input class="mc-input" id="mc-input" type="text" placeholder="Digite sua dúvida..." />
          <button class="mc-send" id="mc-send" onclick="mcSend()">➤</button>
        </div>
        <div class="mc-footer">Mapa Cultural Uberlândia · SMCT</div>
      </div>
    `;
  }

  let mcOpen = false, mcUnread = 0, mcTyping = false;

  function avHtml() { return AVATAR_URL ? `<img src="${AVATAR_URL}" alt="">` : '🎭'; }

  function mcAddBot(html, cards) {
    const msgs = document.getElementById('mc-messages');
    const div = document.createElement('div');
    div.className = 'mc-msg bot';
    const ch = (cards||[]).map(c=>`<div class="mc-ev-card" onclick="window.open('${c.url}','_blank')"><div class="mc-ev-title">${c.title}</div><div class="mc-ev-meta">${c.meta}</div>${c.desc?`<div class="mc-ev-desc">${c.desc}</div>`:''}</div>`).join('');
    div.innerHTML = `<div class="mc-msg-av">${avHtml()}</div><div class="mc-bubble">${html}${ch}</div>`;
    msgs.appendChild(div); msgs.scrollTop = msgs.scrollHeight;
    if (!mcOpen) { mcUnread++; updateBadge(); }
  }

  function mcAddUser(text) {
    const msgs = document.getElementById('mc-messages');
    const div = document.createElement('div');
    div.className = 'mc-msg user';
    div.innerHTML = `<div class="mc-bubble">${text}</div>`;
    msgs.appendChild(div); msgs.scrollTop = msgs.scrollHeight;
  }

  function mcShowTyping() {
    const msgs = document.getElementById('mc-messages');
    const div = document.createElement('div');
    div.className = 'mc-msg bot'; div.id = 'mc-typing-row';
    div.innerHTML = `<div class="mc-msg-av">${avHtml()}</div><div class="mc-typing"><div class="mc-dot"></div><div class="mc-dot"></div><div class="mc-dot"></div></div>`;
    msgs.appendChild(div); msgs.scrollTop = msgs.scrollHeight;
  }

  function mcRemoveTyping() { document.getElementById('mc-typing-row')?.remove(); }

  function mcSetQR(opts) {
    const el = document.getElementById('mc-qr'); el.innerHTML = '';
    opts.forEach(o => {
      const b = document.createElement('button');
      b.className = 'mc-qr-btn'; b.textContent = o;
      b.onclick = () => { document.getElementById('mc-input').value = o; mcSend(); };
      el.appendChild(b);
    });
  }

  function updateBadge() {
    const badge = document.getElementById('mc-badge');
    if (!badge) return;
    if (mcUnread > 0 && !mcOpen) { badge.textContent = mcUnread; badge.classList.remove('hidden'); }
    else badge.classList.add('hidden');
  }

  async function apiGet(endpoint) {
    try { const r = await fetch(MAPA_BASE + endpoint); if (!r.ok) throw new Error(); return await r.json(); }
    catch { return null; }
  }

  function fmtData(str) {
    if (!str) return '';
    return new Date(str).toLocaleDateString('pt-BR', { day:'2-digit', month:'short', year:'numeric' });
  }

  async function buscarEventos(termo) {
    const hoje = new Date(), fim = new Date();
    fim.setDate(fim.getDate() + 30);
    const fmt = d => d.toISOString().split('T')[0];
    let url = `/api/v2/events/?status=1&startsOn=GTE(${fmt(hoje)})&startsOn=LTE(${fmt(fim)})&@select=name,shortDescription,startsOn,space.name,singleUrl&_limit=6`;
    if (termo) url += `&name=ILIKE(%25${encodeURIComponent(termo)}%25)`;
    const d = await apiGet(url); return Array.isArray(d) ? d : [];
  }

  async function buscarEspacos(termo) {
    let url = `/api/v2/spaces/?status=1&@select=name,shortDescription,singleUrl,type&_limit=5`;
    if (termo) url += `&name=ILIKE(%25${encodeURIComponent(termo)}%25)`;
    const d = await apiGet(url); return Array.isArray(d) ? d : [];
  }

  async function buscarAgentes(termo) {
    let url = `/api/v2/agents/?status=1&@select=name,shortDescription,singleUrl&_limit=5`;
    if (termo) url += `&name=ILIKE(%25${encodeURIComponent(termo)}%25)`;
    const d = await apiGet(url); return Array.isArray(d) ? d : [];
  }

  async function buscarOportunidades() {
    const hoje = new Date().toISOString().split('T')[0];
    const url = `/api/v2/opportunities/?status=1&registrationTo=GTE(${hoje})&@select=name,shortDescription,registrationFrom,registrationTo,singleUrl&_limit=5`;
    const d = await apiGet(url); return Array.isArray(d) ? d : [];
  }

  function fmtCards(items, tipo) {
    return items.map(i => {
      let meta = '';
      if (tipo === 'evento') meta = [i.startsOn ? '📅 '+fmtData(i.startsOn) : '', i.space?.name ? '📍 '+i.space.name : ''].filter(Boolean).join(' · ');
      else if (tipo === 'espaco') meta = i.type?.name || 'Espaço Cultural';
      else if (tipo === 'agente') meta = '🎨 Agente Cultural';
      else if (tipo === 'oportunidade') meta = [i.registrationFrom ? '📅 De '+fmtData(i.registrationFrom) : '', i.registrationTo ? 'até '+fmtData(i.registrationTo) : ''].filter(Boolean).join(' ');
      return { title: i.name || '—', meta, desc: i.shortDescription ? i.shortDescription.substring(0,80)+'...' : '', url: i.singleUrl || '#' };
    });
  }

  const CADASTRO = {
    agente: `Para se cadastrar como <strong>Agente Cultural</strong>:<br><br>1️⃣ Clique em <strong>Entrar</strong> no menu superior<br>2️⃣ Crie uma conta com e-mail e senha<br>3️⃣ Acesse <strong>Meus Agentes → Criar Agente</strong><br>4️⃣ Preencha: nome artístico, área cultural, localização e contato<br>5️⃣ Clique em <strong>Salvar</strong> ✅`,
    espaco: `Para cadastrar um <strong>Espaço Cultural</strong>:<br><br>1️⃣ Faça login no Mapa Cultural<br>2️⃣ Acesse <strong>Meus Espaços → Criar Espaço</strong><br>3️⃣ Informe: nome, tipo, endereço, capacidade e acessibilidade<br>4️⃣ Adicione fotos e contato<br>5️⃣ Clique em <strong>Salvar</strong> ✅`,
    evento: `Para cadastrar um <strong>Evento</strong>:<br><br>1️⃣ Faça login no Mapa Cultural<br>2️⃣ Acesse <strong>Meus Eventos → Criar Evento</strong><br>3️⃣ Informe: nome, data, horário e classificação etária<br>4️⃣ Vincule a um <strong>Espaço</strong> já cadastrado<br>5️⃣ Clique em <strong>Salvar</strong> ✅`,
    edital: `Para participar de um <strong>Edital</strong>:<br><br>1️⃣ Faça login no Mapa Cultural<br>2️⃣ Acesse o menu <strong>Oportunidades</strong><br>3️⃣ Filtre por área de atuação<br>4️⃣ Clique na oportunidade e leia os requisitos<br>5️⃣ Clique em <strong>Inscrever-se</strong> ✅`
  };

  async function interpretar(texto) {
    const t = texto.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');

    if (/^(oi|ola|bom dia|boa tarde|boa noite|hello|hi|tudo|opa)/.test(t)) {
      mcAddBot('Olá! 👋 Como posso te ajudar?');
      mcSetQR(['🗓️ Ver agenda', '🏛️ Espaços culturais', '👤 Agentes culturais', '📋 Editais abertos', '📝 Como me cadastrar?']);
      return;
    }
    if (/cadastr.*(agente|artista)|como.*virar.*agente/.test(t)) {
      mcAddBot(CADASTRO.agente); mcSetQR(['Cadastrar espaço','Cadastrar evento','Ver editais','🗓️ Ver agenda']); return;
    }
    if (/cadastr.*(espaco|teatro|galeria)|como.*cadastrar.*espaco/.test(t)) {
      mcAddBot(CADASTRO.espaco); mcSetQR(['Cadastrar agente','Cadastrar evento','Ver editais','🗓️ Ver agenda']); return;
    }
    if (/cadastr.*(evento|show)|como.*criar.*evento/.test(t)) {
      mcAddBot(CADASTRO.evento); mcSetQR(['Cadastrar agente','Cadastrar espaço','Ver editais','🗓️ Ver agenda']); return;
    }
    if (/como.*(cadastr|registr|criar conta)|me cadastr|quero.*cadastr/.test(t)) {
      mcAddBot('O que você quer cadastrar no Mapa Cultural?');
      mcSetQR(['Cadastrar como agente','Cadastrar um espaço','Cadastrar um evento','Participar de edital']); return;
    }
    if (/edital|oportunidad|bolsa|selecao|fomento|inscri/.test(t)) {
      mcShowTyping();
      const ops = await buscarOportunidades(); mcRemoveTyping();
      ops.length === 0
        ? mcAddBot('Não encontrei editais abertos no momento. Acesse a seção <strong>Oportunidades</strong> no site!')
        : mcAddBot(`Encontrei <strong>${ops.length}</strong> oportunidade(s) aberta(s):`, fmtCards(ops,'oportunidade'));
      mcSetQR(['🗓️ Ver agenda','🏛️ Espaços culturais','📝 Como me cadastrar?']); return;
    }
    if (/espaco|teatro|galeria|museu|biblioteca|centro cultural/.test(t)) {
      const termo = texto.replace(/espaco|espacos|cultural|culturais|ver|buscar|lista|tem|onde/gi,'').trim();
      mcShowTyping();
      const es = await buscarEspacos(termo.length > 3 ? termo : null); mcRemoveTyping();
      es.length === 0
        ? mcAddBot('Não encontrei espaços com esse critério. Tente outro termo!')
        : mcAddBot(`Encontrei <strong>${es.length}</strong> espaço(s) cultural(is):`, fmtCards(es,'espaco'));
      mcSetQR(['🗓️ Ver agenda','👤 Agentes culturais','📋 Editais abertos']); return;
    }
    if (/agente|artista|musico|cantor|ator|dancarin|fotografo/.test(t)) {
      const termo = texto.replace(/agente|agentes|cultural|ver|buscar|lista/gi,'').trim();
      mcShowTyping();
      const ag = await buscarAgentes(termo.length > 3 ? termo : null); mcRemoveTyping();
      ag.length === 0
        ? mcAddBot('Não encontrei agentes com esse critério. Tente outro nome!')
        : mcAddBot(`Encontrei <strong>${ag.length}</strong> agente(s) cultural(is):`, fmtCards(ag,'agente'));
      mcSetQR(['🗓️ Ver agenda','🏛️ Espaços culturais','📋 Editais abertos']); return;
    }
    // padrão: eventos
    const termo = /musica|teatro|danca|cinema|show|exposicao|feira|festival|sarau|oficina/.test(t) ? texto.trim() : null;
    mcShowTyping();
    const evs = await buscarEventos(termo); mcRemoveTyping();
    evs.length === 0
      ? mcAddBot('Não encontrei eventos nos próximos 30 dias. Tente outro termo ou confira o site!')
      : mcAddBot(`Encontrei <strong>${evs.length}</strong> evento(s) nos próximos 30 dias:`, fmtCards(evs,'evento'));
    mcSetQR(['🏛️ Espaços culturais','👤 Agentes culturais','📋 Editais abertos','📝 Como me cadastrar?']);
  }

  window.mcSend = async function () {
    const input = document.getElementById('mc-input');
    const text = input.value.trim();
    if (!text || mcTyping) return;
    mcTyping = true; document.getElementById('mc-send').disabled = true;
    document.getElementById('mc-qr').innerHTML = '';
    mcAddUser(text); input.value = '';
    await interpretar(text);
    mcTyping = false; document.getElementById('mc-send').disabled = false; input.focus();
  };

  function mcToggle() {
    const panel = document.getElementById('mc-chat-panel');
    const btn = document.getElementById('mc-chat-btn');
    mcOpen = !mcOpen;
    panel.classList.toggle('open', mcOpen); btn.classList.toggle('open', mcOpen);
    if (mcOpen) { mcUnread = 0; updateBadge(); setTimeout(() => document.getElementById('mc-input')?.focus(), 300); }
  }

  function mcInit() {
    const style = document.createElement('style'); style.textContent = css; document.head.appendChild(style);
    const wrapper = document.createElement('div'); wrapper.id = 'mc-chat-root'; wrapper.innerHTML = buildHTML(); document.body.appendChild(wrapper);
    document.getElementById('mc-chat-btn').addEventListener('click', mcToggle);
    document.getElementById('mc-input').addEventListener('keydown', e => { if (e.key === 'Enter') mcSend(); });
    if (AVATAR_URL) { document.getElementById('mc-avatar-hdr').innerHTML = `<img src="${AVATAR_URL}" alt="">`; }
    setTimeout(() => {
      mcAddBot('Olá! 👋 Sou o <strong>Assistente Cultural de Uberlândia</strong>!<br><br>Posso te ajudar a encontrar <strong>eventos</strong>, <strong>espaços culturais</strong>, <strong>agentes</strong> e <strong>editais</strong> cadastrados no Mapa Cultural, ou te guiar no <strong>cadastro</strong>. O que você procura?');
      mcSetQR(['🗓️ Ver agenda', '🏛️ Espaços culturais', '📋 Editais abertos', '📝 Como me cadastrar?']);
    }, 800);
  }

  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', mcInit); } else { mcInit(); }
})();