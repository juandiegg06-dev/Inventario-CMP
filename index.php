<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>InvCMP · Inventario de Equipos</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  /* Marca CMP */
  --c-yellow:#D9A21B;--c-lime:#8E93F2;--c-green:#9A86E8;--c-deep:#5A5FD3;
  /* Interfaz */
  --bg:#E9EBF4;--card:#F8F8FC;--soft:#F0F1F8;--soft-2:#E4E6F2;
  --line:#D8DBEA;--line-2:#C3C7DE;
  --ink:#1F2238;--ink-2:#474C6A;--mute:#6B7090;
  --raised:#FFFFFF;--hi:linear-gradient(130deg,#DAD6FF 0%,#EFD9F2 50%,#FFE2D2 100%);
  --accent:#CBC7FF;--accent-ink:#2A2563;--accent-soft:#E6E4FF;
  --ok:#2C64C4;--ok-soft:#DCE9FF;
  --warn:#8E5B00;--warn-soft:#FFF0C7;
  --bad:#BE3B37;--bad-soft:#FDE4E2;--bad-line:#F2C3BF;
  --r:12px;--r-lg:18px;--r-xl:24px;
  --sh-1:0 1px 2px rgba(31,34,56,.06);
  --sh-2:0 2px 4px rgba(31,34,56,.05),0 16px 34px -14px rgba(31,34,56,.22);
  --sh-3:0 30px 80px -24px rgba(31,34,56,.45);
  --ease:cubic-bezier(.22,1,.36,1);
  --font:'Plus Jakarta Sans',system-ui,-apple-system,'Segoe UI',sans-serif;
  --mono:'JetBrains Mono',ui-monospace,monospace;
  --topbar:68px;
}
@keyframes rise{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
@keyframes fade{from{opacity:0}to{opacity:1}}
@keyframes pop{from{opacity:0;transform:translateY(16px) scale(.98)}to{opacity:1;transform:none}}
@keyframes slideIn{from{transform:translateX(40px);opacity:0}to{transform:none;opacity:1}}
@keyframes grow{from{transform:scaleX(0)}to{transform:scaleX(1)}}

html{scroll-behavior:smooth}
body{font-family:var(--font);background:var(--bg);color:var(--ink);font-size:14.5px;line-height:1.5;-webkit-font-smoothing:antialiased;font-variant-numeric:tabular-nums;min-height:100vh;
  background-image:radial-gradient(900px 420px at 15% -180px,rgba(203,199,255,.55),transparent 70%),radial-gradient(800px 380px at 90% -160px,rgba(255,214,196,.5),transparent 70%);background-repeat:no-repeat;}
button,input,select,textarea{font-family:inherit;color:inherit}
.ic{display:inline-block;vertical-align:middle;flex-shrink:0}
*:focus-visible{outline:2px solid var(--c-deep);outline-offset:2px;border-radius:8px}
.view-fade{animation:rise .4s var(--ease)}

/* ================= Barra superior ================= */
.topbar{position:sticky;top:0;z-index:100;height:var(--topbar);background:rgba(233,235,244,.82);backdrop-filter:saturate(1.4) blur(14px);-webkit-backdrop-filter:saturate(1.4) blur(14px);border-bottom:1px solid var(--line);}
.topbar-in{max-width:1320px;height:100%;margin:0 auto;padding:0 1.75rem;display:flex;align-items:center;gap:1.25rem;}
.logo{display:flex;align-items:center;gap:.6rem;background:none;border:none;cursor:pointer;padding:.25rem;flex-shrink:0}
.logo-text{font-size:1.2rem;font-weight:800;letter-spacing:-.035em;color:var(--ink)}
.logo-text span{color:var(--c-deep)}

.nav{display:flex;align-items:center;gap:2px;background:var(--card);border:1px solid var(--line);border-radius:999px;padding:4px;margin:0 auto;box-shadow:var(--sh-1);}
.nav-item{display:flex;align-items:center;gap:.5rem;border:none;background:none;cursor:pointer;padding:.5rem .95rem;border-radius:999px;font-size:.85rem;font-weight:600;color:var(--ink-2);white-space:nowrap;transition:background .2s var(--ease),color .2s var(--ease);}
.nav-item .ic{width:17px;height:17px;color:var(--mute);transition:color .2s}
.nav-item:hover{background:var(--soft-2);color:var(--ink)}
.nav-item.on{background:var(--accent);color:var(--accent-ink)}
.nav-item.on .ic{color:var(--accent-ink)}

.top-actions{display:flex;align-items:center;gap:.5rem;flex-shrink:0}
.search-trigger{display:flex;align-items:center;gap:.55rem;height:40px;padding:0 .6rem 0 .85rem;border:1px solid var(--line);background:var(--card);border-radius:999px;color:var(--mute);font-size:.84rem;cursor:pointer;min-width:200px;transition:border-color .15s,box-shadow .15s}
.search-trigger:hover{border-color:var(--line-2);box-shadow:var(--sh-1)}
.search-trigger kbd{margin-left:auto;font-family:var(--mono);font-size:.7rem;background:var(--soft-2);border:1px solid var(--line);border-radius:6px;padding:.05rem .4rem;color:var(--ink-2)}
.icon-btn{width:40px;height:40px;border-radius:50%;border:1px solid var(--line);background:var(--card);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--ink-2);transition:background .15s,color .15s,border-color .15s,transform .15s var(--ease)}
.icon-btn:hover{color:var(--bad);border-color:var(--bad-line);background:var(--bad-soft)}
.icon-btn:active{transform:scale(.94)}
.icon-btn.only-m{display:none}
.icon-btn.only-m:hover{color:var(--ink);border-color:var(--line-2);background:var(--soft)}

/* ================= Contenedor y encabezado ================= */
.page{max-width:1320px;margin:0 auto;padding:2.4rem 1.75rem 4rem;}
.ph{display:flex;align-items:flex-end;justify-content:space-between;gap:1.5rem;margin-bottom:2rem;flex-wrap:wrap}
.ph-l{min-width:0}
.eyebrow{display:inline-flex;align-items:center;gap:.45rem;font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ink-2);background:var(--card);border:1px solid var(--line);padding:.3rem .7rem .3rem .55rem;border-radius:999px;margin-bottom:.85rem}
.eyebrow::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--accent);box-shadow:0 0 0 3px rgba(124,130,232,.25)}
.ph-title{font-size:2.5rem;font-weight:800;letter-spacing:-.045em;line-height:1.05;color:var(--ink);overflow-wrap:anywhere}
.ph-sub{font-size:.98rem;color:var(--mute);margin-top:.5rem;max-width:60ch}
.ph-r{display:flex;gap:.55rem;flex-wrap:wrap;align-items:center}
.back-link{display:inline-flex;align-items:center;gap:.4rem;background:none;border:none;cursor:pointer;color:var(--mute);font-size:.84rem;font-weight:600;padding:.25rem 0;margin-bottom:1rem;transition:color .15s,gap .2s var(--ease)}
.back-link:hover{color:var(--ink);gap:.6rem}

/* ================= Botones ================= */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.45rem;padding:.62rem 1.05rem;border-radius:999px;border:1px solid transparent;cursor:pointer;font-size:.84rem;font-weight:700;line-height:1.15;white-space:nowrap;transition:background .18s var(--ease),border-color .18s,color .18s,transform .15s var(--ease),box-shadow .18s;}
.btn:active{transform:scale(.97)}
.btn:disabled{opacity:.6;cursor:progress;transform:none}
.btn-primary{background:var(--ink);color:var(--bg)}
.btn-primary:hover{background:#363A5E;box-shadow:0 8px 20px -8px rgba(31,34,56,.5)}
.btn-accent{background:var(--accent);color:var(--accent-ink)}
.btn-accent:hover{background:#BAB5FF;box-shadow:0 8px 24px -8px rgba(124,130,232,.55)}
.btn-ghost{background:var(--card);color:var(--ink);border-color:var(--line)}
.btn-ghost:hover{border-color:var(--line-2);background:var(--soft)}
.btn-danger{background:var(--card);color:var(--bad);border-color:var(--bad-line)}
.btn-danger:hover{background:var(--bad-soft)}
.btn-sm{padding:.4rem .75rem;font-size:.78rem}
.btn-ic{padding:0;width:34px;height:34px}
.btn-ic.btn-sm{width:30px;height:30px}

/* ================= Tarjetas genéricas ================= */
.card{background:var(--card);border:1px solid var(--line);border-radius:var(--r-lg);}
.card-pad{padding:1.4rem 1.5rem}
.card-h{display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:1.1rem}
.card-t{display:flex;align-items:center;gap:.55rem;font-size:1rem;font-weight:800;letter-spacing:-.02em}
.card-t .ic{color:var(--c-deep)}
.empty-state{font-size:.86rem;color:var(--mute);padding:1rem;text-align:center;border:1.5px dashed var(--line-2);border-radius:var(--r);background:rgba(255,255,255,.45)}
.chip{display:inline-flex;align-items:center;gap:.35rem;font-size:.74rem;font-weight:700;padding:.25rem .65rem;border-radius:999px;background:var(--soft-2);color:var(--ink-2);white-space:nowrap}
.chip .ic{width:14px;height:14px}
.chip.king{background:#FFF0C2;color:#6E5100}
.chip.lime{background:var(--accent-soft);color:var(--accent-ink)}
.eq-count{font-size:.72rem;font-weight:700;color:var(--ink-2);background:var(--soft-2);padding:.15rem .6rem;border-radius:999px}
.eq-chevron{display:flex;color:var(--mute);transition:transform .22s var(--ease)}

/* ================= Kanban ================= */
.kanban-wrap{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.1rem;align-items:start}
.kanban-col{background:var(--card);border:1px solid var(--line);border-radius:var(--r-xl);padding:.6rem;display:flex;flex-direction:column;min-height:300px;transition:border-color .2s,background .2s,box-shadow .2s;animation:rise .45s var(--ease) both}
.kanban-col:nth-child(2){animation-delay:60ms}.kanban-col:nth-child(3){animation-delay:120ms}
.kanban-col.drag-over{border-color:var(--c-lime);background:var(--accent-soft);box-shadow:0 0 0 4px rgba(124,130,232,.18)}
.kanban-col-h{display:flex;align-items:center;gap:.55rem;padding:.7rem .7rem .9rem;font-size:.92rem;font-weight:800;letter-spacing:-.015em}
.kanban-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;box-shadow:0 0 0 4px color-mix(in srgb,var(--kc) 20%,transparent)}
.kanban-count{margin-left:auto;min-width:26px;text-align:center;font-size:.74rem;font-weight:800;padding:.15rem .5rem;border-radius:999px;background:var(--soft-2);color:var(--ink-2)}
.kanban-cards{display:flex;flex-direction:column;gap:.55rem;flex:1;background:var(--soft);border-radius:var(--r-lg);padding:.55rem;min-height:120px}
.kanban-cards .empty-state{border-color:var(--line);background:transparent;padding:1.4rem .5rem}
.kanban-card{position:relative;background:var(--card);border:1px solid var(--line);border-radius:14px;padding:.85rem .9rem .7rem;cursor:grab;overflow:hidden;box-shadow:var(--sh-1);transition:transform .2s var(--ease),box-shadow .2s var(--ease),border-color .2s,opacity .15s;animation:rise .35s var(--ease) both}
.kanban-card:hover{transform:translateY(-2px);box-shadow:var(--sh-2);border-color:var(--line-2)}
.kanban-card:active{cursor:grabbing}
.kanban-card.dragging{opacity:.45;transform:rotate(2deg)}
.kc-tag{display:block;width:34px;height:5px;border-radius:999px;background:var(--card-color);margin-bottom:.65rem}
.kc-title{font-size:.92rem;font-weight:700;line-height:1.35;letter-spacing:-.01em;overflow-wrap:anywhere}
.kc-desc{font-size:.8rem;color:var(--mute);line-height:1.5;margin-top:.3rem;white-space:pre-wrap;overflow-wrap:anywhere}
.kc-date{display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;font-weight:700;color:var(--ok);background:var(--ok-soft);padding:.18rem .55rem;border-radius:999px;margin-top:.55rem}
.kc-date .ic{width:12px;height:12px}
.kc-actions{display:flex;justify-content:space-between;align-items:center;margin-top:.7rem;opacity:.5;transition:opacity .2s}
.kanban-card:hover .kc-actions,.kanban-card:focus-within .kc-actions{opacity:1}
@media (hover:none){.kc-actions{opacity:1}}
.kc-move,.kc-right{display:flex;gap:.3rem}
.kc-btn{width:28px;height:28px;border-radius:50%;border:1px solid var(--line);background:var(--card);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--ink-2);transition:background .15s,color .15s,border-color .15s}
.kc-btn .ic{width:14px;height:14px}
.kc-btn:hover{background:var(--accent);border-color:var(--accent);color:var(--accent-ink)}
.kc-btn.danger:hover{background:var(--bad-soft);border-color:var(--bad-line);color:var(--bad)}
.kc-btn.spacer{visibility:hidden}
.kanban-add{margin-top:.55rem;width:100%;padding:.7rem;border:none;border-radius:var(--r);background:none;color:var(--mute);font-size:.84rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.4rem;transition:background .15s,color .15s}
.kanban-add:hover{background:var(--accent-soft);color:var(--accent-ink)}

/* ================= Usuarios ================= */
.users-search{position:relative;margin-bottom:2rem}
.users-search .sic{position:absolute;left:1.15rem;top:50%;transform:translateY(-50%);color:var(--mute);display:flex;pointer-events:none}
.users-search .sic .ic{width:20px;height:20px}
.users-search input{width:100%;height:58px;padding:0 1.2rem 0 3.2rem;border:1px solid var(--line);border-radius:999px;background:var(--card);font-size:1rem;outline:none;box-shadow:var(--sh-1);transition:border-color .15s,box-shadow .2s}
.users-search input:focus{border-color:var(--c-lime);box-shadow:0 0 0 5px rgba(124,130,232,.2)}
.u-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:.8rem}
.pc{animation:rise .4s var(--ease) both;position:relative;display:flex;align-items:center;gap:.85rem;background:var(--card);border:1px solid var(--line);border-radius:var(--r-lg);padding:1rem 1.05rem;cursor:pointer;text-align:left;width:100%;transition:transform .2s var(--ease),box-shadow .2s var(--ease),border-color .2s}
.pc:hover{transform:translateY(-3px);box-shadow:var(--sh-2);border-color:var(--line-2);background:var(--raised)}
.pc.active{border-color:var(--c-lime);box-shadow:0 0 0 3px rgba(124,130,232,.2)}
.av{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.86rem;letter-spacing:-.02em;flex-shrink:0;background:var(--av-bg,#E4F4C2);color:var(--av-fg,#2F5A00)}
.pc-body{min-width:0;flex:1}
.pc-name{font-weight:700;font-size:.93rem;letter-spacing:-.01em;display:flex;align-items:center;gap:.35rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pc-meta{font-size:.76rem;color:var(--mute);margin-top:.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pc-pills{display:flex;gap:.3rem;margin-top:.5rem;flex-wrap:wrap}
.pc-pill{font-size:.68rem;font-weight:700;padding:.12rem .5rem;border-radius:999px;background:var(--soft-2);color:var(--ink-2)}
.pc-arrow{position:absolute;top:.9rem;right:.9rem;color:var(--line-2);transition:color .2s,transform .2s var(--ease)}
.pc:hover .pc-arrow{color:var(--c-deep);transform:translate(2px,-2px)}
.badge-king{color:var(--c-yellow);display:inline-flex}

/* ================= Detalle de usuario ================= */
.profile{display:flex;align-items:center;gap:1.4rem;flex-wrap:wrap;background:var(--hi);border:1px solid var(--line-2);color:var(--ink);border-radius:var(--r-xl);padding:1.75rem 1.9rem;margin-bottom:1.6rem;position:relative;overflow:hidden;animation:rise .4s var(--ease)}
.profile::before{content:"";position:absolute;right:-120px;top:-140px;width:380px;height:380px;border-radius:50%;background:radial-gradient(circle,rgba(124,130,232,.35),transparent 65%);pointer-events:none}
.profile::after{content:"";position:absolute;right:120px;bottom:-160px;width:260px;height:260px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.55),transparent 65%);pointer-events:none}
.profile .av{width:76px;height:76px;font-size:1.5rem;background:var(--accent);color:var(--accent-ink);box-shadow:0 0 0 6px rgba(124,130,232,.18)}
.profile-body{flex:1;min-width:220px;position:relative;z-index:1}
.profile-name{font-size:2rem;font-weight:800;letter-spacing:-.04em;line-height:1.1;overflow-wrap:anywhere}
.profile-sub{color:var(--ink-2);font-size:.92rem;margin-top:.3rem}
.profile-tags{display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.8rem}
.profile .chip{background:rgba(255,255,255,.6);color:var(--ink)}
.profile .chip .ic{color:var(--c-deep)}
.profile .chip.king{background:#FFF0C2;color:#6E5100}
.profile .chip.king .ic{color:var(--c-yellow)}
.profile-actions{position:relative;z-index:1}

.eq-tabs{display:flex;gap:.5rem;overflow-x:auto;padding-bottom:.25rem;margin-bottom:1rem;scrollbar-width:thin}
.eq-tab{display:flex;align-items:center;gap:.65rem;padding:.6rem .95rem .6rem .65rem;border-radius:var(--r-lg);border:1px solid var(--line);background:var(--card);cursor:pointer;text-align:left;flex-shrink:0;transition:background .2s,border-color .2s,color .2s}
.eq-tab:hover{border-color:var(--line-2)}
.eq-tab .t-ic{width:34px;height:34px;border-radius:10px;background:var(--soft-2);color:var(--c-deep);display:flex;align-items:center;justify-content:center}
.eq-tab b{display:block;font-size:.84rem;font-weight:700;letter-spacing:-.01em}
.eq-tab small{display:block;font-size:.7rem;color:var(--mute);font-family:var(--mono)}
.eq-tab.on{background:var(--accent);border-color:var(--accent);color:var(--accent-ink)}
.eq-tab.on .t-ic{background:rgba(255,255,255,.55);color:var(--accent-ink)}
.eq-tab.on small{color:rgba(42,37,99,.65)}

.eq-panel{display:none}
.eq-panel.on{display:block;animation:rise .35s var(--ease)}
.eq-head{display:flex;align-items:center;gap:1rem;padding:1.25rem 1.4rem;border-bottom:1px solid var(--line)}
.eq-ic{width:52px;height:52px;border-radius:16px;background:var(--accent-soft);color:var(--c-deep);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.eq-ic .ic{width:24px;height:24px}
.eq-title{font-size:1.25rem;font-weight:800;letter-spacing:-.03em;line-height:1.2;overflow-wrap:anywhere}
.eq-sub{font-size:.76rem;color:var(--mute);font-family:var(--mono);margin-top:.2rem;overflow-wrap:anywhere}
.eq-head-r{margin-left:auto;display:flex;gap:.4rem;flex-shrink:0}
.eq-body{padding:1.3rem 1.4rem 1.5rem}
.spec-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.55rem}
.spec-c{background:var(--soft);border:1px solid transparent;border-radius:14px;padding:.8rem .9rem;transition:border-color .2s,background .2s}
.spec-c:hover{background:var(--raised);border-color:var(--line)}
.spec-k{display:flex;align-items:center;gap:.4rem;font-size:.72rem;font-weight:600;color:var(--mute);margin-bottom:.3rem}
.spec-k .ic{width:14px;height:14px;color:var(--c-green)}
.spec-v{font-size:.92rem;font-weight:700;letter-spacing:-.01em;word-break:break-word}
.note-box{display:flex;gap:.7rem;margin-top:.9rem;background:#FFF6D9;border:1px solid #F1DFA6;border-radius:14px;padding:.85rem 1rem;font-size:.86rem;color:#5E4600;line-height:1.55;white-space:pre-wrap}
.note-box .ic{color:var(--c-yellow);margin-top:.1rem}
.eq-cols{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;margin-top:1rem}
.sub-card{background:var(--card);border:1px solid var(--line);border-radius:var(--r-lg);padding:1.1rem 1.15rem;min-width:0}
.sec-label{display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-bottom:.8rem}
.sec-label-txt{display:flex;align-items:center;gap:.5rem;font-size:.9rem;font-weight:800;letter-spacing:-.015em}
.sec-label-txt .ic{color:var(--c-deep);width:17px;height:17px}
.periph-list{display:flex;flex-direction:column;gap:.45rem}
.periph-item{display:flex;align-items:center;gap:.7rem;padding:.55rem .6rem;border-radius:12px;background:var(--soft);transition:background .15s}
.periph-item:hover{background:var(--soft-2)}
.periph-ic{width:32px;height:32px;border-radius:10px;background:var(--raised);color:var(--c-deep);display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid var(--line)}
.periph-ic .ic{width:16px;height:16px}
.periph-main{flex:1;min-width:0}
.periph-name{font-size:.84rem;font-weight:700;overflow-wrap:anywhere}
.periph-sn{font-size:.7rem;color:var(--mute);font-family:var(--mono);overflow-wrap:anywhere}
.periph-actions{display:flex;gap:.25rem;flex-shrink:0}
.swt{width:100%;border-collapse:collapse;font-size:.83rem}
.swt td{padding:.5rem .35rem;border-bottom:1px solid var(--line)}
.swt tr:last-child td{border-bottom:none}
.swt td:nth-child(2){font-family:var(--mono);font-size:.72rem;color:var(--mute);white-space:nowrap}
.swt td:first-child{font-weight:600;overflow-wrap:anywhere}
.swt td.empty-state{border:1.5px dashed var(--line-2)}
.hidden-row{display:none}
.link-btn{background:none;border:none;cursor:pointer;color:var(--c-deep);font-size:.78rem;font-weight:700;display:inline-flex;align-items:center;gap:.25rem;padding:.4rem 0 0}
.link-btn:hover{color:var(--ink)}

/* ================= Estadísticas ================= */
.bento{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;margin-bottom:1rem}
.tile{position:relative;text-align:left;background:var(--card);border:1px solid var(--line);border-radius:var(--r-xl);padding:1.35rem 1.4rem;cursor:pointer;overflow:hidden;min-height:168px;display:flex;flex-direction:column;justify-content:space-between;transition:transform .22s var(--ease),box-shadow .22s var(--ease),border-color .2s;animation:rise .45s var(--ease) both}
.tile:nth-child(2){animation-delay:50ms}.tile:nth-child(3){animation-delay:100ms}.tile:nth-child(4){animation-delay:150ms}
.tile:hover{transform:translateY(-3px);box-shadow:var(--sh-2);border-color:var(--line-2)}
.tile-top{display:flex;align-items:center;justify-content:space-between}
.tile-ic{width:40px;height:40px;border-radius:12px;background:var(--soft-2);color:var(--c-deep);display:flex;align-items:center;justify-content:center}
.tile-go{color:var(--line-2);transition:color .2s,transform .2s var(--ease)}
.tile:hover .tile-go{color:var(--c-deep);transform:translate(2px,-2px)}
.tile-n{font-size:2.7rem;font-weight:800;letter-spacing:-.05em;line-height:1;margin-top:1.2rem}
.tile-l{font-size:.84rem;font-weight:600;color:var(--mute);margin-top:.35rem}
.tile.dark{background:var(--hi);border-color:var(--line-2);color:var(--ink)}
.tile.dark::after{content:"";position:absolute;right:-70px;bottom:-90px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(124,130,232,.32),transparent 65%)}
.tile.dark .tile-ic{background:rgba(255,255,255,.6);color:var(--c-deep)}
.tile.dark .tile-l{color:var(--ink-2)}
.tile.dark .tile-go{color:rgba(31,34,56,.35)}
.tile.dark:hover .tile-go{color:var(--c-deep)}
.tile.lime{background:#FFE0CF;border-color:#F7CDB6;color:#5C2C12}
.tile.lime .tile-ic{background:rgba(255,255,255,.6);color:#B2552A}
.tile.lime .tile-l{color:rgba(92,44,18,.75)}
.tile.lime .tile-go{color:rgba(92,44,18,.45)}
.vg-alert{display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;font-weight:700;color:var(--bad);background:var(--bad-soft);padding:.15rem .5rem;border-radius:999px;margin-top:.5rem;width:fit-content}
.vg-alert.vg-warn{color:var(--warn);background:var(--warn-soft)}
.comp-strip{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:.7rem;margin-bottom:1.6rem}
.kpi-card{display:flex;align-items:center;gap:.8rem;background:var(--card);border:1px solid var(--line);border-radius:var(--r-lg);padding:.9rem 1rem;cursor:pointer;text-align:left;transition:transform .2s var(--ease),box-shadow .2s,border-color .2s;animation:rise .45s var(--ease) both}
.kpi-card:hover{transform:translateY(-2px);box-shadow:var(--sh-2);border-color:var(--line-2)}
.kpi-ic{width:38px;height:38px;border-radius:50%;background:var(--accent-soft);color:var(--c-deep);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.kpi-n{font-size:1.4rem;font-weight:800;letter-spacing:-.035em;line-height:1}
.kpi-l{font-size:.76rem;color:var(--mute);font-weight:600;margin-top:.15rem}
.grid-2{display:grid;grid-template-columns:1.3fr 1fr;gap:1rem;align-items:start;margin-bottom:1rem}
.bar-row{display:grid;grid-template-columns:minmax(90px,150px) 1fr 40px;align-items:center;gap:.8rem;padding:.42rem 0}
.bar-label{font-size:.84rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.bar-track{height:10px;background:var(--soft-2);box-shadow:inset 0 0 0 1px var(--line);border-radius:999px;overflow:hidden}
.bar-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,#8E93F2,#C3A6F2 60%,#F6B7C9);transform-origin:left;animation:grow .8s var(--ease) both}
.bar-n{font-size:.86rem;font-weight:800;text-align:right}
.simple-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.7rem .15rem;border-bottom:1px solid var(--line);font-size:.86rem}
.simple-row:last-child{border-bottom:none}
.simple-row b{font-weight:700;min-width:0;overflow-wrap:anywhere}
.simple-row small{color:var(--mute);font-size:.76rem;text-align:right}

/* ================= Tablas ================= */
.vg-table-wrap{background:var(--card);border:1px solid var(--line);border-radius:var(--r-lg);overflow-x:auto}
.vgt{width:100%;border-collapse:separate;border-spacing:0;font-size:.86rem;min-width:720px}
.vgt th{text-align:left;padding:.85rem 1rem;color:var(--mute);font-weight:700;font-size:.72rem;letter-spacing:.04em;text-transform:uppercase;background:var(--soft);border-bottom:1px solid var(--line);white-space:nowrap}
.vgt td{padding:.85rem 1rem;border-bottom:1px solid var(--line);vertical-align:middle}
.vgt tbody tr:last-child td{border-bottom:none}
.vgt tbody tr{transition:background .15s}
.vgt tbody tr:hover td{background:var(--soft)}
.vgt td.num,.vgt th.num{text-align:right;white-space:nowrap;padding-left:.6rem;padding-right:.6rem}
.vg-name{font-weight:700;letter-spacing:-.01em}
.vg-sub{font-size:.75rem;color:var(--mute);margin-top:.12rem;overflow-wrap:anywhere}
.vg-type{display:inline-flex;align-items:center;gap:.4rem;font-size:.82rem;font-weight:600;white-space:nowrap}
.vg-type .ic{color:var(--c-deep);width:16px;height:16px}
.vg-date{white-space:nowrap}
.vg-actions{display:flex;gap:.3rem;justify-content:flex-end}
.vg-badge{display:inline-flex;align-items:center;gap:.4rem;font-size:.74rem;font-weight:700;padding:.25rem .65rem;border-radius:999px;white-space:nowrap;background:var(--ok-soft);color:var(--ok)}
.vg-badge.por_vencer{background:var(--warn-soft);color:var(--warn)}
.vg-badge.vencida{background:var(--bad-soft);color:var(--bad)}
.vg-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;background:var(--mute)}
.vg-dot.vigente{background:#5B8FEA}
.vg-dot.por_vencer{background:#E9A93A}
.vg-dot.vencida{background:#E5484D}
.imp-total-row td{background:var(--accent-soft) !important;font-weight:700}
.imp-hist th.num.g{text-align:center}
.imp-hist .imp-grp th{border-bottom:1px solid var(--line)}
.imp-hist td{font-size:.82rem}

/* ================= Vigencias ================= */
.vg-tiles{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.8rem;margin-bottom:1.2rem}
.vg-tile{position:relative;text-align:left;background:var(--card);border:1px solid var(--line);border-radius:var(--r-lg);padding:1.1rem 1.2rem;cursor:pointer;display:flex;flex-direction:column;gap:.35rem;transition:border-color .2s,background .2s,transform .2s var(--ease),box-shadow .2s}
.vg-tile:hover{transform:translateY(-2px);box-shadow:var(--sh-2)}
.vg-tile.active{background:var(--accent);border-color:var(--accent);color:var(--accent-ink)}
.vg-tile.active .vg-tile-l{color:rgba(42,37,99,.75)}
.vg-tile-n{font-size:2.1rem;font-weight:800;letter-spacing:-.045em;line-height:1}
.vg-tile-l{font-size:.8rem;font-weight:600;color:var(--mute);display:flex;align-items:center;gap:.45rem}
.toolbar{display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;margin-bottom:1rem}
.field-ic{position:relative;flex:1;min-width:220px}
.field-ic .sic{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--mute);display:flex;pointer-events:none}
.si{width:100%;height:44px;padding:0 1rem 0 2.6rem;border:1px solid var(--line);border-radius:999px;background:var(--card);font-size:.88rem;outline:none;transition:border-color .15s,box-shadow .15s}
.si:focus{border-color:var(--c-lime);box-shadow:0 0 0 4px rgba(124,130,232,.2)}
.select-pill{height:44px;padding:0 2.2rem 0 1rem;border:1px solid var(--line);border-radius:999px;background:var(--card) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236B7090' stroke-width='2.4' stroke-linecap='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right .9rem center;font-size:.86rem;font-weight:600;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;max-width:100%}
.select-pill:focus{border-color:var(--c-lime);box-shadow:0 0 0 4px rgba(124,130,232,.2)}
.vg-quick{display:flex;flex-wrap:wrap;align-items:center;gap:.4rem;margin:-.1rem 0 .8rem}
.vg-quick-l{font-size:.76rem;font-weight:700;color:var(--ink-2);margin-right:.2rem}
.vg-chip{border:1px solid var(--line);background:var(--card);border-radius:999px;padding:.3rem .8rem;font-size:.76rem;font-weight:700;cursor:pointer;transition:background .15s,border-color .15s,color .15s}
.vg-chip:hover{background:var(--accent);border-color:var(--accent);color:var(--accent-ink)}

/* ================= Impresoras ================= */
.imp-bar{display:flex;gap:.55rem;align-items:center;flex-wrap:wrap;background:var(--card);border:1px solid var(--line);border-radius:999px;padding:.4rem;margin-bottom:1rem}
.imp-bar .select-pill{flex:1;min-width:200px;border-color:transparent;background-color:var(--soft)}
.auto-box{display:flex;align-items:center;gap:.45rem;margin-left:auto;flex-wrap:wrap}
.auto-l{display:inline-flex;align-items:center;gap:.4rem;font-size:.8rem;font-weight:700;color:var(--ink-2);padding-left:.4rem}
.auto-l .ic{color:var(--c-deep)}
.auto-box .select-pill{height:38px;font-size:.82rem;min-width:170px}
.auto-custom{align-items:center;gap:.35rem}
.auto-custom input{width:76px;height:38px;border:1px solid var(--line);border-radius:999px;background:var(--card);padding:0 .8rem;font-size:.84rem;font-weight:600;outline:none}
.auto-custom input:focus{border-color:var(--c-lime);box-shadow:0 0 0 4px rgba(124,130,232,.2)}
.auto-u{font-size:.78rem;color:var(--mute);font-weight:600}
.imp-next{display:flex;align-items:center;gap:.45rem;font-size:.8rem;color:var(--ink-2);margin:-.4rem 0 1rem .4rem}
.imp-next:empty{display:none}
.imp-next .ic{color:var(--c-deep);width:15px;height:15px}
.switch{display:flex;align-items:center;gap:.55rem;font-size:.82rem;font-weight:600;color:var(--ink-2);margin-left:auto;padding:0 .7rem;cursor:pointer;user-select:none}
.switch input{position:absolute;opacity:0;width:1px;height:1px}
.switch .track{width:38px;height:22px;border-radius:999px;background:var(--line-2);position:relative;transition:background .2s;flex-shrink:0}
.switch .track::after{content:"";position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:transform .25s var(--ease)}
.switch input:checked + .track{background:var(--c-deep)}
.switch input:checked + .track::after{transform:translateX(16px)}
.switch input:focus-visible + .track{outline:2px solid var(--c-deep);outline-offset:2px}
.imp-last{display:inline-flex;align-items:center;gap:.5rem;font-size:.82rem;color:var(--mute);margin-bottom:.9rem;background:var(--card);border:1px solid var(--line);padding:.35rem .8rem;border-radius:999px}
.imp-last .ic{color:var(--c-deep);width:15px;height:15px}
.imp-last b{color:var(--ink)}
.imp-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.9rem;margin-bottom:1.8rem}
.imp-card{background:var(--card);border:1px solid var(--line);border-radius:var(--r-xl);padding:1.15rem 1.25rem;animation:rise .4s var(--ease) both}
.imp-card:last-child{background:var(--hi);border-color:var(--line-2);color:var(--ink)}
.imp-card:last-child .imp-row{color:var(--ink-2);border-color:rgba(31,34,56,.1)}
.imp-card:last-child .imp-row b{color:var(--ink)}
.imp-card:last-child .imp-row:first-of-type b{color:var(--c-deep);font-size:1.7rem}
.imp-card:last-child .imp-card-h .ic{color:var(--c-deep);background:rgba(255,255,255,.6)}
.imp-card-h{display:flex;align-items:center;gap:.6rem;font-size:.92rem;font-weight:800;letter-spacing:-.015em;margin-bottom:.6rem}
.imp-card-h .ic{width:32px;height:32px;padding:7px;border-radius:10px;background:var(--accent-soft);color:var(--c-deep)}
.imp-row{display:flex;align-items:baseline;justify-content:space-between;gap:.5rem;padding:.48rem 0;border-top:1px solid var(--line);font-size:.8rem;color:var(--mute)}
.imp-row:first-of-type{border-top:none}
.imp-row b{font-size:1.15rem;color:var(--ink);font-weight:800;letter-spacing:-.03em}
.imp-err{background:var(--bad-soft);color:var(--bad);border:1px solid var(--bad-line);border-radius:var(--r-lg);padding:.9rem 1.1rem;font-size:.86rem;margin-bottom:1rem;line-height:1.5}
.imp-err pre{white-space:pre-wrap;word-break:break-word;font-size:.72rem;margin-top:.5rem;max-height:160px;overflow:auto;color:var(--ink);background:var(--bg);padding:.55rem;border-radius:8px;font-family:var(--mono)}
.section-h{display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin:0 0 .8rem}
.section-h .card-t{font-size:1.05rem}

/* ================= Papelera ================= */
.pap-sec{border:1px solid var(--line);border-radius:var(--r-lg);margin-bottom:.6rem;overflow:hidden;background:var(--card)}
.pap-sec-sum{display:flex;align-items:center;gap:.6rem;padding:.85rem 1rem;cursor:pointer;list-style:none;font-weight:700;font-size:.9rem;transition:background .15s}
.pap-sec-sum:hover{background:var(--soft)}
.pap-sec-sum > .ic{color:var(--c-deep)}
.pap-sec-sum::-webkit-details-marker{display:none}
.pap-sec-sum .eq-count{margin-left:auto}
.pap-sec[open] .pap-sec-sum .eq-chevron{transform:rotate(180deg)}
.pap-sec-body{padding:.2rem .7rem .75rem;display:flex;flex-direction:column;gap:.45rem;animation:fade .2s}
.pap-item{display:flex;align-items:center;gap:.7rem;background:var(--soft);border-radius:12px;padding:.7rem .75rem}
.pap-item-ic{width:34px;height:34px;border-radius:10px;background:var(--raised);border:1px solid var(--line);color:var(--mute);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.pap-item-body{flex:1;min-width:0}
.pap-item-title{font-size:.86rem;font-weight:700;overflow-wrap:anywhere}
.pap-item-meta{font-size:.72rem;color:var(--mute);margin-top:.15rem;overflow-wrap:anywhere}
.pap-item-actions{display:flex;gap:.3rem;flex-shrink:0}
.pap-sw-detail{margin-top:.45rem}
.pap-sw-detail summary{cursor:pointer;list-style:none;font-size:.74rem;font-weight:700;color:var(--c-deep);display:inline-flex;align-items:center;gap:.25rem}
.pap-sw-detail summary::-webkit-details-marker{display:none}
.pap-sw-detail summary .ic{width:14px;height:14px;transition:transform .2s var(--ease)}
.pap-sw-detail[open] summary .ic{transform:rotate(180deg)}
.pap-sw-list{margin-top:.45rem;max-height:180px;overflow-y:auto;display:flex;flex-direction:column;gap:.22rem;padding-right:.3rem}
.pap-sw-row{font-size:.74rem;background:var(--raised);border:1px solid var(--line);border-radius:8px;padding:.3rem .55rem}

/* ================= Modales ================= */
.modal-bg{position:fixed;inset:0;background:rgba(31,34,56,.4);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);z-index:500;display:flex;align-items:center;justify-content:center;padding:1.25rem;animation:fade .2s ease}
.modal{background:var(--card);border-radius:var(--r-xl);width:100%;max-width:620px;max-height:88vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:var(--sh-3);animation:pop .3s var(--ease)}
.modal-head{padding:1.5rem 1.6rem 1.1rem;flex-shrink:0}
.modal h3{font-size:1.3rem;font-weight:800;letter-spacing:-.035em;display:flex;align-items:center;gap:.65rem}
.modal h3 .ic{width:38px;height:38px;padding:9px;border-radius:12px;background:var(--accent);color:var(--accent-ink)}
.modal-sub{font-size:.84rem;color:var(--mute);margin-top:.45rem;line-height:1.55}
.modal-body{padding:.3rem 1.6rem 1.4rem;overflow-y:auto;flex:1;min-height:0}
.modal-tools{display:flex;gap:.5rem;margin-top:1rem}
.modal-footer{display:flex;justify-content:flex-end;gap:.5rem;padding:1rem 1.6rem;border-top:1px solid var(--line);background:var(--soft);flex-shrink:0}
.modal-footer .btn-danger{margin-right:auto}
/* Panel lateral para formularios largos */
.modal-bg.drawer{justify-content:flex-end;padding:0}
.modal-bg.drawer .modal{max-width:580px;height:100vh;max-height:none;border-radius:var(--r-xl) 0 0 var(--r-xl);animation:slideIn .35s var(--ease)}

fieldset{border:none;padding:0;margin-bottom:1.35rem}
fieldset:last-child{margin-bottom:0}
legend{font-size:.72rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:var(--ink-2);margin-bottom:.7rem;display:flex;align-items:center;gap:.55rem;width:100%}
legend::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--accent)}
legend::after{content:"";flex:1;height:1px;background:var(--line)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:.15rem .8rem}
.form-grid.c3{grid-template-columns:1fr 1fr 1fr}
.form-grid > *{min-width:0}
.fg{display:flex;flex-direction:column;gap:.3rem;margin-bottom:.65rem}
.fg.full{grid-column:1/-1}
.fg label{font-size:.76rem;font-weight:700;color:var(--ink-2)}
.fg input,.fg select,.fg textarea{width:100%;min-width:0;padding:.65rem .8rem;border:1px solid var(--line);border-radius:var(--r);font-size:.88rem;outline:none;background:var(--soft);transition:border-color .15s,box-shadow .15s,background .15s}
.fg input:hover,.fg select:hover,.fg textarea:hover{border-color:var(--line-2)}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--c-lime);background:var(--raised);box-shadow:0 0 0 4px rgba(124,130,232,.2)}
.fg input[readonly]{color:var(--mute)}
.fg textarea{resize:vertical;min-height:76px}
.color-picker{display:flex;gap:.6rem;margin-top:.2rem}
.color-swatch{width:30px;height:30px;border-radius:50%;border:3px solid var(--card);box-shadow:0 0 0 1px var(--line-2);cursor:pointer;padding:0;transition:transform .15s var(--ease),box-shadow .15s}
.color-swatch:hover{transform:scale(1.12)}
.color-swatch.active{box-shadow:0 0 0 2px var(--ink);transform:scale(1.08)}

/* ================= Toast ================= */
.toast{position:fixed;bottom:1.5rem;left:50%;background:#262A48;border:1px solid #262A48;color:#fff;padding:.75rem 1.15rem .75rem .9rem;border-radius:999px;transform:translate(-50%,calc(100% + 2rem));opacity:0;visibility:hidden;transition:transform .35s var(--ease),opacity .25s,visibility .35s;z-index:999;display:flex;align-items:center;gap:.55rem;font-size:.86rem;font-weight:600;box-shadow:var(--sh-3);max-width:calc(100vw - 2rem)}
.toast .ic{color:var(--accent-ink);background:var(--accent);border-radius:50%;padding:3px;width:22px;height:22px}
.toast.show{transform:translate(-50%,0);opacity:1;visibility:visible}

/* ================= Requisiciones ================= */
.seg{display:inline-flex;gap:3px;background:var(--card);border:1px solid var(--line);border-radius:999px;padding:4px;margin-bottom:1.2rem}
.seg button{border:none;background:none;cursor:pointer;padding:.5rem 1rem;border-radius:999px;font-size:.84rem;font-weight:700;color:var(--ink-2);display:inline-flex;align-items:center;gap:.45rem;transition:background .2s var(--ease),color .2s}
.seg button:hover{background:var(--soft-2)}
.seg button.on{background:var(--accent);color:var(--accent-ink)}
.seg .eq-count{background:rgba(255,255,255,.6)}
.req-sheet{padding:0;overflow:hidden;animation:rise .4s var(--ease)}
.req-head{display:grid;grid-template-columns:150px 1fr 230px;border-bottom:1px solid var(--line);background:var(--soft)}
.req-logo{display:flex;align-items:center;justify-content:center;border-right:1px solid var(--line);padding:.4rem;background:#fff}
.req-logo img{height:74px;width:auto}
.req-tit{display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;gap:.15rem;padding:.6rem;border-right:1px solid var(--line)}
.req-tit small{font-size:.72rem;font-weight:600;color:var(--mute);letter-spacing:.04em}
.req-tit b{font-size:1.05rem;font-weight:800;letter-spacing:-.02em}
.req-meta{display:grid;grid-template-columns:auto 1fr;font-size:.74rem;align-content:center}
.req-meta span{padding:.28rem .7rem;border-bottom:1px solid var(--line)}
.req-meta span:nth-child(odd){font-weight:800;color:var(--ink-2)}
.req-meta span:nth-last-child(-n+2){border-bottom:none}
.req-body{padding:1.4rem 1.5rem 1.5rem}
.req-top{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.8rem 1rem;margin-bottom:1rem}
.req-top .span2{grid-column:span 2}.req-top .span4{grid-column:1/-1}
.rf{display:flex;flex-direction:column;gap:.3rem;min-width:0}
.rf label{font-size:.68rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#8A4A22;background:#FFE6D6;border-radius:6px;padding:.18rem .5rem;width:fit-content}
.rf input,.rf select,.rf textarea,.req-items input,.req-items select,.req-items textarea{width:100%;min-width:0;padding:.6rem .75rem;border:1px solid var(--line);border-radius:10px;background:var(--soft);font-size:.86rem;outline:none;transition:border-color .15s,box-shadow .15s,background .15s}
.rf input:focus,.rf select:focus,.rf textarea:focus,.req-items input:focus,.req-items select:focus,.req-items textarea:focus{border-color:var(--c-lime);background:#fff;box-shadow:0 0 0 4px rgba(124,130,232,.18)}
.rf input[readonly]{color:var(--ink-2);background:var(--soft-2);border-color:transparent}
.req-code{display:flex;align-items:center;gap:.6rem;flex-wrap:wrap}
.req-code b{font-family:var(--mono);font-size:1.15rem;font-weight:500;letter-spacing:.02em;background:#E5F2DC;color:#2C4A1E;border:1px solid #CFE5BF;border-radius:10px;padding:.42rem .8rem}
.req-code b i{font-style:normal;color:#7A9A66}
.req-code small{font-size:.74rem;color:var(--mute)}
.req-items-wrap{position:relative;border:1px solid var(--line);border-radius:14px;overflow-x:auto;margin:.4rem 0 .6rem}
.req-items{width:100%;border-collapse:collapse;min-width:920px}
.req-items th{font-size:.66rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#8A4A22;background:#FFE6D6;padding:.55rem .5rem;text-align:left;white-space:nowrap}
.req-items td{padding:.28rem .3rem;border-top:1px solid var(--line);vertical-align:top}
.req-items td:first-child{width:42px;text-align:center;font-weight:800;color:var(--mute);font-size:.8rem;padding-top:.75rem}
.req-items input,.req-items select,.req-items textarea{padding:.48rem .55rem;font-size:.82rem;border-radius:8px;background:transparent;border-color:transparent}
.req-items tr:hover input,.req-items tr:hover select,.req-items tr:hover textarea{background:var(--soft);border-color:var(--line)}
.req-items textarea{resize:none;min-height:34px;line-height:1.35;overflow:hidden}
.req-items tr.lleno td:first-child{color:var(--c-deep)}
.req-nota{background:#EAF4E2;border:1px solid #D6EAC6;color:#3B5530;border-radius:12px;padding:.65rem .9rem;font-size:.78rem;font-style:italic;line-height:1.5;margin-bottom:1.1rem}
.req-firmas{display:grid;grid-template-columns:1.1fr 1fr 1fr;gap:.8rem;margin-top:1.1rem}
.req-firma{border:1px solid var(--line);border-radius:14px;padding:.85rem .95rem;background:var(--soft)}
.req-firma h4{font-size:.74rem;font-weight:800;letter-spacing:.07em;text-transform:uppercase;margin-bottom:.6rem;color:var(--ink-2)}
.req-firma .rf{margin-bottom:.5rem}
.req-firma .rf input{background:#fff}
.firma-datos{display:grid;grid-template-columns:auto 1fr;gap:.45rem .8rem;font-size:.84rem;margin-bottom:.4rem}
.firma-datos dt{font-size:.66rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#8A4A22;background:#FFE6D6;border-radius:6px;padding:.18rem .5rem;height:fit-content;align-self:center}
.firma-datos dd{font-weight:600;color:var(--ink);align-self:center}
.firma-fija{display:flex;align-items:center;gap:.35rem;font-size:.72rem;color:var(--mute);margin-top:.2rem}
.firma-fija .ic{width:13px;height:13px;color:var(--c-deep)}
.req-firma.vacia{display:flex;flex-direction:column;justify-content:space-between;min-height:120px}
.req-firma.vacia p{font-size:.76rem;color:var(--mute);border-top:1px dashed var(--line-2);padding-top:.5rem;text-align:center}
.rf-hint{font-size:.7rem;color:var(--mute)}
.req-items td:last-child{padding-top:.45rem;text-align:center}
.req-del{width:28px;height:28px;border-radius:50%;border:1px solid transparent;background:none;color:var(--mute);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:background .15s,color .15s,border-color .15s}
.req-del:hover{background:var(--bad-soft);color:var(--bad);border-color:var(--bad-line)}
.req-add-bar{display:flex;align-items:center;gap:.8rem;margin:-.1rem 0 .9rem}
.req-add{display:inline-flex;align-items:center;gap:.45rem;border:1.5px dashed var(--line-2);background:var(--card);color:var(--ink-2);border-radius:999px;padding:.5rem 1rem;font-size:.82rem;font-weight:700;cursor:pointer;transition:border-color .15s,background .15s,color .15s}
.req-add:hover:not(:disabled){border-color:var(--c-lime);background:var(--accent-soft);color:var(--accent-ink)}
.req-add:disabled{opacity:.5;cursor:not-allowed}
.req-add-bar small{font-size:.76rem;color:var(--mute)}
.sr-only{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
.hist-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column}
.hist-item{display:flex;gap:.75rem;padding:.75rem 0;border-bottom:1px solid var(--line)}
.hist-item:last-child{border-bottom:none}
.hist-ic{width:32px;height:32px;border-radius:9px;background:var(--soft);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--accent-ink)}
.hist-main{flex:1;min-width:0}
.hist-top{display:flex;align-items:center;gap:.45rem;flex-wrap:wrap;font-size:.86rem}
.hist-fecha{margin-left:auto;font-size:.74rem;color:var(--mute);white-space:nowrap}
.hist-tag{font-size:.68rem;font-weight:700;padding:.1rem .5rem;border-radius:999px;background:var(--soft-2);color:var(--ink-2)}
.hist-det{font-size:.8rem;color:var(--ink-2);margin-top:.3rem;line-height:1.5;overflow-wrap:anywhere}
.hist-cambio span{color:var(--mute)}
.hist-vacio{color:var(--mute)}
.req-edit-bar{display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;margin-bottom:.8rem;padding:.7rem 1rem;border:1px solid var(--line);border-left:4px solid var(--accent);border-radius:10px;background:var(--soft);font-size:.84rem}
.req-edit-bar span{flex:1;min-width:200px}
.req-acciones{display:flex;justify-content:flex-end;align-items:center;gap:.55rem;padding:1rem 1.5rem;border-top:1px solid var(--line);background:var(--soft);flex-wrap:wrap}
.req-acciones .hint{margin-right:auto;font-size:.78rem;color:var(--mute)}
.req-prio{display:inline-flex;align-items:center;gap:.35rem;font-size:.72rem;font-weight:800;padding:.2rem .6rem;border-radius:999px;background:var(--soft-2);color:var(--ink-2)}
.req-prio.URGENTE{background:var(--bad-soft);color:var(--bad)}
.req-prio.ALTA{background:#FFE6D6;color:#8A4A22}
.req-prio.MEDIA{background:var(--warn-soft);color:var(--warn)}
.req-prio.BAJA{background:var(--ok-soft);color:var(--ok)}
.req-n{font-family:var(--mono);font-size:1.05rem;font-weight:500;color:var(--c-deep)}

/* ================= Listado IP ================= */
.ip-sec{margin-bottom:1.8rem;animation:rise .4s var(--ease) both}
.ip-sec-h{display:flex;align-items:baseline;justify-content:space-between;gap:.8rem;margin-bottom:.7rem;flex-wrap:wrap}
.ip-sec-h h2{font-size:1.1rem;font-weight:800;letter-spacing:-.025em;display:flex;align-items:center;gap:.55rem}
.ip-sec-h h2 .ic{color:var(--c-deep)}
.ip-sec-h small{font-size:.8rem;color:var(--mute)}
.ip{font-family:var(--mono);font-size:.86rem;font-weight:500;color:var(--ink);background:var(--soft-2);border:1px solid var(--line);border-radius:8px;padding:.22rem .55rem;cursor:pointer;white-space:nowrap;transition:background .15s,border-color .15s}
.ip:hover{background:var(--accent-soft);border-color:var(--c-lime)}
.ip-sin{color:var(--mute);font-style:italic}
.ip-estado{display:inline-flex;align-items:center;gap:.35rem;font-size:.74rem;font-weight:700;padding:.2rem .6rem;border-radius:999px;background:var(--ok-soft);color:var(--ok)}
.ip-estado::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.ip-estado.otro{background:var(--warn-soft);color:var(--warn)}
.ip-abrir{display:inline-flex;align-items:center;gap:.35rem;text-decoration:none;font-size:.78rem;font-weight:700;color:var(--accent-ink);background:var(--accent-soft);border:1px solid transparent;border-radius:999px;padding:.35rem .75rem;white-space:nowrap;transition:background .15s,border-color .15s}
.ip-abrir:hover{background:var(--accent);border-color:var(--c-lime)}
.ip-n{color:var(--mute);font-weight:700;font-size:.8rem}
.ip-ssid{font-size:.78rem;color:var(--mute);white-space:nowrap}
.ip-sec .vgt td{white-space:nowrap}

/* ================= Responsive ================= */
@media (max-width:1180px){
  .search-trigger{min-width:0}
  .search-trigger .st-txt,.search-trigger kbd{display:none}
  .search-trigger{width:40px;padding:0;justify-content:center;border-radius:50%}
  .bento{grid-template-columns:repeat(2,minmax(0,1fr))}
  .imp-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
  .eq-cols{grid-template-columns:1fr 1fr}
  .eq-cols .sub-card:last-child{grid-column:1/-1}
}
@media (max-width:980px){
  .nav-item{padding:.5rem .7rem}
  .nav-item span{display:none}
  .nav-item.on span{display:inline}
  .kanban-wrap{grid-template-columns:1fr}
  .kanban-col{min-height:0}
  .grid-2{grid-template-columns:1fr}
  .vg-tiles{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width:760px){
  :root{--topbar:60px}
  /* sin desenfoque: el filtro convertiría la barra en contenedor de la navegación fija */
  .topbar{backdrop-filter:none;-webkit-backdrop-filter:none;background:var(--bg)}
  .topbar-in{padding:0 1rem;gap:.6rem}
  .top-actions{margin-left:auto}
  .icon-btn.only-m{display:flex}
  .search-trigger{display:none}
  /* Navegación inferior tipo app */
  .nav{position:fixed;left:.75rem;right:.75rem;bottom:.75rem;margin:0;justify-content:space-between;padding:5px;border-radius:22px;box-shadow:var(--sh-3);z-index:120}
  .nav{overflow-x:auto;scrollbar-width:none;justify-content:flex-start}
  .nav::-webkit-scrollbar{display:none}
  .nav-item{flex:1 0 auto;min-width:62px;flex-direction:column;gap:.15rem;padding:.45rem .35rem;border-radius:17px;font-size:.64rem}
  .nav-item span,.nav-item.on span{display:block}
  .nav-item .ic{width:19px;height:19px}
  .page{padding:1.5rem 1rem 7rem}
  .ph{margin-bottom:1.4rem;align-items:flex-start}
  .ph-title{font-size:1.85rem}
  .ph-r{width:100%}
  .ph-r .btn{flex:1}
  .profile{padding:1.3rem;gap:1rem}
  .profile .av{width:60px;height:60px;font-size:1.2rem}
  .profile-name{font-size:1.5rem}
  .profile-actions,.profile-actions .btn{width:100%}
  .eq-head{flex-wrap:wrap;padding:1rem}
  .eq-body{padding:1rem}
  .eq-cols{grid-template-columns:1fr}
  .eq-cols .sub-card:last-child{grid-column:auto}
  .spec-grid{grid-template-columns:1fr 1fr}
  .bento{gap:.7rem}
  .tile{min-height:140px;padding:1.1rem}
  .tile-n{font-size:2.2rem}
  .comp-strip{grid-template-columns:1fr 1fr}
  .imp-grid{grid-template-columns:1fr}
  .imp-bar{border-radius:var(--r-lg)}
  .switch{margin-left:0}
  .auto-box{margin-left:0;width:100%}
  .auto-box .select-pill{flex:1;min-width:0}
  .auto-custom{width:100%}
  .auto-custom input{flex:1}
  .form-grid,.form-grid.c3{grid-template-columns:1fr}
  .modal-bg{align-items:flex-end;padding:0}
  .modal,.modal-bg.drawer .modal{max-width:none;height:auto;max-height:92vh;border-radius:var(--r-xl) var(--r-xl) 0 0;animation:pop .3s var(--ease)}
  .modal-head,.modal-body,.modal-footer{padding-left:1.15rem;padding-right:1.15rem}
  .toast{bottom:6.2rem}
  .bar-row{grid-template-columns:minmax(80px,110px) 1fr 34px}
  .u-grid{grid-template-columns:1fr}
  .req-head{grid-template-columns:1fr}
  .req-logo,.req-tit{border-right:none;border-bottom:1px solid var(--line)}
  .req-body{padding:1rem}
  .req-top{grid-template-columns:1fr 1fr}
  .req-top .span2,.req-top .span4{grid-column:1/-1}
  .req-firmas{grid-template-columns:1fr}
  .req-acciones{padding:1rem}
  .req-acciones .hint{width:100%}
  .req-acciones .btn{flex:1}
}
@media (prefers-reduced-motion:reduce){
  *,*::before,*::after{animation-duration:.01ms !important;animation-iteration-count:1 !important;transition-duration:.01ms !important;scroll-behavior:auto !important}
}
</style>
</head>
<body>

<header class="topbar">
  <div class="topbar-in">
    <button class="logo" onclick="loadKanban()" aria-label="Ir al inicio">
      <svg width="30" height="30" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M20 3C20 3 6 18 6 27a14 14 0 0 0 28 0C34 18 20 3 20 3Z" fill="#CED800"/>
        <path d="M20 9C20 9 10 20 10 27a10 10 0 0 0 20 0c0-7-10-18-10-18Z" fill="#94D500"/>
        <path d="M20 15C20 15 14 22 14 27a6 6 0 0 0 12 0c0-5-6-12-6-12Z" fill="#3FAF29"/>
        <circle cx="20" cy="28" r="3" fill="#00953A"/>
      </svg>
      <span class="logo-text">Inv<span>CMP</span></span>
    </button>

    <nav class="nav" aria-label="Secciones">
      <button class="nav-item" id="btnTareas" onclick="loadKanban()"><span>Tareas</span></button>
      <button class="nav-item" id="btnUsuarios" onclick="loadUsuarios()"><span>Usuarios</span></button>
      <button class="nav-item" id="btnImpresoras" onclick="loadImpresoras()"><span>Impresoras</span></button>
      <button class="nav-item" id="btnVigencias" onclick="loadVigencias()"><span>Vigencias</span></button>
      <button class="nav-item" id="btnRequisiciones" onclick="loadRequisiciones()"><span>Requisiciones</span></button>
      <button class="nav-item" id="btnIPs" onclick="loadListadoIP()"><span>Listado IP</span></button>
      <button class="nav-item" id="btnStats" onclick="loadDashboard()"><span>Estadísticas</span></button>
    </nav>

    <div class="top-actions">
      <button class="search-trigger" id="btnBuscarTop" onclick="abrirBuscador()" aria-label="Buscar usuario">
        <span id="searchTopIcon"></span><span class="st-txt">Buscar usuario...</span><kbd>/</kbd>
      </button>
      <button class="icon-btn only-m" id="btnBuscarM" onclick="abrirBuscador()" aria-label="Buscar usuario"></button>
      <button class="icon-btn" id="btnPapelera" onclick="abrirPapelera()" title="Papelera" aria-label="Papelera"></button>
    </div>
  </div>
</header>

<main class="page" id="rightPanel"></main>

<!-- Editar equipo / nuevo periférico (panel lateral) -->
<div class="modal-bg drawer" id="modalEquipo" style="display:none" onclick="if(event.target===this)closeModal('modalEquipo')">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="meTitle">
    <div class="modal-head">
      <h3 id="meTitle">Editar elemento</h3>
      <div class="modal-sub" id="meSub">Completa la información del equipo o periférico.</div>
    </div>
    <div class="modal-body">
      <input type="hidden" id="meId">
      <input type="hidden" id="mePadreId">

      <fieldset>
        <legend>Ubicación y responsable</legend>
        <div class="form-grid">
          <div class="fg"><label for="meResp">Responsable</label><input id="meResp" readonly placeholder="Nombre del usuario"></div>
          <div class="fg"><label for="meComp">Componente</label>
            <select id="meComp" onchange="toggleFormFields()">
              <option value="Equipo de computo">Equipo de computo</option>
              <option value="Mouse">Mouse</option>
              <option value="Teclado">Teclado</option>
              <option value="Monitor">Monitor</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
          <div class="fg"><label for="meArea">Área</label><input id="meArea" placeholder="Ej: Cartera"></div>
          <div class="fg"><label for="meSede">Sede</label><input id="meSede" placeholder="Ej: Cúcuta"></div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Identificación</legend>
        <div class="form-grid c3">
          <div class="fg" id="fg-tipo_equipo"><label for="meTipo">Tipo de equipo</label>
            <select id="meTipo">
              <option value="">Seleccionar</option>
              <option>Portatil</option><option>Todo en Uno</option><option>Torre</option><option>Otro</option>
            </select>
          </div>
          <div class="fg"><label for="meMarca">Marca</label><input id="meMarca" placeholder="Ej: HP, Dell..."></div>
          <div class="fg"><label for="meModelo">Modelo</label><input id="meModelo"></div>
          <div class="fg full"><label for="meSerial">Serial</label><input id="meSerial" placeholder="Número de serie" style="font-family:var(--mono)"></div>
        </div>
      </fieldset>

      <fieldset id="fs-hardware">
        <legend>Hardware</legend>
        <div class="form-grid c3">
          <div class="fg full"><label for="meProc">Procesador</label><input id="meProc" placeholder="Ej: Intel Core i5-1035G1"></div>
          <div class="fg"><label for="meRam">RAM total</label><input id="meRam" placeholder="Ej: 8 GB"></div>
          <div class="fg"><label for="meTipoRam">Tipo de RAM</label><input id="meTipoRam" placeholder="Ej: DDR4"></div>
          <div class="fg"><label for="meRamUsada">RAM usada</label><input id="meRamUsada" placeholder="Ej: 60%"></div>
          <div class="fg"><label for="meDisco">Disco total</label><input id="meDisco" placeholder="Ej: 256 GB"></div>
          <div class="fg"><label for="meTipoDisco">Tipo de disco</label><input id="meTipoDisco" placeholder="Ej: SSD"></div>
          <div class="fg"><label for="meDiscoUsado">Disco usado</label><input id="meDiscoUsado" placeholder="Ej: 45%"></div>
        </div>
      </fieldset>

      <fieldset id="fs-sistema">
        <legend>Sistema y usuario</legend>
        <div class="form-grid">
          <div class="fg"><label for="meSO">Sistema operativo</label><input id="meSO" placeholder="Ej: Windows 11 Pro"></div>
          <div class="fg"><label for="meOffice">Office instalado</label><input id="meOffice" placeholder="Ej: Office 2019"></div>
          <div class="fg"><label for="meUsuarioActual">Usuario actual (sesión)</label><input id="meUsuarioActual"></div>
          <div class="fg"><label for="meNombreCompleto">Nombre completo (equipo)</label><input id="meNombreCompleto"></div>
        </div>
      </fieldset>

      <fieldset id="fs-red">
        <legend>Conectividad</legend>
        <div class="form-grid">
          <div class="fg"><label for="meIP">IP local</label><input id="meIP" placeholder="Ej: 192.168.1.10" style="font-family:var(--mono)"></div>
          <div class="fg"><label for="meConexion">Conexión</label>
            <select id="meConexion">
              <option value="">Seleccionar</option>
              <option>WiFi</option><option>Cable</option><option>No determinada</option>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Observaciones</legend>
        <div class="fg full"><textarea id="meObs" aria-label="Observaciones" placeholder="Fallas, notas o pendientes de este equipo..."></textarea></div>
      </fieldset>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalEquipo')">Cancelar</button>
      <button class="btn btn-primary" id="btnGuardarEquipo" onclick="guardarEquipo()"><span>Guardar</span></button>
    </div>
  </div>
</div>

<!-- Software -->
<div class="modal-bg" id="modalSW" style="display:none" onclick="if(event.target===this)closeModal('modalSW')">
  <div class="modal" style="max-width:440px" role="dialog" aria-modal="true" aria-labelledby="swModalTitle">
    <div class="modal-head">
      <h3 id="swModalTitle"><span>Agregar software</span></h3>
      <div class="modal-sub">Registrar un programa instalado en este equipo.</div>
    </div>
    <div class="modal-body">
      <input type="hidden" id="swEqId">
      <div class="fg"><label for="swProg">Programa</label><input id="swProg" placeholder="Ej: Adobe Acrobat"></div>
      <div class="fg"><label for="swVer">Versión</label><input id="swVer" placeholder="Ej: 26.001"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalSW')">Cancelar</button>
      <button class="btn btn-primary" onclick="guardarSoftware()">Guardar</button>
    </div>
  </div>
</div>

<!-- Papelera -->
<div class="modal-bg" id="modalPapelera" style="display:none" onclick="if(event.target===this)closeModal('modalPapelera')">
  <div class="modal" style="max-width:660px" role="dialog" aria-modal="true" aria-labelledby="papeleraModalTitle">
    <div class="modal-head">
      <h3 id="papeleraModalTitle"><span>Papelera de reciclaje</span></h3>
      <div class="modal-sub">Elementos eliminados, agrupados por tipo. Puedes restaurarlos o borrarlos definitivamente.</div>
    </div>
    <div class="modal-body" id="papeleraContent">Cargando...</div>
    <div class="modal-footer">
      <button class="btn btn-danger" id="btnVaciarPapelera" onclick="vaciarPapelera()"><span>Vaciar papelera</span></button>
      <button class="btn btn-ghost" onclick="closeModal('modalPapelera')">Cerrar</button>
    </div>
  </div>
</div>

<!-- Asignar equipo -->
<div class="modal-bg" id="modalAsignar" style="display:none" onclick="if(event.target===this)closeModal('modalAsignar')">
  <div class="modal" style="max-width:580px;max-height:82vh" role="dialog" aria-modal="true" aria-labelledby="asgTitle">
    <div class="modal-head">
      <h3 id="asgTitle"><span>Asignar equipo existente</span></h3>
      <div class="modal-sub" id="asgSub">Busca un equipo de cómputo y asígnalo a este usuario. Se le quitará automáticamente a quien lo tenía; sus periféricos (mouse/teclado) no se ven afectados.</div>
      <input type="hidden" id="asgResponsable">
      <div class="modal-tools">
        <div class="field-ic">
          <span class="sic" id="asgSearchIcon"></span>
          <input class="si" id="asgQ" type="text" placeholder="Buscar por responsable, marca, modelo, serial, área..." aria-label="Buscar equipo" oninput="buscarEquiposAsignar()">
        </div>
        <button class="btn btn-ghost" id="asgVerTodos" onclick="verTodosEquiposAsignar()"><span>Ver todos</span></button>
      </div>
    </div>
    <div id="asgResults" class="modal-body" style="display:flex;flex-direction:column;gap:.45rem"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalAsignar')">Cerrar</button>
    </div>
  </div>
</div>

<!-- Tarea -->
<div class="modal-bg" id="modalTarea" style="display:none" onclick="if(event.target===this)closeModal('modalTarea')">
  <div class="modal" style="max-width:480px" role="dialog" aria-modal="true" aria-labelledby="tareaModalTitle">
    <div class="modal-head">
      <h3 id="tareaModalTitle"><span>Nueva tarea</span></h3>
      <div class="modal-sub">Tareas generales del área de tecnología.</div>
    </div>
    <div class="modal-body">
      <input type="hidden" id="tareaId">
      <div class="fg"><label for="tareaTitulo">Título</label><input id="tareaTitulo" placeholder="Ej: Revisar cable de red - Cartera"></div>
      <div class="fg"><label for="tareaDesc">Descripción</label><textarea id="tareaDesc" placeholder="Detalles adicionales (opcional)..."></textarea></div>
      <div class="fg">
        <label>Color</label>
        <div class="color-picker" id="colorPicker"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalTarea')">Cancelar</button>
      <button class="btn btn-primary" id="btnGuardarTarea" onclick="guardarTarea()"><span>Guardar</span></button>
    </div>
  </div>
</div>

<!-- Resumen KPI -->
<div class="modal-bg" id="modalKpiResumen" style="display:none" onclick="if(event.target===this)closeModal('modalKpiResumen')">
  <div class="modal" style="max-width:560px;max-height:80vh" role="dialog" aria-modal="true" aria-labelledby="kpiModalTitle">
    <div class="modal-head">
      <h3 id="kpiModalTitle">Resumen</h3>
      <div class="modal-sub">Detalle actualizado en tiempo real desde la base de datos.</div>
    </div>
    <div id="kpiModalContent" class="modal-body"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalKpiResumen')">Cerrar</button>
    </div>
  </div>
</div>

<div class="modal-bg" id="modalHistorial" style="display:none" onclick="if(event.target===this)closeModal('modalHistorial')">
  <div class="modal" style="max-width:600px;max-height:85vh" role="dialog" aria-modal="true" aria-labelledby="histModalTitle">
    <div class="modal-head">
      <h3 id="histModalTitle">Historial del equipo</h3>
      <div class="modal-sub" id="histModalSub"></div>
    </div>
    <div id="histModalContent" class="modal-body"></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalHistorial')">Cerrar</button>
    </div>
  </div>
</div>

<!-- Vigencia (panel lateral) -->
<div class="modal-bg drawer" id="modalVigencia" style="display:none" onclick="if(event.target===this)closeModal('modalVigencia')">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="vigModalTitle">
    <div class="modal-head">
      <h3 id="vigModalTitle"><span>Nueva vigencia</span></h3>
      <div class="modal-sub">Licencia, garantía o suscripción de hardware o software con su fecha de vencimiento.</div>
    </div>
    <div class="modal-body">
      <input type="hidden" id="vigId">
      <div class="form-grid">
        <div class="fg"><label for="vigTipo">Tipo</label>
          <select id="vigTipo"><option value="Software">Software</option><option value="Hardware">Hardware</option></select>
        </div>
        <div class="fg"><label for="vigEquipo">Equipo asociado</label>
          <select id="vigEquipo"><option value="">Sin equipo (general)</option></select>
        </div>
        <div class="fg full"><label for="vigNombre">Nombre *</label><input id="vigNombre" maxlength="200" placeholder="Ej: Microsoft 365, Garantía Dell, Antivirus"></div>
        <div class="fg"><label for="vigProveedor">Proveedor</label><input id="vigProveedor" maxlength="150" placeholder="Ej: Microsoft"></div>
        <div class="fg"><label for="vigReferencia">Clave / Referencia</label><input id="vigReferencia" maxlength="250" placeholder="Clave, contrato o n.º de serie"></div>
        <div class="fg"><label for="vigInicio">Fecha de inicio</label><input id="vigInicio" type="date"></div>
        <div class="fg"><label for="vigVence">Fecha de vencimiento *</label><input id="vigVence" type="date"></div>
        <div class="vg-quick full" style="grid-column:1/-1">
          <span class="vg-quick-l">Vence en</span>
          <button type="button" class="vg-chip" onclick="vigSumar(3)">3 meses</button>
          <button type="button" class="vg-chip" onclick="vigSumar(6)">6 meses</button>
          <button type="button" class="vg-chip" onclick="vigSumar(12)">1 año</button>
          <button type="button" class="vg-chip" onclick="vigSumar(24)">2 años</button>
          <button type="button" class="vg-chip" onclick="vigSumar(36)">3 años</button>
        </div>
        <div class="fg full"><label for="vigObs">Observaciones</label><textarea id="vigObs" maxlength="2000" placeholder="Notas adicionales (opcional)..."></textarea></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('modalVigencia')">Cancelar</button>
      <button class="btn btn-primary" id="btnGuardarVigencia" onclick="guardarVigencia()"><span>Guardar</span></button>
    </div>
  </div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script>
/* ============ ICONOS SVG (sin dependencias externas) ============ */
function svg(path, size=18, vb='0 0 24 24'){
  return `<svg class="ic" width="${size}" height="${size}" viewBox="${vb}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${path}</svg>`;
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
  gear:     svg('<circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.2 4.2l2.8 2.8M17 17l2.8 2.8M1 12h4M19 12h4M4.2 19.8 7 17M17 7l2.8-2.8"/>', 16),
  history:  svg('<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/>'),
  pencil:   svg('<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>', 15),
  kanban:   svg('<rect x="3" y="3" width="6" height="18" rx="1.2"/><rect x="10" y="3" width="6" height="11" rx="1.2"/><rect x="17" y="3" width="4" height="7" rx="1.2"/>'),
  copy:     svg('<rect x="8" y="8" width="12" height="12" rx="1.5"/><path d="M16 8V5.5A1.5 1.5 0 0 0 14.5 4h-9A1.5 1.5 0 0 0 4 5.5v9A1.5 1.5 0 0 0 5.5 16H8"/>'),
  scan:     svg('<path d="M4 8V5.5A1.5 1.5 0 0 1 5.5 4H8M16 4h2.5A1.5 1.5 0 0 1 20 5.5V8M20 16v2.5a1.5 1.5 0 0 1-1.5 1.5H16M8 20H5.5A1.5 1.5 0 0 1 4 18.5V16M3 12h18"/>'),
  calendar: svg('<rect x="3" y="4.5" width="18" height="16" rx="1.5"/><path d="M3 9.5h18M8 2.5v4M16 2.5v4"/>'),
  alert:    svg('<path d="M12 3 2 20h20z"/><path d="M12 10v4M12 17h.01"/>', 14),
  doc:      svg('<path d="M14 2H6.5A1.5 1.5 0 0 0 5 3.5v17A1.5 1.5 0 0 0 6.5 22h11a1.5 1.5 0 0 0 1.5-1.5V7z"/><path d="M14 2v5h5M8.5 12h7M8.5 15.5h7M8.5 8.5h2.5"/>'),
  network:  svg('<rect x="9" y="2.5" width="6" height="5" rx="1"/><rect x="2.5" y="16.5" width="6" height="5" rx="1"/><rect x="15.5" y="16.5" width="6" height="5" rx="1"/><path d="M12 7.5v4.5M5.5 16.5V12h13v4.5"/>'),
  external: svg('<path d="M14 4h6v6M20 4l-9 9"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>', 15),
  download: svg('<path d="M12 3v12M7 10l5 5 5-5M4 20h16"/>', 16),
  printer:  svg('<path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="1.5"/><path d="M6 14h12v7H6z"/>'),
  chart:    svg('<path d="M4 20V11M12 20V4M20 20v-8"/>'),
  chevLeft: svg('<path d="M15 18l-6-6 6-6"/>', 14),
  chevRight:svg('<path d="M9 18l6-6-6-6"/>', 14),
  arrowLeft:svg('<path d="M19 12H5M11 18l-6-6 6-6"/>', 16),
  arrowUR:  svg('<path d="M7 17 17 7M8 7h9v9"/>', 18),
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
  {estado:'pendiente',   label:'Por hacer',    color:'#EC6F78'},
  {estado:'en_proceso',  label:'En proceso',   color:'#E9A93A'},
  {estado:'completada',  label:'Completadas',  color:'#5B8FEA'},
];
const CARD_COLORS = { verde:'#8BC34A', amarillo:'#F2C94C', rojo:'#EB5757', azul:'#4A90D9', morado:'#9B6FD1' };
let selectedColor = 'verde';
let dragTareaId = null;
let activeUser = null;
let vistaActual = 'tareas';

/* Vuelve a cargar los datos de la vista en la que está el usuario (sin cambiarlo de sección) */
function recargarVista(){
  if(vistaActual === 'usuario' && activeUser) return verUsuario(activeUser);
  if(vistaActual === 'usuarios') return loadResponsables(usrQuery);
  if(vistaActual === 'tareas') return refreshKanban();
  if(vistaActual === 'vigencias') return refreshVigencias();
  if(vistaActual === 'stats') return loadDashboard();
  if(vistaActual === 'requisiciones') return reqTab === 'historial' ? cargarHistorialReq() : null;
}

/* Error ya mostrado al usuario: detiene la acción sin mostrar "Guardado" ni nada de éxito */
class ApiError extends Error {}
window.addEventListener('unhandledrejection', e=>{ if(e.reason instanceof ApiError) e.preventDefault(); });

async function api(params, body=null){
  const url = API + '?' + new URLSearchParams(params);
  const opts = body ? {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body)} : {method:'GET'};
  let r;
  try { r = await fetch(url, opts); }
  catch(e){
    toast('No se pudo conectar con el servidor. Revisa que XAMPP (Apache y MySQL) esté encendido.', I.close);
    throw new ApiError('sin conexión');
  }
  let data = null;
  try { data = await r.json(); } catch(e){}
  if(!r.ok || !data || data.error){
    const msg = (data && data.error) || `El servidor respondió con un error (${r.status}). No se guardaron los cambios.`;
    toast(msg, I.close);
    throw new ApiError(msg);
  }
  return data;
}

function marcarNav(id){
  document.querySelectorAll('.nav .nav-item').forEach(b=>b.classList.toggle('on', b.id===id));
  const act = id && document.getElementById(id);
  if(act && window.innerWidth <= 760) act.scrollIntoView({block:'nearest', inline:'center'});
}

function toast(msg, icon=I.check){
  const t = document.getElementById('toast');
  t.innerHTML = icon + '<span>' + esc(msg) + '</span>'; t.classList.add('show');
  clearTimeout(toast._t);
  toast._t = setTimeout(()=>t.classList.remove('show'), icon === I.close ? 5000 : 3000);
}
function closeModal(id){ document.getElementById(id).style.display='none'; }
function esc(s){ return (s===null||s===undefined) ? '' : String(s).replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

/* ============ Ayudas de presentación ============ */
function iniciales(n){
  return String(n||'?').trim().split(/\s+/).slice(0,2).map(w=>w.charAt(0)).join('').toUpperCase() || '?';
}
const AV_TONOS = [['#E2DFFF','#3E3891'],['#FFE0CF','#8A3F18'],['#FBD9E7','#8A2B55'],['#D9E8FF','#22508F'],['#FFF0C2','#6E5100']];
function avatarStyle(n){
  let h = 0; for(const ch of String(n||'')) h = (h*31 + ch.charCodeAt(0)) >>> 0;
  const t = AV_TONOS[h % AV_TONOS.length];
  return `--av-bg:${t[0]};--av-fg:${t[1]}`;
}
function encabezado({eyebrow='', titulo='', sub='', acciones='', antes=''}){
  return `${antes}
  <header class="ph">
    <div class="ph-l">
      ${eyebrow ? `<div class="eyebrow">${eyebrow}</div>` : ''}
      <h1 class="ph-title">${titulo}</h1>
      ${sub ? `<p class="ph-sub">${sub}</p>` : ''}
    </div>
    ${acciones ? `<div class="ph-r">${acciones}</div>` : ''}
  </header>`;
}
function irArriba(){ window.scrollTo({top:0, behavior:'auto'}); }

/* ============ DASHBOARD ============ */
function impKpiHTML(t){
  if(!t || !(t.impresoras||[]).length) return '';
  return `<button class="tile" onclick="loadImpresoras()">
    <div class="tile-top"><span class="tile-ic">${I.printer}</span><span class="tile-go">${I.arrowUR}</span></div>
    <div class="kpi-info"><div class="tile-n">${fmtNum(t.suma.contador_total)}</div><div class="tile-l">Páginas (todas las impresoras)</div></div>
  </button>`;
}

function vigKpiHTML(v){
  if(!v) return '';
  const aviso = v.vencidas > 0
    ? `<div class="vg-alert">${I.alert}<span>${v.vencidas} vencida${v.vencidas==1?'':'s'}</span></div>`
    : (v.por_vencer > 0 ? `<div class="vg-alert vg-warn">${I.alert}<span>${v.por_vencer} por vencer</span></div>` : '');
  return `<button class="tile" onclick="loadVigencias()">
    <div class="tile-top"><span class="tile-ic">${I.calendar}</span><span class="tile-go">${I.arrowUR}</span></div>
    <div class="kpi-info"><div class="tile-n">${v.total}</div><div class="tile-l">Vigencias</div>${aviso}</div>
  </button>`;
}

async function loadDashboard(){
  vistaActual = 'stats';
  activeUser = null; vigViewActiva = false; marcarNav('btnStats');
  const d = await api({action:'stats'});

  const comps = (d.por_componente||[]).map(c=>({ic:deviceIcon(c.componente), n:c.total, label:compLabel(c.componente), componente:c.componente}));

  const porArea = d.por_area || [];
  const porComp = d.por_componente || [];
  const maxArea = Math.max(1, ...porArea.map(a=>a.total));
  const maxComp = Math.max(1, ...porComp.map(c=>c.total));

  const right = document.getElementById('rightPanel');
  right.innerHTML = `
    <div class="view-fade">
    ${encabezado({eyebrow:'Estadísticas', titulo:'Panel de inventario', sub:'Resumen general de equipos y usuarios de CMP'})}

    <section class="bento">
      <button class="tile dark" onclick="openKpiModal('usuarios')">
        <div class="tile-top"><span class="tile-ic">${I.users}</span><span class="tile-go">${I.arrowUR}</span></div>
        <div><div class="tile-n">${d.resp}</div><div class="tile-l">Usuarios</div></div>
      </button>
      <button class="tile lime" onclick="openKpiModal('areas')">
        <div class="tile-top"><span class="tile-ic">${I.building}</span><span class="tile-go">${I.arrowUR}</span></div>
        <div><div class="tile-n">${d.areas}</div><div class="tile-l">Áreas</div></div>
      </button>
      ${vigKpiHTML(d.vigencias)}
      ${impKpiHTML(d.impresoras)}
    </section>

    <section class="comp-strip">
      ${comps.map((c,i)=>`
        <button class="kpi-card" style="animation-delay:${120+i*40}ms" onclick="openKpiModal('componente','${esc(c.componente).replace(/'/g,"\\'")}')">
          <div class="kpi-ic">${c.ic}</div>
          <div class="kpi-info"><div class="kpi-n">${c.n}</div><div class="kpi-l">${esc(c.label)}</div></div>
        </button>`).join('')}
    </section>

    <div class="grid-2">
      <div class="card card-pad">
        <div class="card-h"><div class="card-t">${I.building}<span>Equipos por área</span></div></div>
        ${porArea.length ? porArea.map(a=>`
          <div class="bar-row">
            <div class="bar-label" title="${esc(a.area)}">${esc(a.area)}</div>
            <div class="bar-track"><div class="bar-fill" style="width:${Math.round(a.total/maxArea*100)}%"></div></div>
            <div class="bar-n">${a.total}</div>
          </div>`).join('') : '<div class="empty-state">Sin datos de áreas.</div>'}
      </div>
      <div class="card card-pad">
        <div class="card-h"><div class="card-t">${I.disk}<span>Componentes registrados</span></div></div>
        ${porComp.length ? porComp.map(c=>`
          <div class="bar-row">
            <div class="bar-label">${esc(compLabel(c.componente))}</div>
            <div class="bar-track"><div class="bar-fill" style="width:${Math.round(c.total/maxComp*100)}%"></div></div>
            <div class="bar-n">${c.total}</div>
          </div>`).join('') : '<div class="empty-state">Sin datos.</div>'}
      </div>
    </div>
    ${d.impresoras && (d.impresoras.impresoras||[]).length ? `
    <div class="section-h" style="margin-top:1.6rem"><div class="card-t">${I.printer}<span>Impresoras · contador total</span></div></div>
    <div class="vg-table-wrap">${impTotalesHTML(d.impresoras)}</div>` : ''}
    </div>
  `;
  irArriba();
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

/* ============ USUARIOS ============ */
let usrQuery = '';
let usrDebounce = null;

async function loadUsuarios(q=usrQuery){
  vistaActual = 'usuarios';
  activeUser = null; vigViewActiva = false; marcarNav('btnUsuarios');
  usrQuery = q || '';
  document.getElementById('rightPanel').innerHTML = `
    <div class="view-fade">
    ${encabezado({eyebrow:'Directorio', titulo:'Usuarios', sub:'<span id="peopleCount">Cargando...</span>'})}
    <div class="users-search">
      <span class="sic" id="searchIcon">${svg('<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/>', 20)}</span>
      <input id="searchInput" type="text" placeholder="Buscar usuario, área o sede..." aria-label="Buscar usuario, área o sede" value="${esc(usrQuery)}" oninput="clearTimeout(usrDebounce);const v=this.value;usrDebounce=setTimeout(()=>loadResponsables(v),150)">
    </div>
    <div id="peopleList"></div>
    </div>`;
  irArriba();
  await loadResponsables(usrQuery);
}

function abrirBuscador(){
  if(!document.getElementById('searchInput')) loadUsuarios();
  setTimeout(()=>{ const s = document.getElementById('searchInput'); if(s){ s.focus(); s.select(); } }, 60);
}

function userCardHTML(r, i=0){
  const isAlex = r.responsable.toLowerCase()==='alexander c';
  const isActive = activeUser === r.responsable ? 'active' : '';
  const perif = r.total_items - r.total_equipos;
  return `
  <button class="pc ${isActive}" style="animation-delay:${Math.min(i,16)*25}ms" data-responsable="${esc(r.responsable)}" onclick="verUsuario('${esc(r.responsable).replace(/'/g,"\\'")}')">
    <div class="av" style="${avatarStyle(r.responsable)}">${esc(iniciales(r.responsable))}</div>
    <div class="pc-body">
      <div class="pc-name">${esc(r.responsable)} ${isAlex ? `<span class="badge-king" title="Responsable General TI">${I.shield}</span>` : ''}</div>
      <div class="pc-meta">${esc(r.area)||'Sin área'}${r.sede ? ' · '+esc(r.sede) : ''}</div>
      <div class="pc-pills">
        <span class="pc-pill">${r.total_equipos} equipo${r.total_equipos==1?'':'s'}</span>
        ${perif > 0 ? `<span class="pc-pill">${perif} periférico${perif==1?'':'s'}</span>` : ''}
      </div>
    </div>
    <span class="pc-arrow">${I.arrowUR}</span>
  </button>`;
}

async function loadResponsables(q=''){
  usrQuery = q;
  const d = await api({action:'responsables', q});
  const box = document.getElementById('peopleList');
  if(!box) return;
  const lista = d.responsables || [];

  const cnt = document.getElementById('peopleCount');
  if(cnt) cnt.textContent = q
    ? `${lista.length} resultado${lista.length==1?'':'s'} para “${q}”`
    : `${lista.length} usuario${lista.length==1?'':'s'} con equipos asignados`;

  if(!lista.length){ box.innerHTML = '<div class="empty-state">No se encontraron usuarios.</div>'; return; }

  box.innerHTML = `<div class="u-grid">${lista.map((r,i)=>userCardHTML(r, i)).join('')}</div>`;
}

/* ============ VISTA DETALLE USUARIO ============ */
function selEquipo(idx){
  document.querySelectorAll('.eq-tab').forEach((t,i)=>{ t.classList.toggle('on', i===idx); t.setAttribute('aria-selected', i===idx ? 'true' : 'false'); });
  document.querySelectorAll('.eq-panel').forEach((p,i)=>p.classList.toggle('on', i===idx));
}

async function verUsuario(nombre){
  vistaActual = 'usuario';
  vigViewActiva = false; marcarNav('btnUsuarios');
  activeUser = nombre;
  const d = await api({action:'responsable', nombre});
  const rp = document.getElementById('rightPanel');
  const nombreJs = esc(nombre).replace(/'/g,"\\'");

  const isAlex = nombre.toLowerCase() === 'alexander c';
  const badgeAlex = isAlex ? `<span class="chip king">${I.shield}<span>Responsable General TI</span></span>` : '';
  const totalPCs = d.items.filter(e => e.componente === 'Equipo de computo').length;
  const tagEquipos = totalPCs > 0
    ? `<span class="chip">${I.laptop}<span>${totalPCs} equipo${totalPCs==1?'':'s'} de cómputo</span></span>`
    : (d.items.length ? `<span class="chip">${I.mouse}<span>${d.items.length} periférico${d.items.length==1?'':'s'} sin equipo asociado</span></span>` : '');

  let html = `
    <div class="view-fade">
    <button class="back-link" onclick="loadUsuarios()">${I.arrowLeft}<span>Todos los usuarios</span></button>
    <section class="profile">
      <div class="av">${esc(iniciales(nombre))}</div>
      <div class="profile-body">
        <h1 class="profile-name">${esc(nombre)}</h1>
        <div class="profile-sub">${d.items.length ? esc(d.items[0].area) + ' · ' + esc(d.items[0].sede) : 'Sin equipos asignados'}</div>
        ${badgeAlex || tagEquipos ? `<div class="profile-tags">${badgeAlex}${tagEquipos}</div>` : ''}
      </div>
      <div class="profile-actions">
        <button class="btn btn-accent" onclick="abrirAsignarEquipo('${nombreJs}')">${I.refresh}<span>Asignarle un equipo existente</span></button>
      </div>
    </section>
  `;

  if(!d.items.length) html += `<div class="empty-state">Este usuario no tiene equipos asignados.</div>`;

  if(d.items.length > 1){
    html += `<div class="eq-tabs" role="tablist">${d.items.map((eq, idx)=>`
      <button class="eq-tab ${idx===0?'on':''}" role="tab" aria-selected="${idx===0}" onclick="selEquipo(${idx})">
        <span class="t-ic">${deviceIcon(eq.componente, eq.tipo_equipo)}</span>
        <span><b>${esc(eq.marca)||esc(eq.componente)||'Equipo'} ${esc(eq.modelo)||''}</b><small>${esc(eq.serial)||'S/N: N/A'}</small></span>
      </button>`).join('')}</div>`;
  }

  d.items.forEach((eq, idx) => {
    let swRows = '';
    eq.software.forEach((s, i2) => {
      const isHidden = i2 >= 5 ? 'hidden-row' : '';
      swRows += `<tr class="sw-item-${eq.id} ${isHidden}"><td>${esc(s.programa)}</td><td>${esc(s.version)||'-'}</td>
        <td style="text-align:right;width:36px"><button class="btn btn-danger btn-ic btn-sm" title="Eliminar software" aria-label="Eliminar software" onclick="borrarSoftware(${s.id},'${nombreJs}')">${I.close}</button></td></tr>`;
    });
    const swBtn = eq.software.length > 5 ? `<button class="link-btn" onclick="toggleSW(${eq.id}, this)">Ver todos (${eq.software.length}) ${I.chevron}</button>` : '';

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
    <article class="eq-panel card ${idx===0?'on':''}">
      <div class="eq-head">
        <div class="eq-ic">${deviceIcon(eq.componente, eq.tipo_equipo)}</div>
        <div style="min-width:0">
          <div class="eq-title">${esc(eq.marca)||'Equipo'} ${esc(eq.modelo)||''}</div>
          <div class="eq-sub">S/N: ${esc(eq.serial)||'N/A'} ${eq.tipo_equipo ? ' · '+esc(eq.tipo_equipo) : ''}${totalPCs>1 && eq.componente==='Equipo de computo' ? ` · Equipo ${idx+1} de ${totalPCs}` : ''}</div>
        </div>
        <div class="eq-head-r">
          <button class="btn btn-ghost btn-sm" onclick="verHistorialEquipo(${eq.id})">${I.history}<span>Historial</span></button>
          <button class="btn btn-ghost btn-sm" onclick="openModalEquipo(${eq.id}, '${nombreJs}', null)">${I.pencil}<span>Editar</span></button>
          <button class="btn btn-danger btn-ic btn-sm" title="Enviar a la papelera" aria-label="Enviar a la papelera" onclick="borrarEquipo(${eq.id}, '${nombreJs}')">${I.trash}</button>
        </div>
      </div>
      <div class="eq-body">
        ${specs.length ? `<div class="spec-grid">
          ${specs.map(s=>`<div class="spec-c"><div class="spec-k">${s[2]}<span>${s[0]}</span></div><div class="spec-v">${esc(s[1])}</div></div>`).join('')}
        </div>` : ''}

        ${eq.observaciones ? `<div class="note-box">${I.note}<div>${esc(eq.observaciones)}</div></div>` : ''}

        <div class="eq-cols">
          <section class="sub-card">
            <div class="sec-label">
              <span class="sec-label-txt">${I.mouse}<span>Periféricos</span></span>
              <button class="btn btn-ghost btn-sm" onclick="openModalEquipo(null, '${nombreJs}', ${eq.id})">${I.plus}<span>Agregar</span></button>
            </div>
            <div class="periph-list">
              ${eq.perifericos.map(p=> `<div class="periph-item">
                <div class="periph-ic">${deviceIcon(p.componente)}</div>
                <div class="periph-main">
                  <div class="periph-name">${esc(p.componente)} ${esc(p.marca)||''}</div>
                  <div class="periph-sn">S/N: ${esc(p.serial)||'N/A'}</div>
                </div>
                <div class="periph-actions">
                  <button class="btn btn-ghost btn-ic btn-sm" title="Editar" aria-label="Editar periférico" onclick="openModalEquipo(${p.id}, '${nombreJs}', null)">${I.pencil}</button>
                  <button class="btn btn-danger btn-ic btn-sm" title="Enviar a la papelera" aria-label="Enviar periférico a la papelera" onclick="borrarEquipo(${p.id}, '${nombreJs}')">${I.close}</button>
                </div>
              </div>`).join('') || '<div class="empty-state">Sin periféricos asociados.</div>'}
            </div>
          </section>

          <section class="sub-card">
            <div class="sec-label">
              <span class="sec-label-txt">${I.disk}<span>Software (${eq.software.length})</span></span>
              <button class="btn btn-ghost btn-sm" onclick="openModalSW(${eq.id})">${I.plus}<span>Agregar</span></button>
            </div>
            ${swRows ? `<table class="swt"><tbody>${swRows}</tbody></table>` : '<div class="empty-state">Sin software registrado.</div>'}
            ${swBtn}
          </section>

          <section class="sub-card">
            <div class="sec-label">
              <span class="sec-label-txt">${I.calendar}<span>Vigencias (${(eq.vigencias||[]).length})</span></span>
              <button class="btn btn-ghost btn-sm" onclick="abrirModalVigencia(null, ${eq.id})">${I.plus}<span>Agregar</span></button>
            </div>
            <div class="periph-list">
            ${(eq.vigencias||[]).map(v=>`<div class="periph-item">
              <div class="periph-ic">${v.tipo==='Hardware' ? I.chip : I.disk}</div>
              <div class="periph-main">
                <div class="periph-name">${esc(v.nombre)}</div>
                <div class="vg-sub">${esc(v.tipo)}${v.proveedor ? ' · '+esc(v.proveedor) : ''} · Vence: ${fmtFecha(v.fecha_vencimiento)}</div>
              </div>
              ${vigBadge(v)}
              <button class="btn btn-ghost btn-ic btn-sm" onclick="abrirModalVigencia(${v.id})" title="Editar" aria-label="Editar vigencia">${I.pencil}</button>
            </div>`).join('') || '<div class="empty-state">Sin vigencias registradas.</div>'}
            </div>
          </section>
        </div>
      </div>
    </article>`;
  });

  html += `</div>`;
  rp.innerHTML = html;
  irArriba();
}

function toggleSW(id, btn){
  const rows = document.querySelectorAll(`.sw-item-${id}`);
  let isExpanded = btn.textContent.includes('Ocultar');
  rows.forEach((r, idx)=>{ if(idx>=5) r.classList.toggle('hidden-row'); });
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
  document.getElementById('meTitle').innerHTML = (id ? I.pencil : I.plus) + '<span>' + (id ? 'Editar elemento' : 'Nuevo periférico') + '</span>';
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
  recargarVista();
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
      <div class="periph-main">
        <div class="periph-name">${esc(e.marca)||'Equipo'} ${esc(e.modelo)||''}</div>
        <div class="eq-sub">S/N: ${esc(e.serial)||'N/A'} · Actual: ${esc(e.responsable)||'Sin asignar'} · ${esc(e.area)||''}</div>
      </div>
      <button class="btn btn-primary btn-sm" onclick="asignarEquipoAUsuario(${e.id})"><span>Asignar</span></button>
    </div>`).join('');
}

/* ============ HISTORIAL DE CAMBIOS POR EQUIPO ============ */
const HIST_CAMPOS = {
  sede:'Sede', area:'Área', componente:'Componente', tipo_equipo:'Tipo de equipo', usuario_actual:'Usuario de sesión',
  nombre_completo:'Nombre del equipo', grupo_usuario:'Grupo de usuario', marca:'Marca', modelo:'Modelo', serial:'Serial',
  procesador:'Procesador', disco_total:'Disco total', tipo_disco:'Tipo de disco', disco_usado:'Disco usado',
  tipo_ram:'Tipo de RAM', ram_total:'RAM total', ram_usada:'RAM usada', sistema_op:'Sistema operativo',
  ip_local:'IP local', conexion:'Conexión', observaciones:'Observaciones', office:'Office'
};
function fmtFechaHora(f){
  if(!f) return '—';
  const [fe, ho] = String(f).split(' ');
  return fmtFecha(fe) + (ho ? ' · ' + ho.slice(0,5) : '');
}
function histVal(v){ return (v === null || v === undefined || v === '') ? '<i class="hist-vacio">vacío</i>' : esc(v); }
function histEntradaHTML(h){
  const d = h.detalle || {};
  const sw = s => esc(s.programa) + (s.version ? ' ' + esc(s.version) : '');
  let titulo = '', cuerpo = '', ic = I.pencil;
  switch(h.accion){
    case 'reasignado': ic = I.users; titulo = 'Reasignado';
      cuerpo = `${histVal(d.de)} → <b>${histVal(d.a)}</b>`; break;
    case 'editado': titulo = 'Datos modificados';
      cuerpo = (Array.isArray(d) ? d : []).map(c=>`<div class="hist-cambio"><span>${esc(HIST_CAMPOS[c.campo] || c.campo)}:</span> ${histVal(c.antes)} → <b>${histVal(c.despues)}</b></div>`).join(''); break;
    case 'creado': ic = I.plus; titulo = 'Registrado'; cuerpo = d.responsable ? `Asignado a ${esc(d.responsable)}` : ''; break;
    case 'eliminado': ic = I.trash; titulo = 'Enviado a la papelera'; break;
    case 'restaurado': ic = I.refresh; titulo = 'Restaurado de la papelera'; break;
    case 'software_agregado': ic = I.disk; titulo = 'Software agregado'; cuerpo = sw(d); break;
    case 'software_eliminado': ic = I.disk; titulo = 'Software eliminado'; cuerpo = sw(d); break;
    case 'software_restaurado': ic = I.disk; titulo = 'Software restaurado'; cuerpo = sw(d); break;
    default: titulo = esc(h.accion);
  }
  const quien = h.es_periferico ? `<span class="hist-tag">${esc(h.componente || 'Periférico')}${h.marca ? ' ' + esc(h.marca) : ''}</span>` : '';
  return `<li class="hist-item">
    <div class="hist-ic">${ic}</div>
    <div class="hist-main">
      <div class="hist-top"><b>${titulo}</b>${quien}<span class="hist-fecha">${fmtFechaHora(h.creado_at)}</span></div>
      ${cuerpo ? `<div class="hist-det">${cuerpo}</div>` : ''}
    </div>
  </li>`;
}
async function verHistorialEquipo(id){
  const [eq, d] = await Promise.all([api({action:'get_equipo', id}), api({action:'historial_equipo', id})]);
  const e = eq.equipo || {};
  document.getElementById('histModalTitle').innerHTML = I.history + '<span>Historial del equipo</span>';
  document.getElementById('histModalSub').textContent = `${[e.marca, e.modelo].filter(Boolean).join(' ') || 'Equipo'} · S/N: ${e.serial || 'N/A'} · Actual: ${e.responsable || 'Sin asignar'}`;
  const rows = d.historial || [];
  document.getElementById('histModalContent').innerHTML = rows.length
    ? `<ul class="hist-list">${rows.map(histEntradaHTML).join('')}</ul>`
    : '<div class="empty-state">Todavía no hay movimientos registrados. A partir de ahora se guardan las reasignaciones, cambios de datos, software y periféricos de este equipo.</div>';
  document.getElementById('modalHistorial').style.display = 'flex';
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
  if(!lista.length) return '<div class="empty-state" style="border:none">Aún no hay impresoras.</div>';
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
  return `<table class="vgt" style="min-width:560px">
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
  vistaActual = 'impresoras';
  activeUser = null; vigViewActiva = false; marcarNav('btnImpresoras');
  document.getElementById('rightPanel').innerHTML = `
    <div class="view-fade" id="impLive">
    ${encabezado({eyebrow:'Monitoreo', titulo:'Impresoras', sub:'Contadores de copia, impresión y escaneado',
      acciones:`<button class="btn btn-accent" id="btnConsultarImp" onclick="consultarImpresora()">${I.refresh}<span>Consultar ahora</span></button>`})}

    <div class="imp-bar">
      <select id="impSelect" class="select-pill" aria-label="Impresora" onchange="impSel=Number(this.value);impMostrarVacio();cargarLecturasImp();"></select>
      <div class="auto-box">
        <span class="auto-l">${I.refresh}<span>Actualizar</span></span>
        <select id="impAuto" class="select-pill" aria-label="Frecuencia de actualización automática" onchange="impCambiarIntervalo(this.value)">
          <option value="0">Desactivado</option>
          <option value="20">Cada 20 minutos</option>
          <option value="30">Cada 30 minutos</option>
          <option value="40">Cada 40 minutos</option>
          <option value="60">Cada 1 hora</option>
          <option value="180">Cada 3 horas</option>
          <option value="custom">Personalizado...</option>
        </select>
        <span class="auto-custom" id="impAutoCustom" style="display:none">
          <input type="number" id="impAutoMin" min="1" max="1440" step="1" inputmode="numeric" aria-label="Minutos" placeholder="min">
          <span class="auto-u">min</span>
          <button class="btn btn-primary btn-sm" onclick="impAplicarPersonalizado()">Aplicar</button>
        </span>
      </div>
    </div>
    <div class="imp-next" id="impNext"></div>
    <div id="impMsg"></div>
    <div id="impActual"></div>

    <div class="section-h"><div class="card-t">${I.printer}<span>Totales de todas las impresoras</span></div></div>
    <div class="vg-table-wrap" id="impTotales" style="margin-bottom:1.8rem"></div>

    <div class="section-h"><div class="card-t">${I.calendar}<span>Historial de lecturas</span></div>
      <button class="btn btn-danger btn-sm" id="btnBorrarHist" onclick="borrarHistorialImp()" style="display:none">${I.trash}<span>Borrar historial</span></button></div>
    <div class="vg-table-wrap" id="impHistorial"></div>
    </div>`;
  irArriba();
  const d = await api({action:'impresoras'});
  impLista = d.impresoras || [];
  if(!impLista.find(i=>i.id===impSel)) impSel = impLista.length ? impLista[0].id : null;
  const sel = document.getElementById('impSelect');
  sel.innerHTML = impLista.map(i=>`<option value="${i.id}" ${i.id===impSel?'selected':''}>${esc(i.nombre)} (${esc(i.ip)})</option>`).join('')
                  || '<option value="">Sin impresoras</option>';
  cargarTotalesImp();
  impToggleAuto(impLeerPreferencia());
  impSincronizarSelector();
  await cargarLecturasImp();
}

function impMostrarVacio(){
  document.getElementById('impMsg').innerHTML = '';
}

async function cargarLecturasImp(){
  const hist = document.getElementById('impHistorial');
  if(!hist) return;
  if(!impSel){ hist.innerHTML = '<div class="empty-state" style="border:none">No hay impresoras configuradas. Se agregan en el archivo php/impresoras.php.</div>'; document.getElementById('impActual').innerHTML=''; return; }
  const d = await api({action:'lecturas_impresora', id: impSel});
  const rows = d.lecturas || [];
  if(!rows.length){
    document.getElementById('impActual').innerHTML = '<div class="empty-state" style="margin-bottom:1.6rem">Aún no hay lecturas. Pulsa “Consultar ahora” para leer los contadores de la impresora.</div>';
    hist.innerHTML = '<div class="empty-state" style="border:none">Sin historial.</div>';
    const bb0 = document.getElementById('btnBorrarHist'); if(bb0) bb0.style.display = 'none';
    return;
  }
  renderImpActual(rows[0]);
  const cols = ['copia_color','copia_bn','copia_color_pers','copia_dos_colores','impresion_color','impresion_bn','impresion_color_pers','impresion_dos_colores','escaneo_color','escaneo_bn'];
  hist.innerHTML = `<table class="vgt imp-hist" style="min-width:980px">
    <thead>
      <tr class="imp-grp"><th rowspan="2">Fecha</th><th rowspan="2">Hora</th><th colspan="4" class="num g">Copia</th><th colspan="4" class="num g">Impresión</th><th colspan="2" class="num g">Escaneado</th><th rowspan="2" class="num">Total</th><th rowspan="2"></th></tr>
      <tr><th class="num">Todo color</th><th class="num">B/N</th><th class="num">Personal.</th><th class="num">Dos col.</th><th class="num">Todo color</th><th class="num">B/N</th><th class="num">Personal.</th><th class="num">Dos col.</th><th class="num">Color</th><th class="num">B/N</th></tr>
    </thead>
    <tbody>${rows.map(r=>{const t=fmtFH(r.fecha_hora);return `<tr>
      <td class="vg-date">${t.f}</td><td class="vg-date">${t.h}</td>
      ${cols.map(k=>`<td class="num">${fmtNum(r[k])}</td>`).join('')}<td class="num"><b>${fmtNum(r.contador_total)}</b></td>
      <td><button class="btn btn-danger btn-ic btn-sm" onclick="borrarLecturaImp(${r.id})" title="Borrar esta lectura" aria-label="Borrar esta lectura">${I.trash}</button></td></tr>`;}).join('')}</tbody></table>`;
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
  const card = (ic, titulo, filas, i) => `<div class="imp-card" style="animation-delay:${i*50}ms">
      <div class="imp-card-h">${ic}<span>${titulo}</span></div>
      ${filas.map(([n,v])=>`<div class="imp-row"><span>${n}</span><b>${fmtNum(v)}</b></div>`).join('')}
    </div>`;
  document.getElementById('impActual').innerHTML = `
    <div class="imp-last">${I.calendar}<span>Última lectura: <b>${t.f}</b> a las <b>${t.h}</b></span></div>
    <div class="imp-grid">
      ${card(I.copy, 'Copiadora', [['A todo color',l.copia_color],['Blanco y Negro',l.copia_bn],['Color personalizado',l.copia_color_pers],['Dos colores',l.copia_dos_colores]], 0)}
      ${card(I.printer, 'Impresora', [['A todo color',l.impresion_color],['Blanco y Negro',l.impresion_bn],['Color personalizado',l.impresion_color_pers],['Dos colores',l.impresion_dos_colores]], 1)}
      ${card(I.scan, 'Envío por escáner', [['Color',l.escaneo_color],['Blanco y Negro',l.escaneo_bn]], 2)}
      ${card(I.chart, 'Total', [['Contador total',l.contador_total],
          ...(l.total_color!=null ? [['A todo color',l.total_color]] : []),
          ...(l.total_bn!=null ? [['Blanco y Negro',l.total_bn]] : []),
          ...(l.total_color_pers!=null ? [['Color personalizado',l.total_color_pers]] : []),
          ...(l.total_dos_colores!=null ? [['Dos colores',l.total_dos_colores]] : [])], 3)}
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

/* ---- Actualización automática: intervalo en minutos (0 = desactivado) ---- */
const IMP_PRESETS = [20, 30, 40, 60, 180];
const IMP_MIN = 1, IMP_MAX = 1440;
let impMinutos = 0;
let impProxima = null;

function impLeerPreferencia(){
  try { const v = Number(localStorage.getItem('invcmp_imp_auto_min')); return Number.isFinite(v) && v >= IMP_MIN && v <= IMP_MAX ? v : 0; }
  catch(e){ return 0; }
}
function impGuardarPreferencia(min){
  try { localStorage.setItem('invcmp_imp_auto_min', String(min)); } catch(e){}
}
function impTextoIntervalo(min){
  if(min % 60 === 0){ const h = min/60; return `cada ${h} hora${h===1?'':'s'}`; }
  if(min > 60){ return `cada ${Math.floor(min/60)} h ${min%60} min`; }
  return `cada ${min} minuto${min===1?'':'s'}`;
}
function impMostrarProxima(){
  const el = document.getElementById('impNext');
  if(!el) return;
  if(!impMinutos || !impProxima){ el.innerHTML = ''; return; }
  const hh = impProxima.toLocaleTimeString('es-CO', {hour:'2-digit', minute:'2-digit'});
  el.innerHTML = `${I.refresh}<span>Actualización automática ${impTextoIntervalo(impMinutos)} · próxima consulta: <b>${hh}</b> · Funciona mientras esta página esté abierta en Impresoras</span>`;
}
function impSincronizarSelector(){
  const sel = document.getElementById('impAuto');
  if(!sel) return;
  const esPreset = impMinutos === 0 || IMP_PRESETS.includes(impMinutos);
  sel.value = esPreset ? String(impMinutos) : 'custom';
  const custom = document.getElementById('impAutoCustom');
  custom.style.display = esPreset ? 'none' : 'inline-flex';
  if(!esPreset) document.getElementById('impAutoMin').value = impMinutos;
}

/* Arranca o detiene el temporizador con el intervalo indicado (minutos) */
function impToggleAuto(min){
  if(impTimer){ clearInterval(impTimer); impTimer = null; }
  impMinutos = Number(min) || 0;
  impProxima = null;
  if(impMinutos > 0){
    const ms = impMinutos * 60000;
    impProxima = new Date(Date.now() + ms);
    impTimer = setInterval(()=>{
      if(!document.getElementById('impAuto')){ clearInterval(impTimer); impTimer = null; return; } // se salió de la vista
      impProxima = new Date(Date.now() + ms);
      impMostrarProxima();
      consultarImpresora(true);
    }, ms);
  }
  impMostrarProxima();
}

function impCambiarIntervalo(valor){
  const custom = document.getElementById('impAutoCustom');
  if(valor === 'custom'){
    custom.style.display = 'inline-flex';
    const inp = document.getElementById('impAutoMin');
    if(!inp.value) inp.value = impMinutos || 15;
    inp.focus(); inp.select();
    return;
  }
  custom.style.display = 'none';
  const min = Number(valor) || 0;
  impGuardarPreferencia(min);
  impToggleAuto(min);
  if(min) consultarImpresora(true);   // primera lectura de inmediato, como antes
  toast(min ? `Actualización automática ${impTextoIntervalo(min)}` : 'Actualización automática desactivada', min ? I.check : I.close);
}

function impAplicarPersonalizado(){
  const min = Math.round(Number(document.getElementById('impAutoMin').value));
  if(!Number.isFinite(min) || min < IMP_MIN || min > IMP_MAX){
    toast(`Escribe un número de minutos entre ${IMP_MIN} y ${IMP_MAX}`, I.close); return;
  }
  impGuardarPreferencia(min);
  impToggleAuto(min);
  impSincronizarSelector();
  consultarImpresora(true);
  toast(`Actualización automática ${impTextoIntervalo(min)}`);
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
  vistaActual = 'vigencias';
  activeUser = null; vigViewActiva = true; marcarNav('btnVigencias');
  document.getElementById('rightPanel').innerHTML = `
    <div class="view-fade">
    ${encabezado({eyebrow:'Licencias y garantías', titulo:'Vigencias', sub:'Licencias y garantías de hardware y software',
      acciones:`<button class="btn btn-accent" onclick="abrirModalVigencia()">${I.plus}<span>Nueva vigencia</span></button>`})}
    <div class="vg-tiles" id="vigTiles"></div>
    <div class="toolbar">
      <div class="field-ic"><span class="sic">${I.search}</span><input class="si" id="vigBuscar" type="text" placeholder="Buscar por nombre, proveedor, equipo o referencia..." aria-label="Buscar vigencias" oninput="renderVigTabla()"></div>
      <select id="vigFiltroTipo" class="select-pill" aria-label="Filtrar por tipo" onchange="renderVigTabla()"><option value="">Todos los tipos</option><option value="Software">Software</option><option value="Hardware">Hardware</option></select>
    </div>
    <div class="vg-table-wrap" id="vigTabla"></div>
    </div>`;
  irArriba();
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
    <button class="vg-tile ${vigFiltro===k?'active':''}" aria-pressed="${vigFiltro===k}" onclick="setVigFiltro('${k}')">
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
    box.innerHTML = `<div class="empty-state" style="border:none;padding:2rem">${vigData.length ? 'Ninguna vigencia coincide con el filtro.' : 'Aún no hay vigencias registradas. Usa “Nueva vigencia” para agregar la primera.'}</div>`;
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
        <button class="btn btn-ghost btn-ic btn-sm" onclick="abrirModalVigencia(${v.id})" title="Editar" aria-label="Editar vigencia">${I.pencil}</button>
        <button class="btn btn-danger btn-ic btn-sm" onclick="eliminarVigencia(${v.id})" title="Enviar a la papelera" aria-label="Enviar vigencia a la papelera">${I.trash}</button>
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

function papAcciones(tipo, id){
  return `<div class="pap-item-actions">
      <button class="btn btn-primary btn-ic btn-sm" onclick="restaurar('${tipo}', ${id})" title="Restaurar" aria-label="Restaurar">${I.refresh}</button>
      <button class="btn btn-danger btn-ic btn-sm" onclick="borrarPermanente('${tipo}', ${id})" title="Eliminar definitivamente" aria-label="Eliminar definitivamente">${I.trash}</button>
    </div>`;
}

function papItemEquipoHTML(e){
  const incluye = [];
  if(e.perifericos_incluidos > 0) incluye.push(`${e.perifericos_incluidos} periférico${e.perifericos_incluidos==1?'':'s'}`);
  if(e.software_incluido > 0) incluye.push(`${e.software_incluido} software`);
  if(e.vigencias_incluidas > 0) incluye.push(`${e.vigencias_incluidas} vigencia${e.vigencias_incluidas==1?'':'s'}`);

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
    ${papAcciones('equipo', e.id)}
  </div>`;
}

function papItemSoftwareHTML(s){
  return `<div class="pap-item">
    <div class="pap-item-ic">${I.disk}</div>
    <div class="pap-item-body">
      <div class="pap-item-title">${esc(s.programa)} ${s.version ? '· '+esc(s.version) : ''}</div>
      <div class="pap-item-meta">De: ${esc(s.responsable)||'Sin responsable'} · Eliminado: ${esc(s.deleted_at)}</div>
    </div>
    ${papAcciones('software', s.id)}
  </div>`;
}

function papItemTareaHTML(t){
  return `<div class="pap-item">
    <div class="pap-item-ic">${I.kanban}</div>
    <div class="pap-item-body">
      <div class="pap-item-title">${esc(t.titulo)}</div>
      <div class="pap-item-meta">Eliminada: ${esc(t.deleted_at)}</div>
    </div>
    ${papAcciones('tarea', t.id)}
  </div>`;
}

function papItemVigenciaHTML(v){
  return `<div class="pap-item">
    <div class="pap-item-ic">${I.calendar}</div>
    <div class="pap-item-body">
      <div class="pap-item-title">${esc(v.nombre)}</div>
      <div class="pap-item-meta">${esc(v.tipo)}${v.proveedor ? ' · '+esc(v.proveedor) : ''} · Vencía: ${fmtFecha(v.fecha_vencimiento)} · Eliminada: ${esc(v.deleted_at)}</div>
    </div>
    ${papAcciones('vigencia', v.id)}
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
  toast('Elemento eliminado definitivamente', I.trash);
  abrirPapelera();
}

async function restaurar(tipo, id){
  const actionMap = { equipo: 'restore_equipo', software: 'restore_software', tarea: 'restore_tarea', vigencia: 'restore_vigencia' };
  const res = await api({action: actionMap[tipo]}, {id});
  toast('Elemento restaurado', I.refresh);
  abrirPapelera();
  recargarVista();
}

/* ============ TABLERO KANBAN (TAREAS) ============ */
async function loadKanban(){
  vistaActual = 'tareas';
  activeUser = null; vigViewActiva = false; marcarNav('btnTareas');
  const rp = document.getElementById('rightPanel');
  rp.innerHTML = `
    <div class="view-fade">
    ${encabezado({eyebrow:'Seguimiento', titulo:'Tablero de tareas', sub:'Pendientes, en proceso y completadas. Arrastra las tarjetas entre columnas.',
      acciones:`<button class="btn btn-accent" onclick="openModalTarea()">${I.plus}<span>Nueva tarea</span></button>`})}
    <div class="kanban-wrap" id="kanbanWrap"></div>
    </div>
  `;
  irArriba();
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
        ${items.map(t=>kanbanCardHTML(t)).join('') || '<div class="empty-state">Sin tareas.</div>'}
      </div>
      ${col.estado==='pendiente' ? `<button class="kanban-add" onclick="openModalTarea()">${I.plus}<span>Añadir tarea</span></button>` : ''}
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
    <span class="kc-tag"></span>
    <div class="kc-title">${esc(t.titulo)}</div>
    ${t.descripcion ? `<div class="kc-desc">${esc(t.descripcion)}</div>` : ''}
    ${t.completado_at ? `<div class="kc-date">${I.check}<span>Completada: ${esc(t.completado_at)}</span></div>` : ''}
    <div class="kc-actions">
      <div class="kc-move">
        ${prevEstado ? `<button class="kc-btn" onclick="moverTarea(${t.id},'${prevEstado}')" title="Mover atrás" aria-label="Mover atrás">${I.chevLeft}</button>` : `<span class="kc-btn spacer"></span>`}
        ${nextEstado ? `<button class="kc-btn" onclick="moverTarea(${t.id},'${nextEstado}')" title="Mover adelante" aria-label="Mover adelante">${I.chevRight}</button>` : `<span class="kc-btn spacer"></span>`}
      </div>
      <div class="kc-right">
        <button class="kc-btn" onclick="openModalTarea(${t.id})" title="Editar" aria-label="Editar tarea">${I.pencil}</button>
        <button class="kc-btn danger" onclick="borrarTarea(${t.id})" title="Eliminar" aria-label="Eliminar tarea">${I.close}</button>
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
    `<button type="button" class="color-swatch ${selectedColor===k?'active':''}" style="background:${v}" aria-label="Color ${k}" aria-pressed="${selectedColor===k}" onclick="selectColor('${k}')"></button>`
  ).join('');
}
function selectColor(c){ selectedColor = c; renderColorPicker(); }

async function openModalTarea(id=null){
  document.getElementById('tareaId').value = id || '';
  document.getElementById('tareaTitulo').value = '';
  document.getElementById('tareaDesc').value = '';
  selectedColor = 'verde';
  document.getElementById('tareaModalTitle').innerHTML = (id ? I.pencil : I.plus) + '<span>' + (id ? 'Editar tarea' : 'Nueva tarea') + '</span>';

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

/* ============ REQUISICIONES DE COMPRA ============ */
let reqListas = null;          // listas del formato (php/requisiciones.php) + siguiente consecutivo
let reqTab = 'nueva';
let reqBorrador = null;        // lo escrito en el formulario, para no perderlo al cambiar de sección
let reqLogo = null;            // logo en base64 para el PDF
let reqBusqDebounce = null;
let reqEditId = null;          // id de la requisición que se está editando (null = nueva)
let reqEditCodigo = '';
let reqBorradorNuevo = null;   // borrador de la requisición nueva, guardado mientras se edita otra

function cargarScript(src){
  return new Promise((ok, mal)=>{
    if(document.querySelector(`script[src="${src}"]`)) return ok();
    const s = document.createElement('script'); s.src = src; s.onload = ok; s.onerror = ()=>mal(new Error(src));
    document.head.appendChild(s);
  });
}
/* Carga la librería del PDF y el logo (desde el propio servidor, sin internet) */
async function prepararPDF(){
  try{
    await cargarScript('js/jspdf.umd.min.js');
    // ?v= cambia cada vez que se modifica el archivo, así el navegador no usa una copia vieja
    await cargarScript('js/requisicion-pdf.js?v=<?= @filemtime(__DIR__ . '/js/requisicion-pdf.js') ?: '1' ?>');
    if(!reqLogo){
      const b = await (await fetch('img/logo-coomulpinort.jpg')).blob();
      reqLogo = await new Promise(r=>{ const f = new FileReader(); f.onload = ()=>r(f.result); f.readAsDataURL(b); });
    }
    return true;
  } catch(e){
    toast('No se pudo cargar el generador de PDF (carpeta js/ o img/). No se guardó nada.', I.close);
    return false;
  }
}

async function loadRequisiciones(){
  vistaActual = 'requisiciones';
  activeUser = null; vigViewActiva = false; marcarNav('btnRequisiciones');
  document.getElementById('rightPanel').innerHTML = `
    <div class="view-fade">
    ${encabezado({eyebrow:'Compras', titulo:'Requisiciones', sub:'Formato de requisición de compras MA-PC-FT-001. Llena las casillas y descarga el PDF; cada exportación toma el siguiente consecutivo.'})}
    <div class="seg" role="tablist">
      <button id="reqTabNueva" role="tab" onclick="cambiarTabReq('nueva')">${reqEditId ? I.pencil : I.plus}<span>${reqEditId ? 'Editando requisición' : 'Nueva requisición'}</span></button>
      <button id="reqTabHist" role="tab" onclick="cambiarTabReq('historial')">${I.calendar}<span>Historial</span><span class="eq-count" id="reqHistCount"></span></button>
    </div>
    <div id="reqContenido"></div>
    </div>`;
  irArriba();
  reqListas = await api({action:'requisicion_listas'});
  cambiarTabReq(reqTab);
}

function cambiarTabReq(tab){
  if(reqTab === 'nueva' && document.getElementById('reqForm')) reqBorrador = leerFormReq();
  reqTab = tab;
  document.getElementById('reqTabNueva')?.classList.toggle('on', tab === 'nueva');
  document.getElementById('reqTabHist')?.classList.toggle('on', tab === 'historial');
  if(tab === 'nueva') pintarFormReq(); else pintarHistorialReq();
}

function opciones(lista, sel, conCodigo, vacio='Seleccionar...'){
  return `<option value="">${vacio}</option>` + lista.map(o=>{
    const v = typeof o === 'string' ? o : o.cod;
    const t = typeof o === 'string' ? o : (conCodigo ? `${o.cod} — ${o.nombre}` : o.nombre);
    return `<option value="${esc(v)}" ${String(sel??'')===String(v)?'selected':''}>${esc(t)}</option>`;
  }).join('');
}

function pintarFormReq(){
  const L = reqListas, b = reqBorrador || {};
  const def = (k, lista) => b[k] ?? (lista.length === 1 ? (typeof lista[0]==='string' ? lista[0] : lista[0].cod) : '');
  // Se empieza con una fila; con el botón + se agregan las que hagan falta
  const items = (b.items && b.items.length) ? b.items : [{}];
  const filas = items.map((it, i) => filaReqHTML(i, it)).join('');

  document.getElementById('reqContenido').innerHTML = `
  ${reqEditId ? `<div class="req-edit-bar">${I.pencil}<span>Editando la requisición <b>${esc(reqEditCodigo)}</b>. Al guardar se reemplaza en el historial.</span>
    <button type="button" class="btn btn-ghost btn-sm" onclick="cancelarEdicionReq()">Cancelar edición</button></div>` : ''}
  <form class="card req-sheet" id="reqForm" novalidate onsubmit="event.preventDefault();exportarRequisicion()" oninput="reqCambio(event)" onchange="reqCambio(event)">
    <div class="req-head">
      <div class="req-logo"><img src="img/logo-coomulpinort.jpg" alt="Coomulpinort"></div>
      <div class="req-tit"><small>PROCESO DE COMPRAS · COMPRAS Y CONTRATACIÓN DE SERVICIOS</small><b>FORMATO DE REQUISICIÓN DE COMPRAS</b></div>
      <div class="req-meta"><span>CÓDIGO:</span><span>${esc(L.formato.codigo)}</span><span>VERSIÓN:</span><span>${esc(L.formato.version)}</span><span>FECHA:</span><span>${esc(L.formato.fecha)}</span><span>PÁGINA</span><span>1 de 1</span></div>
    </div>
    <div class="req-body">
      <div class="req-top">
        <div class="rf span4"><label>REQ N°</label><div class="req-code"><b id="reqCodigo"></b><small>Se arma solo: empresa · área · solicitante · consecutivo</small></div></div>
        <div class="rf"><label for="reqFecha">Fecha</label><input type="date" id="reqFecha" name="fecha" required value="${esc(b.fecha || L.hoy)}"></div>
        <div class="rf"><label for="reqCons">Consecutivo</label><input type="number" id="reqCons" name="consecutivo" min="1" step="1" inputmode="numeric" required value="${esc(b.consecutivo ?? L.siguiente)}"><small class="rf-hint">${reqEditId ? 'Consecutivo de esta requisición. Puedes cambiarlo.' : `Sigue el orden (próximo: ${L.siguiente}). Puedes cambiarlo.`}</small></div>
        <div class="rf span2"><label for="reqPrioridad">Prioridad</label><select id="reqPrioridad" name="prioridad" required>${opciones(L.prioridades, b.prioridad)}</select></div>
        <div class="rf span4"><label for="reqEmpresa">Empresa</label><select id="reqEmpresa" name="empresa_cod" required>${opciones(L.empresas, def('empresa_cod', L.empresas))}</select></div>
        <div class="rf span2"><label for="reqArea">Área</label><select id="reqArea" name="area_cod" required>${opciones(L.areas, b.area_cod, true)}</select></div>
        <div class="rf span2"></div>
        <div class="rf span2"><label for="reqSol">Solicitante</label><select id="reqSol" name="solicitante_cod" required>${opciones(L.solicitantes, b.solicitante_cod, true)}</select></div>
        <div class="rf span2"><label for="reqAut">Autorizado por</label><select id="reqAut" name="autorizado_por" required>${opciones(L.autorizado_por, def('autorizado_por', L.autorizado_por))}</select></div>
      </div>

      <div class="req-items-wrap">
        <table class="req-items" id="reqItems">
          <thead><tr><th>Ítem</th><th>Cantidad</th><th>Unidad</th><th>Artículo o servicio</th><th>Proveedor (o sugerido)</th><th>Centro de trabajo</th><th><span class="sr-only">Quitar</span></th></tr></thead>
          <tbody>${filas}</tbody>
        </table>
      </div>
      <div class="req-add-bar">
        <button type="button" class="req-add" id="btnAgregarItem" onclick="agregarFilaReq()">${I.plus}<span>Agregar ítem</span></button>
        <small id="reqItemsCuenta"></small>
      </div>
      <p class="req-nota">En el campo de artículo o servicio, indicar de manera clara, las características del producto o servicio, modelo, color, material de elaboración y cualquier otra especificación importante para solicitar una correcta cotización y proceso de compra.</p>

      <div class="rf"><label for="reqObs">Observaciones</label><textarea id="reqObs" name="observaciones" rows="2" maxlength="2000" placeholder="Información adicional para la cotización (opcional)">${esc(b.observaciones ?? '')}</textarea></div>

      <div class="req-firmas">
        <div class="req-firma">
          <h4>Solicitante</h4>
          <dl class="firma-datos">
            <dt>Nombre</dt><dd id="reqSolNombre">${esc(L.firma_solicitante.nombre)}</dd>
            <dt>Cédula</dt><dd id="reqCedula">${esc(L.firma_solicitante.cedula)}</dd>
            <dt>Cargo</dt><dd id="reqCargo">${esc(L.firma_solicitante.cargo)}</dd>
          </dl>
          <p class="firma-fija">${I.shield}<span>Datos fijos del formato</span></p>
        </div>
        <div class="req-firma vacia"><h4>Revisó</h4><p>Firma a mano en el documento impreso</p></div>
        <div class="req-firma vacia"><h4>Aprobó</h4><p>Firma a mano en el documento impreso</p></div>
      </div>
    </div>
    <div class="req-acciones">
      <span class="hint">${reqEditId ? 'Al guardar se actualiza en el historial y se descarga el PDF corregido.' : 'Al exportar se guarda en el historial con el consecutivo que aparece arriba.'}</span>
      ${reqEditId ? `<button type="button" class="btn btn-ghost" onclick="cancelarEdicionReq()">Cancelar</button>` : `<button type="button" class="btn btn-ghost" onclick="limpiarReq()">Limpiar</button>`}
      <button type="submit" class="btn btn-primary" id="btnExportarReq">${I.download}<span>${reqEditId ? 'Guardar cambios y descargar PDF' : 'Guardar y descargar PDF'}</span></button>
    </div>
  </form>`;
  document.querySelectorAll('#reqItems textarea').forEach(autoAltura);
  renumerarFilasReq();
  actualizarCodigoReq();
}

function filaReqHTML(i, it = {}){
  const L = reqListas;
  return `<tr>
      <td>${i+1}</td>
      <td style="width:92px"><input type="number" min="0" step="any" inputmode="decimal" data-k="cantidad" value="${esc(it.cantidad ?? '')}" aria-label="Cantidad"></td>
      <td style="width:130px"><select data-k="unidad" aria-label="Unidad">${opciones(L.unidades, it.unidad, false, '')}</select></td>
      <td><textarea rows="1" data-k="articulo" maxlength="1000" aria-label="Artículo o servicio">${esc(it.articulo ?? '')}</textarea></td>
      <td style="width:190px"><input data-k="proveedor" maxlength="200" value="${esc(it.proveedor ?? '')}" aria-label="Proveedor"></td>
      <td style="width:200px"><select data-k="centro_trabajo" aria-label="Centro de trabajo">${opciones(L.centros_trabajo, it.centro_trabajo, false, '')}</select></td>
      <td style="width:44px"><button type="button" class="req-del" title="Quitar ítem" aria-label="Quitar ítem" onclick="quitarFilaReq(this)">${I.close}</button></td>
    </tr>`;
}
function renumerarFilasReq(){
  const filas = [...document.querySelectorAll('#reqItems tbody tr')];
  filas.forEach((tr, i)=>{
    tr.firstElementChild.textContent = i + 1;
    const art = tr.querySelector('[data-k=articulo]');
    art.placeholder = i === 0 ? 'Ej: Monitor Samsung 22" S3 Full HD HDMI VGA 120Hz' : '';
  });
  const max = reqListas.max_items;
  const btn = document.getElementById('btnAgregarItem'); if(btn) btn.disabled = filas.length >= max;
  const c = document.getElementById('reqItemsCuenta');
  if(c) c.textContent = `${filas.length} ítem${filas.length==1?'':'s'}` + (filas.length > 15 ? ` · el PDF tendrá ${Math.ceil(filas.length/15)} páginas` : '') + (filas.length >= max ? ` · máximo ${max}` : '');
  marcarFilasReq();
}
function agregarFilaReq(){
  const tb = document.querySelector('#reqItems tbody');
  if(tb.children.length >= reqListas.max_items){ toast(`Máximo ${reqListas.max_items} ítems por requisición`, I.close); return; }
  tb.insertAdjacentHTML('beforeend', filaReqHTML(tb.children.length));
  renumerarFilasReq();
  const nueva = tb.lastElementChild;
  autoAltura(nueva.querySelector('textarea'));
  nueva.querySelector('[data-k=cantidad]').focus();
  reqBorrador = leerFormReq();
}
function quitarFilaReq(btn){
  const tb = document.querySelector('#reqItems tbody');
  const tr = btn.closest('tr');
  if(tb.children.length === 1){
    // Siempre queda al menos una fila: solo se vacía
    tr.querySelectorAll('[data-k]').forEach(e=>{ e.value = ''; });
    autoAltura(tr.querySelector('textarea'));
  } else {
    tr.remove();
  }
  renumerarFilasReq();
  reqBorrador = leerFormReq();
}

function autoAltura(t){ t.style.height = 'auto'; t.style.height = Math.max(34, t.scrollHeight) + 'px'; }
function marcarFilasReq(){
  document.querySelectorAll('#reqItems tbody tr').forEach(tr=>{
    tr.classList.toggle('lleno', [...tr.querySelectorAll('[data-k]')].some(e=>e.value.trim() !== ''));
  });
}
function reqCambio(e){
  if(e.target.tagName === 'TEXTAREA' && e.target.closest('#reqItems')) autoAltura(e.target);
  if(e.target.closest('#reqItems')) marcarFilasReq();
  actualizarCodigoReq();
  reqBorrador = leerFormReq();
}
function actualizarCodigoReq(){
  const v = id => document.getElementById(id)?.value || '';
  const part = (x, ph) => x ? esc(x) : `<i>${ph}</i>`;
  const el = document.getElementById('reqCodigo');
  if(el) el.innerHTML = `${part(v('reqEmpresa'),'EMP')}-${part(v('reqArea'),'ÁREA')}-${part(v('reqSol'),'SOL')}-${part(v('reqCons'),'N.°')}`;
}
function leerFormReq(){
  const f = document.getElementById('reqForm'); if(!f) return reqBorrador;
  const d = {};
  ['fecha','consecutivo','prioridad','empresa_cod','area_cod','solicitante_cod','autorizado_por','observaciones'].forEach(k=>{ d[k] = f.elements[k].value; });
  d.items = [...document.querySelectorAll('#reqItems tbody tr')].map(tr=>{
    const o = {}; tr.querySelectorAll('[data-k]').forEach(e=>{ o[e.dataset.k] = e.value; }); return o;
  });
  return d;
}
function limpiarReq(){
  if(reqBorrador && !confirm('¿Borrar todo lo escrito en el formulario?')) return;
  reqBorrador = null; pintarFormReq();
}

/* Revisión en la página (el servidor vuelve a revisar todo) */
function validarReqLocal(d){
  if(!/^\d{4}-\d{2}-\d{2}$/.test(d.fecha || '')) return ['reqFecha','Escribe una fecha válida.'];
  if(!/^\d+$/.test(String(d.consecutivo).trim()) || Number(d.consecutivo) < 1) return ['reqCons','El consecutivo debe ser un número entero mayor que 0.'];
  if(!d.prioridad) return ['reqPrioridad','Selecciona la prioridad.'];
  if(!d.empresa_cod) return ['reqEmpresa','Selecciona la empresa.'];
  if(!d.area_cod) return ['reqArea','Selecciona el área.'];
  if(!d.solicitante_cod) return ['reqSol','Selecciona el solicitante.'];
  if(!d.autorizado_por) return ['reqAut','Selecciona quién autoriza.'];
  let hay = false;
  for(let i = 0; i < d.items.length; i++){
    const it = d.items[i], fila = document.querySelectorAll('#reqItems tbody tr')[i];
    const algo = Object.values(it).some(v=>String(v).trim() !== '');
    if(!algo) continue;
    hay = true;
    if(!String(it.articulo).trim()) return [fila.querySelector('[data-k=articulo]'), `Fila ${i+1}: escribe el artículo o servicio.`];
    if(!(Number(String(it.cantidad).replace(',','.')) > 0)) return [fila.querySelector('[data-k=cantidad]'), `Fila ${i+1}: la cantidad debe ser mayor que 0.`];
  }
  if(!hay) return [document.querySelector('#reqItems [data-k=articulo]'), 'Agrega al menos un artículo o servicio.'];
  return null;
}

async function exportarRequisicion(){
  const d = leerFormReq();
  const err = validarReqLocal(d);
  if(err){
    const el = typeof err[0] === 'string' ? document.getElementById(err[0]) : err[0];
    toast(err[1], I.close); if(el){ el.focus(); el.scrollIntoView({block:'center', behavior:'smooth'}); }
    return;
  }
  const cons = Number(d.consecutivo);
  if(reqEditId) return guardarEdicionReq(d, cons);
  // Si se deja el número sugerido, el servidor asigna el siguiente libre (por si alguien exportó al mismo tiempo)
  const envio = {...d, consecutivo: cons === reqListas.siguiente ? '' : cons};
  if(!confirm(`Se guardará la requisición N.° ${cons} con fecha ${fmtFecha(d.fecha)} y se descargará el PDF.\n\nDespués no se puede modificar. ¿Continuar?`)) return;
  const btn = document.getElementById('btnExportarReq');
  btn.disabled = true; btn.querySelector('span').textContent = 'Guardando...';
  try{
    if(!(await prepararPDF())) return;
    const res = await api({action:'crear_requisicion'}, envio);
    const r = res.requisicion;
    RequisicionPDF.descargar(r, reqListas.formato, reqLogo);
    toast(`Requisición ${r.codigo} guardada y descargada`);
    reqBorrador = null;
    reqListas = await api({action:'requisicion_listas'});
    pintarFormReq();
    irArriba();
  } finally {
    const b2 = document.getElementById('btnExportarReq');
    if(b2){ b2.disabled = false; b2.querySelector('span').textContent = 'Guardar y descargar PDF'; }
  }
}

/* ---- Edición de requisiciones guardadas ---- */
async function editarReq(id, btn){
  if(btn) btn.disabled = true;
  try{
    const d = await api({action:'requisicion', id});
    const r = d.requisicion;
    if(!reqEditId) reqBorradorNuevo = reqBorrador;   // no perder lo que se llevaba de una nueva
    reqEditId = r.id; reqEditCodigo = r.codigo;
    reqBorrador = {
      fecha: r.fecha, consecutivo: r.consecutivo, prioridad: r.prioridad, empresa_cod: r.empresa_cod,
      area_cod: r.area_cod, solicitante_cod: String(r.solicitante_cod), autorizado_por: r.autorizado_por,
      observaciones: r.observaciones || '',
      items: (r.items || []).map(it => ({cantidad: String(Number(it.cantidad)), unidad: it.unidad || '', articulo: it.articulo,
        proveedor: it.proveedor || '', centro_trabajo: it.centro_trabajo || ''}))
    };
    reqTab = 'nueva';
    loadRequisiciones();
  } finally { if(btn) btn.disabled = false; }
}
function salirEdicionReq(){
  reqEditId = null; reqEditCodigo = '';
  reqBorrador = reqBorradorNuevo; reqBorradorNuevo = null;
}
function cancelarEdicionReq(){
  if(!confirm('¿Descartar los cambios de esta requisición?')) return;
  salirEdicionReq();
  reqTab = 'historial';
  loadRequisiciones();
}
async function guardarEdicionReq(d, cons){
  if(!confirm(`Se guardarán los cambios de la requisición ${reqEditCodigo} (N.° ${cons}) y se descargará el PDF corregido. ¿Continuar?`)) return;
  const btn = document.getElementById('btnExportarReq');
  btn.disabled = true; btn.querySelector('span').textContent = 'Guardando...';
  try{
    if(!(await prepararPDF())) return;
    const res = await api({action:'actualizar_requisicion'}, {...d, id: reqEditId});
    const r = res.requisicion;
    RequisicionPDF.descargar(r, reqListas.formato, reqLogo);
    toast(`Requisición ${r.codigo} actualizada`);
    salirEdicionReq();
    reqTab = 'historial';
    loadRequisiciones();
  } finally {
    const b2 = document.getElementById('btnExportarReq');
    if(b2){ b2.disabled = false; b2.querySelector('span').textContent = 'Guardar cambios y descargar PDF'; }
  }
}

/* ---- Historial ---- */
function pintarHistorialReq(){
  document.getElementById('reqContenido').innerHTML = `
    <div class="toolbar">
      <div class="field-ic"><span class="sic">${I.search}</span><input class="si" id="reqBuscar" type="text" placeholder="Buscar por código, consecutivo, solicitante, área, artículo o proveedor..." aria-label="Buscar requisiciones" oninput="clearTimeout(reqBusqDebounce);reqBusqDebounce=setTimeout(cargarHistorialReq,200)"></div>
      <button class="btn btn-danger" id="btnVaciarReq" style="display:none" onclick="vaciarHistorialReq()">${I.trash}<span>Borrar todo</span></button>
    </div>
    <div class="vg-table-wrap" id="reqTabla"><div class="empty-state" style="border:none">Cargando...</div></div>`;
  cargarHistorialReq();
}
async function cargarHistorialReq(){
  const box = document.getElementById('reqTabla'); if(!box) return;
  const q = document.getElementById('reqBuscar')?.value.trim() || '';
  const d = await api({action:'requisiciones', q});
  const rows = d.requisiciones || [];
  const cnt = document.getElementById('reqHistCount'); if(cnt && !q) cnt.textContent = rows.length;
  const bv = document.getElementById('btnVaciarReq'); if(bv && !q) bv.style.display = rows.length ? 'inline-flex' : 'none';
  if(!rows.length){
    box.innerHTML = `<div class="empty-state" style="border:none;padding:2rem">${q ? 'Ninguna requisición coincide con la búsqueda.' : 'Todavía no hay requisiciones. Llena el formulario en “Nueva requisición” y expórtala.'}</div>`;
    return;
  }
  box.innerHTML = `<table class="vgt">
    <thead><tr><th>N.°</th><th>REQ N°</th><th>Fecha</th><th>Solicitante</th><th>Artículos</th><th>Prioridad</th><th></th></tr></thead>
    <tbody>${rows.map(r=>`<tr>
      <td><span class="req-n">${r.orden}</span></td>
      <td><div class="vg-name" style="font-family:var(--mono);font-weight:500">${esc(r.codigo)}</div><div class="vg-sub">${esc(r.area_nombre)}</div></td>
      <td class="vg-date">${fmtFecha(r.fecha)}</td>
      <td><div class="vg-name">${esc(r.solicitante_nombre)}</div></td>
      <td style="max-width:320px"><div class="vg-name" style="font-weight:600">${esc(r.primer_item || '')}</div><div class="vg-sub">${r.total_items} ítem${r.total_items==1?'':'s'}</div></td>
      <td><span class="req-prio ${esc(r.prioridad)}">${esc(r.prioridad)}</span></td>
      <td><div class="vg-actions">
        <button class="btn btn-ghost btn-ic btn-sm" title="Editar" aria-label="Editar requisición ${esc(r.codigo)}" onclick="editarReq(${r.id}, this)">${I.pencil}</button>
        <button class="btn btn-ghost btn-sm" onclick="descargarReq(${r.id}, this)">${I.download}<span>PDF</span></button>
        <button class="btn btn-danger btn-ic btn-sm" title="Borrar del historial" aria-label="Borrar requisición ${esc(r.codigo)}" onclick="borrarReq(${r.id}, '${esc(r.codigo)}', ${r.orden})">${I.trash}</button>
      </div></td>
    </tr>`).join('')}</tbody></table>`;
}
async function borrarReq(id, codigo, orden){
  if(!confirm(`¿Borrar la requisición N.° ${orden} (${codigo}) del historial?\n\nSe borra definitivamente y no se puede recuperar. Las demás se renumeran.`)) return;
  await api({action:'eliminar_requisicion'}, {id});
  toast(`Requisición ${codigo} borrada`, I.trash);
  if(reqEditId === id) salirEdicionReq();
  await despuesDeBorrarReq();
}
async function vaciarHistorialReq(){
  if(!confirm('¿Borrar TODO el historial de requisiciones?\n\nSe borran definitivamente y no se pueden recuperar.')) return;
  await api({action:'vaciar_requisiciones'}, {});
  toast('Historial de requisiciones borrado', I.trash);
  if(reqEditId) salirEdicionReq();
  await despuesDeBorrarReq();
}
/* El consecutivo sugerido puede cambiar al borrar: se recargan las listas y el historial */
async function despuesDeBorrarReq(){
  reqListas = await api({action:'requisicion_listas'});
  if(reqBorrador) reqBorrador.consecutivo = undefined;
  await cargarHistorialReq();
}
async function descargarReq(id, btn){
  btn.disabled = true;
  try{
    if(!(await prepararPDF())) return;
    const d = await api({action:'requisicion', id});
    RequisicionPDF.descargar(d.requisicion, reqListas.formato, reqLogo);
  } finally { btn.disabled = false; }
}

/* ============ LISTADO IP ============ */
let ipDatos = null;

/* Ordena IPs por número (192.168.2.35 antes que 192.168.2.101) */
const ipNum = ip => String(ip).split('.').reduce((a, n) => a * 256 + Number(n), 0);

async function loadListadoIP(){
  vistaActual = 'ips';
  activeUser = null; vigViewActiva = false; marcarNav('btnIPs');
  document.getElementById('rightPanel').innerHTML = `
    <div class="view-fade">
    ${encabezado({eyebrow:'Red', titulo:'Listado IP', sub:'Direcciones IP de la red de la sede administrativa y la planta. “Abrir” lleva a la página del equipo (funciona desde la red de la empresa).'})}
    <div class="toolbar">
      <div class="field-ic"><span class="sic">${I.search}</span><input class="si" id="ipBuscar" type="text" placeholder="Buscar por IP, nombre, equipo, ubicación..." aria-label="Buscar IP" oninput="pintarListadoIP()"></div>
    </div>
    <div id="ipContenido"><div class="empty-state">Cargando...</div></div>
    </div>`;
  irArriba();
  ipDatos = await api({action:'listado_ip'});
  pintarListadoIP();
}

function pintarListadoIP(){
  const box = document.getElementById('ipContenido'); if(!box || !ipDatos) return;
  const q = (document.getElementById('ipBuscar')?.value || '').trim().toLowerCase();
  const filtra = (lista, campos) => lista
    .filter(f => !q || campos.some(k => String(f[k] ?? '').toLowerCase().includes(q)))
    .sort((a, b) => ipNum(a.ip) - ipNum(b.ip));
  const adm = filtra(ipDatos.administrativa, ['ip','nombre','oficina','ssid','estado']);
  const pla = filtra(ipDatos.planta, ['ip','equipo','ubicacion','productos']);
  const ipCelda = ip => `<button type="button" class="ip" title="Copiar IP" onclick="copiarIP('${esc(ip)}')">${esc(ip)}</button>`;
  const abrir = f => `<a class="ip-abrir" href="${esc(f.url)}" target="_blank" rel="noopener noreferrer">${I.external}<span>Abrir</span></a>`;
  const nombre = n => (!n || n === 'N/A') ? `<span class="ip-sin">Sin nombre</span>` : `<span class="vg-name">${esc(n)}</span>`;
  const seccion = (titulo, icono, total, filas, cabecera, fila) => `
    <section class="ip-sec">
      <div class="ip-sec-h"><h2>${icono}<span>${titulo}</span></h2><small>${filas.length === total ? total : `${filas.length} de ${total}`} IP${total==1?'':'s'}</small></div>
      <div class="vg-table-wrap">${filas.length ? `<table class="vgt" style="min-width:760px"><thead><tr>${cabecera}</tr></thead><tbody>${filas.map(fila).join('')}</tbody></table>`
        : '<div class="empty-state" style="border:none">Ninguna IP coincide con la búsqueda.</div>'}</div>
    </section>`;
  box.innerHTML =
    seccion('Sede administrativa', I.building, ipDatos.administrativa.length, adm,
      '<th>#</th><th>Dirección IP</th><th>Nombre</th><th>Oficina</th><th>SSID</th><th>Segmento</th><th>Estado</th><th></th>',
      (f, i) => `<tr>
        <td class="ip-n">${i + 1}</td>
        <td>${ipCelda(f.ip)}</td>
        <td>${nombre(f.nombre)}</td>
        <td>${esc(f.oficina ?? '')}</td>
        <td><span class="ip-ssid">${esc(f.ssid ?? '')}</span></td>
        <td>${esc(f.segmento ?? '')}</td>
        <td><span class="ip-estado ${String(f.estado||'').toLowerCase()==='operativo' ? '' : 'otro'}">${esc(f.estado || '—')}</span></td>
        <td style="text-align:right">${abrir(f)}</td>
      </tr>`) +
    seccion('Planta', I.chip, ipDatos.planta.length, pla,
      '<th>#</th><th>Ubicación</th><th>Equipo</th><th>Dirección IP</th><th>Productos</th><th></th>',
      (f, i) => `<tr>
        <td class="ip-n">${i + 1}</td>
        <td>${esc(f.ubicacion ?? '')}</td>
        <td><span class="vg-name">${esc(f.equipo ?? '')}</span></td>
        <td>${ipCelda(f.ip)}</td>
        <td>${esc(f.productos ?? '')}</td>
        <td style="text-align:right">${abrir(f)}</td>
      </tr>`);
}

async function copiarIP(ip){
  try { await navigator.clipboard.writeText(ip); toast(`IP ${ip} copiada`); }
  catch(e){ toast(`No se pudo copiar. La IP es ${ip}`, I.close); }
}

/* ============ INIT ============ */
document.getElementById('btnTareas').innerHTML = I.kanban + '<span>Tareas</span>';
document.getElementById('btnUsuarios').innerHTML = I.users + '<span>Usuarios</span>';
document.getElementById('btnImpresoras').innerHTML = I.printer + '<span>Impresoras</span>';
document.getElementById('btnVigencias').innerHTML = I.calendar + '<span>Vigencias</span>';
document.getElementById('btnRequisiciones').innerHTML = I.doc + '<span>Requisiciones</span>';
document.getElementById('btnIPs').innerHTML = I.network + '<span>Listado IP</span>';
document.getElementById('btnStats').innerHTML = I.chart + '<span>Estadísticas</span>';
document.getElementById('searchTopIcon').innerHTML = I.search;
document.getElementById('btnBuscarM').innerHTML = I.search;
document.getElementById('btnPapelera').innerHTML = I.trash;
document.getElementById('asgSearchIcon').innerHTML = I.search;
document.getElementById('btnGuardarEquipo').innerHTML = I.check + '<span>Guardar</span>';
document.getElementById('btnVaciarPapelera').innerHTML = I.trash + '<span>Vaciar papelera</span>';
document.getElementById('swModalTitle').innerHTML = I.disk + '<span>Agregar software</span>';
document.getElementById('papeleraModalTitle').innerHTML = I.trash + '<span>Papelera de reciclaje</span>';
document.getElementById('asgTitle').innerHTML = I.refresh + '<span>Asignar equipo existente</span>';

/* Atajos: "/" abre el buscador de usuarios; Esc cierra el modal abierto */
document.addEventListener('keydown', e=>{
  const tag = (e.target.tagName||'').toLowerCase();
  if(e.key === '/' && !['input','textarea','select'].includes(tag)){ e.preventDefault(); abrirBuscador(); }
  if(e.key === 'Escape'){
    const abiertos = [...document.querySelectorAll('.modal-bg')].filter(m=>m.style.display==='flex');
    if(abiertos.length) closeModal(abiertos[abiertos.length-1].id);
  }
});

toggleFormFields();
loadKanban();
</script>
</body>
</html>
