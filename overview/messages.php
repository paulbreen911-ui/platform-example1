<?php

$page_title = 'Messages';

require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/functions.php';
include ROOT_PATH . '/required/header.php';
require_login();

?>
<style>
main { padding-top:0; background:var(--black); min-height:calc(100vh - var(--nav-h)); }
.app-wrap { max-width:1200px; margin:0 auto; padding:24px 24px 0; }
.ph-row { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
.ph-ey  { font-size:10px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); margin-bottom:5px; }
.ph-title { font-family:var(--disp); font-size:36px; letter-spacing:1.5px; color:var(--text-1); line-height:1; }
.messages-layout { display:grid; grid-template-columns:280px 1fr; gap:0; height:calc(100vh - var(--nav-h) - 110px); border:.5px solid var(--border); border-radius:12px; overflow:hidden; }
.msg-sidebar { border-right:.5px solid var(--border); display:flex; flex-direction:column; background:var(--dark); }
.msg-sidebar-hd { padding:14px 16px; border-bottom:.5px solid var(--border); display:flex; justify-content:space-between; align-items:center; }
.msg-sidebar-title { font-size:13px; font-weight:600; color:var(--text-1); }
.msg-search { display:flex; align-items:center; gap:8px; padding:10px 14px; border-bottom:.5px solid var(--border); }
.msg-search svg { color:var(--text-3); flex-shrink:0; }
.msg-search input { background:transparent; border:none; color:var(--text-1); font-family:var(--sans); font-size:12px; outline:none; flex:1; }
.msg-search input::placeholder { color:var(--text-3); }
.msg-conv-list { flex:1; overflow-y:auto; }
.msg-section-lbl { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:var(--text-3); padding:10px 16px 4px; }
.msg-conv-row { display:flex; align-items:center; gap:10px; padding:10px 14px; cursor:pointer; transition:background .12s; border-bottom:.5px solid var(--border); }
.msg-conv-row:hover, .msg-conv-row.active { background:var(--dark-2); }
.msg-conv-av { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; flex-shrink:0; }
.msg-conv-av.group { border-radius:8px; font-size:16px; }
.msg-online-pip { position:relative; }
.msg-online-pip::after { content:''; position:absolute; bottom:0; right:0; width:8px; height:8px; border-radius:50%; background:#66BB6A; border:2px solid var(--dark); }
.msg-conv-body { flex:1; min-width:0; }
.msg-conv-name { font-size:12px; font-weight:500; color:var(--text-1); }
.msg-conv-preview { font-size:11px; color:var(--text-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px; }
.unread-conv .msg-conv-preview { color:var(--text-2); }
.msg-conv-meta { display:flex; flex-direction:column; align-items:flex-end; gap:4px; flex-shrink:0; }
.msg-conv-time { font-size:10px; color:var(--text-3); }
.msg-conv-badge { background:var(--gold); color:#000; font-size:9px; font-weight:700; width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
.msg-chat { display:flex; flex-direction:column; background:var(--black); }
.msg-chat-hd { display:flex; align-items:center; gap:12px; padding:14px 18px; border-bottom:.5px solid var(--border); background:var(--dark); flex-shrink:0; }
.msg-chat-name { font-size:13px; font-weight:600; color:var(--text-1); }
.msg-chat-sub  { font-size:11px; color:var(--text-3); margin-top:2px; }
.msg-chat-actions { margin-left:auto; display:flex; gap:8px; }
.msg-chat-icon { background:transparent; border:none; font-size:16px; cursor:pointer; opacity:.5; transition:opacity .15s; }
.msg-chat-icon:hover { opacity:1; }
.msg-messages { flex:1; overflow-y:auto; padding:16px; display:flex; flex-direction:column; gap:12px; }
.msg-date-divider { text-align:center; font-size:10px; color:var(--text-3); letter-spacing:1px; padding:4px 0; }
.msg-system { text-align:center; font-size:11px; color:var(--text-3); background:var(--dark-2); border-radius:4px; padding:4px 10px; align-self:center; }
.msg-bubble-wrap { display:flex; align-items:flex-end; gap:8px; }
.msg-bubble-wrap.mine { flex-direction:row-reverse; }
.msg-bubble-col { display:flex; flex-direction:column; gap:3px; max-width:75%; }
.msg-bubble { padding:10px 14px; border-radius:12px; font-size:13px; line-height:1.55; }
.msg-bubble.other { background:var(--dark-2); color:var(--text-1); border-radius:4px 12px 12px 12px; }
.msg-bubble.mine  { background:rgba(232,184,75,.15); color:var(--text-1); border:.5px solid var(--gold-bd); border-radius:12px 4px 12px 12px; }
.msg-sender-name  { font-size:10px; color:var(--text-3); margin-bottom:2px; }
.msg-bubble-time  { font-size:10px; color:var(--text-3); }
.mine .msg-bubble-time { text-align:right; }
.msg-ref { display:flex; align-items:center; gap:8px; background:var(--dark); border:.5px solid var(--border); border-radius:6px; padding:8px 10px; margin-top:8px; text-decoration:none; transition:border-color .12s; }
.msg-ref:hover { border-color:var(--gold-bd); }
.msg-ref-icon { font-size:16px; flex-shrink:0; }
.msg-ref-title { font-size:12px; color:var(--text-1); font-weight:500; }
.msg-ref-sub   { font-size:10px; color:var(--text-3); }
.msg-ref-picker { display:flex; gap:6px; flex-wrap:wrap; padding:8px 16px 6px; }
.msg-ref-chip { font-size:11px; padding:4px 10px; border:.5px solid var(--border); border-radius:6px; color:var(--text-3); cursor:pointer; text-decoration:none; transition:all .12s; }
.msg-ref-chip:hover { border-color:var(--gold-bd); color:var(--gold); }
.msg-input-area { padding:12px 16px; border-top:.5px solid var(--border); flex-shrink:0; background:var(--dark); }
.msg-input-row { display:flex; gap:10px; align-items:flex-end; }
.msg-input-wrap { flex:1; background:var(--dark-2); border:.5px solid var(--border); border-radius:10px; display:flex; align-items:center; padding:0 10px; }
.msg-attach-btn { background:transparent; border:none; font-size:16px; cursor:pointer; opacity:.4; padding:8px 4px; flex-shrink:0; }
.msg-input { flex:1; background:transparent; border:none; color:var(--text-1); font-family:var(--sans); font-size:13px; padding:10px 6px; outline:none; resize:none; max-height:120px; }
.msg-input::placeholder { color:var(--text-3); }
.msg-send-btn { width:36px; height:36px; border-radius:50%; background:var(--gold); border:none; cursor:pointer; font-size:16px; color:#000; font-weight:700; flex-shrink:0; transition:opacity .15s; }
.msg-send-btn:hover { opacity:.85; }
.btn-back { font-family:var(--sans); font-size:12px; color:var(--text-3); background:transparent; border:.5px solid var(--border); padding:7px 14px; border-radius:6px; text-decoration:none; transition:all .15s; }
.btn-back:hover { color:var(--text-1); }
.btn-gold { background:rgba(232,184,75,.15); color:var(--gold); border:.5px solid var(--gold-bd); font-family:var(--sans); font-size:12px; font-weight:600; padding:9px 18px; border-radius:6px; cursor:pointer; transition:all .15s; }
.btn-gold:hover { background:rgba(232,184,75,.25); }
/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:500; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal { background:var(--dark); border:.5px solid var(--border); border-radius:12px; width:480px; max-width:96vw; }
.modal-hd { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:.5px solid var(--border); }
.modal-title { font-size:14px; font-weight:600; color:var(--text-1); }
.modal-close { background:transparent; border:none; color:var(--text-3); font-size:18px; cursor:pointer; }
.modal-body { padding:20px; display:flex; flex-direction:column; gap:14px; }
.form-grp { display:flex; flex-direction:column; gap:6px; }
.form-lbl { font-size:11px; letter-spacing:.5px; text-transform:uppercase; color:var(--text-3); }
.form-inp { background:var(--dark-2); border:.5px solid var(--border); border-radius:6px; color:var(--text-1); font-family:var(--sans); font-size:13px; padding:9px 12px; outline:none; width:100%; }
.form-inp:focus { border-color:var(--gold); }
.modal-ft { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px; border-top:.5px solid var(--border); }
.btn-ghost { background:transparent; border:.5px solid var(--border); color:var(--text-3); font-family:var(--sans); font-size:12px; padding:8px 16px; border-radius:6px; cursor:pointer; }
@media(max-width:700px) { .messages-layout { grid-template-columns:1fr; height:auto; } .msg-sidebar { max-height:250px; } }
</style>

<div class="app-wrap">
  <div class="ph-row">
    <div>
      <div class="ph-ey">Communication</div>
      <div class="ph-title">Messages</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <a class="btn-back" href="/overview/overview.php">← Overview</a>
      <button class="btn-gold" onclick="document.getElementById('modal-new-msg').classList.add('open')">+ New message</button>
    </div>
  </div>

  <div class="messages-layout">
    <!-- Sidebar -->
    <div class="msg-sidebar">
      <div class="msg-sidebar-hd"><div class="msg-sidebar-title">All conversations</div><span style="font-size:11px;color:var(--text-3)">4 unread</span></div>
      <div class="msg-search">
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="1.5"/><path d="M11 11l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <input type="text" placeholder="Search messages…" oninput="filterConvs(this.value)">
      </div>
      <div class="msg-conv-list" id="conv-list">
        <div class="msg-section-lbl">Productions</div>
        <div class="msg-conv-row unread-conv active" onclick="openChat('gala-team')" id="conv-gala-team"><div class="msg-conv-av group" style="background:rgba(232,53,10,.15);color:#E8350A">🎭</div><div class="msg-conv-body"><div class="msg-conv-name">Summerset Gala — Team</div><div class="msg-conv-preview">Sarah M.: ROS loaded, cue 1 on standby</div></div><div class="msg-conv-meta"><div class="msg-conv-time">8m</div><div class="msg-conv-badge">2</div></div></div>
        <div class="msg-conv-row" onclick="openChat('summit-team')" id="conv-summit-team"><div class="msg-conv-av group" style="background:rgba(91,159,224,.15);color:#5B9FE0">🎭</div><div class="msg-conv-body"><div class="msg-conv-name">Tech Summit — Team</div><div class="msg-conv-preview">You: Shared the updated venue contact sheet</div></div><div class="msg-conv-meta"><div class="msg-conv-time">2h</div></div></div>
        <div class="msg-conv-row" onclick="openChat('sphere-team')" id="conv-sphere-team"><div class="msg-conv-av group" style="background:rgba(0,200,212,.15);color:#4DB6AC">🏗</div><div class="msg-conv-body"><div class="msg-conv-name">Sphere Build — Team</div><div class="msg-conv-preview">Dana K.: Permit docs uploaded to drive</div></div><div class="msg-conv-meta"><div class="msg-conv-time">1d</div></div></div>
        <div class="msg-section-lbl">Direct messages</div>
        <div class="msg-conv-row unread-conv msg-online-pip" onclick="openChat('tom')" id="conv-tom"><div class="msg-conv-av" style="background:rgba(91,159,224,.15);color:#5B9FE0">TR</div><div class="msg-conv-body"><div class="msg-conv-name">Tom R.</div><div class="msg-conv-preview">Did you see the updated AV spec from Apex?</div></div><div class="msg-conv-meta"><div class="msg-conv-time">22m</div><div class="msg-conv-badge">1</div></div></div>
        <div class="msg-conv-row" onclick="openChat('sarah')" id="conv-sarah"><div class="msg-conv-av msg-online-pip" style="background:rgba(232,53,10,.15);color:#FF7043">SM</div><div class="msg-conv-body"><div class="msg-conv-name">Sarah M.</div><div class="msg-conv-preview">Crew call confirmed for 3pm. All good.</div></div><div class="msg-conv-meta"><div class="msg-conv-time">3h</div></div></div>
        <div class="msg-conv-row unread-conv" onclick="openChat('mara')" id="conv-mara"><div class="msg-conv-av" style="background:rgba(76,175,80,.15);color:#66BB6A">ML</div><div class="msg-conv-body"><div class="msg-conv-name">Mara L.</div><div class="msg-conv-preview">Quick question about the Austin venue load-in</div></div><div class="msg-conv-meta"><div class="msg-conv-time">5h</div><div class="msg-conv-badge">1</div></div></div>
        <div class="msg-conv-row" onclick="openChat('dana')" id="conv-dana"><div class="msg-conv-av" style="background:rgba(232,184,75,.15);color:var(--gold)">DL</div><div class="msg-conv-body"><div class="msg-conv-name">Dana L.</div><div class="msg-conv-preview">Lighting plot v3 is ready for review</div></div><div class="msg-conv-meta"><div class="msg-conv-time">1d</div></div></div>
      </div>
    </div>
    <!-- Chat window -->
    <div class="msg-chat" id="chat-window"></div>
  </div>
</div>

<!-- NEW MESSAGE MODAL -->
<div class="modal-overlay" id="modal-new-msg" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="modal">
    <div class="modal-hd"><div class="modal-title">New message</div><button class="modal-close" onclick="document.getElementById('modal-new-msg').classList.remove('open')">✕</button></div>
    <div class="modal-body">
      <div class="form-grp"><div class="form-lbl">To</div><input class="form-inp" type="text" placeholder="Search people or productions…"></div>
      <div class="form-grp"><div class="form-lbl">Message</div><textarea class="form-inp" rows="4" placeholder="Write a message…" style="resize:vertical"></textarea></div>
    </div>
    <div class="modal-ft">
      <button class="btn-ghost" onclick="document.getElementById('modal-new-msg').classList.remove('open')">Cancel</button>
      <button class="btn-gold" onclick="document.getElementById('modal-new-msg').classList.remove('open')">Send</button>
    </div>
  </div>
</div>

<script>
const CHATS = {
  'gala-team':   { name:'Summerset Gala — Team',  sub:'5 members · Sarah M., Tom R., Dana L., Marcus B., Priya N.', icon:'🎭', iconBg:'rgba(232,53,10,.15)', iconColor:'#E8350A', isGroup:true,  prod:'/overview/production.php?id=gala',
    refs:[{icon:'▶',label:'Run of show',sub:'Show night 1 · 23 cues',href:'/overview/production.php?id=gala&tab=ros'},{icon:'📋',label:'Call sheet',sub:'Jun 14 · Show day',href:'/overview/production.php?id=gala&tab=callsheet'},{icon:'✅',label:'Tasks',sub:'2 open',href:'/overview/production.php?id=gala&tab=tasks'}],
    messages:[{type:'date',text:'Today · June 14, 2025'},{type:'other',av:'SM',avBg:'rgba(232,53,10,.15)',avColor:'#FF7043',name:'Sarah M.',text:'Morning team — load-in is running smooth. AV is set up, waiting on lighting focus.',time:'8:14 AM'},{type:'other',av:'TR',avBg:'rgba(91,159,224,.15)',avColor:'#5B9FE0',name:'Tom R.',text:'Lighting focus done by 10:30. Soundcheck at 11.',time:'8:22 AM'},{type:'mine',text:'Great. Client arrives at 4pm for a walkthrough. Make sure the room looks clean by 3:30.',time:'8:45 AM'},{type:'system',text:'Sarah M. started the run of show · 5:58 PM'},{type:'other',av:'SM',avBg:'rgba(232,53,10,.15)',avColor:'#FF7043',name:'Sarah M.',text:'ROS is loaded and ready. Cue 1 on standby. Connect to follow the show.',time:'5:59 PM'}]},
  'summit-team': { name:'Tech Summit — Team', sub:'6 members · Planning phase', icon:'🎭', iconBg:'rgba(91,159,224,.15)', iconColor:'#5B9FE0', isGroup:true, prod:'/overview/production.php?id=summit', refs:[],
    messages:[{type:'date',text:'Yesterday'},{type:'other',av:'ML',avBg:'rgba(76,175,80,.15)',avColor:'#66BB6A',name:'Mara L.',text:'Venue walkthrough confirmed for May 15. Tom and I will be there.',time:'2:30 PM'},{type:'mine',text:"Perfect. Can you get me the loading dock specs while you're there?",time:'2:45 PM'},{type:'date',text:'Today'},{type:'mine',text:'Just shared the updated venue contact sheet in Documents.',time:'10:12 AM'}]},
  'sphere-team': { name:'Sphere Build — Team', sub:'8 members · In progress', icon:'🏗', iconBg:'rgba(0,200,212,.15)', iconColor:'#4DB6AC', isGroup:true, prod:'/overview/project.php?id=sphere', refs:[],
    messages:[{type:'date',text:'Yesterday'},{type:'other',av:'DK',avBg:'rgba(0,200,212,.15)',avColor:'#4DB6AC',name:'Dana K.',text:'Permit docs uploaded to drive. Waiting on the city to confirm the inspection date.',time:'4:15 PM'},{type:'mine',text:"Thanks Dana. Let me know the moment that comes through.",time:'4:30 PM'}]},
  'tom':   { name:'Tom R.',   sub:'Technical director · Online',       icon:'TR', iconBg:'rgba(91,159,224,.15)', iconColor:'#5B9FE0', isGroup:false, refs:[],
    messages:[{type:'date',text:'Today'},{type:'other',av:'TR',avBg:'rgba(91,159,224,.15)',avColor:'#5B9FE0',name:'Tom R.',text:"Hey — did you see the updated AV spec from Apex? They revised the speaker placement for the awards section.",time:'9:38 AM'},{type:'mine',text:"Just looking at it now. The new placement makes sense — more even coverage.",time:'9:52 AM'}]},
  'sarah': { name:'Sarah M.', sub:'Stage manager · Online',            icon:'SM', iconBg:'rgba(232,53,10,.15)',  iconColor:'#FF7043',  isGroup:false, refs:[],
    messages:[{type:'date',text:'Today'},{type:'other',av:'SM',avBg:'rgba(232,53,10,.15)',avColor:'#FF7043',name:'Sarah M.',text:"Crew call confirmed for 3pm. All good on my end — ROS is locked.",time:'11:20 AM'},{type:'mine',text:"Perfect. See you at 3.",time:'11:22 AM'}]},
  'mara':  { name:'Mara L.',  sub:'Production coordinator',            icon:'ML', iconBg:'rgba(76,175,80,.15)',  iconColor:'#66BB6A',  isGroup:false, refs:[],
    messages:[{type:'date',text:'Today'},{type:'other',av:'ML',avBg:'rgba(76,175,80,.15)',avColor:'#66BB6A',name:'Mara L.',text:"Quick question about the Austin venue load-in — is the freight elevator large enough for the LED wall sections? They're 8ft wide.",time:'7:44 AM'}]},
  'dana':  { name:'Dana L.',  sub:'Lighting designer',                 icon:'DL', iconBg:'rgba(232,184,75,.15)', iconColor:'var(--gold)', isGroup:false, refs:[],
    messages:[{type:'date',text:'Yesterday'},{type:'other',av:'DL',avBg:'rgba(232,184,75,.15)',avColor:'var(--gold)',name:'Dana L.',text:"Lighting plot v3 is ready for your review. Made a few changes to the awards section coverage.",time:'3:10 PM'},{type:'mine',text:"Great — will look at it tonight.",time:'5:30 PM'}]},
};

let activeChat = null;

function openChat(id) {
  activeChat = id;
  const chat = CHATS[id]; if (!chat) return;
  document.querySelectorAll('.msg-conv-row').forEach(r=>r.classList.remove('active'));
  const row = document.getElementById('conv-'+id);
  if (row) { row.classList.add('active'); row.classList.remove('unread-conv'); row.querySelector('.msg-conv-badge')?.remove(); }
  const win = document.getElementById('chat-window'); if (!win) return;
  const avStyle = chat.isGroup
    ? `width:34px;height:34px;background:${chat.iconBg};color:${chat.iconColor};font-size:16px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0`
    : `width:34px;height:34px;background:${chat.iconBg};color:${chat.iconColor};font-size:12px;font-weight:600;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0`;
  const chips = (chat.refs||[]).map(r=>`<a class="msg-ref-chip" href="${r.href}">${r.icon} ${r.label}</a>`).join('');
  const msgs = chat.messages.map(renderMsg).join('');
  win.innerHTML = `
    <div class="msg-chat-hd"><div style="${avStyle}">${chat.icon}</div><div><div class="msg-chat-name">${chat.name}</div><div class="msg-chat-sub">${chat.sub}</div></div><div class="msg-chat-actions">${chat.prod?`<a class="msg-chat-icon" href="${chat.prod}" title="Open workspace">📋</a>`:''}<button class="msg-chat-icon">🔍</button></div></div>
    <div class="msg-messages" id="msg-messages">${msgs}</div>
    ${chips?`<div style="border-top:.5px solid var(--border)"><div class="msg-ref-picker">${chips}</div></div>`:''}
    <div class="msg-input-area"><div class="msg-input-row"><div class="msg-input-wrap"><button class="msg-attach-btn">📎</button><textarea class="msg-input" id="msg-input" placeholder="Message ${chat.name}…" rows="1" onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg()}" oninput="autoResize(this)"></textarea></div><button class="msg-send-btn" onclick="sendMsg()">↑</button></div></div>`;
  setTimeout(()=>{const el=document.getElementById('msg-messages');if(el)el.scrollTop=el.scrollHeight;},50);
}

function renderMsg(m) {
  if (m.type==='date')   return `<div class="msg-date-divider">${m.text}</div>`;
  if (m.type==='system') return `<div class="msg-system">${m.text}</div>`;
  const ref = m.ref ? `<a class="msg-ref" href="${m.ref.href}"><div class="msg-ref-icon">${m.ref.icon}</div><div><div class="msg-ref-title">${m.ref.label}</div><div class="msg-ref-sub">${m.ref.sub}</div></div></a>` : '';
  if (m.type==='mine') return `<div class="msg-bubble-wrap mine"><div class="msg-bubble-col"><div class="msg-bubble mine">${m.text}${ref}</div><div class="msg-bubble-time">${m.time}</div></div></div>`;
  return `<div class="msg-bubble-wrap"><div style="width:24px;height:24px;border-radius:50%;background:${m.avBg};color:${m.avColor};font-size:9px;font-weight:600;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-bottom:2px">${m.av}</div><div class="msg-bubble-col"><div class="msg-sender-name">${m.name}</div><div class="msg-bubble other">${m.text}${ref}</div><div class="msg-bubble-time">${m.time}</div></div></div>`;
}

function sendMsg() {
  const input=document.getElementById('msg-input'); if(!input)return;
  const text=input.value.trim(); if(!text)return;
  const list=document.getElementById('msg-messages'); if(!list)return;
  const wrap=document.createElement('div'); wrap.className='msg-bubble-wrap mine';
  wrap.innerHTML=`<div class="msg-bubble-col"><div class="msg-bubble mine">${text}</div><div class="msg-bubble-time">Just now</div></div>`;
  list.appendChild(wrap); list.scrollTop=list.scrollHeight;
  const row=document.getElementById('conv-'+activeChat);
  if(row){const p=row.querySelector('.msg-conv-preview');if(p)p.textContent='You: '+text;const t=row.querySelector('.msg-conv-time');if(t)t.textContent='Just now';}
  input.value=''; input.style.height='auto';
}

function autoResize(el) { el.style.height='auto'; el.style.height=Math.min(el.scrollHeight,120)+'px'; }
function filterConvs(q) { document.querySelectorAll('.msg-conv-row').forEach(r=>{const n=r.querySelector('.msg-conv-name')?.textContent?.toLowerCase()||''; r.style.display=n.includes(q.toLowerCase())||!q?'':'none';}); }

document.addEventListener('DOMContentLoaded', ()=>openChat('gala-team'));
</script>

<?php include ROOT_PATH . '/required/footer.php'; ?>
