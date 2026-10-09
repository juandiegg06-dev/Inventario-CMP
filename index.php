<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>InvCMP · Inventario de Equipos</title>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,700&family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  /* Marca CMP: verdes, lima y amarillo (intactos) */
  --brand-yellow:#CED800;--brand-lime:#94D500;--brand-green:#3FAF29;--brand-deep:#00953A;
  --ink:#0B2410;--ink-2:#143A1C;--paper:#F2F5EC;--surface:#fff;
  --line:#DCE4D1;--line-2:#EBF0E3;--mute:#677A62;--text:#15221A;
  --brand:#00953A;--brand-soft:#E7F3DB;--lime:#94D500;
  /* alias de nombres anteriores */
  --gd:var(--ink);--gk:var(--brand);--gm:var(--brand-green);--gl:var(--lime);--gy:var(--brand-yellow);
  --wh:#fff;--of:var(--paper);--mu:var(--brand-soft);--bd:var(--line);--tm:var(--mute);--tx:var(--text);--ts:#0F7A34;
  --rad:10px;--rad-lg:14px;--rad-xl:18px;
  --shadow:0 1px 2px rgba(11,36,16,.06);
  --shadow-lift:0 18px 44px rgba(11,36,16,.22);
  --ring:0 0 0 3px rgba(148,213,0,.35);
  --ease:cubic-bezier(.22,1,.36,1);
  --f-head:'Bricolage Grotesque','Hanken Grotesk',system-ui,sans-serif;
  --f-body:'Hanken Grotesk',system-ui,-apple-system,'Segoe UI',sans-serif;
}
@keyframes rise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@keyframes modalPop{from{opacity:0;transform:translateY(12px) scale(.985)}to{opacity:1;transform:none}}
@keyframes modalBgFade{from{opacity:0}to{opacity:1}}
.view-fade{animation:fadeIn .22s ease;}
*:focus-visible{outline:2px solid var(--brand);outline-offset:2px;}
body{font-family:var(--f-body);background:var(--paper);color:var(--text);min-height:100vh;font-size:14.5px;line-height:1.55;-webkit-font-smoothing:antialiased;font-variant-numeric:tabular-nums;}
.ic{display:inline-block;vertical-align:middle;flex-shrink:0}
button,input,select,textarea{font-family:inherit;}

/* ================= Estructura: riel lateral + contenido ================= */
.app{display:grid;grid-template-columns:276px minmax(0,1fr);height:100vh;overflow:hidden;}
.sidebar{background:var(--ink);color:#fff;display:flex;flex-direction:column;min-height:0;overflow:hidden;position:relative;}
.sidebar::after{content:"";position:absolute;right:0;top:0;bottom:0;width:1px;background:rgba(255,255,255,.06);}
.logo{display:flex;align-items:center;gap:.7rem;background:none;border:none;cursor:pointer;padding:1.5rem 1.4rem 1.2rem;text-align:left;}
.logo-text{font-family:var(--f-head);font-size:1.3rem;font-weight:700;color:#fff;letter-spacing:-.03em;}
.logo-text span{color:var(--lime);}

.nav-seg{display:flex;flex-direction:column;gap:2px;padding:0 .8rem;}
.nav-seg .btn{border-color:transparent;position:relative;width:100%;justify-content:flex-start;gap:.75rem;padding:.65rem .85rem;border-radius:var(--rad);background:none;color:rgba(255,255,255,.66);font-size:.9rem;font-weight:500;transition:background .18s var(--ease),color .18s;}
.nav-seg .btn:hover{background:rgba(255,255,255,.07);color:#fff;transform:none;}
.nav-seg .btn .ic{color:rgba(255,255,255,.5);transition:color .18s;}
.nav-seg .btn.on{background:rgba(255,255,255,.1);color:#fff;font-weight:600;}
.nav-seg .btn.on .ic{color:var(--lime);}
.nav-seg .btn.on::before{content:"";position:absolute;left:-.8rem;top:.55rem;bottom:.55rem;width:3px;border-radius:0 3px 3px 0;background:var(--lime);}

.side-users{display:flex;flex-direction:column;min-height:0;flex:1;margin-top:1.4rem;padding-top:1.2rem;border-top:1px solid rgba(255,255,255,.08);}
.search-area{padding:0 1.1rem .7rem;}
.sw-wrap{position:relative;}
.sic{position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:var(--brand);pointer-events:none;}
.si{width:100%;padding:.62rem .85rem .62rem 2.4rem;border:1px solid var(--line);border-radius:var(--rad);font-size:.86rem;background:#fff;outline:none;color:var(--text);transition:border-color .15s,box-shadow .15s;}
.si:focus{border-color:var(--brand);box-shadow:var(--ring);}
.sidebar .si{background:rgba(255,255,255,.07);border-color:rgba(255,255,255,.1);color:#fff;}
.sidebar .si::placeholder{color:rgba(255,255,255,.45);}
.sidebar .si:focus{background:rgba(255,255,255,.12);border-color:var(--lime);}
.sidebar .sic{color:rgba(255,255,255,.55);}
.btn-toggle-people{margin:0 1.1rem .4rem;padding:.4rem .1rem;background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;font-size:.82rem;font-weight:600;color:rgba(255,255,255,.75);width:calc(100% - 2.2rem);text-align:left;}
.btn-toggle-people:hover{color:#fff;}
.btn-toggle-people .tp-left{display:flex;align-items:center;gap:.55rem;}
.btn-toggle-people .tp-chevron{color:rgba(255,255,255,.45);transition:transform .2s var(--ease);}
.btn-toggle-people.open .tp-chevron{transform:rotate(180deg);}
.pl{flex:1;overflow-y:auto;padding:.1rem .7rem .7rem;}
.pl::-webkit-scrollbar,.right-panel::-webkit-scrollbar{width:6px;}
.pl::-webkit-scrollbar-thumb{background:rgba(255,255,255,.18);border-radius:3px;}
.right-panel::-webkit-scrollbar-thumb{background:#C9D6BA;border-radius:3px;}
.pc{border-radius:var(--rad);padding:.55rem .65rem;margin-bottom:2px;cursor:pointer;display:flex;align-items:center;gap:.7rem;transition:background .15s;}
.pc:hover{background:rgba(255,255,255,.07);}
.pc.active{background:rgba(148,213,0,.15);}
.pc-ic{width:32px;height:32px;border-radius:50%;background:rgba(148,213,0,.14);color:var(--lime);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.pc-ic .ic{width:16px;height:16px;}
.pc-body{min-width:0;flex:1;}
.pc-name{font-weight:600;font-size:.86rem;color:#fff;display:flex;align-items:center;gap:.35rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.pc-meta{font-size:.74rem;color:rgba(255,255,255,.5);margin-top:.1rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.badge-king{color:var(--brand-yellow);flex-shrink:0;}
.btn-papelera{margin:.5rem .8rem 1rem;padding:.65rem .85rem;background:none;color:rgba(255,255,255,.6);border:none;border-radius:var(--rad);font-weight:500;cursor:pointer;display:flex;align-items:center;gap:.75rem;font-size:.9rem;transition:background .18s,color .18s;text-align:left;}
.btn-papelera:hover{background:rgba(255,255,255,.07);color:#fff;}

.right-panel{overflow-y:auto;background:var(--paper);min-width:0;}

/* ================= Botones ================= */
.btn{display:inline-flex;align-items:center;gap:.45rem;padding:.55rem 1rem;border-radius:var(--rad);border:1px solid transparent;cursor:pointer;font-size:.84rem;font-weight:600;line-height:1.2;transition:background .15s,border-color .15s,color .15s,transform .15s var(--ease),box-shadow .15s;}
.btn:hover{transform:translateY(-1px);}
.btn:active{transform:translateY(0) scale(.98);}
.btn:disabled{opacity:.6;cursor:progress;transform:none;}
.btn-primary{background:var(--brand);color:#fff;}
.btn-primary:hover{background:var(--ink);box-shadow:0 6px 16px rgba(11,36,16,.18);}
.btn-ghost{background:#fff;color:var(--ink);border-color:var(--line);}
.btn-ghost:hover{background:var(--brand-soft);border-color:#BFD8A8;}
.btn-danger{background:#fff;color:#B42318;border-color:#F1D3CF;}
.btn-danger:hover{background:#FDEEEC;border-color:#E8B7B0;}
.btn-sm{padding:.36rem .65rem;font-size:.78rem;}

/* ================= Encabezado de vista ================= */
.dh{padding:2.1rem 2.75rem 1.5rem;border-bottom:1px solid var(--line);background:var(--paper);}
.dh-top{display:flex;align-items:center;gap:1.1rem;}
.dh-ic{width:46px;height:46px;border-radius:13px;background:var(--ink);color:var(--lime);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.dhn{font-family:var(--f-head);font-size:2rem;font-weight:700;letter-spacing:-.03em;line-height:1.1;color:var(--ink);}
.dhs{font-size:.92rem;color:var(--mute);margin-top:.25rem;}
.tag{font-size:.78rem;font-weight:600;padding:.28rem .7rem;border-radius:20px;background:#fff;border:1px solid var(--line);margin-right:.4rem;display:inline-flex;align-items:center;gap:.35rem;color:var(--ink);}
.tag .ic{color:var(--brand);}
.tag.king{background:#FBF6D9;border-color:#EADF8E;color:#6C5A00;}
.tag.king .ic{color:#8A7400;}
.dh-actions{margin-top:1.1rem;}
.db{padding:1.75rem 2.75rem 3.2rem;max-width:1180px;}

/* ================= Equipos ================= */
.eq-wrap{margin-bottom:.9rem;}
details.eq-card{background:var(--surface);border:1px solid var(--line);border-radius:var(--rad-lg);overflow:hidden;transition:border-color .2s,box-shadow .2s var(--ease);}
details.eq-card:hover{border-color:#C3D5B1;}
details.eq-card[open]{border-color:#B4CE9A;box-shadow:0 10px 30px rgba(11,36,16,.07);}
summary.eq-sum{padding:1.1rem 1.35rem;cursor:pointer;list-style:none;display:flex;justify-content:space-between;align-items:center;gap:.75rem;}
summary.eq-sum::-webkit-details-marker{display:none;}
.eq-sum-left{display:flex;align-items:center;gap:1rem;min-width:0;}
.eq-ic{width:44px;height:44px;border-radius:12px;background:var(--ink);color:var(--lime);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.eq-title{font-family:var(--f-head);font-weight:600;font-size:1.08rem;color:var(--ink);letter-spacing:-.015em;}
.eq-sub{font-size:.78rem;color:var(--mute);font-family:'JetBrains Mono',monospace;margin-top:.2rem;}
.eq-chevron{color:var(--mute);transition:transform .22s var(--ease);flex-shrink:0;}
details[open] .eq-chevron{transform:rotate(180deg);}
.eq-count{font-size:.74rem;font-weight:600;color:var(--brand);background:var(--brand-soft);padding:.15rem .6rem;border-radius:20px;}
.det-content{padding:.2rem 1.35rem 1.35rem;border-top:1px solid var(--line-2);}
.sec-label{font-size:.88rem;font-weight:700;color:var(--ink);margin:1.5rem 0 .7rem;display:flex;align-items:center;justify-content:space-between;gap:.6rem;font-family:var(--f-head);letter-spacing:-.01em;}
.sec-label-txt{display:flex;align-items:center;gap:.5rem;}
.sec-label-txt .ic{color:var(--brand);}
.spec-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:0 1.5rem;}
.spec-c{padding:.7rem 0;border-top:1px solid var(--line-2);}
.spec-k{font-size:.76rem;color:var(--mute);margin-bottom:.2rem;display:flex;align-items:center;gap:.35rem;}
.spec-v{font-size:.92rem;font-weight:600;color:var(--text);word-break:break-word;}
.spec-v.muted{color:var(--mute);font-weight:500;}
.note-box{background:#FBFCF7;border-left:3px solid var(--lime);padding:.85rem 1.1rem;font-size:.88rem;color:var(--text);line-height:1.6;border-radius:0 var(--rad) var(--rad) 0;}
.periph-list{display:flex;flex-direction:column;}
.periph-item{display:flex;align-items:center;gap:.8rem;border-top:1px solid var(--line-2);padding:.65rem 0;}
.periph-ic{width:34px;height:34px;border-radius:10px;background:var(--brand-soft);color:var(--brand);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.periph-name{font-size:.9rem;font-weight:600;flex:1;min-width:0;}
.periph-sn{font-size:.76rem;color:var(--mute);font-family:'JetBrains Mono',monospace;}
.empty-state{font-size:.88rem;color:var(--mute);padding:.7rem .1rem;}
.swt{width:100%;border-collapse:collapse;font-size:.88rem;margin-bottom:.5rem;}
.swt td{padding:.6rem .3rem;border-top:1px solid var(--line-2);}
.hidden-row{display:none;}

/* ================= Papelera ================= */
.pap-sec{border:1px solid var(--line);border-radius:var(--rad-lg);margin-bottom:.65rem;overflow:hidden;background:#fff;}
.pap-sec-sum{display:flex;align-items:center;gap:.6rem;padding:.8rem 1rem;cursor:pointer;list-style:none;font-weight:600;font-size:.9rem;color:var(--ink);}
.pap-sec-sum .ic{color:var(--brand);}
.pap-sec-sum::-webkit-details-marker{display:none;}
.pap-sec-sum .eq-count{margin-left:auto;}
.pap-sec-sum .eq-chevron{margin-left:.2rem;}
.pap-sec[open] .pap-sec-sum .eq-chevron{transform:rotate(180deg);}
.pap-sec-body{padding:.2rem 1rem .9rem;display:flex;flex-direction:column;}
.pap-item{display:flex;align-items:center;gap:.75rem;border-top:1px solid var(--line-2);padding:.7rem 0;}
.pap-item-ic{width:34px;height:34px;border-radius:10px;background:var(--brand-soft);color:var(--brand);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.pap-item-body{flex:1;min-width:0;}
.pap-item-title{font-size:.9rem;font-weight:600;color:var(--text);}
.pap-item-meta{font-size:.76rem;color:var(--mute);margin-top:.15rem;}
.pap-item-actions{display:flex;gap:.35rem;flex-shrink:0;}
.pap-sw-detail{margin-top:.5rem;}
.pap-sw-detail summary{cursor:pointer;list-style:none;font-size:.78rem;font-weight:600;color:var(--brand);display:flex;align-items:center;gap:.25rem;}
.pap-sw-detail summary::-webkit-details-marker{display:none;}
.pap-sw-list{margin-top:.5rem;max-height:180px;overflow-y:auto;display:flex;flex-direction:column;gap:.22rem;padding-right:.3rem;}
.pap-sw-row{font-size:.78rem;color:var(--text);background:var(--paper);border-radius:8px;padding:.35rem .6rem;}

/* ================= Modales y formularios ================= */
.modal-bg{position:fixed;inset:0;background:rgba(7,26,11,.55);backdrop-filter:blur(4px);z-index:500;display:flex;align-items:center;justify-content:center;padding:1rem;animation:modalBgFade .18s ease;}
.modal{background:#fff;border-radius:var(--rad-xl);padding:1.8rem;width:100%;max-width:630px;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-lift);animation:modalPop .26s var(--ease);}
.modal.modal-flex{display:flex;flex-direction:column;overflow:hidden;}
.modal.modal-flex > *{flex-shrink:0;}
.modal.modal-flex .modal-scroll{flex:1;overflow-y:auto;min-height:0;}
.modal h3{font-family:var(--f-head);font-size:1.35rem;font-weight:700;display:flex;align-items:center;gap:.6rem;margin-bottom:.35rem;letter-spacing:-.025em;color:var(--ink);}
.modal h3 .ic{color:var(--brand);}
.modal-sub{font-size:.88rem;color:var(--mute);margin-bottom:1.4rem;line-height:1.55;}
fieldset{border:none;border-top:1px solid var(--line);padding:.9rem 0 .2rem;margin-bottom:.6rem;}
legend{font-family:var(--f-head);font-size:.9rem;font-weight:700;color:var(--ink);padding:0 .6rem 0 0;display:flex;align-items:center;gap:.45rem;letter-spacing:-.01em;}
legend .ic{color:var(--brand);}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.2rem .9rem;}
.form-grid.c3{grid-template-columns:1fr 1fr 1fr;}
.form-grid > *{min-width:0;}
.fg{display:flex;flex-direction:column;gap:.3rem;margin-bottom:.65rem;}
.fg.full{grid-column:1/-1;}
.fg label{font-size:.8rem;font-weight:600;color:var(--ink);}
.fg input,.fg select,.fg textarea{width:100%;min-width:0;padding:.6rem .75rem;border:1px solid var(--line);border-radius:var(--rad);font-size:.9rem;color:var(--text);outline:none;background:#fff;transition:border-color .15s,box-shadow .15s;}
.fg input:hover,.fg select:hover,.fg textarea:hover{border-color:#BBCDA8;}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--brand);box-shadow:var(--ring);}
.fg textarea{resize:vertical;min-height:60px;}
.modal-footer{display:flex;justify-content:flex-end;gap:.55rem;margin-top:.8rem;}
.toast{position:fixed;bottom:1.4rem;right:1.4rem;background:var(--ink);color:#fff;padding:.8rem 1.15rem;border-radius:var(--rad);transform:translateY(160%);transition:transform .35s var(--ease);z-index:999;display:flex;align-items:center;gap:.55rem;font-size:.88rem;font-weight:500;box-shadow:var(--shadow-lift);}
.toast .ic{color:var(--lime);}
.toast.show{transform:translateY(0);}

/* ================= Estadisticas ================= */
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(176px,1fr));border-top:1px solid var(--line);border-left:1px solid var(--line);background:#fff;border-radius:var(--rad-lg);margin-bottom:1.8rem;overflow:hidden;}
.kpi-card{position:relative;min-height:140px;padding-top:2.6rem !important;background:#fff;border-right:1px solid var(--line);border-bottom:1px solid var(--line);cursor:pointer;display:flex;flex-direction:column;justify-content:flex-end;gap:.15rem;padding:1.2rem 1.3rem;transition:background .2s;animation:rise .5s var(--ease) both;}
.kpi-card:nth-child(1){animation-delay:0ms}.kpi-card:nth-child(2){animation-delay:45ms}.kpi-card:nth-child(3){animation-delay:90ms}.kpi-card:nth-child(4){animation-delay:135ms}.kpi-card:nth-child(5){animation-delay:180ms}.kpi-card:nth-child(6){animation-delay:225ms}.kpi-card:nth-child(n+7){animation-delay:270ms}
.kpi-card:hover{background:#F8FBF3;}
.kpi-ic{position:absolute;top:1.05rem;right:1.1rem;color:#9DB094;transition:color .2s,transform .25s var(--ease);}
.kpi-card:hover .kpi-ic{color:var(--brand);transform:translateY(-2px);}
.kpi-info{display:flex;flex-direction:column;}
.kpi-n{font-family:var(--f-head);font-size:2.4rem;font-weight:700;line-height:1;letter-spacing:-.04em;color:var(--ink);}
.kpi-l{font-size:.84rem;font-weight:500;margin-top:.45rem;color:var(--mute);}
.grid-2{display:grid;grid-template-columns:1.3fr 1fr;gap:1.4rem;align-items:start;}
.panel{background:#fff;border:1px solid var(--line);border-radius:var(--rad-lg);padding:1.4rem 1.5rem;margin-bottom:1.2rem;}
.panel-h{font-family:var(--f-head);font-size:1.05rem;font-weight:700;color:var(--ink);display:flex;align-items:center;gap:.55rem;margin-bottom:1.2rem;letter-spacing:-.02em;}
.panel-h .ic{color:var(--brand);}
.bar-row{display:flex;align-items:center;gap:.8rem;margin-bottom:.85rem;}
.bar-row:last-child{margin-bottom:0;}
.bar-label{font-size:.86rem;font-weight:500;width:140px;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text);}
.bar-track{flex:1;height:7px;background:var(--line-2);border-radius:20px;overflow:hidden;}
.bar-fill{height:100%;background:linear-gradient(90deg,var(--brand),var(--lime));border-radius:20px;transform-origin:left;animation:barGrow .7s var(--ease) both;}
@keyframes barGrow{from{transform:scaleX(0)}to{transform:scaleX(1)}}
.bar-n{font-size:.86rem;font-weight:700;color:var(--ink);width:30px;text-align:right;flex-shrink:0;}
.list-search{margin-bottom:1rem;}
.simple-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.65rem .1rem;border-bottom:1px solid var(--line-2);font-size:.88rem;}
.simple-row:last-child{border-bottom:none;}
.simple-row b{font-weight:600;}
.simple-row small{color:var(--mute);font-size:.8rem;text-align:right;}

/* ================= Kanban ================= */
.kanban-wrap{display:flex;gap:1.1rem;align-items:flex-start;overflow-x:auto;padding-bottom:.6rem;}
.kanban-col{flex:1;min-width:268px;background:#E9EEDF;border-radius:var(--rad-lg);padding:.5rem .7rem .8rem;display:flex;flex-direction:column;min-height:240px;border-top:3px solid var(--kc);transition:background .2s;}
.kanban-col.drag-over{background:#DCE8C9;}
.kanban-col-h{display:flex;align-items:center;gap:.55rem;font-family:var(--f-head);font-weight:700;font-size:1rem;color:var(--ink);padding:.8rem .2rem .85rem;letter-spacing:-.015em;}
.kanban-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0;background:var(--kc);}
.kanban-count{margin-left:auto;font-size:.76rem;font-weight:700;color:var(--ink);background:#fff;padding:.1rem .6rem;border-radius:20px;font-family:var(--f-body);}
.kanban-cards{display:flex;flex-direction:column;gap:.55rem;flex:1;}
.kanban-card{background:#fff;border-left:4px solid var(--card-color);border-radius:var(--rad);padding:.75rem .85rem;cursor:grab;max-width:100%;overflow:hidden;box-shadow:var(--shadow);transition:box-shadow .18s var(--ease),transform .18s var(--ease),opacity .15s;}
.kanban-card:hover{box-shadow:0 8px 22px rgba(11,36,16,.12);transform:translateY(-2px);}
.kanban-card:active{cursor:grabbing;}
.kanban-card.dragging{opacity:.4;}
.kc-title{font-size:.92rem;font-weight:600;color:var(--ink);margin-bottom:.3rem;line-height:1.4;overflow-wrap:anywhere;word-break:break-word;}
.kc-desc{font-size:.82rem;color:var(--mute);line-height:1.5;margin-bottom:.6rem;white-space:pre-wrap;overflow-wrap:anywhere;word-break:break-word;}
.kc-date{font-size:.74rem;color:var(--brand);display:flex;align-items:center;gap:.3rem;margin-bottom:.5rem;font-weight:600;}
.kc-actions{display:flex;justify-content:space-between;align-items:center;margin-top:.3rem;}
.kc-move{display:flex;gap:.3rem;}
.kc-btn{background:var(--paper);border:none;border-radius:8px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--brand);flex-shrink:0;transition:background .15s;}
.kc-btn:hover{background:var(--brand-soft);}
.kc-btn.danger{color:#B42318;}
.kc-btn.danger:hover{background:#FDEEEC;}
.kc-btn.spacer{visibility:hidden;}
.kanban-add{margin-top:.8rem;width:100%;padding:.62rem;border:1.5px dashed #B5C6A3;border-radius:var(--rad);background:none;color:var(--mute);font-size:.84rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.4rem;transition:border-color .15s,color .15s,background .15s;}
.kanban-add:hover{border-color:var(--brand);color:var(--brand);background:rgba(255,255,255,.55);}
.color-picker{display:flex;gap:.65rem;margin-top:.25rem;}
.color-swatch{width:28px;height:28px;border-radius:50%;border:2px solid transparent;cursor:pointer;padding:0;transition:transform .15s var(--ease);}
.color-swatch:hover{transform:scale(1.12);}
.color-swatch.active{border-color:var(--ink);box-shadow:0 0 0 2px #fff inset;}

/* ================= Impresoras ================= */
.imp-bar{display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;margin-bottom:1.2rem;}
.imp-bar select{flex:1;min-width:220px;max-width:380px;padding:.6rem .75rem;border:1px solid var(--line);border-radius:var(--rad);font-size:.9rem;font-weight:600;color:var(--ink);background:#fff;outline:none;}
.imp-bar select:focus{border-color:var(--brand);box-shadow:var(--ring);}
.imp-auto{display:flex;align-items:center;gap:.45rem;font-size:.84rem;color:var(--mute);margin-left:auto;cursor:pointer;}
.imp-auto input{accent-color:var(--brand);}
.imp-last{display:flex;align-items:center;gap:.55rem;font-size:.86rem;color:var(--mute);margin-bottom:.9rem;}
.imp-last .ic{color:var(--brand);}
.imp-last b{color:var(--ink);}
.imp-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:1.6rem;}
.imp-card{background:#fff;border:1px solid var(--line);border-radius:var(--rad-lg);padding:1.15rem 1.3rem;}
.imp-card-h{display:flex;align-items:center;gap:.55rem;font-family:var(--f-head);font-size:1rem;font-weight:700;color:var(--ink);margin-bottom:.7rem;letter-spacing:-.015em;}
.imp-card-h .ic{color:var(--brand);}
.imp-row{display:flex;align-items:baseline;justify-content:space-between;padding:.5rem 0;border-top:1px solid var(--line-2);font-size:.86rem;color:var(--mute);}
.imp-row:first-of-type{border-top:none;}
.imp-row b{font-family:var(--f-head);font-size:1.3rem;color:var(--ink);font-weight:700;letter-spacing:-.02em;}
.imp-err{background:#FDEEEC;color:#B42318;border-radius:var(--rad);padding:.85rem 1.05rem;font-size:.88rem;margin-bottom:1rem;line-height:1.5;}
.imp-err pre{white-space:pre-wrap;word-break:break-word;font-size:.74rem;margin-top:.5rem;max-height:160px;overflow:auto;color:var(--text);background:#fff;padding:.55rem;border-radius:8px;}
.imp-total-row td{background:var(--brand-soft);border-top:1px solid #BFD8A8;}
.imp-hist th{padding:.6rem .6rem;}
.imp-hist td{padding:.65rem .6rem;font-size:.84rem;}
.imp-hist th.num.g{text-align:center;border-bottom:1px solid var(--line);}
.imp-hist .imp-grp th{border-bottom:none;}

/* ================= Vigencias / tablas ================= */
.vg-tiles{display:grid;grid-template-columns:repeat(4,1fr);gap:.8rem;margin-bottom:1.3rem;}
.vg-tile{text-align:left;background:#fff;border:1px solid var(--line);border-radius:var(--rad-lg);padding:1rem 1.15rem;cursor:pointer;color:var(--text);display:flex;flex-direction:column;gap:.3rem;transition:border-color .15s,background .15s,transform .18s var(--ease);}
.vg-tile:hover{border-color:#B9CFA3;transform:translateY(-1px);}
.vg-tile.active{border-color:var(--brand);background:var(--brand-soft);box-shadow:inset 0 0 0 1px var(--brand);}
.vg-tile-n{font-family:var(--f-head);font-size:2rem;font-weight:700;line-height:1;letter-spacing:-.03em;color:var(--ink);}
.vg-tile-l{font-size:.82rem;font-weight:500;color:var(--mute);display:flex;align-items:center;gap:.45rem;}
.vg-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;background:var(--mute);}
.vg-dot.vigente{background:#3FAF29;}
.vg-dot.por_vencer{background:#D4A017;}
.vg-dot.vencida{background:#E5484D;}
.vg-toolbar{display:flex;gap:.6rem;align-items:center;margin-bottom:1rem;flex-wrap:wrap;}
.vg-toolbar .sw-wrap{flex:1;min-width:200px;}
.vg-toolbar .si{background:#fff;color:var(--text);border-color:var(--line);}
.vg-toolbar .sic{color:var(--brand);}
.vg-toolbar select{padding:.6rem .75rem;border:1px solid var(--line);border-radius:var(--rad);font-size:.88rem;color:var(--text);background:#fff;outline:none;}
.vg-table-wrap{background:#fff;border:1px solid var(--line);border-radius:var(--rad-lg);overflow-x:auto;}
.vgt{width:100%;border-collapse:collapse;font-size:.88rem;min-width:720px;}
.vgt th{text-align:left;padding:.8rem 1rem;color:var(--mute);border-bottom:1px solid var(--line);font-weight:600;font-size:.78rem;background:#FAFBF6;white-space:nowrap;}
.vgt td{padding:.85rem 1rem;border-bottom:1px solid var(--line-2);vertical-align:middle;}
.vgt tr:last-child td{border-bottom:none;}
.vgt tbody tr{transition:background .15s;}
.vgt tbody tr:hover{background:#F8FBF3;}
.vgt td.num,.vgt th.num{text-align:right;white-space:nowrap;padding-left:.6rem;padding-right:.6rem;}
.vg-name{font-weight:600;color:var(--ink);}
.vg-sub{font-size:.78rem;color:var(--mute);margin-top:.12rem;overflow-wrap:anywhere;}
.vg-type{display:inline-flex;align-items:center;gap:.4rem;font-size:.84rem;color:var(--text);white-space:nowrap;}
.vg-type .ic{color:var(--brand);}
.vg-date{white-space:nowrap;}
.vg-actions{display:flex;gap:.3rem;justify-content:flex-end;}
.vg-badge{display:inline-flex;align-items:center;gap:.45rem;font-size:.78rem;font-weight:600;padding:.25rem .65rem;border-radius:20px;white-space:nowrap;background:var(--brand-soft);color:#0F6E2E;}
.vg-badge.por_vencer{background:#FFF4D1;color:#7A5A00;}
.vg-badge.vencida{background:#FDEBE9;color:#B42318;}
.vg-item{display:flex;align-items:center;gap:.8rem;border-top:1px solid var(--line-2);padding:.65rem 0;}
.vg-item-body{flex:1;min-width:0;}
.vg-alert{display:inline-flex;align-items:center;gap:.35rem;font-size:.78rem;font-weight:600;color:#B42318;margin-top:.3rem;}
.kpi-card .vg-warn{color:#8A6500;}
.vg-quick{display:flex;flex-wrap:wrap;align-items:center;gap:.45rem;margin:-.1rem 0 .8rem;}
.vg-quick-l{font-size:.8rem;font-weight:600;color:var(--ink);margin-right:.2rem;}
.vg-chip{border:1px solid var(--line);background:#fff;color:var(--ink);border-radius:20px;padding:.3rem .8rem;font-size:.8rem;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s,transform .15s var(--ease);}
.vg-chip:hover{background:var(--brand-soft);border-color:var(--brand);transform:translateY(-1px);}

/* ================= Responsive ================= */
@media (max-width:1100px){
  .db{padding:1.5rem 1.6rem 2.6rem;}
  .dh{padding:1.7rem 1.6rem 1.2rem;}
}
@media (max-width:900px){
  .grid-2{grid-template-columns:1fr;}
  .vg-tiles{grid-template-columns:repeat(2,1fr);}
  .app{display:flex;flex-direction:column;height:auto;min-height:100vh;overflow:visible;}
  .sidebar{flex-direction:column;overflow:visible;}
  .sidebar::after{display:none;}
  .logo{padding:1rem 1.1rem .5rem;}
  .nav-seg{flex-direction:row;overflow-x:auto;padding:0 .8rem .7rem;gap:.3rem;}
  .nav-seg .btn{width:auto;flex-shrink:0;padding:.55rem .85rem;}
  .nav-seg .btn.on::before{display:none;}
  .side-users{flex:none;margin-top:0;padding-top:.8rem;}
  .pl{max-height:34vh;}
  .btn-papelera{margin:.2rem .8rem .8rem;}
  .right-panel{overflow:visible;}
}
@media (max-width:600px){
  .nav-seg .btn:not(.on) span{display:none;}
  .nav-seg{justify-content:flex-start;}
  .dh{padding:1.3rem 1.1rem 1rem;}
  .db{padding:1.2rem 1.1rem 2.2rem;}
  .dhn{font-size:1.55rem;}
  .dh-top{flex-wrap:wrap;}
  .form-grid,.form-grid.c3{grid-template-columns:1fr;}
  .kpi-n{font-size:2.1rem;}
  .modal{padding:1.3rem;border-radius:var(--rad-lg);}
  .bar-label{width:100px;}
}
@media (prefers-reduced-motion:reduce){
  *,*::before,*::after{animation:none !important;transition:none !important;}
}
</style>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <button class="logo" onclick="loadKanban()">
    <svg width="28" height="28" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
      <path d="M20 3C20 3 6 18 6 27a14 14 0 0 0 28 0C34 18 20 3 20 3Z" fill="#CED800"/>
      <path d="M20 9C20 9 10 20 10 27a10 10 0 0 0 20 0c0-7-10-18-10-18Z" fill="#94D500"/>
      <path d="M20 15C20 15 14 22 14 27a6 6 0 0 0 12 0c0-5-6-12-6-12Z" fill="#3FAF29"/>
      <circle cx="20" cy="28" r="3" fill="#00953A"/>
    </svg>
    <span class="logo-text">Inv<span>CMP</span></span>
  </button>
    <nav class="nav-seg" aria-label="Secciones">
    <button class="btn btn-ghost" id="btnTareas" onclick="loadKanban()"><span>Tareas</span></button>
    <button class="btn btn-ghost" id="btnImpresoras" onclick="loadImpresoras()"><span>Impresoras</span></button>
    <button class="btn btn-ghost" id="btnVigencias" onclick="loadVigencias()"><span>Vigencias</span></button>
    <button class="btn btn-ghost" id="btnStats" onclick="loadDashboard()"><span>Estadísticas</span></button>
  </nav>
    <div class="side-users">
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
    </div>
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

<div class="modal-bg" id="modalVigencia" style="display:none" onclick="if(event.target===this)closeModal('modalVigencia')">
  <div class="modal" style="max-width:560px">
    <h3 id="vigModalTitle"><span>Nueva vigencia</span></h3>
    <div class="modal-sub">Licencia, garantía o suscripción de hardware o software con su fecha de vencimiento.</div>
    <input type="hidden" id="vigId">
    <div class="form-grid">
      <div class="fg"><label>Tipo</label>
        <select id="vigTipo"><option value="Software">Software</option><option value="Hardware">Hardware</option></select>
      </div>
      <div class="fg"><label>Equipo asociado</label>
        <select id="vigEquipo"><option value="">Sin equipo (general)</option></select>
      </div>
      <div class="fg full"><label>Nombre *</label><input id="vigNombre" maxlength="200" placeholder="Ej: Microsoft 365, Garantía Dell, Antivirus"></div>
      <div class="fg"><label>Proveedor</label><input id="vigProveedor" maxlength="150" placeholder="Ej: Microsoft"></div>
      <div class="fg"><label>Clave / Referencia</label><input id="vigReferencia" maxlength="250" placeholder="Clave, contrato o n.º de serie"></div>
      <div class="fg"><label>Fecha de inicio</label><input id="vigInicio" type="date"></div>
      <div class="fg"><label>Fecha de vencimiento *</label><input id="vigVence" type="date"></div>
      <div class="vg-quick full" style="grid-column:1/-1">
        <span class="vg-quick-l">Vence en</span>
        <button type="button" class="vg-chip" onclick="vigSumar(3)">3 meses</button>
        <button type="button" class="vg-chip" onclick="vigSumar(6)">6 meses</button>
        <button type="button" class="vg-chip" onclick="vigSumar(12)">1 año</button>
        <button type="button" class="vg-chip" onclick="vigSumar(24)">2 años</button>
        <button type="button" class="vg-chip" onclick="vigSumar(36)">3 años</button>
      </div>
      <div class="fg full"><label>Observaciones</label><textarea id="vigObs" maxlength="2000" placeholder="Notas adicionales (opcional)..."></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalVigencia')">Cancelar</button>
      <button class="btn btn-primary" id="btnGuardarVigencia" onclick="guardarVigencia()"><span>Guardar</span></button>
    </div>
  </div>
</div>

<div class="modal-bg" id="modalImpresora" style="display:none" onclick="if(event.target===this)closeModal('modalImpresora')">
  <div class="modal" style="max-width:440px">
    <h3 id="impModalTitle"><span>Agregar impresora</span></h3>
    <div class="modal-sub">Se consulta su página web de contadores desde la red local.</div>
    <div class="fg"><label>Nombre *</label><input id="impNombre" maxlength="150" placeholder="Ej: Ricoh Administración"></div>
    <div class="fg"><label>Dirección IP *</label><input id="impIp" maxlength="64" placeholder="Ej: 192.168.2.179"></div>
    <div class="fg"><label>Ubicación</label><input id="impUbic" maxlength="150" placeholder="Ej: Piso 2 (opcional)"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalImpresora')">Cancelar</button>
      <button class="btn btn-primary" onclick="guardarImpresora()"><span>Guardar</span></button>
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
  copy:     svg('<rect x="8" y="8" width="12" height="12" rx="1.5"/><path d="M16 8V5.5A1.5 1.5 0 0 0 14.5 4h-9A1.5 1.5 0 0 0 4 5.5v9A1.5 1.5 0 0 0 5.5 16H8"/>'),
  scan:     svg('<path d="M4 8V5.5A1.5 1.5 0 0 1 5.5 4H8M16 4h2.5A1.5 1.5 0 0 1 20 5.5V8M20 16v2.5a1.5 1.5 0 0 1-1.5 1.5H16M8 20H5.5A1.5 1.5 0 0 1 4 18.5V16M3 12h18"/>'),
  calendar: svg('<rect x="3" y="4.5" width="18" height="16" rx="1.5"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/>'),
  alert:    svg('<path d="M12 3 2 20h20z"/><path d="M12 10v4M12 17h.01"/>', 14),
  printer:  svg('<path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1.5"/><path d="M6 14h12v7H6z"/>'),
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

function marcarNav(id){
  document.querySelectorAll('.nav-seg .btn').forEach(b=>b.classList.toggle('on', b.id===id));
}

function toast(msg, icon=I.check){
  const t = document.getElementById('toast');
  t.innerHTML = icon + '<span>' + msg + '</span>'; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'), 3000);
}
function closeModal(id){ document.getElementById(id).style.display='none'; }
function esc(s){ return (s===null||s===undefined) ? '' : String(s).replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

/* ============ DASHBOARD ============ */
function impKpiHTML(t){
  if(!t || !(t.impresoras||[]).length) return '';
  return `<div class="kpi-card" onclick="loadImpresoras()">
    <div class="kpi-ic">${I.printer}</div>
    <div class="kpi-info"><div class="kpi-n">${fmtNum(t.suma.contador_total)}</div><div class="kpi-l">Páginas (todas las impresoras)</div></div>
  </div>`;
}

function vigKpiHTML(v){
  if(!v) return '';
  const aviso = v.vencidas > 0
    ? `<div class="vg-alert">${I.alert}<span>${v.vencidas} vencida${v.vencidas==1?'':'s'}</span></div>`
    : (v.por_vencer > 0 ? `<div class="vg-alert vg-warn">${I.alert}<span>${v.por_vencer} por vencer</span></div>` : '');
  return `<div class="kpi-card" onclick="loadVigencias()">
    <div class="kpi-ic">${I.calendar}</div>
    <div class="kpi-info"><div class="kpi-n">${v.total}</div><div class="kpi-l">Vigencias</div>${aviso}</div>
  </div>`;
}

async function loadDashboard(){
  activeUser = null; vigViewActiva = false; marcarNav('btnStats');
  document.querySelectorAll('.pc.active').forEach(el=>el.classList.remove('active'));
  const d = await api({action:'stats'});

  const cards = [
    {ic:I.users, n:d.resp, label:'Usuarios', tipo:'usuarios'},
    {ic:I.building, n:d.areas, label:'Áreas', tipo:'areas'},
    ...(d.por_componente||[]).map(c=>({ic:deviceIcon(c.componente), n:c.total, label:compLabel(c.componente), tipo:'componente', componente:c.componente}))
  ];

  const porArea = d.por_area || [];
  const porComp = d.por_componente || [];
  const maxArea = Math.max(1, ...porArea.map(a=>a.total));
  const maxComp = Math.max(1, ...porComp.map(c=>c.total));

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
        ${vigKpiHTML(d.vigencias)}
        ${impKpiHTML(d.impresoras)}
        ${cards.map(c=>`
          <div class="kpi-card" onclick="openKpiModal('${c.tipo}'${c.componente?`,'${esc(c.componente).replace(/'/g,"\\'")}'`:''})">
            <div class="kpi-ic">${c.ic}</div>
            <div class="kpi-info"><div class="kpi-n">${c.n}</div><div class="kpi-l">${esc(c.label)}</div></div>
          </div>`).join('')}
      </div>

      <div class="grid-2">
        <div class="panel">
          <div class="panel-h">${I.building}<span>Equipos por área</span></div>
          ${porArea.length ? porArea.map(a=>`
            <div class="bar-row">
              <div class="bar-label">${esc(a.area)}</div>
              <div class="bar-track"><div class="bar-fill" style="width:${Math.round(a.total/maxArea*100)}%"></div></div>
              <div class="bar-n">${a.total}</div>
            </div>`).join('') : '<div class="empty-state">Sin datos de áreas.</div>'}
        </div>
        <div class="panel">
          <div class="panel-h">${I.disk}<span>Componentes registrados</span></div>
          ${porComp.length ? porComp.map(c=>`
            <div class="bar-row">
              <div class="bar-label">${esc(compLabel(c.componente))}</div>
              <div class="bar-track"><div class="bar-fill" style="width:${Math.round(c.total/maxComp*100)}%"></div></div>
              <div class="bar-n">${c.total}</div>
            </div>`).join('') : '<div class="empty-state">Sin datos.</div>'}
        </div>
      </div>
      ${d.impresoras && (d.impresoras.impresoras||[]).length ? `
      <div class="panel" style="margin-top:.2rem">
        <div class="panel-h">${I.printer}<span>Impresoras · contador total</span></div>
        <div class="vg-table-wrap" style="box-shadow:none">${impTotalesHTML(d.impresoras)}</div>
      </div>` : ''}
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
  vigViewActiva = false; marcarNav(null);
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
        <button class="btn btn-ghost btn-sm" onclick="abrirAsignarEquipo('${esc(nombre).replace(/'/g,"\\'")}')">${I.refresh}<span>Asignarle un equipo existente</span></button>
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

        <div class="sec-label">
          <span class="sec-label-txt">${I.calendar}<span>Vigencias (${(eq.vigencias||[]).length})</span></span>
          <button class="btn btn-ghost btn-sm" onclick="abrirModalVigencia(null, ${eq.id})">${I.plus}<span>Agregar</span></button>
        </div>
        ${(eq.vigencias||[]).map(v=>`<div class="vg-item">
          <div class="periph-ic">${v.tipo==='Hardware' ? I.chip : I.disk}</div>
          <div class="vg-item-body">
            <div class="periph-name">${esc(v.nombre)}</div>
            <div class="vg-sub">${esc(v.tipo)}${v.proveedor ? ' · '+esc(v.proveedor) : ''} · Vence: ${fmtFecha(v.fecha_vencimiento)}</div>
          </div>
          ${vigBadge(v)}
        </div>`).join('') || '<div class="empty-state">Sin vigencias registradas.</div>'}
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

/* ============ IMPRESORAS ============ */
let impLista = [];
let impSel = null;
let impTimer = null;

function sumaCampos(r, ks){
  return ks.reduce((a,k)=>a + (r[k]==null ? 0 : Number(r[k])), 0);
}
const K_COPIA = ['copia_color','copia_bn','copia_color_pers','copia_dos_colores'];
const K_IMPR  = ['impresion_color','impresion_bn','impresion_color_pers','impresion_dos_colores'];
const K_ESC   = ['escaneo_color','escaneo_bn'];

/* Tabla de totales: cada impresora por separado + total general (suma de todas) */
function impTotalesHTML(t){
  const lista = (t && t.impresoras) || [];
  if(!lista.length) return '<div class="empty-state" style="padding:1.2rem;text-align:center">Aún no hay impresoras.</div>';
  const fila = r => {
    const ok = r.fecha_hora != null;
    const f = ok ? fmtFH(r.fecha_hora) : null;
    return `<tr>
      <td><div class="vg-name">${esc(r.nombre)}</div><div class="vg-sub">${ok ? 'Lectura: '+f.f+' '+f.h.slice(0,5) : 'Sin lecturas'}</div></td>
      <td class="num">${ok ? fmtNum(sumaCampos(r,K_COPIA)) : '—'}</td>
      <td class="num">${ok ? fmtNum(sumaCampos(r,K_IMPR)) : '—'}</td>
      <td class="num">${ok ? fmtNum(sumaCampos(r,K_ESC)) : '—'}</td>
      <td class="num"><b>${ok ? fmtNum(r.contador_total) : '—'}</b></td></tr>`;
  };
  const sm = t.suma || {};
  return `<table class="vgt" style="min-width:0">
    <thead><tr><th>Impresora</th><th class="num">Copia</th><th class="num">Impresión</th><th class="num">Escaneado</th><th class="num">Contador total</th></tr></thead>
    <tbody>${lista.map(fila).join('')}
    <tr class="imp-total-row"><td><div class="vg-name">Total de todas las impresoras</div></td>
      <td class="num">${fmtNum(sumaCampos(sm,K_COPIA))}</td><td class="num">${fmtNum(sumaCampos(sm,K_IMPR))}</td>
      <td class="num">${fmtNum(sumaCampos(sm,K_ESC))}</td><td class="num"><b>${fmtNum(sm.contador_total)}</b></td></tr>
    </tbody></table>`;
}

async function cargarTotalesImp(){
  const box = document.getElementById('impTotales');
  if(!box) return;
  const t = await api({action:'impresoras_totales'});
  box.innerHTML = impTotalesHTML(t);
}

function fmtNum(n){ return (n===null||n===undefined) ? '—' : Number(n).toLocaleString('es-CO'); }
function fmtFH(fh){
  if(!fh) return {f:'—',h:'—'};
  const [d,t] = String(fh).split(' ');
  const [y,m,dd] = d.split('-');
  return {f:`${dd}/${m}/${y}`, h:(t||'').slice(0,8)};
}

async function loadImpresoras(){
  activeUser = null; vigViewActiva = false; marcarNav('btnImpresoras');
  document.querySelectorAll('.pc.active').forEach(el=>el.classList.remove('active'));
  document.getElementById('rightPanel').innerHTML = `
    <div class="view-fade" id="impLive">
    <div class="dh">
      <div class="dh-top">
        <div class="dh-ic">${I.printer}</div>
        <div style="flex:1;min-width:0">
          <div class="dhn">Impresoras</div>
          <div class="dhs">Contadores de copia, impresión y escaneado</div>
        </div>
        <button class="btn btn-primary" id="btnConsultarImp" onclick="consultarImpresora()">${I.refresh}<span>Consultar ahora</span></button>
      </div>
    </div>
    <div class="db">
      <div class="panel-h">${I.printer}<span>Totales de todas las impresoras</span></div>
      <div class="vg-table-wrap" id="impTotales" style="margin-bottom:1.4rem"></div>
      <div class="imp-bar">
        <select id="impSelect" onchange="impSel=Number(this.value);impMostrarVacio();cargarLecturasImp();"></select>
        <button class="btn btn-ghost btn-sm" onclick="abrirModalImpresora()">${I.plus}<span>Agregar</span></button>
        <button class="btn btn-danger btn-sm" onclick="eliminarImpresora()" title="Eliminar impresora">${I.trash}</button>
        <label class="imp-auto"><input type="checkbox" id="impAuto" onchange="impToggleAuto(this.checked)"> Actualizar cada minuto</label>
      </div>
      <div id="impMsg"></div>
      <div id="impActual"></div>
      <div class="panel-h" style="margin-top:.4rem;justify-content:space-between"><span style="display:flex;align-items:center;gap:.55rem">${I.calendar}<span>Historial de lecturas</span></span>
        <button class="btn btn-danger btn-sm" id="btnBorrarHist" onclick="borrarHistorialImp()" style="display:none">${I.trash}<span>Borrar historial</span></button></div>
      <div class="vg-table-wrap" id="impHistorial"></div>
    </div>
    </div>`;
  const d = await api({action:'impresoras'});
  impLista = d.impresoras || [];
  if(!impLista.find(i=>i.id===impSel)) impSel = impLista.length ? impLista[0].id : null;
  const sel = document.getElementById('impSelect');
  sel.innerHTML = impLista.map(i=>`<option value="${i.id}" ${i.id===impSel?'selected':''}>${esc(i.nombre)} (${esc(i.ip)})</option>`).join('')
                  || '<option value="">Sin impresoras</option>';
  cargarTotalesImp();
  await cargarLecturasImp();
}

function impMostrarVacio(){
  document.getElementById('impMsg').innerHTML = '';
}

async function cargarLecturasImp(){
  const hist = document.getElementById('impHistorial');
  if(!hist) return;
  if(!impSel){ hist.innerHTML = '<div class="empty-state" style="padding:1.4rem;text-align:center">Agrega una impresora para empezar.</div>'; document.getElementById('impActual').innerHTML=''; return; }
  const d = await api({action:'lecturas_impresora', id: impSel});
  const rows = d.lecturas || [];
  if(!rows.length){
    document.getElementById('impActual').innerHTML = '<div class="empty-state" style="padding:.4rem 0 1.2rem">Aún no hay lecturas. Pulsa “Consultar ahora” para leer los contadores de la impresora.</div>';
    hist.innerHTML = '<div class="empty-state" style="padding:1.4rem;text-align:center">Sin historial.</div>';
    const bb0 = document.getElementById('btnBorrarHist'); if(bb0) bb0.style.display = 'none';
    return;
  }
  renderImpActual(rows[0]);
  const cols = ['copia_color','copia_bn','copia_color_pers','copia_dos_colores','impresion_color','impresion_bn','impresion_color_pers','impresion_dos_colores','escaneo_color','escaneo_bn'];
  hist.innerHTML = `<table class="vgt imp-hist" style="min-width:0">
    <thead>
      <tr class="imp-grp"><th rowspan="2">Fecha</th><th rowspan="2">Hora</th><th colspan="4" class="num g">Copia</th><th colspan="4" class="num g">Impresión</th><th colspan="2" class="num g">Escaneado</th><th rowspan="2" class="num">Total</th><th rowspan="2"></th></tr>
      <tr><th class="num">Todo color</th><th class="num">B/N</th><th class="num">Personal.</th><th class="num">Dos col.</th><th class="num">Todo color</th><th class="num">B/N</th><th class="num">Personal.</th><th class="num">Dos col.</th><th class="num">Color</th><th class="num">B/N</th></tr>
    </thead>
    <tbody>${rows.map(r=>{const t=fmtFH(r.fecha_hora);return `<tr>
      <td class="vg-date">${t.f}</td><td class="vg-date">${t.h}</td>
      ${cols.map(k=>`<td class="num">${fmtNum(r[k])}</td>`).join('')}<td class="num"><b>${fmtNum(r.contador_total)}</b></td>
      <td><button class="btn btn-danger btn-sm" onclick="borrarLecturaImp(${r.id})" title="Borrar esta lectura">${I.trash}</button></td></tr>`;}).join('')}</tbody></table>`;
  const bb = document.getElementById('btnBorrarHist'); if(bb) bb.style.display = 'inline-flex';
}

async function borrarLecturaImp(id){
  if(!confirm('¿Borrar esta lectura?')) return;
  await api({action:'borrar_lectura'}, {id});
  toast('Lectura borrada', I.trash);
  cargarTotalesImp();
  cargarLecturasImp();
}

async function borrarHistorialImp(){
  if(!impSel || !confirm('¿Borrar todo el historial de lecturas de esta impresora?')) return;
  await api({action:'vaciar_lecturas'}, {id: impSel});
  toast('Historial borrado', I.trash);
  cargarTotalesImp();
  cargarLecturasImp();
}

function renderImpActual(l){
  const t = fmtFH(l.fecha_hora);
  const card = (ic, titulo, filas) => `<div class="imp-card">
      <div class="imp-card-h">${ic}<span>${titulo}</span></div>
      ${filas.map(([n,v])=>`<div class="imp-row"><span>${n}</span><b>${fmtNum(v)}</b></div>`).join('')}
    </div>`;
  document.getElementById('impActual').innerHTML = `
    <div class="imp-last">${I.calendar}<span>Última lectura: <b>${t.f}</b> a las <b>${t.h}</b></span></div>
    <div class="imp-grid">
      ${card(I.copy, 'Copiadora', [['A todo color',l.copia_color],['Blanco y Negro',l.copia_bn],['Color personalizado',l.copia_color_pers],['Dos colores',l.copia_dos_colores]])}
      ${card(I.printer, 'Impresora', [['A todo color',l.impresion_color],['Blanco y Negro',l.impresion_bn],['Color personalizado',l.impresion_color_pers],['Dos colores',l.impresion_dos_colores]])}
      ${card(I.scan, 'Envío por escáner', [['Color',l.escaneo_color],['Blanco y Negro',l.escaneo_bn]])}
      ${card(I.chart, 'Total', [['Contador total',l.contador_total],
          ...(l.total_color!=null ? [['A todo color',l.total_color]] : []),
          ...(l.total_bn!=null ? [['Blanco y Negro',l.total_bn]] : []),
          ...(l.total_color_pers!=null ? [['Color personalizado',l.total_color_pers]] : []),
          ...(l.total_dos_colores!=null ? [['Dos colores',l.total_dos_colores]] : [])])}
    </div>`;
}

async function consultarImpresora(silencioso=false){
  if(!impSel) return;
  const btn = document.getElementById('btnConsultarImp');
  if(btn){ btn.disabled = true; btn.querySelector('span').textContent = 'Consultando...'; }
  const msg = document.getElementById('impMsg');
  try{
    const r = await fetch(API + '?action=consultar_impresora', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id: impSel})});
    const res = await r.json();
    if(!document.getElementById('impLive')) return;
    if(res.error){
      msg.innerHTML = `<div class="imp-err"><b>No se pudo leer la impresora.</b> ${esc(res.error)}${res.detalle ? `<pre>${esc(res.detalle)}</pre>` : ''}</div>`;
    } else {
      msg.innerHTML = '';
      if(!silencioso) toast('Lectura guardada');
      cargarTotalesImp();
      await cargarLecturasImp();
    }
  } catch(e){
    if(msg) msg.innerHTML = `<div class="imp-err"><b>No se pudo consultar.</b> Revisa que XAMPP (Apache) esté activo.</div>`;
  } finally {
    if(btn){ btn.disabled = false; btn.querySelector('span').textContent = 'Consultar ahora'; }
  }
}

function impToggleAuto(on){
  if(impTimer){ clearInterval(impTimer); impTimer = null; }
  if(!on) return;
  consultarImpresora(true);
  impTimer = setInterval(()=>{
    if(!document.getElementById('impAuto')){ clearInterval(impTimer); impTimer = null; return; } // se salió de la vista
    consultarImpresora(true);
  }, 60000);
}

function abrirModalImpresora(){
  ['impNombre','impIp','impUbic'].forEach(k=>document.getElementById(k).value='');
  document.getElementById('impModalTitle').innerHTML = I.printer + '<span>Agregar impresora</span>';
  document.getElementById('modalImpresora').style.display = 'flex';
}

async function guardarImpresora(){
  const body = { nombre: document.getElementById('impNombre').value, ip: document.getElementById('impIp').value.trim(), ubicacion: document.getElementById('impUbic').value };
  if(!body.nombre.trim()){ toast('El nombre es obligatorio', I.close); return; }
  const res = await api({action:'crear_impresora'}, body);
  if(res && res.error){ toast(res.error, I.close); return; }
  closeModal('modalImpresora');
  impSel = res.id;
  toast('Impresora agregada');
  loadImpresoras();
}

async function eliminarImpresora(){
  if(!impSel) return;
  if(!confirm('¿Eliminar esta impresora y todo su historial de lecturas?')) return;
  await api({action:'eliminar_impresora'}, {id: impSel});
  impSel = null;
  toast('Impresora eliminada', I.trash);
  loadImpresoras();
}

/* ============ VIGENCIAS ============ */
let vigData = [];
let vigFiltro = 'todas';
let vigViewActiva = false;

function fmtFecha(f){
  if(!f) return '—';
  const [y,m,d] = String(f).split('-');
  return `${d}/${m}/${y}`;
}
function vigTextoDias(v){
  const n = v.dias_restantes;
  if(n < 0) return `Venció hace ${-n} día${n===-1?'':'s'}`;
  if(n === 0) return 'Vence hoy';
  return `Faltan ${n} día${n===1?'':'s'}`;
}
function vigBadge(v){
  const label = v.estado === 'vencida' ? 'Vencida' : (v.estado === 'por_vencer' ? 'Por vencer' : 'Vigente');
  return `<span class="vg-badge ${v.estado}" title="${esc(vigTextoDias(v))}"><span class="vg-dot ${v.estado}"></span>${label}</span>`;
}

async function loadVigencias(){
  activeUser = null; vigViewActiva = true; marcarNav('btnVigencias');
  document.querySelectorAll('.pc.active').forEach(el=>el.classList.remove('active'));
  document.getElementById('rightPanel').innerHTML = `
    <div class="view-fade">
    <div class="dh">
      <div class="dh-top">
        <div class="dh-ic">${I.calendar}</div>
        <div style="flex:1;min-width:0">
          <div class="dhn">Vigencias</div>
          <div class="dhs">Licencias y garantías de hardware y software</div>
        </div>
        <button class="btn btn-primary" onclick="abrirModalVigencia()">${I.plus}<span>Nueva vigencia</span></button>
      </div>
    </div>
    <div class="db">
      <div class="vg-tiles" id="vigTiles"></div>
      <div class="vg-toolbar">
        <div class="sw-wrap"><span class="sic">${I.search}</span><input class="si" id="vigBuscar" type="text" placeholder="Buscar por nombre, proveedor, equipo o referencia..." oninput="renderVigTabla()"></div>
        <select id="vigFiltroTipo" onchange="renderVigTabla()"><option value="">Todos los tipos</option><option value="Software">Software</option><option value="Hardware">Hardware</option></select>
      </div>
      <div class="vg-table-wrap" id="vigTabla"></div>
    </div>
    </div>`;
  await refreshVigencias();
}

async function refreshVigencias(){
  const d = await api({action:'vigencias'});
  vigData = d.vigencias || [];
  renderVigTiles();
  renderVigTabla();
}

function renderVigTiles(){
  const c = { todas: vigData.length, vigente:0, por_vencer:0, vencida:0 };
  vigData.forEach(v=>c[v.estado]++);
  const tiles = [
    ['todas','Todas',c.todas],['vigente','Vigentes',c.vigente],
    ['por_vencer','Por vencer (30 días)',c.por_vencer],['vencida','Vencidas',c.vencida]
  ];
  const el = document.getElementById('vigTiles');
  if(!el) return;
  el.innerHTML = tiles.map(([k,l,n])=>`
    <button class="vg-tile ${vigFiltro===k?'active':''}" onclick="setVigFiltro('${k}')">
      <span class="vg-tile-n">${n}</span>
      <span class="vg-tile-l">${k!=='todas' ? `<span class="vg-dot ${k}"></span>` : ''}${l}</span>
    </button>`).join('');
}
function setVigFiltro(k){ vigFiltro = k; renderVigTiles(); renderVigTabla(); }

function renderVigTabla(){
  const box = document.getElementById('vigTabla');
  if(!box) return;
  const q = (document.getElementById('vigBuscar')?.value || '').trim().toLowerCase();
  const tipo = document.getElementById('vigFiltroTipo')?.value || '';
  const rows = vigData.filter(v=>{
    if(vigFiltro !== 'todas' && v.estado !== vigFiltro) return false;
    if(tipo && v.tipo !== tipo) return false;
    if(!q) return true;
    return [v.nombre, v.proveedor, v.referencia, v.equipo_responsable, v.equipo_marca, v.equipo_modelo]
      .some(x => x && String(x).toLowerCase().includes(q));
  });
  if(!rows.length){
    box.innerHTML = `<div class="empty-state" style="padding:1.6rem;text-align:center">${vigData.length ? 'Ninguna vigencia coincide con el filtro.' : 'Aún no hay vigencias registradas. Usa “Nueva vigencia” para agregar la primera.'}</div>`;
    return;
  }
  box.innerHTML = `<table class="vgt">
    <thead><tr><th>Licencia</th><th>Tipo</th><th>Equipo</th><th>Vencimiento</th><th>Estado</th><th></th></tr></thead>
    <tbody>${rows.map(v=>`<tr>
      <td><div class="vg-name">${esc(v.nombre)}</div><div class="vg-sub">${[v.proveedor, v.referencia].filter(Boolean).map(esc).join(' · ') || '&nbsp;'}</div></td>
      <td><span class="vg-type">${v.tipo==='Hardware' ? I.chip : I.disk}${esc(v.tipo)}</span></td>
      <td>${v.equipo_id && v.equipo_responsable ? `<div class="vg-name">${esc(v.equipo_responsable)}</div><div class="vg-sub">${esc([v.equipo_marca, v.equipo_modelo].filter(Boolean).join(' '))}</div>` : '<span class="vg-sub">General</span>'}</td>
      <td class="vg-date"><div class="vg-name">${fmtFecha(v.fecha_vencimiento)}</div><div class="vg-sub">${esc(vigTextoDias(v))}</div></td>
      <td>${vigBadge(v)}</td>
      <td><div class="vg-actions">
        <button class="btn btn-ghost btn-sm" onclick="abrirModalVigencia(${v.id})" title="Editar">${I.gear}</button>
        <button class="btn btn-danger btn-sm" onclick="eliminarVigencia(${v.id})" title="Enviar a la papelera">${I.trash}</button>
      </div></td>
    </tr>`).join('')}</tbody></table>`;
}

async function abrirModalVigencia(id=null, equipoId=null){
  const sel = document.getElementById('vigEquipo');
  const eq = await api({action:'buscar_equipos'});
  sel.innerHTML = '<option value="">Sin equipo (general)</option>' + (eq.equipos||[]).map(e=>
    `<option value="${e.id}">${esc(e.responsable)||'Sin responsable'} — ${esc([e.marca,e.modelo].filter(Boolean).join(' '))||'Equipo'}${e.serial?' ('+esc(e.serial)+')':''}</option>`).join('');
  const set = (k,v)=>{ document.getElementById(k).value = v ?? ''; };
  set('vigId',''); set('vigTipo','Software'); set('vigEquipo', equipoId||''); set('vigNombre','');
  set('vigProveedor',''); set('vigReferencia',''); set('vigInicio',''); set('vigVence',''); set('vigObs','');
  document.getElementById('vigModalTitle').innerHTML = I.calendar + '<span>' + (id ? 'Editar vigencia' : 'Nueva vigencia') + '</span>';
  if(id){
    const r = await api({action:'get_vigencia', id});
    if(r.error){ toast(r.error, I.close); return; }
    const v = r.vigencia;
    set('vigId',v.id); set('vigTipo',v.tipo); set('vigEquipo',v.equipo_id||''); set('vigNombre',v.nombre);
    set('vigProveedor',v.proveedor); set('vigReferencia',v.referencia); set('vigInicio',v.fecha_inicio);
    set('vigVence',v.fecha_vencimiento); set('vigObs',v.observaciones);
  }
  document.getElementById('modalVigencia').style.display = 'flex';
}

function vigSumar(meses){
  // Cuenta desde la fecha de inicio (si la hay) o desde hoy
  const ini = document.getElementById('vigInicio').value;
  const base = ini ? new Date(ini + 'T00:00:00') : new Date();
  const dia = base.getDate();
  const f = new Date(base.getFullYear(), base.getMonth() + meses, 1);
  const ultimo = new Date(f.getFullYear(), f.getMonth() + 1, 0).getDate();
  f.setDate(Math.min(dia, ultimo));
  const p = n => String(n).padStart(2,'0');
  document.getElementById('vigVence').value = `${f.getFullYear()}-${p(f.getMonth()+1)}-${p(f.getDate())}`;
}

async function guardarVigencia(){
  const g = k => document.getElementById(k).value;
  const id = g('vigId');
  const body = {
    tipo: g('vigTipo'), equipo_id: g('vigEquipo'), nombre: g('vigNombre'), proveedor: g('vigProveedor'),
    referencia: g('vigReferencia'), fecha_inicio: g('vigInicio'), fecha_vencimiento: g('vigVence'), observaciones: g('vigObs')
  };
  if(!body.nombre.trim()){ toast('El nombre es obligatorio', I.close); return; }
  if(!body.fecha_vencimiento){ toast('Indica la fecha de vencimiento', I.close); return; }
  if(id) body.id = Number(id);
  const res = await api({action: id ? 'actualizar_vigencia' : 'crear_vigencia'}, body);
  if(res && res.error){ toast(res.error, I.close); return; }
  closeModal('modalVigencia');
  toast(id ? 'Vigencia actualizada' : 'Vigencia registrada');
  if(vigViewActiva && document.getElementById('vigTabla')) refreshVigencias();
  else if(activeUser) verUsuario(activeUser);
}

async function eliminarVigencia(id){
  if(!confirm('¿Enviar esta vigencia a la papelera?')) return;
  await api({action:'eliminar_vigencia'}, {id});
  toast('Vigencia enviada a la papelera', I.trash);
  refreshVigencias();
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

function papItemVigenciaHTML(v){
  return `<div class="pap-item">
    <div class="pap-item-ic">${I.calendar}</div>
    <div class="pap-item-body">
      <div class="pap-item-title">${esc(v.nombre)}</div>
      <div class="pap-item-meta">${esc(v.tipo)}${v.proveedor ? ' · '+esc(v.proveedor) : ''} · Vencía: ${fmtFecha(v.fecha_vencimiento)} · Eliminada: ${esc(v.deleted_at)}</div>
    </div>
    <div class="pap-item-actions">
      <button class="btn btn-primary btn-sm" onclick="restaurar('vigencia', ${v.id})" title="Restaurar">${I.refresh}</button>
      <button class="btn btn-danger btn-sm" onclick="borrarPermanente('vigencia', ${v.id})" title="Eliminar definitivamente">${I.trash}</button>
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

  if((d.vigencias||[]).length){
    html += papSeccionHTML(I.calendar, 'Vigencias', d.vigencias.map(papItemVigenciaHTML).join(''), d.vigencias.length);
  }

  const totalPapelera = (d.equipos||[]).length + (d.software||[]).length + (d.tareas||[]).length + (d.vigencias||[]).length;
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
  const permMap = { equipo:'delete_equipo_permanente', software:'delete_software_permanente', tarea:'delete_tarea_permanente', vigencia:'delete_vigencia_permanente' };
  const action = permMap[tipo];
  const res = await api({action}, {id});
  if(res && res.error){ toast(res.error, I.close); return; }
  toast('Elemento eliminado definitivamente', I.trash);
  abrirPapelera();
}

async function restaurar(tipo, id){
  const actionMap = { equipo: 'restore_equipo', software: 'restore_software', tarea: 'restore_tarea', vigencia: 'restore_vigencia' };
  const res = await api({action: actionMap[tipo]}, {id});
  if(res && res.error){ toast(res.error, I.close); return; }
  toast('Elemento restaurado', I.refresh);
  abrirPapelera();
  loadResponsables();
  if(activeUser) verUsuario(activeUser); else loadDashboard();
  if(tipo === 'tarea') refreshKanban();
  if(tipo === 'vigencia' && vigViewActiva) loadVigencias();
}

/* ============ TABLERO KANBAN (TAREAS) ============ */
async function loadKanban(){
  activeUser = null; vigViewActiva = false; marcarNav('btnTareas');
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
document.getElementById('btnImpresoras').innerHTML = I.printer + '<span>Impresoras</span>';
document.getElementById('btnVigencias').innerHTML = I.calendar + '<span>Vigencias</span>';
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