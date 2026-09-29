<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>InvCMP · Inventario de Equipos</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  /* Paleta de marca (Pantone 381C / 361C / 375C / 355C) — sin cambios */
  --brand-yellow:#CED800;--brand-lime:#94D500;--brand-green:#3FAF29;--brand-deep:#00953A;
  --gd:#0C2A11;--gk:#00953A;--gm:#3FAF29;--gl:#94D500;--gy:#CED800;
  --wh:#fff;--of:#FAFCF8;--mu:#EEF6E7;--bd:#E7EEE1;
  --ts:#0F7A34;--tm:#8A9A85;--tx:#16211A;
  --rad:8px;--rad-lg:10px;
  --shadow:none;
  --shadow-lift:0 12px 28px rgba(12,42,17,.14);
  --ring:0 0 0 3px rgba(148,213,0,.28);
  --ease:cubic-bezier(.22,1,.36,1);
}
@keyframes fadeSlideUp{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
@keyframes modalPop{from{opacity:0;transform:scale(.97) translateY(8px)}to{opacity:1;transform:scale(1) translateY(0)}}
@keyframes modalBgFade{from{opacity:0}to{opacity:1}}
.view-fade{animation:fadeSlideUp .28s var(--ease);}
*:focus-visible{outline:2px solid var(--gl);outline-offset:2px;}
body{font-family:'Inter',sans-serif;background:var(--of);color:var(--tx);min-height:100vh;display:flex;flex-direction:column;font-size:14px;line-height:1.5;-webkit-font-smoothing:antialiased;}
.ic{display:inline-block;vertical-align:middle;flex-shrink:0}

/* ---- Header: plano, sin decoración ---- */
header{background:var(--gd);padding:0 2rem;height:56px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:200;}
.logo{display:flex;align-items:center;gap:.6rem;text-decoration:none;cursor:pointer;background:none;border:none;}
.logo-text{font-size:1rem;font-weight:600;color:#fff;letter-spacing:-.01em;}
.logo-text span{color:var(--gl);}
.app{flex:1;display:flex;height:calc(100vh - 56px);overflow:hidden;}

/* ---- Sidebar ---- */
.left-panel{width:300px;min-width:260px;background:#fff;border-right:1px solid var(--bd);display:flex;flex-direction:column;overflow:hidden;}
.search-area{padding:1.25rem 1.25rem .9rem;}
.si{width:100%;padding:.6rem .8rem .6rem 2.3rem;border:1px solid var(--bd);border-radius:var(--rad);font-family:'Inter',sans-serif;font-size:.8rem;background:var(--of);outline:none;color:var(--tx);transition:border-color .15s;}
.si:focus{border-color:var(--gl);background:#fff;}
.sw-wrap{position:relative;}
.sic{position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--tm);}
.btn-toggle-people{margin:0 1.25rem .5rem;padding:0 0 .6rem;background:none;border:none;border-bottom:1px solid var(--bd);cursor:pointer;display:flex;align-items:center;justify-content:space-between;font-family:'Inter',sans-serif;font-size:.68rem;font-weight:600;color:var(--tm);width:calc(100% - 2.5rem);text-transform:uppercase;letter-spacing:.05em;}
.btn-toggle-people:hover{color:var(--gk);}
.btn-toggle-people .tp-left{display:flex;align-items:center;gap:.5rem;}
.btn-toggle-people .tp-chevron{color:var(--tm);transition:transform .18s var(--ease);}
.btn-toggle-people.open .tp-chevron{transform:rotate(180deg);}
.pl{flex:1;overflow-y:auto;padding:0 .5rem;}
.pl::-webkit-scrollbar, .right-panel::-webkit-scrollbar {width:4px;}
.pl::-webkit-scrollbar-thumb, .right-panel::-webkit-scrollbar-thumb {background:#DCE7D4;border-radius:3px;}
.pc{background:none;border:none;border-radius:var(--rad);padding:.65rem .75rem;margin-bottom:.1rem;cursor:pointer;display:flex;align-items:center;gap:.7rem;transition:background .12s;}
.pc:hover{background:var(--of);}
.pc.active{background:var(--mu);}
.pc-ic{width:32px;height:32px;border-radius:50%;background:var(--mu);color:var(--gk);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.pc.active .pc-ic{background:var(--gk);color:#fff;}
.pc-body{min-width:0;flex:1;}
.pc-name{font-weight:600;font-size:.81rem;color:var(--tx);display:flex;align-items:center;gap:.35rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.pc-meta{font-size:.68rem;color:var(--tm);margin-top:.1rem;display:flex;align-items:center;gap:.3rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.badge-king{color:#B8860B;flex-shrink:0;}

/* ---- Botones: planos ---- */
.btn{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem .85rem;border-radius:var(--rad);border:none;cursor:pointer;font-family:'Inter',sans-serif;font-size:.78rem;font-weight:600;transition:background .12s,color .12s;}
.btn:hover{filter:none;}
.btn:active{transform:scale(.98);}
.btn-primary{background:var(--gk);color:#fff;}
.btn-primary:hover{background:var(--gd);}
.btn-danger{background:none;color:#B42318;}
.btn-danger:hover{background:#FDEDED;}
.btn-ghost{background:none;color:var(--gk);border:1px solid var(--bd);}
.btn-ghost:hover{background:var(--mu);border-color:var(--mu);}
.btn-sm{padding:.32rem .55rem;font-size:.7rem;}
.btn-papelera{margin:.75rem 1.25rem 1.1rem;padding:0;background:none;color:var(--tm);border:none;font-weight:600;cursor:pointer;width:calc(100% - 2.5rem);display:flex;align-items:center;justify-content:flex-start;gap:.5rem;font-size:.75rem;}
.btn-papelera:hover{color:#B42318;}
.right-panel{flex:1;overflow-y:auto;background:var(--of);}

/* ---- Detail header ---- */
.dh{background:var(--gd);padding:2rem 2.5rem;color:#fff;}
.dh-top{display:flex;align-items:center;gap:1rem;}
.dh-ic{width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;}
.dhn{font-size:1.4rem;font-weight:600;letter-spacing:-.02em;}
.dhs{font-size:.78rem;color:rgba(255,255,255,.5);margin-top:.2rem;}
.tag{font-size:.66rem;font-weight:600;padding:.3rem .7rem;border-radius:20px;background:rgba(255,255,255,.08);margin-right:.4rem;display:inline-flex;align-items:center;gap:.3rem;color:rgba(255,255,255,.8);}
.tag.king{background:rgba(242,201,76,.16);color:#F2C94C;}
.dh-actions{margin-top:1.3rem;}
.db{padding:2rem 2.5rem 2.5rem;max-width:980px;}

/* ---- Equipo cards: filas planas, no cajas ---- */
.eq-wrap{margin-bottom:0;border-bottom:1px solid var(--bd);}
details.eq-card{background:none;border:none;border-radius:0;margin-bottom:0;overflow:visible;box-shadow:none;}
summary.eq-sum{padding:1.1rem 0;cursor:pointer;list-style:none;display:flex;justify-content:space-between;align-items:center;gap:.75rem;}
summary.eq-sum::-webkit-details-marker{display:none;}
.eq-sum-left{display:flex;align-items:center;gap:.9rem;min-width:0;}
.eq-ic{width:38px;height:38px;border-radius:50%;background:var(--mu);color:var(--gk);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.eq-title{font-weight:600;font-size:.88rem;color:var(--tx);letter-spacing:-.01em;}
.eq-sub{font-size:.72rem;color:var(--tm);font-family:'JetBrains Mono',monospace;margin-top:.2rem;}
.eq-chevron{color:var(--tm);transition:transform .18s var(--ease);flex-shrink:0;}
details[open] .eq-chevron{transform:rotate(180deg);}
.eq-count{font-size:.6rem;font-weight:600;color:var(--tm);text-transform:uppercase;letter-spacing:.04em;}
.det-content{padding:0 0 1.6rem;}

.sec-label{font-size:.64rem;font-weight:600;color:var(--tm);text-transform:uppercase;letter-spacing:.06em;margin:1.4rem 0 .7rem;display:flex;align-items:center;justify-content:space-between;}
.sec-label-txt{display:flex;align-items:center;gap:.4rem;}

.spec-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1px;background:var(--bd);border:1px solid var(--bd);border-radius:var(--rad);overflow:hidden;}
.spec-c{background:#fff;padding:.75rem .85rem;}
.spec-k{font-size:.6rem;color:var(--tm);text-transform:uppercase;letter-spacing:.03em;margin-bottom:.3rem;display:flex;align-items:center;gap:.3rem;}
.spec-v{font-size:.82rem;font-weight:600;color:var(--tx);word-break:break-word;font-variant-numeric:tabular-nums;}
.spec-v.muted{color:var(--tm);font-weight:500;}

.note-box{background:var(--mu);border:none;border-left:2px solid var(--gk);border-radius:0 var(--rad) var(--rad) 0;padding:.8rem 1rem;font-size:.79rem;color:var(--tx);line-height:1.6;}

.periph-list{display:flex;flex-direction:column;}
.periph-item{display:flex;align-items:center;gap:.7rem;background:none;border:none;border-bottom:1px solid var(--bd);border-radius:0;padding:.7rem .1rem;}
.periph-item:last-child{border-bottom:none;}
.periph-ic{width:26px;height:26px;border-radius:50%;background:var(--mu);border:none;color:var(--gk);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.periph-name{font-size:.78rem;font-weight:600;flex:1;min-width:0;}
.periph-sn{font-size:.68rem;color:var(--tm);font-family:'JetBrains Mono',monospace;}
.empty-state{font-size:.78rem;color:var(--tm);padding:.6rem .1rem;}

.swt{width:100%;border-collapse:collapse;font-size:.76rem;}
.swt th{text-align:left;padding:.6rem .3rem;color:var(--tm);border-bottom:1px solid var(--bd);font-weight:600;font-size:.63rem;text-transform:uppercase;letter-spacing:.04em;}
.swt td{padding:.6rem .3rem;border-bottom:1px solid var(--bd);}
.pap-sec{border:none;border-bottom:1px solid var(--bd);border-radius:0;margin-bottom:0;overflow:visible;}
.hidden-row{display:none;}
.pap-sec-sum::-webkit-details-marker{display:none;}
.pap-sec-sum .eq-count{margin-left:auto;}
.pap-sec-sum .eq-chevron{margin-left:.2rem;}
.pap-sec[open] .pap-sec-sum .eq-chevron{transform:rotate(180deg);}
.pap-sec-body{padding:.2rem 0 1.1rem;display:flex;flex-direction:column;gap:.5rem;}
.pap-item{display:flex;align-items:center;gap:.65rem;background:var(--of);border:1px solid var(--bd);border-radius:var(--rad);padding:.7rem .8rem;}
.pap-item-ic{width:28px;height:28px;border-radius:50%;background:#fff;border:1px solid var(--bd);color:var(--gk);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.pap-item-body{flex:1;min-width:0;}
.pap-item-title{font-size:.8rem;font-weight:600;color:var(--tx);}
.pap-item-meta{font-size:.68rem;color:var(--tm);margin-top:.15rem;}
.pap-item-actions{display:flex;gap:.3rem;flex-shrink:0;}
.pap-sw-detail{margin-top:.5rem;}
.pap-sw-detail summary{cursor:pointer;list-style:none;font-size:.68rem;font-weight:600;color:var(--gk);display:flex;align-items:center;gap:.25rem;}
.pap-sw-detail summary::-webkit-details-marker{display:none;}
.pap-sw-list{margin-top:.5rem;max-height:180px;overflow-y:auto;display:flex;flex-direction:column;gap:.2rem;padding-right:.3rem;}
.pap-sw-row{font-size:.7rem;color:var(--tx);background:#fff;border:1px solid var(--bd);border-radius:6px;padding:.35rem .55rem;}

/* ---- Modals ---- */
.modal-bg{position:fixed;inset:0;background:rgba(12,42,17,.45);z-index:500;display:flex;align-items:center;justify-content:center;padding:1rem;animation:modalBgFade .16s var(--ease);}
.modal{background:#fff;border-radius:14px;padding:1.75rem;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-lift);animation:modalPop .2s var(--ease);}
.modal.modal-flex{display:flex;flex-direction:column;overflow:hidden;}
.modal.modal-flex > *{flex-shrink:0;}
.modal.modal-flex .modal-scroll{flex:1;overflow-y:auto;min-height:0;}
.modal h3{font-size:1.05rem;font-weight:600;display:flex;align-items:center;gap:.5rem;margin-bottom:.3rem;letter-spacing:-.01em;}
.modal-sub{font-size:.78rem;color:var(--tm);margin-bottom:1.4rem;line-height:1.55;}
fieldset{border:none;border-top:1px solid var(--bd);border-radius:0;padding:1rem 0 .2rem;margin-bottom:.3rem;}
legend{font-size:.63rem;font-weight:600;color:var(--tm);text-transform:uppercase;letter-spacing:.05em;padding:0;margin-left:-.05em;display:flex;align-items:center;gap:.35rem;float:left;width:100%;margin-bottom:.7rem;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.8rem;clear:both;}
.form-grid.c3{grid-template-columns:1fr 1fr 1fr;}
.fg{display:flex;flex-direction:column;gap:.3rem;margin-bottom:.6rem;}
.fg.full{grid-column:1/-1;}
.fg label{font-size:.64rem;font-weight:600;color:var(--tm);text-transform:uppercase;letter-spacing:.03em;}
.fg input, .fg select, .fg textarea{padding:.6rem .7rem;border:1px solid var(--bd);border-radius:var(--rad);font-family:'Inter';font-size:.8rem;color:var(--tx);outline:none;background:var(--of);transition:border-color .15s,background .15s;}
.fg input:focus, .fg select:focus, .fg textarea:focus{border-color:var(--gl);background:#fff;}
.fg textarea{resize:vertical;min-height:56px;}
.modal-footer{display:flex;justify-content:flex-end;gap:.6rem;margin-top:.8rem;padding-top:1.2rem;border-top:1px solid var(--bd);}
.toast{position:fixed;bottom:1.25rem;right:1.25rem;background:var(--gd);color:#fff;padding:.75rem 1.1rem;border-radius:var(--rad);transform:translateY(150%);transition:transform .3s var(--ease);z-index:999;display:flex;align-items:center;gap:.55rem;font-size:.81rem;font-weight:500;box-shadow:var(--shadow-lift);}
.toast.show{transform:translateY(0);}

/* ---- Dashboard / KPIs: siempre visibles, ordenados ---- */
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1px;background:var(--bd);border:1px solid var(--bd);border-radius:var(--rad-lg);overflow:hidden;margin-bottom:1.6rem;}
.kpi-card{position:relative;aspect-ratio:auto;min-height:96px;background:#fff;border:none;border-radius:0;box-shadow:none;cursor:pointer;overflow:hidden;display:flex;flex-direction:column;align-items:flex-start;justify-content:center;gap:.35rem;padding:1.1rem 1.2rem;transition:background .15s;}
.kpi-card:hover{background:var(--mu);}
.kpi-ic{width:20px;height:20px;color:var(--gk);order:2;}
.kpi-ic svg{width:100%;height:100%;}
.kpi-info{position:static;display:flex;flex-direction:column;color:var(--tx);text-align:left;padding:0;opacity:1;transform:none;order:1;}
.kpi-n{font-size:1.55rem;font-weight:700;line-height:1;font-variant-numeric:tabular-nums;letter-spacing:-.02em;}
.kpi-l{font-size:.68rem;font-weight:500;margin-top:.35rem;text-transform:none;letter-spacing:0;color:var(--tm);opacity:1;}

.grid-2{display:grid;grid-template-columns:1.3fr 1fr;gap:1.5rem;align-items:start;}
.panel{background:#fff;border:1px solid var(--bd);border-radius:var(--rad-lg);padding:1.4rem 1.5rem;box-shadow:none;margin-bottom:1.1rem;}
.panel-h{font-size:.85rem;font-weight:600;color:var(--tx);display:flex;align-items:center;gap:.55rem;margin-bottom:1.2rem;letter-spacing:-.005em;}
.panel-h .ic{color:var(--gk);}

.bar-row{display:flex;align-items:center;gap:.8rem;margin-bottom:.9rem;}
.bar-row:last-child{margin-bottom:0;}
.bar-label{font-size:.76rem;font-weight:500;width:130px;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--tx);}
.bar-track{flex:1;height:5px;background:var(--mu);border-radius:20px;overflow:hidden;}
.bar-fill{height:100%;background:var(--gk);border-radius:20px;}
.bar-n{font-size:.74rem;font-weight:600;color:var(--tm);width:26px;text-align:right;flex-shrink:0;font-variant-numeric:tabular-nums;}

.list-search{margin-bottom:1rem;}
.list-search input{width:100%;padding:.6rem .8rem;border:1px solid var(--bd);border-radius:var(--rad);font-size:.78rem;font-family:'Inter';outline:none;background:var(--of);transition:border-color .15s;}
.list-search input:focus{border-color:var(--gl);background:#fff;}
.simple-row{display:flex;align-items:center;justify-content:space-between;padding:.65rem .1rem;border-bottom:1px solid var(--bd);font-size:.78rem;}
.simple-row:last-child{border-bottom:none;}
.simple-row b{font-weight:600;}
.simple-row small{color:var(--tm);}

/* ---- Kanban ---- */
.kanban-wrap{display:flex;gap:1.5rem;align-items:flex-start;overflow-x:auto;padding-bottom:.5rem;}
.kanban-col{flex:1;min-width:250px;background:none;border:none;border-radius:0;padding:0;box-shadow:none;display:flex;flex-direction:column;min-height:220px;}
.kanban-col.drag-over{background:var(--mu);border-radius:var(--rad-lg);}
.kanban-col-h{display:flex;align-items:center;gap:.5rem;font-weight:600;font-size:.8rem;color:var(--tx);margin-bottom:1rem;padding-bottom:.8rem;border-bottom:1px solid var(--bd);letter-spacing:-.005em;}
.kanban-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;background:var(--kc);}
.kanban-count{margin-left:auto;font-size:.68rem;font-weight:600;color:var(--tm);background:none;padding:0;}
.kanban-cards{display:flex;flex-direction:column;gap:.6rem;flex:1;}
.kanban-card{background:#fff;border:1px solid var(--bd);border-left:none;border-radius:var(--rad);padding:.8rem .9rem;cursor:grab;max-width:100%;overflow:hidden;transition:border-color .15s,box-shadow .15s,opacity .15s;position:relative;}
.kanban-card::before{content:'';position:absolute;left:0;top:.9rem;bottom:.9rem;width:3px;border-radius:0 3px 3px 0;background:var(--card-color);}
.kanban-card{padding-left:1.05rem;}
.kanban-card:hover{border-color:#c9d9c1;box-shadow:0 4px 14px rgba(12,42,17,.08);}
.kanban-card:active{cursor:grabbing;}
.kanban-card.dragging{opacity:.4;}
.kc-title{font-size:.8rem;font-weight:600;color:var(--tx);margin-bottom:.3rem;line-height:1.4;overflow-wrap:anywhere;word-break:break-word;}
.kc-desc{font-size:.72rem;color:var(--tm);line-height:1.55;margin-bottom:.65rem;white-space:pre-wrap;overflow-wrap:anywhere;word-break:break-word;}
.kc-date{font-size:.63rem;color:var(--gk);display:flex;align-items:center;gap:.25rem;margin-bottom:.55rem;font-weight:600;}
.kc-actions{display:flex;justify-content:space-between;align-items:center;margin-top:.3rem;}
.kc-move{display:flex;gap:.3rem;}
.kc-btn{background:none;border:none;border-radius:6px;width:24px;height:24px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--tm);flex-shrink:0;transition:background .15s,color .15s;}
.kc-btn:hover{background:var(--mu);color:var(--gk);}
.kc-btn.danger{color:#B42318;}
.kc-btn.spacer{visibility:hidden;}
.kanban-add{margin-top:.9rem;width:100%;padding:.6rem;border:none;border-radius:var(--rad);background:none;color:var(--tm);font-size:.76rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:flex-start;gap:.4rem;}
.kanban-add:hover{color:var(--gk);}
.color-picker{display:flex;gap:.6rem;margin-top:.25rem;}
.color-swatch{width:24px;height:24px;border-radius:50%;border:2px solid transparent;cursor:pointer;padding:0;transition:transform .15s var(--ease);}
.color-swatch:hover{transform:scale(1.12);}
.color-swatch.active{border-color:var(--tx);box-shadow:0 0 0 2px #fff inset;}
</style>
</head>
<body>
<header>
  <button class="logo" onclick="loadKanban()">
    <svg width="28" height="28" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 3C20 3 6 18 6 27a14 14 0 0 0 28 0C34 18 20 3 20 3Z" fill="#CED800"/>
      <path d="M20 9C20 9 10 20 10 27a10 10 0 0 0 20 0c0-7-10-18-10-18Z" fill="#94D500"/>
      <path d="M20 15C20 15 14 22 14 27a6 6 0 0 0 12 0c0-5-6-12-6-12Z" fill="#3FAF29"/>
      <circle cx="20" cy="28" r="3" fill="#00953A"/>
    </svg>
    <span class="logo-text">Inv<span>CMP</span></span>
  </button>
  <div style="display:flex;align-items:center;gap:.5rem">
    <button class="btn btn-ghost" id="btnTareas" onclick="loadKanban()"><span>Tareas</span></button>
    <button class="btn btn-ghost" id="btnStats" onclick="loadDashboard()"><span>Estadísticas</span></button>
  </div>
</header>

<div class="app">
  <aside class="left-panel">
    <div class="search-area">
      <div class="sw-wrap">
        <span class="sic" id="searchIcon"></span>
        <input class="si" id="searchInput" type="text" placeholder="Buscar usuario, área o sede..." oninput="loadResponsables(this.value)">
      </div>
    </div>
    <button class="btn-toggle-people" id="btnTogglePeople" onclick="togglePeopleList()">
      <span class="tp-left"><span id="tpIcon"></span><span id="peopleToggleLabel">Ver usuarios</span></span>
      <span class="tp-chevron" id="tpChevron"></span>
    </button>
    <div class="pl" id="peopleList" style="display:none"></div>
    <button class="btn-papelera" id="btnPapelera" onclick="abrirPapelera()"><span>Papelera</span></button>
  </aside>

  <main class="right-panel" id="rightPanel"></main>
</div>

<div class="modal-bg" id="modalEquipo" style="display:none" onclick="if(event.target===this)closeModal('modalEquipo')">
  <div class="modal">
    <h3 id="meTitle">Editar elemento</h3>
    <div class="modal-sub" id="meSub">Completa la información del equipo o periférico.</div>
    <input type="hidden" id="meId">
    <input type="hidden" id="mePadreId">

    <fieldset>
      <legend>Ubicación y responsable</legend>
      <div class="form-grid">
        <div class="fg"><label>Responsable</label><input id="meResp" readonly placeholder="Nombre del usuario"></div>
        <div class="fg"><label>Área</label><input id="meArea" placeholder="Ej: Cartera"></div>
        <div class="fg"><label>Sede</label><input id="meSede" placeholder="Ej: Cúcuta"></div>
        <div class="fg"><label>Componente</label>
          <select id="meComp" onchange="toggleFormFields()">
            <option value="Equipo de computo">Equipo de computo</option>
            <option value="Mouse">Mouse</option>
            <option value="Teclado">Teclado</option>
            <option value="Monitor">Monitor</option>
            <option value="Otro">Otro</option>
          </select>
        </div>
      </div>
    </fieldset>

    <fieldset>
      <legend>Identificación</legend>
      <div class="form-grid c3">
        <div class="fg" id="fg-tipo_equipo"><label>Tipo de equipo</label>
          <select id="meTipo">
            <option value="">Seleccionar</option>
            <option>Portatil</option><option>Todo en Uno</option><option>Torre</option><option>Otro</option>
          </select>
        </div>
        <div class="fg"><label>Marca</label><input id="meMarca" placeholder="Ej: HP, Dell..."></div>
        <div class="fg"><label>Modelo</label><input id="meModelo"></div>
        <div class="fg full"><label>Serial</label><input id="meSerial" placeholder="Número de serie"></div>
      </div>
    </fieldset>

    <fieldset id="fs-hardware">
      <legend>Hardware</legend>
      <div class="form-grid c3">
        <div class="fg full"><label>Procesador</label><input id="meProc" placeholder="Ej: Intel Core i5-1035G1"></div>
        <div class="fg"><label>RAM total</label><input id="meRam" placeholder="Ej: 8 GB"></div>
        <div class="fg"><label>Tipo de RAM</label><input id="meTipoRam" placeholder="Ej: DDR4"></div>
        <div class="fg"><label>RAM usada</label><input id="meRamUsada" placeholder="Ej: 60%"></div>
        <div class="fg"><label>Disco total</label><input id="meDisco" placeholder="Ej: 256 GB"></div>
        <div class="fg"><label>Tipo de disco</label><input id="meTipoDisco" placeholder="Ej: SSD"></div>
        <div class="fg"><label>Disco usado</label><input id="meDiscoUsado" placeholder="Ej: 45%"></div>
      </div>
    </fieldset>

    <fieldset id="fs-sistema">
      <legend>Sistema y usuario</legend>
      <div class="form-grid">
        <div class="fg"><label>Sistema operativo</label><input id="meSO" placeholder="Ej: Windows 11 Pro"></div>
        <div class="fg"><label>Office instalado</label><input id="meOffice" placeholder="Ej: Office 2019"></div>
        <div class="fg"><label>Usuario actual (sesión)</label><input id="meUsuarioActual"></div>
        <div class="fg"><label>Nombre completo (equipo)</label><input id="meNombreCompleto"></div>
      </div>
    </fieldset>

    <fieldset id="fs-red">
      <legend>Conectividad</legend>
      <div class="form-grid">
        <div class="fg"><label>IP local</label><input id="meIP" placeholder="Ej: 192.168.1.10"></div>
        <div class="fg"><label>Conexión</label>
          <select id="meConexion">
            <option value="">Seleccionar</option>
            <option>WiFi</option><option>Cable</option><option>No determinada</option>
          </select>
        </div>
      </div>
    </fieldset>

    <fieldset>
      <legend>Observaciones</legend>
      <div class="fg full"><textarea id="meObs" placeholder="Fallas, notas o pendientes de este equipo..."></textarea></div>
    </fieldset>

    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalEquipo')">Cancelar</button>
      <button class="btn btn-primary" id="btnGuardarEquipo" onclick="guardarEquipo()"><span>Guardar</span></button>
    </div>
  </div>
</div>

<div class="modal-bg" id="modalSW" style="display:none" onclick="if(event.target===this)closeModal('modalSW')">
  <div class="modal" style="max-width:420px">
    <h3 id="swModalTitle"><span>Agregar software</span></h3>
    <div class="modal-sub">Registrar un programa instalado en este equipo.</div>
    <input type="hidden" id="swEqId">
    <div class="fg"><label>Programa</label><input id="swProg" placeholder="Ej: Adobe Acrobat"></div>
    <div class="fg"><label>Versión</label><input id="swVer" placeholder="Ej: 26.001"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalSW')">Cancelar</button>
      <button class="btn btn-primary" onclick="guardarSoftware()">Guardar</button>
    </div>
  </div>
</div>

<div class="modal-bg" id="modalPapelera" style="display:none" onclick="if(event.target===this)closeModal('modalPapelera')">
  <div class="modal" style="max-width:640px">
    <h3 id="papeleraModalTitle"><span>Papelera de reciclaje</span></h3>
    <div class="modal-sub">Elementos eliminados, agrupados por tipo. Puedes restaurarlos o borrarlos definitivamente.</div>
    <div id="papeleraContent">Cargando...</div>
    <div class="modal-footer">
      <button class="btn btn-danger" id="btnVaciarPapelera" onclick="vaciarPapelera()"><span>Vaciar papelera</span></button>
      <button class="btn btn-ghost" onclick="closeModal('modalPapelera')">Cerrar</button>
    </div>
  </div>
</div>

<div class="modal-bg" id="modalAsignar" style="display:none" onclick="if(event.target===this)closeModal('modalAsignar')">
  <div class="modal modal-flex" style="max-width:560px;max-height:80vh">
    <h3 id="asgTitle"><span>Asignar equipo existente</span></h3>
    <div class="modal-sub" id="asgSub">Busca un equipo de cómputo y asígnalo a este usuario. Se le quitará automáticamente a quien lo tenía; sus periféricos (mouse/teclado) no se ven afectados.</div>
    <input type="hidden" id="asgResponsable">
    <div style="display:flex;gap:.5rem;margin-bottom:.8rem">
      <div class="sw-wrap" style="flex:1">
        <span class="sic" id="asgSearchIcon" style="left:.75rem"></span>
        <input class="si" id="asgQ" type="text" placeholder="Buscar por responsable, marca, modelo, serial, área..." oninput="buscarEquiposAsignar()">
      </div>
      <button class="btn btn-ghost" id="asgVerTodos" onclick="verTodosEquiposAsignar()"><span>Ver todos</span></button>
    </div>
    <div id="asgResults" class="modal-scroll" style="display:flex;flex-direction:column;gap:.4rem"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalAsignar')">Cerrar</button>
    </div>
  </div>
</div>

<div class="modal-bg" id="modalTarea" style="display:none" onclick="if(event.target===this)closeModal('modalTarea')">
  <div class="modal" style="max-width:460px">
    <h3 id="tareaModalTitle"><span>Nueva tarea</span></h3>
    <div class="modal-sub">Tareas generales del área de tecnología.</div>
    <input type="hidden" id="tareaId">
    <div class="fg"><label>Título</label><input id="tareaTitulo" placeholder="Ej: Revisar cable de red - Cartera"></div>
    <div class="fg"><label>Descripción</label><textarea id="tareaDesc" placeholder="Detalles adicionales (opcional)..."></textarea></div>
    <div class="fg">
      <label>Color</label>
      <div class="color-picker" id="colorPicker"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalTarea')">Cancelar</button>
      <button class="btn btn-primary" id="btnGuardarTarea" onclick="guardarTarea()"><span>Guardar</span></button>
    </div>
  </div>
</div>

<div class="modal-bg" id="modalKpiResumen" style="display:none" onclick="if(event.target===this)closeModal('modalKpiResumen')">
  <div class="modal modal-flex" style="max-width:560px;max-height:80vh">
    <h3 id="kpiModalTitle">Resumen</h3>
    <div class="modal-sub">Detalle actualizado en tiempo real desde la base de datos.</div>
    <div id="kpiModalContent" class="modal-scroll"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalKpiResumen')">Cerrar</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
/* ============ ICONOS SVG (sin dependencias externas) ============ */
function svg(path, size=18, vb='0 0 24 24'){
  return `<svg class="ic" width="${size}" height="${size}" viewBox="${vb}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">${path}</svg>`;
}
const I = {
  laptop:   svg('<rect x="3" y="4" width="18" height="11" rx="1.2"/><path d="M1.5 19.5h21l-1.8-3.5h-17.4z"/>'),
  desktop:  svg('<rect x="4" y="3.5" width="16" height="11" rx="1.2"/><path d="M9 19h6M12 14.5V19"/>'),
  monitor:  svg('<rect x="3" y="4" width="18" height="12" rx="1.2"/><path d="M8 20h8M12 16v4"/>'),
  mouse:    svg('<rect x="7.5" y="2.5" width="9" height="17" rx="4.5"/><path d="M12 2.5v6"/>'),
  keyboard: svg('<rect x="2" y="6" width="20" height="12" rx="1.5"/><path d="M6 10h.01M9.5 10h.01M13 10h.01M16.5 10h.01M20 10h.01M6 14h12"/>'),
  box:      svg('<path d="M21 7.5 12 3 3 7.5l9 4.5 9-4.5z"/><path d="M3 7.5v9l9 4.5 9-4.5v-9"/><path d="M12 12v9"/>'),
  user:     svg('<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>'),
  users:    svg('<circle cx="9" cy="8" r="3.3"/><path d="M2.5 20c0-3.6 2.9-6.5 6.5-6.5s6.5 2.9 6.5 6.5"/><path d="M16 4.3c1.7.4 3 2 3 3.9 0 1.9-1.3 3.5-3 3.9M20 20c0-2.9-1.7-5.4-4.2-6.2"/>'),
  building: svg('<rect x="4" y="3" width="16" height="18" rx="1"/><path d="M9 8h.01M15 8h.01M9 12h.01M15 12h.01M9 16h.01M15 16h.01"/>'),
  search:   svg('<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/>', 16),
  trash:    svg('<path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-.9 13.4A2 2 0 0 1 16.1 21H7.9a2 2 0 0 1-2-1.6L5 6"/><path d="M10 11v6M14 11v6"/>', 16),
  plus:     svg('<path d="M12 5v14M5 12h14"/>', 16),
  check:    svg('<path d="M20 6 9 17l-5-5"/>', 16),
  chevron:  svg('<path d="M6 9l6 6 6-6"/>', 16),
  chip:     svg('<rect x="6" y="6" width="12" height="12" rx="1.5"/><rect x="9" y="9" width="6" height="6"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M1 9h3M1 15h3M20 9h3M20 15h3"/>'),
  ram:      svg('<rect x="3" y="9" width="18" height="7" rx="1.2"/><path d="M6.5 9V6M10.5 9V6M14.5 9V6M18 9V6"/>'),
  disk:     svg('<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="2.6"/><path d="M12 3v6"/>'),
  wifi:     svg('<path d="M4.5 12.5a10.6 10.6 0 0 1 15 0M7.8 15.8a6.2 6.2 0 0 1 8.4 0"/><circle cx="12" cy="19" r="1"/>'),
  system:   svg('<rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/>'),
  office:   svg('<rect x="2.5" y="4.5" width="19" height="15" rx="1.5"/><path d="M2.5 9h19"/>'),
  note:     svg('<path d="M14 2H6.5A1.5 1.5 0 0 0 5 3.5v17A1.5 1.5 0 0 0 6.5 22h11a1.5 1.5 0 0 0 1.5-1.5V8z"/><path d="M14 2v6h5"/>'),
  shield:   svg('<path d="M12 2 4 5.5v5c0 5 3.4 8.8 8 10 4.6-1.2 8-5 8-10v-5z"/><path d="M9 12l2 2 4-4.5"/>', 14),
  refresh:  svg('<path d="M22 4v6h-6M2 20v-6h6"/><path d="M3.5 9a9 9 0 0 1 14.6-3.4L22 10M2 14l3.9 3.4A9 9 0 0 0 20.5 15"/>', 16),
  close:    svg('<path d="M18 6 6 18M6 6l12 12"/>', 12),
  gear:     svg('<circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.2 4.2l2.8 2.8M17 17l2.8 2.8M1 12h4M19 12h4M4.2 19.8 7 17M17 7l2.8-2.8"/>'),
  kanban:   svg('<rect x="3" y="3" width="6" height="18" rx="1.2"/><rect x="10" y="3" width="6" height="11" rx="1.2"/><rect x="17" y="3" width="4" height="7" rx="1.2"/>'),
  chart:    svg('<path d="M4 20V11M12 20V4M20 20v-8"/>'),
  chevLeft: svg('<path d="M15 18l-6-6 6-6"/>', 14),
  chevRight:svg('<path d="M9 18l6-6-6-6"/>', 14),
};
function deviceIcon(componente, tipo){
  if(componente === 'Mouse') return I.mouse;
  if(componente === 'Teclado') return I.keyboard;
  if(componente === 'Monitor') return I.monitor;
  if(componente === 'Equipo de computo'){
    if(tipo === 'Todo en Uno' || tipo === 'Torre') return I.desktop;
    return I.laptop;
  }
  return I.box;
}
function compLabel(c){
  const map = {'Equipo de computo':'Equipos de cómputo','Mouse':'Mouse','Teclado':'Teclados','Monitor':'Monitores','Otro':'Otros'};
  return map[c] || c;
}

const API = './php/api.php';

const KANBAN_COLS = [
  {estado:'pendiente',   label:'Por hacer',    color:'#E5484D'},
  {estado:'en_proceso',  label:'En proceso',   color:'#D4A017'},
  {estado:'completada',  label:'Completadas',  color:'#2D5A27'},
];
const CARD_COLORS = { verde:'#8BC34A', amarillo:'#F2C94C', rojo:'#EB5757', azul:'#4A90D9', morado:'#9B6FD1' };
let selectedColor = 'verde';
let dragTareaId = null;
let activeUser = null;

async function api(params, body=null){
  const url = API + '?' + new URLSearchParams(params);
  const opts = body ? {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body)} : {method:'GET'};
  const r = await fetch(url, opts);
  return await r.json();
}

function toast(msg, icon=I.check){
  const t = document.getElementById('toast');
  t.innerHTML = icon + '<span>' + msg + '</span>'; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'), 3000);
}
function closeModal(id){ document.getElementById(id).style.display='none'; }
function esc(s){ return (s===null||s===undefined) ? '' : String(s).replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

/* ============ DASHBOARD ============ */
async function loadDashboard(){
  activeUser = null;
  document.querySelectorAll('.pc.active').forEach(el=>el.classList.remove('active'));
  const d = await api({action:'stats'});

  const cards = [
    {ic:I.users, n:d.resp, label:'Usuarios', tipo:'usuarios'},
    {ic:I.building, n:d.areas, label:'Áreas', tipo:'areas'},
    ...(d.por_componente||[]).map(c=>({ic:deviceIcon(c.componente), n:c.total, label:compLabel(c.componente), tipo:'componente', componente:c.componente}))
  ];

  const right = document.getElementById('rightPanel');
  right.innerHTML = `
    <div class="view-fade">
    <div class="dh">
      <div class="dh-top">
        <div class="dh-ic">${I.building}</div>
        <div>
          <div class="dhn">Panel de inventario</div>
          <div class="dhs">Resumen general de equipos y usuarios de CMP</div>
        </div>
      </div>
    </div>
    <div class="db">
      <div class="kpi-grid">
        ${cards.map(c=>`
          <div class="kpi-card" onclick="openKpiModal('${c.tipo}'${c.componente?`,'${esc(c.componente).replace(/'/g,"\\'")}'`:''})">
            <div class="kpi-ic">${c.ic}</div>
            <div class="kpi-info"><div class="kpi-n">${c.n}</div><div class="kpi-l">${esc(c.label)}</div></div>
          </div>`).join('')}
      </div>
    </div>
    </div>
  `;
}

async function openKpiModal(tipo, componente=null){
  const d = await api({action:'stats'});
  let title = '', itemsHtml = '';

  if(tipo === 'usuarios'){
    title = I.users + '<span>Usuarios registrados</span>';
    itemsHtml = (d.lista_resp||[]).map(r=>`<div class="simple-row"><b>${esc(r.responsable)}</b><small>${esc(r.area)}</small></div>`).join('');
  } else if(tipo === 'areas'){
    title = I.building + '<span>Áreas registradas</span>';
    itemsHtml = (d.lista_areas||[]).map(a=>`<div class="simple-row"><b>${esc(a.area)}</b><small>${esc(a.sede)}</small></div>`).join('');
  } else if(tipo === 'componente'){
    title = deviceIcon(componente) + '<span>' + esc(compLabel(componente)) + '</span>';
    const items = (d.lista_eq||[]).filter(e=>e.componente===componente);
    itemsHtml = items.map(e=>`<div class="simple-row"><b>${esc(e.marca)||esc(compLabel(componente))} ${esc(e.modelo||'')}</b><small>${esc(e.responsable)||'Sin asignar'} · S/N: ${esc(e.serial)||'N/A'}</small></div>`).join('');
  }

  document.getElementById('kpiModalTitle').innerHTML = title;
  document.getElementById('kpiModalContent').innerHTML = itemsHtml || '<div class="empty-state">Sin datos.</div>';
  document.getElementById('modalKpiResumen').style.display = 'flex';
}

/* ============ PANEL IZQUIERDO ============ */
let peopleListOpen = false;

async function loadResponsables(q=''){
  const d = await api({action:'responsables', q});
  document.getElementById('peopleList').innerHTML = d.responsables.map(r=>{
    const isAlex = r.responsable.toLowerCase()==='alexander c';
    const isActive = activeUser === r.responsable ? 'active' : '';
    return `
    <div class="pc ${isActive}" data-responsable="${esc(r.responsable)}" onclick="verUsuario('${esc(r.responsable).replace(/'/g,"\\'")}')">
      <div class="pc-ic">${I.laptop}</div>
      <div class="pc-body">
        <div class="pc-name">${esc(r.responsable)} ${isAlex ? `<span class="badge-king">${I.shield}</span>` : ''}</div>
        <div class="pc-meta">${esc(r.area)} · ${r.total_equipos} equipo${r.total_equipos==1?'':'s'}${r.total_items>r.total_equipos ? ` · ${r.total_items-r.total_equipos} periférico${(r.total_items-r.total_equipos)==1?'':'s'}` : ''}</div>
      </div>
    </div>`;
  }).join('') || '<div class="empty-state" style="padding:1rem">No se encontraron usuarios.</div>';

  const label = document.getElementById('peopleToggleLabel');
  if(label) label.textContent = (q ? `Resultados (${d.responsables.length})` : `Ver usuarios (${d.responsables.length})`);

  if(q && !peopleListOpen) togglePeopleList(true);
}

function togglePeopleList(forceOpen=null){
  peopleListOpen = forceOpen !== null ? forceOpen : !peopleListOpen;
  document.getElementById('peopleList').style.display = peopleListOpen ? 'block' : 'none';
  document.getElementById('btnTogglePeople').classList.toggle('open', peopleListOpen);
}

/* ============ VISTA DETALLE USUARIO ============ */
async function verUsuario(nombre){
  activeUser = nombre;
  const d = await api({action:'responsable', nombre});
  const rp = document.getElementById('rightPanel');
  document.querySelectorAll('.pc').forEach(el=>el.classList.toggle('active', el.dataset.responsable === nombre));
  togglePeopleList(true);

  const isAlex = nombre.toLowerCase() === 'alexander c';
  const badgeAlex = isAlex ? `<span class="tag king">${I.shield}<span>Responsable General TI</span></span>` : '';
  const totalPCs = d.items.filter(e => e.componente === 'Equipo de computo').length;
  const tagEquipos = totalPCs > 0
    ? `<span class="tag">${I.laptop}<span>${totalPCs} equipo${totalPCs==1?'':'s'} de cómputo</span></span>`
    : (d.items.length ? `<span class="tag">${I.mouse}<span>${d.items.length} periférico${d.items.length==1?'':'s'} sin equipo asociado</span></span>` : '');

  let html = `
    <div class="view-fade">
    <div class="dh">
      <div class="dh-top">
        <div class="dh-ic">${I.user}</div>
        <div>
          <div class="dhn">${esc(nombre)}</div>
          <div class="dhs">${d.items.length ? esc(d.items[0].area) + ' · ' + esc(d.items[0].sede) : ''}</div>
        </div>
      </div>
      <div style="margin-top:.9rem">${badgeAlex}${tagEquipos}</div>
      <div class="dh-actions">
        <button class="btn btn-ghost btn-sm" style="background:rgba(255,255,255,.1);color:#fff" onclick="abrirAsignarEquipo('${esc(nombre).replace(/'/g,"\\'")}')">${I.refresh}<span>Asignarle un equipo existente</span></button>
      </div>
    </div>
    <div class="db">
  `;

  if(!d.items.length) html += `<div class="empty-state">Este usuario no tiene equipos asignados.</div>`;

  d.items.forEach((eq, idx) => {
    let swRows = '';
    eq.software.forEach((s, i2) => {
      const isHidden = i2 >= 3 ? 'hidden-row' : '';
      swRows += `<tr class="sw-item-${eq.id} ${isHidden}"><td>${esc(s.programa)}</td><td>${esc(s.version)||'-'}</td>
        <td style="text-align:right"><button class="btn btn-danger btn-sm" onclick="borrarSoftware(${s.id},'${esc(nombre).replace(/'/g,"\\'")}')">${I.close}</button></td></tr>`;
    });
    const swBtn = eq.software.length > 3 ? `<button class="btn btn-ghost btn-sm" onclick="toggleSW(${eq.id}, this)">Ver todos (${eq.software.length}) ${I.chevron}</button>` : '';

    const specs = [
      ['Procesador', eq.procesador, I.chip],
      ['RAM', [eq.ram_total, eq.tipo_ram].filter(Boolean).join(' · '), I.ram],
      ['RAM en uso', eq.ram_usada, I.ram],
      ['Disco', [eq.disco_total, eq.tipo_disco].filter(Boolean).join(' · '), I.disk],
      ['Disco en uso', eq.disco_usado, I.disk],
      ['Sistema operativo', eq.sistema_op, I.system],
      ['Office', eq.office, I.office],
      ['IP local', eq.ip_local, I.wifi],
      ['Conexión', eq.conexion, I.wifi],
      ['Usuario de sesión', eq.usuario_actual, I.user],
    ].filter(s => s[1]);

    html += `
    <div class="eq-wrap">
    <details class="eq-card" ${d.items.length===1?'open':''}>
      <summary class="eq-sum">
        <div class="eq-sum-left">
          <div class="eq-ic">${deviceIcon(eq.componente, eq.tipo_equipo)}</div>
          <div>
            <div class="eq-title">${esc(eq.marca)||'Equipo'} ${esc(eq.modelo)||''} ${totalPCs>1 && eq.componente==='Equipo de computo' ? `<span class="eq-count">Equipo ${idx+1} de ${totalPCs}</span>` : ''}</div>
            <div class="eq-sub">S/N: ${esc(eq.serial)||'N/A'} ${eq.tipo_equipo ? ' · '+esc(eq.tipo_equipo) : ''}</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:.5rem">
          <button class="btn btn-ghost btn-sm" onclick="event.preventDefault();openModalEquipo(${eq.id}, '${esc(nombre).replace(/'/g,"\\'")}', null)">${I.gear}</button>
          <button class="btn btn-danger btn-sm" onclick="event.preventDefault();borrarEquipo(${eq.id}, '${esc(nombre).replace(/'/g,"\\'")}')">${I.trash}</button>
          <span class="eq-chevron">${I.chevron}</span>
        </div>
      </summary>
      <div class="det-content">
        ${specs.length ? `
        <div class="sec-label"><span class="sec-label-txt">${I.chip}<span>Ficha técnica</span></span></div>
        <div class="spec-grid">
          ${specs.map(s=>`<div class="spec-c"><div class="spec-k">${s[2]}<span>${s[0]}</span></div><div class="spec-v">${esc(s[1])}</div></div>`).join('')}
        </div>` : ''}

        ${eq.observaciones ? `
        <div class="sec-label"><span class="sec-label-txt">${I.note}<span>Observaciones</span></span></div>
        <div class="note-box">${esc(eq.observaciones)}</div>` : ''}

        <div class="sec-label">
          <span class="sec-label-txt">${I.mouse}<span>Periféricos asignados</span></span>
          <button class="btn btn-ghost btn-sm" onclick="openModalEquipo(null, '${esc(nombre).replace(/'/g,"\\'")}', ${eq.id})">${I.plus}<span>Agregar</span></button>
        </div>
        <div class="periph-list">
          ${eq.perifericos.map(p=> `<div class="periph-item">
            <div class="periph-ic">${deviceIcon(p.componente)}</div>
            <div class="periph-name">${esc(p.componente)} ${esc(p.marca)||''}</div>
            <span class="periph-sn">S/N: ${esc(p.serial)||'N/A'}</span>
            <div style="display:flex; gap:.3rem;">
              <button class="btn btn-ghost btn-sm" onclick="openModalEquipo(${p.id}, '${esc(nombre).replace(/'/g,"\\'")}', null)">${I.gear}</button>
              <button class="btn btn-danger btn-sm" onclick="borrarEquipo(${p.id}, '${esc(nombre).replace(/'/g,"\\'")}')">${I.close}</button>
            </div>
          </div>`).join('') || '<div class="empty-state">Sin periféricos asociados.</div>'}
        </div>

        <div class="sec-label">
          <span class="sec-label-txt">${I.disk}<span>Software instalado (${eq.software.length})</span></span>
          <button class="btn btn-ghost btn-sm" onclick="openModalSW(${eq.id})">${I.plus}<span>Agregar</span></button>
        </div>
        <table class="swt">
          <tbody>${swRows || '<tr><td colspan="3" class="empty-state">Sin software registrado.</td></tr>'}</tbody>
        </table>
        ${swBtn}
      </div>
    </details>
    </div>`;
  });

  html += `</div></div>`;
  rp.innerHTML = html;
}

function toggleSW(id, btn){
  const rows = document.querySelectorAll(`.sw-item-${id}`);
  let isExpanded = btn.textContent.includes('Ocultar');
  rows.forEach((r, idx)=>{ if(idx>=3) r.classList.toggle('hidden-row'); });
  btn.innerHTML = isExpanded ? `Ver todos (${rows.length}) ${I.chevron}` : `Ocultar ${I.chevron}`;
}

/* ============ MODAL: editar equipo o agregar periferico ============ */
const camposMap = {
  meId:'id', meResp:'responsable', meArea:'area', meSede:'sede', meComp:'componente',
  meTipo:'tipo_equipo', meMarca:'marca', meModelo:'modelo', meSerial:'serial',
  meProc:'procesador', meRam:'ram_total', meTipoRam:'tipo_ram', meRamUsada:'ram_usada',
  meDisco:'disco_total', meTipoDisco:'tipo_disco', meDiscoUsado:'disco_usado',
  meSO:'sistema_op', meOffice:'office', meUsuarioActual:'usuario_actual', meNombreCompleto:'nombre_completo',
  meIP:'ip_local', meConexion:'conexion', meObs:'observaciones'
};

function toggleFormFields(){
  const isPC = document.getElementById('meComp').value === 'Equipo de computo';
  ['fs-hardware','fs-sistema','fs-red'].forEach(id=>{
    document.getElementById(id).style.display = isPC ? 'block' : 'none';
  });
  document.getElementById('fg-tipo_equipo').style.display = isPC ? 'flex' : 'none';
}

async function openModalEquipo(id=null, resp='', padreId=null){
  if (!id && !padreId) return; // Validación estricta: Solo editar o añadir periférico

  Object.keys(camposMap).forEach(k=>{ const el=document.getElementById(k); if(el) el.value=''; });
  document.getElementById('meId').value = id||'';
  document.getElementById('mePadreId').value = padreId||'';
  document.getElementById('meResp').value = resp;
  document.getElementById('meTitle').innerHTML = (id ? I.gear : I.plus) + '<span>' + (id ? 'Editar elemento' : 'Nuevo periférico') + '</span>';
  document.getElementById('meSub').textContent = padreId ? 'Se asignará como periférico del equipo seleccionado.' : 'Modifica la información del elemento.';
  
  const compSelect = document.getElementById('meComp');
  Array.from(compSelect.options).forEach(opt => {
      opt.style.display = (padreId && opt.value === 'Equipo de computo') ? 'none' : 'block';
  });

  if(padreId){ 
    compSelect.value='Mouse'; 
  }

  if(id){
    const res = await api({action: 'get_equipo', id: id});
    if(res.equipo){
      Object.keys(camposMap).forEach(k => {
        const el = document.getElementById(k);
        if(el && res.equipo[camposMap[k]] !== null) {
          el.value = res.equipo[camposMap[k]];
        }
      });
    }
  }

  toggleFormFields();
  document.getElementById('modalEquipo').style.display = 'flex';
}

async function guardarEquipo(){
  const body = {};
  Object.keys(camposMap).forEach(k=>{
    const el = document.getElementById(k);
    if(el) body[camposMap[k]] = el.value;
  });
  body.equipo_padre_id = document.getElementById('mePadreId').value;
  await api({action: body.id ? 'update_equipo' : 'create_equipo'}, body);
  closeModal('modalEquipo'); toast('Guardado correctamente');
  loadResponsables();
  if(activeUser) verUsuario(activeUser); else loadDashboard();
}

async function borrarEquipo(id, resp){
  if(confirm('¿Enviar este elemento a la papelera?')){
    await api({action:'delete_equipo'}, {id});
    toast('Enviado a la papelera', I.trash);
    verUsuario(resp); loadResponsables();
  }
}

/* ============ MODAL: asignar equipo existente a un usuario ============ */
let asgDebounce = null;

function abrirAsignarEquipo(nombre){
  document.getElementById('asgResponsable').value = nombre;
  document.getElementById('asgSub').textContent = `Busca un equipo de cómputo y asígnaselo a ${nombre}. Se le quitará automáticamente a quien lo tenía; sus periféricos (mouse/teclado) no se ven afectados.`;
  document.getElementById('asgQ').value = '';
  document.getElementById('asgResults').innerHTML = '<div class="empty-state">Escribe para buscar o pulsa "Ver todos".</div>';
  document.getElementById('modalAsignar').style.display = 'flex';
}

function buscarEquiposAsignar(){
  clearTimeout(asgDebounce);
  const q = document.getElementById('asgQ').value.trim();
  asgDebounce = setTimeout(async ()=>{
    const d = await api({action:'buscar_equipos', q});
    renderAsignarResults(d.equipos || []);
  }, 250);
}

async function verTodosEquiposAsignar(){
  document.getElementById('asgQ').value = '';
  const d = await api({action:'buscar_equipos', q:''});
  renderAsignarResults(d.equipos || []);
}

function renderAsignarResults(equipos){
  const cont = document.getElementById('asgResults');
  if(!equipos.length){ cont.innerHTML = '<div class="empty-state">No se encontraron equipos.</div>'; return; }
  cont.innerHTML = equipos.map(e => `
    <div class="periph-item">
      <div class="periph-ic">${I.laptop}</div>
      <div style="flex:1;min-width:0">
        <div class="periph-name">${esc(e.marca)||'Equipo'} ${esc(e.modelo)||''}</div>
        <div class="eq-sub">S/N: ${esc(e.serial)||'N/A'} · Actual: ${esc(e.responsable)||'Sin asignar'} · ${esc(e.area)||''}</div>
      </div>
      <button class="btn btn-primary btn-sm" onclick="asignarEquipoAUsuario(${e.id})"><span>Asignar</span></button>
    </div>`).join('');
}

async function asignarEquipoAUsuario(id){
  const nombre = document.getElementById('asgResponsable').value;
  if(!confirm(`¿Asignar este equipo a ${nombre}? Se le quitará a quien lo tenía actualmente.`)) return;
  await api({action:'asignar_equipo'}, {id, responsable: nombre});
  toast('Equipo asignado correctamente');
  closeModal('modalAsignar');
  loadResponsables();
  verUsuario(nombre);
}

function openModalSW(eqId){
  document.getElementById('swEqId').value = eqId;
  document.getElementById('swProg').value = '';
  document.getElementById('swVer').value = '';
  document.getElementById('modalSW').style.display='flex';
}

async function guardarSoftware(){
  await api({action:'add_software'}, {
    equipo_id: document.getElementById('swEqId').value,
    programa: document.getElementById('swProg').value,
    version: document.getElementById('swVer').value
  });
  closeModal('modalSW'); toast('Software agregado');
  if(activeUser) verUsuario(activeUser);
}

async function borrarSoftware(id, resp){
  if(confirm('¿Eliminar este software?')){
    await api({action:'delete_software'}, {id});
    toast('Software eliminado', I.trash);
    verUsuario(resp);
  }
}

/* ============ PAPELERA ============ */
const COMPONENTE_LABEL = {'Equipo de computo':'Equipos de cómputo','Mouse':'Mouse','Teclado':'Teclados','Monitor':'Monitores','Otro':'Otros'};
const COMPONENTE_ORDEN = ['Equipo de computo','Mouse','Teclado','Monitor','Otro'];

function papSeccionHTML(icono, titulo, itemsHtml, count){
  return `<details class="pap-sec">
    <summary class="pap-sec-sum">${icono}<span>${titulo}</span><span class="eq-count">${count}</span><span class="eq-chevron">${I.chevron}</span></summary>
    <div class="pap-sec-body">${itemsHtml}</div>
  </details>`;
}

function papItemEquipoHTML(e){
  const incluye = [];
  if(e.perifericos_incluidos > 0) incluye.push(`${e.perifericos_incluidos} periférico${e.perifericos_incluidos==1?'':'s'}`);
  if(e.software_incluido > 0) incluye.push(`${e.software_incluido} software`);

  const swLista = e.software_lista || [];
  const swListHTML = swLista.length ? `
    <details class="pap-sw-detail">
      <summary>${I.chevron}<span>Ver software instalado (${swLista.length})</span></summary>
      <div class="pap-sw-list">
        ${swLista.map(s=>`<div class="pap-sw-row">${esc(s.programa)}${s.version ? ' · '+esc(s.version) : ''}</div>`).join('')}
      </div>
    </details>` : '';

  return `<div class="pap-item" style="align-items:flex-start">
    <div class="pap-item-ic">${deviceIcon(e.componente, e.tipo_equipo)}</div>
    <div class="pap-item-body">
      <div class="pap-item-title">${esc(e.marca)||''} ${esc(e.modelo)||''} ${!e.marca && !e.modelo ? esc(e.componente) : ''}</div>
      <div class="pap-item-meta">S/N: ${esc(e.serial)||'N/A'} · De: ${esc(e.responsable)||'Sin responsable'} · Eliminado: ${esc(e.deleted_at)}</div>
      ${incluye.length ? `<div class="pap-item-meta">Incluye: ${incluye.join(' · ')} (se restauran o eliminan junto con este equipo)</div>` : ''}
      ${swListHTML}
    </div>
    <div class="pap-item-actions">
      <button class="btn btn-primary btn-sm" onclick="restaurar('equipo', ${e.id})" title="Restaurar">${I.refresh}</button>
      <button class="btn btn-danger btn-sm" onclick="borrarPermanente('equipo', ${e.id})" title="Eliminar definitivamente">${I.trash}</button>
    </div>
  </div>`;
}

function papItemSoftwareHTML(s){
  return `<div class="pap-item">
    <div class="pap-item-ic">${I.disk}</div>
    <div class="pap-item-body">
      <div class="pap-item-title">${esc(s.programa)} ${s.version ? '· '+esc(s.version) : ''}</div>
      <div class="pap-item-meta">De: ${esc(s.responsable)||'Sin responsable'} · Eliminado: ${esc(s.deleted_at)}</div>
    </div>
    <div class="pap-item-actions">
      <button class="btn btn-primary btn-sm" onclick="restaurar('software', ${s.id})" title="Restaurar">${I.refresh}</button>
      <button class="btn btn-danger btn-sm" onclick="borrarPermanente('software', ${s.id})" title="Eliminar definitivamente">${I.trash}</button>
    </div>
  </div>`;
}

function papItemTareaHTML(t){
  return `<div class="pap-item">
    <div class="pap-item-ic">${I.kanban}</div>
    <div class="pap-item-body">
      <div class="pap-item-title">${esc(t.titulo)}</div>
      <div class="pap-item-meta">Eliminada: ${esc(t.deleted_at)}</div>
    </div>
    <div class="pap-item-actions">
      <button class="btn btn-primary btn-sm" onclick="restaurar('tarea', ${t.id})" title="Restaurar">${I.refresh}</button>
      <button class="btn btn-danger btn-sm" onclick="borrarPermanente('tarea', ${t.id})" title="Eliminar definitivamente">${I.trash}</button>
    </div>
  </div>`;
}

async function abrirPapelera(){
  const d = await api({action:'papelera'});
  let html = '';

  const gruposEquipo = {};
  (d.equipos || []).forEach(e => {
    const key = COMPONENTE_ORDEN.includes(e.componente) ? e.componente : 'Otro';
    (gruposEquipo[key] ||= []).push(e);
  });
  COMPONENTE_ORDEN.forEach(comp => {
    const items = gruposEquipo[comp];
    if(items && items.length){
      html += papSeccionHTML(deviceIcon(comp), COMPONENTE_LABEL[comp], items.map(papItemEquipoHTML).join(''), items.length);
    }
  });

  if((d.software||[]).length){
    html += papSeccionHTML(I.disk, 'Software', d.software.map(papItemSoftwareHTML).join(''), d.software.length);
  }
  if((d.tareas||[]).length){
    html += papSeccionHTML(I.kanban, 'Tareas', d.tareas.map(papItemTareaHTML).join(''), d.tareas.length);
  }

  const totalPapelera = (d.equipos||[]).length + (d.software||[]).length + (d.tareas||[]).length;
  if(!totalPapelera) html = `<div class="empty-state">La papelera está vacía.</div>`;

  document.getElementById('papeleraContent').innerHTML = html;
  document.getElementById('btnVaciarPapelera').style.display = totalPapelera ? 'inline-flex' : 'none';
  document.getElementById('modalPapelera').style.display='flex';
}

async function vaciarPapelera(){
  if(confirm('Esto eliminará permanentemente todos los elementos de la papelera. Esta acción no se puede deshacer. ¿Continuar?')){
    await api({action:'vaciar_papelera'}, {});
    toast('Papelera vaciada', I.trash);
    abrirPapelera();
  }
}

async function borrarPermanente(tipo, id){
  if(!confirm('Esto eliminará el elemento definitivamente. Esta acción no se puede deshacer. ¿Continuar?')) return;
  const action = tipo === 'equipo' ? 'delete_equipo_permanente' : (tipo === 'software' ? 'delete_software_permanente' : 'delete_tarea_permanente');
  const res = await api({action}, {id});
  if(res && res.error){ toast(res.error, I.close); return; }
  toast('Elemento eliminado definitivamente', I.trash);
  abrirPapelera();
}

async function restaurar(tipo, id){
  const actionMap = { equipo: 'restore_equipo', software: 'restore_software', tarea: 'restore_tarea' };
  const res = await api({action: actionMap[tipo]}, {id});
  if(res && res.error){ toast(res.error, I.close); return; }
  toast('Elemento restaurado', I.refresh);
  abrirPapelera();
  loadResponsables();
  if(activeUser) verUsuario(activeUser); else loadDashboard();
  if(tipo === 'tarea') refreshKanban();
}

/* ============ TABLERO KANBAN (TAREAS) ============ */
async function loadKanban(){
  activeUser = null;
  document.querySelectorAll('.pc.active').forEach(el=>el.classList.remove('active'));
  const rp = document.getElementById('rightPanel');
  rp.innerHTML = `
    <div class="view-fade">
    <div class="dh">
      <div class="dh-top">
        <div class="dh-ic">${I.kanban}</div>
        <div>
          <div class="dhn">Tablero de tareas</div>
          <div class="dhs">Pendientes, en proceso y completadas</div>
        </div>
      </div>
    </div>
    <div class="db">
      <div class="kanban-wrap" id="kanbanWrap"></div>
    </div>
    </div>
  `;
  await refreshKanban();
}

async function refreshKanban(){
  const d = await api({action:'tareas'});
  renderKanbanCols(d.tareas || []);
}

function renderKanbanCols(tareas){
  const wrap = document.getElementById('kanbanWrap');
  if(!wrap) return;
  wrap.innerHTML = KANBAN_COLS.map(col => {
    const items = tareas.filter(t=>t.estado===col.estado);
    return `
    <div class="kanban-col" style="--kc:${col.color}" data-estado="${col.estado}"
         ondragover="event.preventDefault(); this.classList.add('drag-over')"
         ondragleave="this.classList.remove('drag-over')"
         ondrop="onDropTarea(event,'${col.estado}')">
      <div class="kanban-col-h">
        <span class="kanban-dot" style="background:${col.color}"></span>
        <span>${col.label}</span>
        <span class="kanban-count">${items.length}</span>
      </div>
      <div class="kanban-cards">
        ${items.map(t=>kanbanCardHTML(t)).join('') || '<div class="empty-state" style="padding:.4rem 0">Sin tareas.</div>'}
      </div>
      ${col.estado==='pendiente' ? `<button class="kanban-add" onclick="openModalTarea()">${I.plus}<span>Nueva tarea</span></button>` : ''}
    </div>`;
  }).join('');
}

function kanbanCardHTML(t){
  const idx = KANBAN_COLS.findIndex(c=>c.estado===t.estado);
  const prevEstado = idx>0 ? KANBAN_COLS[idx-1].estado : null;
  const nextEstado = idx<KANBAN_COLS.length-1 ? KANBAN_COLS[idx+1].estado : null;
  return `
  <div class="kanban-card" draggable="true"
       ondragstart="onDragTarea(event, ${t.id})" ondragend="onDragEndTarea(event)"
       style="--card-color:${CARD_COLORS[t.color]||CARD_COLORS.verde}">
    <div class="kc-title">${esc(t.titulo)}</div>
    ${t.descripcion ? `<div class="kc-desc">${esc(t.descripcion)}</div>` : ''}
    ${t.completado_at ? `<div class="kc-date">${I.check}<span>Completada: ${esc(t.completado_at)}</span></div>` : ''}
    <div class="kc-actions">
      <div class="kc-move">
        ${prevEstado ? `<button class="kc-btn" onclick="moverTarea(${t.id},'${prevEstado}')" title="Mover atrás">${I.chevLeft}</button>` : `<span class="kc-btn spacer"></span>`}
        ${nextEstado ? `<button class="kc-btn" onclick="moverTarea(${t.id},'${nextEstado}')" title="Mover adelante">${I.chevRight}</button>` : `<span class="kc-btn spacer"></span>`}
      </div>
      <div style="display:flex;gap:.3rem">
        <button class="kc-btn" onclick="openModalTarea(${t.id})" title="Editar">${I.gear}</button>
        <button class="kc-btn danger" onclick="borrarTarea(${t.id})" title="Eliminar">${I.close}</button>
      </div>
    </div>
  </div>`;
}

function onDragTarea(e, id){ dragTareaId = id; e.dataTransfer.effectAllowed = 'move'; e.target.classList.add('dragging'); }
function onDragEndTarea(e){ e.target.classList.remove('dragging'); document.querySelectorAll('.kanban-col.drag-over').forEach(c=>c.classList.remove('drag-over')); }
async function onDropTarea(e, estado){
  e.preventDefault();
  e.currentTarget.classList.remove('drag-over');
  if(dragTareaId){ await moverTarea(dragTareaId, estado); dragTareaId = null; }
}
async function moverTarea(id, estado){
  await api({action:'actualizar_tarea'}, {id, estado});
  refreshKanban();
}

function renderColorPicker(){
  const cp = document.getElementById('colorPicker');
  cp.innerHTML = Object.entries(CARD_COLORS).map(([k,v]) =>
    `<button type="button" class="color-swatch ${selectedColor===k?'active':''}" style="background:${v}" onclick="selectColor('${k}')"></button>`
  ).join('');
}
function selectColor(c){ selectedColor = c; renderColorPicker(); }

async function openModalTarea(id=null){
  document.getElementById('tareaId').value = id || '';
  document.getElementById('tareaTitulo').value = '';
  document.getElementById('tareaDesc').value = '';
  selectedColor = 'verde';
  document.getElementById('tareaModalTitle').innerHTML = (id ? I.gear : I.plus) + '<span>' + (id ? 'Editar tarea' : 'Nueva tarea') + '</span>';

  if(id){
    const d = await api({action:'tareas'});
    const t = (d.tareas || []).find(x => x.id === id);
    if(t){
      document.getElementById('tareaTitulo').value = t.titulo;
      document.getElementById('tareaDesc').value = t.descripcion || '';
      selectedColor = t.color || 'verde';
    }
  }
  renderColorPicker();
  document.getElementById('modalTarea').style.display = 'flex';
}

async function guardarTarea(){
  const id = document.getElementById('tareaId').value;
  const titulo = document.getElementById('tareaTitulo').value.trim();
  if(!titulo){ toast('Escribe un título para la tarea', I.close); return; }
  const body = { titulo, descripcion: document.getElementById('tareaDesc').value.trim(), color: selectedColor };
  if(id){ body.id = id; await api({action:'actualizar_tarea'}, body); }
  else   { await api({action:'crear_tarea'}, body); }
  closeModal('modalTarea');
  toast('Tarea guardada');
  refreshKanban();
}

async function borrarTarea(id){
  if(confirm('¿Enviar esta tarea a la papelera?')){
    await api({action:'eliminar_tarea'}, {id});
    toast('Tarea enviada a la papelera', I.trash);
    refreshKanban();
  }
}

/* ============ INIT ============ */
document.getElementById('btnTareas').innerHTML = I.kanban + '<span>Tareas</span>';
document.getElementById('btnStats').innerHTML = I.chart + '<span>Estadísticas</span>';
document.getElementById('searchIcon').innerHTML = I.search;
document.getElementById('tpIcon').innerHTML = I.users;
document.getElementById('tpChevron').innerHTML = I.chevron;
document.getElementById('asgSearchIcon').innerHTML = I.search;
document.getElementById('btnPapelera').innerHTML = I.trash + '<span>Papelera</span>';
document.getElementById('btnGuardarEquipo').innerHTML = I.check + '<span>Guardar</span>';
document.getElementById('swModalTitle').innerHTML = I.disk + '<span>Agregar software</span>';
document.getElementById('papeleraModalTitle').innerHTML = I.trash + '<span>Papelera de reciclaje</span>';
toggleFormFields();
loadKanban();
loadResponsables();
</script>
</body>
</html>