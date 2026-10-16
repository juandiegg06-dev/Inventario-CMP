/*
 * PDF del FORMATO DE REQUISICIÓN DE COMPRAS (MA-PC-FT-001).
 * Reproduce la hoja "FORMATO REQUISICIONES" del Excel en hojas carta horizontales, todo en blanco
 * (sin los colores del Excel). La tabla tiene siempre 15 filas por página, como el formato; si hay
 * más de 15 ítems, continúa en otra página con el mismo encabezado. Observaciones y firmas en la última.
 * Requiere js/jspdf.umd.min.js (window.jspdf).
 */
(function(){
  const NEGRO   = [0, 0, 0];

  const fmtFecha = f => { if(!f) return ''; const [y,m,d] = String(f).slice(0,10).split('-'); return `${Number(d)}/${Number(m)}/${y}`; };
  const fmtCant = n => { const v = Number(n); return Number.isInteger(v) ? String(v) : v.toLocaleString('es-CO', {maximumFractionDigits:2}); };

  const POR_PAGINA = 15;

  function crear(req, formato, logo){
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation:'landscape', unit:'mm', format:'letter' });
    const M = 8, W = 279.4 - 2*M;          // margen y ancho útil
    const items = req.items || [];
    const paginas = Math.max(1, Math.ceil(items.length / POR_PAGINA));
    const totalFilas = paginas * POR_PAGINA;   // 15 filas por página, las sobrantes en blanco
    let y = M;
    doc.setLineWidth(0.2); doc.setDrawColor(...NEGRO); doc.setTextColor(...NEGRO);

    /* Celda: borde, relleno y texto que se ajusta (reduce letra y parte en líneas si no cabe) */
    function celda(x, yy, w, h, texto, o = {}){
      if(o.fill){ doc.setFillColor(...o.fill); doc.rect(x, yy, w, h, o.borde === false ? 'F' : 'FD'); }
      else if(o.borde !== false) doc.rect(x, yy, w, h);
      if(texto === undefined || texto === null || texto === '') return;
      const estilo = (o.bold ? 'bold' : '') + (o.italic ? 'italic' : '') || 'normal';
      doc.setFont('helvetica', estilo);
      const pad = o.pad ?? 1.2;
      let size = o.size || 7.5, lineas, lh;
      for(;;){
        doc.setFontSize(size);
        lh = size * 0.3528 * 1.15;
        lineas = doc.splitTextToSize(String(texto), w - 2*pad);
        if(lineas.length * lh <= h - 0.6 || size <= 4.5) break;
        size -= 0.3;
      }
      const maxL = Math.max(1, Math.floor((h - 0.6) / lh));
      if(lineas.length > maxL){ lineas = lineas.slice(0, maxL); lineas[maxL-1] = lineas[maxL-1].replace(/.{0,2}$/, '…'); }
      const alto = lineas.length * lh;
      let ty = o.valign === 'top' ? yy + pad + lh*0.8 : yy + (h - alto)/2 + lh*0.8;
      const align = o.align || 'center';
      const tx = align === 'left' ? x + pad : align === 'right' ? x + w - pad : x + w/2;
      lineas.forEach(l => { doc.text(l, tx, ty, {align}); ty += lh; });
    }

    for(let pag = 1; pag <= paginas; pag++){
      if(pag > 1) doc.addPage();
      y = M;
      pagina(pag);
    }

    function pagina(pag){
    const ultima = pag === paginas;
    /* ---------- Encabezado ---------- */
    const hH = 4.6, wLogo = 50.5, wTit = 150, wLab = 25.2;
    const wVal = W - wLogo - wTit - wLab;
    const xT = M + wLogo, xL = xT + wTit, xV = xL + wLab;
    doc.rect(M, y, W, hH*4);
    celda(M, y, wLogo, hH*4, '');
    if(logo){
      const s = hH*4 - 2.2;
      try { doc.addImage(logo, 'JPEG', M + (wLogo - s)/2, y + 1.1, s, s); } catch(e){}
    }
    celda(xT, y, wTit, hH, 'PROCESO DE COMPRAS', {size:8});
    celda(xT, y + hH, wTit, hH, 'COMPRAS Y CONTRATACIÓN DE SERVICIOS', {size:8});
    celda(xT, y + 2*hH, wTit, 2*hH, 'FORMATO DE REQUISICIÓN DE COMPRAS', {size:9.5, bold:true});
    [['CÓDIGO:', formato.codigo], ['VERSIÓN:', formato.version], ['FECHA:', formato.fecha]].forEach(([l, v], i) => {
      celda(xL, y + i*hH, wLab, hH, l, {bold:true, size:7.5});
      celda(xV, y + i*hH, wVal, hH, v, {size:7.5});
    });
    celda(xL, y + 3*hH, wLab + wVal, hH, `PÁGINA ${pag} de ${paginas}`, {bold:true, size:7.5});
    y += hH*4 + 4;

    /* ---------- Datos generales ---------- */
    const wEt = 21.5, rH = 5.2, gap = 2.6;
    const lab = (x, w, t) => celda(x, y, w, rH, t, {bold:true, size:7});
    // REQ N°
    lab(M, wEt, 'REQ N°');
    celda(M + wEt, y, 37, rH, req.codigo, {bold:true, size:8});
    y += rH + gap;
    // Fecha · Consecutivo · Prioridad
    lab(M, wEt, 'FECHA:');
    celda(M + wEt, y, 37, rH, fmtFecha(req.fecha), {size:7.5});
    lab(M + 77, 20, 'CONSECUTIVO:');
    celda(M + 97, y, 20, rH, String(req.consecutivo), {size:7.5});
    lab(M + 197, 16, 'PRIORIDAD:');
    celda(M + 213, y, W - 213, rH, req.prioridad, {size:7.5});
    y += rH + gap;
    // Empresa
    lab(M, wEt, 'EMPRESA:');
    celda(M + wEt, y, 173, rH, req.empresa_nombre, {bold:true, size:7.5});
    y += rH + gap;
    // Área
    lab(M, wEt, 'ÁREA:');
    celda(M + wEt, y, 15, rH, req.area_cod, {size:7.5});
    celda(M + wEt + 15, y, 158, rH, req.area_nombre, {size:7.5});
    y += rH + gap;
    // Solicitante · Autorizado por
    lab(M, wEt, 'SOLICITANTE:');
    celda(M + wEt, y, 15, rH, String(req.solicitante_cod), {size:7.5});
    celda(M + wEt + 15, y, 60, rH, req.solicitante_nombre, {size:7});
    lab(M + 120, 74.5, 'AUTORIZADO POR:');
    celda(M + 194.5, y, W - 194.5, rH, req.autorizado_por, {size:7});
    y += rH + gap;

    /* ---------- Tabla de artículos (15 filas por página, como el formato) ---------- */
    const cols = [['ITEM',14],['CANTIDAD',17],['UNIDAD',19],['ARTICULO O SERVICIO',0],['PROVEEDOR (O SUGERIDO)',39],['CENTRO DE TRABAJO',45]];
    cols[3][1] = W - cols.reduce((a,c)=>a + c[1], 0);
    let x = M;
    cols.forEach(([t, w]) => { celda(x, y, w, 5, t, {bold:true, size:7}); x += w; });
    y += 5;
    const fH = 5.0, inicio = (pag - 1) * POR_PAGINA;
    const fin = Math.min(totalFilas, inicio + POR_PAGINA);
    for(let i = inicio; i < fin; i++){
      const it = items[i];
      const vals = it ? [String(i+1), fmtCant(it.cantidad), it.unidad || '', it.articulo || '', it.proveedor || '', it.centro_trabajo || '']
                      : [String(i+1), '', '', '', '', ''];
      x = M;
      cols.forEach(([, w], k) => { celda(x, y, w, fH, vals[k], {size:7, bold:k===0}); x += w; });
      y += fH;
    }
    // Nota (verde)
    celda(M, y, W, 8, 'En el campo de artículo o servicio, indicar de manera clara, las caracteristicas del producto o servicio, modelo, color, material de elaboración y cualquier otra especificación importante para solicitar una correcta cotización y proceso de compra.', {italic:true, size:6.8, align:'left', pad:2});
    y += 8 + gap;

    if(!ultima){
      celda(M, y, W, 6, `Continúa en la página ${pag + 1}…`, {borde:false, italic:true, size:7.5, align:'right'});
      return;
    }

    /* ---------- Observaciones ---------- */
    celda(M, y, W, 13, req.observaciones || '', {size:7.5});
    y += 13 + gap;

    /* ---------- Firmas ---------- */
    const wS = 76.8, wR = 115.4, wA = W - wS - wR;
    const fy = y, cabH = 4.2, espH = 13, lH = 3.9;
    celda(M, fy, wS, cabH, 'SOLICITANTE', {bold:true, size:7});
    celda(M + wS, fy, wR, cabH, 'REVISO', {bold:true, size:7});
    celda(M + wS + wR, fy, wA, cabH, 'APROBO', {bold:true, size:7});
    let sy = fy + cabH;
    celda(M, sy, wS, espH, '');                                     // espacio para la firma
    sy += espH;
    celda(M, sy, wS, lH, 'FIRMA', {size:6.5});
    sy += lH;
    [['NOMBRE:', req.firma_nombre || req.solicitante_nombre], ['CEDULA:', req.sol_cedula || ''], ['CARGO:', req.sol_cargo || '']].forEach(([l, v]) => {
      celda(M, sy, wEt, lH, l, {bold:true, size:6.5, align:'left'});
      celda(M + wEt, sy, wS - wEt, lH, v, {size:6.5});
      sy += lH;
    });
    const altoFirma = sy - (fy + cabH);
    celda(M + wS, fy + cabH, wR, altoFirma - lH, '');
    celda(M + wS, sy - lH, wR, lH, 'FIRMA', {size:6.5});
    celda(M + wS + wR, fy + cabH, wA, altoFirma - lH, '');
    celda(M + wS + wR, sy - lH, wA, lH, 'FIRMA', {size:6.5});
    }

    doc.setProperties({ title: `Requisición ${req.codigo}`, subject: 'Formato de requisición de compras MA-PC-FT-001', creator: 'InvCMP' });
    return doc;
  }

  window.RequisicionPDF = {
    crear,
    descargar(req, formato, logo){ crear(req, formato, logo).save(`Requisicion_${req.codigo}.pdf`); },
  };
})();
