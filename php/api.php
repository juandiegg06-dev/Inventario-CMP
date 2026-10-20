<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/config.php';

/**
 * Función robusta para unificar nombres de áreas con errores o variaciones
 */
function normalizarArea(string $area): string {
    $a = trim($area);
    $lower = mb_strtolower($a);
    if ($lower === 'aministrativa' || $lower === 'administrativa') return 'Administrativa';
    if ($lower === 'archivo' || $lower === 'archivos') return 'Archivos';
    if ($lower === 'consejo' || $lower === 'consejos') return 'Consejos';
    if ($lower === 'tecnologia' || $lower === 'ti') return 'TI';
    
    return mb_convert_case($a, MB_CASE_TITLE, "UTF-8");
}

/**
 * Agrupa las filas de `equipos` por responsable.
 */
function agruparResponsables(PDO $pdo, string $q = ''): array {
    $rows = $pdo->query("SELECT responsable, area, sede, componente, item FROM equipos WHERE deleted_at IS NULL")->fetchAll();

    $grupos = [];
    foreach ($rows as $r) {
        $nombre = trim($r['responsable']);
        if(empty($nombre)) continue;

        $key = mb_strtolower($nombre);
        if (!isset($grupos[$key])) {
            $grupos[$key] = [
                'responsable' => $nombre,
                'area' => normalizarArea($r['area']),
                'sede' => trim($r['sede']),
                'total_equipos' => 0,
                'total_items' => 0,
                '_minItemPC' => PHP_INT_MAX,
            ];
        }
        $grupos[$key]['total_items']++;
        if ($r['componente'] === 'Equipo de computo') {
            $grupos[$key]['total_equipos']++;
            if ((int)$r['item'] < $grupos[$key]['_minItemPC']) {
                $grupos[$key]['_minItemPC'] = (int)$r['item'];
                $grupos[$key]['area'] = normalizarArea($r['area']);
                $grupos[$key]['sede'] = trim($r['sede']);
            }
        }
    }

    $out = array_values($grupos);
    foreach ($out as &$g) unset($g['_minItemPC']);
    unset($g);

    if ($q !== '') {
        $ql = mb_strtolower($q);
        $out = array_values(array_filter($out, function ($g) use ($ql) {
            return mb_strpos(mb_strtolower($g['responsable']), $ql) !== false
                || mb_strpos(mb_strtolower($g['area']), $ql) !== false
                || mb_strpos(mb_strtolower($g['sede']), $ql) !== false;
        }));
    }

    usort($out, fn($a, $b) => strcasecmp($a['responsable'], $b['responsable']));
    return $out;
}

// ---------------------------------------------------------------------------
// Vigencias (licencias / garantías de hardware o software)
// ---------------------------------------------------------------------------
const VIGENCIA_ALERTA_DIAS = 30;

/** Crea la tabla `vigencias` la primera vez que se necesita (instalaciones existentes). */
function asegurarTablaVigencias(PDO $pdo): void {
    static $listo = false;
    if ($listo) return;
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `vigencias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `equipo_id` int(11) DEFAULT NULL,
  `tipo` enum('Software','Hardware') NOT NULL DEFAULT 'Software',
  `nombre` varchar(200) NOT NULL,
  `proveedor` varchar(150) DEFAULT NULL,
  `referencia` varchar(250) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_vencimiento` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vig_equipo` (`equipo_id`),
  KEY `idx_vig_vencimiento` (`fecha_vencimiento`),
  CONSTRAINT `fk_vig_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    $listo = true;
}

function responderError(int $codigo, string $msg): void {
    http_response_code($codigo);
    echo json_encode(['error' => $msg]);
}

function fechaValida(?string $f): bool {
    if ($f === null || $f === '') return false;
    $d = DateTime::createFromFormat('Y-m-d', $f);
    return $d && $d->format('Y-m-d') === $f;
}

function textoOpcional($v, int $max): ?string {
    $v = trim((string)($v ?? ''));
    if ($v === '') return null;
    return mb_substr($v, 0, $max);
}

// ---------------------------------------------------------------------------
// Validaciones de equipos, software y tareas
// ---------------------------------------------------------------------------
const COMPONENTES_VALIDOS = ['Equipo de computo', 'Mouse', 'Teclado', 'Monitor', 'Otro'];
const COLORES_TAREA = ['verde', 'amarillo', 'rojo', 'azul', 'morado'];
const ESTADOS_TAREA = ['pendiente', 'en_proceso', 'completada'];
/** Seriales "de relleno" que no cuentan como repetidos. */
const SERIALES_GENERICOS = ['', 'N/A', 'NA', 'N.A', '-', '--', 'S/N', 'SN', 'SIN SERIAL', 'NO TIENE', '0',
    // valores de relleno que traen algunas placas base de fábrica
    'DEFAULT STRING', 'TO BE FILLED BY O.E.M.', 'SYSTEM SERIAL NUMBER', 'NONE', 'NOT APPLICABLE', '0123456789'];

/** Largo máximo de cada columna editable de `equipos`. */
const EQUIPO_LARGOS = [
    'sede'=>100,'area'=>100,'responsable'=>150,'componente'=>100,'tipo_equipo'=>100,'usuario_actual'=>150,
    'nombre_completo'=>200,'grupo_usuario'=>150,'marca'=>100,'modelo'=>150,'serial'=>150,'procesador'=>250,
    'disco_total'=>50,'tipo_disco'=>50,'disco_usado'=>50,'tipo_ram'=>50,'ram_total'=>50,'ram_usada'=>50,
    'sistema_op'=>150,'ip_local'=>50,'conexion'=>50,'observaciones'=>65535,'office'=>200,
];

/** Equipo existente y fuera de la papelera, o null. */
function equipoActivo(PDO $pdo, int $id): ?array {
    if ($id <= 0) return null;
    $st = $pdo->prepare("SELECT * FROM equipos WHERE id = ? AND deleted_at IS NULL");
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

/** Normaliza un valor de texto de equipo: recorta, vacío => null, respeta el largo de la columna. */
function valorEquipo(string $col, $v): ?string {
    if ($v === null || is_array($v)) return null;
    $v = trim((string)$v);
    if ($v === '') return null;
    return mb_substr($v, 0, EQUIPO_LARGOS[$col] ?? 255);
}

/**
 * Comprueba que el serial de un equipo de cómputo no esté ya en otro equipo de cómputo activo.
 * Devuelve el mensaje de error o null.
 */
function serialRepetido(PDO $pdo, ?string $serial, ?string $componente, int $exceptoId = 0): ?string {
    if ($componente !== 'Equipo de computo' || $serial === null) return null;
    $norm = mb_strtoupper(trim($serial));
    if (in_array($norm, SERIALES_GENERICOS, true)) return null;
    $st = $pdo->prepare("SELECT responsable FROM equipos
        WHERE UPPER(TRIM(serial)) = ? AND componente = 'Equipo de computo' AND deleted_at IS NULL AND id <> ? LIMIT 1");
    $st->execute([$norm, $exceptoId]);
    $otro = $st->fetchColumn();
    if ($otro === false) return null;
    return "Ya existe otro equipo de cómputo con el serial $serial" . ($otro ? " (asignado a $otro)." : '.');
}

/** Valida y normaliza el cuerpo de una vigencia. Devuelve [datos, error]. */
function validarVigencia(PDO $pdo, array $b): array {
    $nombre = textoOpcional($b['nombre'] ?? '', 200);
    if ($nombre === null) return [null, 'El nombre es obligatorio.'];
    $tipo = ($b['tipo'] ?? '') === 'Hardware' ? 'Hardware' : 'Software';
    $venc = trim((string)($b['fecha_vencimiento'] ?? ''));
    if (!fechaValida($venc)) return [null, 'La fecha de vencimiento no es válida.'];
    $ini = trim((string)($b['fecha_inicio'] ?? ''));
    if ($ini !== '') {
        if (!fechaValida($ini)) return [null, 'La fecha de inicio no es válida.'];
        if ($ini > $venc) return [null, 'La fecha de inicio no puede ser posterior al vencimiento.'];
    } else {
        $ini = null;
    }
    $equipoId = (int)($b['equipo_id'] ?? 0);
    if ($equipoId > 0) {
        $st = $pdo->prepare("SELECT 1 FROM equipos WHERE id = ? AND deleted_at IS NULL");
        $st->execute([$equipoId]);
        if (!$st->fetchColumn()) return [null, 'El equipo seleccionado no existe.'];
    } else {
        $equipoId = null;
    }
    return [[
        'equipo_id' => $equipoId,
        'tipo' => $tipo,
        'nombre' => $nombre,
        'proveedor' => textoOpcional($b['proveedor'] ?? '', 150),
        'referencia' => textoOpcional($b['referencia'] ?? '', 250),
        'fecha_inicio' => $ini,
        'fecha_vencimiento' => $venc,
        'observaciones' => textoOpcional($b['observaciones'] ?? '', 2000),
    ], null];
}

/** Añade estado (vigente / por_vencer / vencida) y días restantes a cada fila. */
function conEstadoVigencia(array $rows): array {
    $hoy = new DateTime('today');
    foreach ($rows as &$r) {
        $dias = (int)$hoy->diff(new DateTime($r['fecha_vencimiento']))->format('%r%a');
        $r['dias_restantes'] = $dias;
        $r['estado'] = $dias < 0 ? 'vencida' : ($dias <= VIGENCIA_ALERTA_DIAS ? 'por_vencer' : 'vigente');
    }
    unset($r);
    return $rows;
}

const VIGENCIA_SELECT = "SELECT v.id, v.equipo_id, v.tipo, v.nombre, v.proveedor, v.referencia,
        v.fecha_inicio, v.fecha_vencimiento, v.observaciones,
        e.responsable AS equipo_responsable, e.marca AS equipo_marca, e.modelo AS equipo_modelo
    FROM vigencias v
    LEFT JOIN equipos e ON e.id = v.equipo_id AND e.deleted_at IS NULL";

// ---------------------------------------------------------------------------
// Impresoras (lectura de contadores desde la web de la impresora, p. ej. Ricoh)
// ---------------------------------------------------------------------------
function asegurarTablasImpresoras(PDO $pdo): void {
    static $listo = false;
    if ($listo) return;
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `impresoras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `ubicacion` varchar(150) DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `impresora_lecturas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `impresora_id` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `copia_color` int(11) DEFAULT NULL,
  `copia_bn` int(11) DEFAULT NULL,
  `copia_color_pers` int(11) DEFAULT NULL,
  `copia_dos_colores` int(11) DEFAULT NULL,
  `impresion_color` int(11) DEFAULT NULL,
  `impresion_bn` int(11) DEFAULT NULL,
  `impresion_color_pers` int(11) DEFAULT NULL,
  `impresion_dos_colores` int(11) DEFAULT NULL,
  `escaneo_color` int(11) DEFAULT NULL,
  `escaneo_bn` int(11) DEFAULT NULL,
  `contador_total` int(11) DEFAULT NULL,
  `total_color` int(11) DEFAULT NULL,
  `total_bn` int(11) DEFAULT NULL,
  `total_color_pers` int(11) DEFAULT NULL,
  `total_dos_colores` int(11) DEFAULT NULL,
  `crudo` mediumtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lec_impresora` (`impresora_id`,`fecha_hora`),
  CONSTRAINT `fk_lec_impresora` FOREIGN KEY (`impresora_id`) REFERENCES `impresoras` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    // Tablas creadas antes de agregar color personalizado y dos colores
    foreach (['copia_color_pers','copia_dos_colores','impresion_color_pers','impresion_dos_colores','contador_total','total_color','total_bn','total_color_pers','total_dos_colores'] as $col) {
        if (!$pdo->query("SHOW COLUMNS FROM impresora_lecturas LIKE '$col'")->fetch()) {
            $pdo->exec("ALTER TABLE impresora_lecturas ADD COLUMN `$col` int(11) DEFAULT NULL");
        }
    }
    // Sincroniza con la lista definida en php/impresoras.php (única fuente de impresoras)
    foreach (impresorasConfig() as $c) {
        $ex = $pdo->prepare("SELECT id FROM impresoras WHERE ip = ? ORDER BY id LIMIT 1");
        $ex->execute([$c['ip']]);
        $id = $ex->fetchColumn();
        if ($id) $pdo->prepare("UPDATE impresoras SET nombre = ?, ubicacion = ? WHERE id = ?")->execute([$c['nombre'], $c['ubicacion'], $id]);
        else $pdo->prepare("INSERT INTO impresoras (nombre, ip, ubicacion) VALUES (?, ?, ?)")->execute([$c['nombre'], $c['ip'], $c['ubicacion']]);
    }
    $listo = true;
}

/** Impresoras definidas en php/impresoras.php (solo IPs locales válidas, sin repetidas). */
function impresorasConfig(): array {
    static $lista = null;
    if ($lista !== null) return $lista;
    $lista = [];
    $vistas = [];
    $archivo = __DIR__ . '/impresoras.php';
    $datos = is_file($archivo) ? (require $archivo) : [];
    foreach ((array)$datos as $c) {
        $ip = trim((string)($c['ip'] ?? ''));
        $nombre = trim((string)($c['nombre'] ?? ''));
        if ($nombre === '' || !ipLocalValida($ip) || isset($vistas[$ip])) continue;
        $vistas[$ip] = true;
        $ubic = trim((string)($c['ubicacion'] ?? ''));
        $lista[] = ['ip' => $ip, 'nombre' => mb_substr($nombre, 0, 150), 'ubicacion' => $ubic === '' ? null : mb_substr($ubic, 0, 150)];
    }
    return $lista;
}

/** Condición SQL para mostrar solo las impresoras de la lista ([sql, parámetros]). */
function filtroImpresoras(string $col = 'i.ip'): array {
    $ips = array_column(impresorasConfig(), 'ip');
    if (!$ips) return ['0 = 1', []];
    return ["$col IN (" . implode(',', array_fill(0, count($ips), '?')) . ")", $ips];
}

/** Solo se permiten IPv4 de red local (evita que la API se use para consultar sitios externos). */
function ipLocalValida(string $ip): bool {
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) return false;
    return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE);
}

function descargarPagina(string $url, string &$cookies, ?string &$err = null): ?string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false, // las impresoras usan certificado propio
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_COOKIE => $cookies,
        CURLOPT_HEADER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 InvCMP',
        CURLOPT_HTTPHEADER => ['Accept-Language: es'],
    ]);
    $resp = curl_exec($ch);
    if ($resp === false) { $err = curl_error($ch); curl_close($ch); return null; }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    $headers = substr($resp, 0, $hs);
    if (preg_match_all('/^Set-Cookie:\s*([^=;\s]+=[^;\r\n]*)/mi', $headers, $m)) {
        foreach ($m[1] as $c) $cookies .= ($cookies === '' ? '' : '; ') . $c;
    }
    if ($code >= 400) { $err = "HTTP $code"; return null; }
    return substr($resp, $hs);
}

function sinAcentos(string $s): string {
    return strtr(mb_strtolower($s), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
}

/**
 * Lee la pagina de contadores (Ricoh) y devuelve todos los pares etiqueta/valor encontrados
 * y los campos que usa el inventario. Toma siempre la primera cifra de cada fila (columna "Total").
 */
function parsearContadores(string $html): array {
    $dom = new DOMDocument();
    $prev = libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($prev);

    $pares = [];
    $seccion = '';
    foreach ($dom->getElementsByTagName('tr') as $tr) {
        $celdas = [];
        foreach ($tr->childNodes as $n) {
            if ($n instanceof DOMElement && in_array(strtolower($n->tagName), ['td','th'])) {
                $t = trim(preg_replace('/\s+/u', ' ', str_replace("\xC2\xA0", ' ', $n->textContent)));
                if ($t !== '') $celdas[] = $t;
            }
        }
        if (!$celdas) continue;

        $etiqueta = null; $valor = null;
        if (preg_match('/^([^:\d][^:]*?)\s*:\s*(\d[\d.,]*)\b/u', $celdas[0], $m)) {
            // "A todo color : 107745" en una sola celda
            $etiqueta = $m[1]; $valor = $m[2];
        } else {
            for ($i = 1; $i < count($celdas); $i++) {
                if (preg_match('/^:?\s*(\d[\d.,]*)\s*$/', $celdas[$i], $m)) {
                    $etiqueta = trim(str_replace(':', ' ', implode(' ', array_slice($celdas, 0, $i))));
                    $valor = $m[1];
                    break;
                }
            }
        }
        if ($etiqueta !== null && $etiqueta !== '') {
            $pares[] = ['seccion' => $seccion, 'etiqueta' => $etiqueta, 'valor' => (int)preg_replace('/\D/', '', $valor)];
        } elseif (count($celdas) === 1 && mb_strlen($celdas[0]) <= 40 && !preg_match('/\d/', $celdas[0])) {
            $seccion = $celdas[0]; // encabezado de seccion (las descripciones largas se ignoran)
        }
    }

    $campos = [
        'copia_color'=>null,'copia_bn'=>null,'copia_color_pers'=>null,'copia_dos_colores'=>null,
        'impresion_color'=>null,'impresion_bn'=>null,'impresion_color_pers'=>null,'impresion_dos_colores'=>null,
        'escaneo_color'=>null,'escaneo_bn'=>null,
        'contador_total'=>null,'total_color'=>null,'total_bn'=>null,'total_color_pers'=>null,'total_dos_colores'=>null,
    ];
    foreach ($pares as $p) {
        $sec = trim(sinAcentos($p['seccion']));
        $et  = sinAcentos($p['etiqueta']);
        // Contador total de la impresora (encabezado de la pagina) y su desglose
        if ($sec === '' || $sec === 'total') {
            if (preg_match('/^(contador\s+)?total$/', trim($et))) {
                if ($campos['contador_total'] === null) $campos['contador_total'] = $p['valor'];
                continue;
            }
            if ($sec === 'total') {
                if (preg_match('/personaliz|custom/', $et)) $k2 = 'total_color_pers';
                elseif (preg_match('/dos colores|two.?color/', $et)) $k2 = 'total_dos_colores';
                elseif (preg_match('/negro|b\/n|blanco|mono|black/', $et) && !preg_match('/color/', $et)) $k2 = 'total_bn';
                elseif (preg_match('/color/', $et) && !preg_match('/un color|single/', $et)) $k2 = 'total_color';
                else $k2 = null;
                if ($k2 && $campos[$k2] === null) $campos[$k2] = $p['valor'];
                continue;
            }
        }
        if (preg_match('/total|fax|otros|cobertura|a3|dlt|duplex|banner|enviar\/tx/', $sec . ' ' . $et)) continue;
        if (preg_match('/copi|copy/', $sec)) $cat = 'copia';
        elseif (preg_match('/escan|scan/', $sec)) $cat = 'escaneo';
        elseif (preg_match('/impres|print/', $sec)) $cat = 'impresion';
        else continue;

        if (preg_match('/personaliz|custom/', $et)) $tipo = 'color_pers';
        elseif (preg_match('/dos colores|two.?color/', $et)) $tipo = 'dos_colores';
        elseif (preg_match('/negro|b\/n|\bbn\b|blanco|mono|black|b&w/', $et) && !preg_match('/color/', $et)) $tipo = 'bn';
        elseif (preg_match('/color/', $et) && !preg_match('/un color|single/', $et)) $tipo = 'color';
        else continue;

        if ($cat === 'escaneo' && ($tipo === 'color_pers' || $tipo === 'dos_colores')) continue;
        $k = $cat . '_' . $tipo;
        if ($campos[$k] === null) $campos[$k] = $p['valor'];
    }
    return ['campos' => $campos, 'pares' => $pares];
}

/** Consulta la impresora y devuelve [lectura|null, error|null, textoCrudo]. */
function consultarImpresora(string $ip): array {
    if (!ipLocalValida($ip)) return [null, 'La IP debe ser una dirección de red local.', ''];
    $rutas = [
        '/web/guest/es/websys/status/getUnificationCounter.cgi',
        '/web/guest/en/websys/status/getUnificationCounter.cgi',
        '/web/entry/es/websys/status/getUnificationCounter.cgi',
    ];
    $ultimoErr = null; $ultimoTxt = '';
    foreach (['https', 'http'] as $esq) {
        $cookies = '';
        $err = null;
        // Visita inicial para obtener la cookie de sesion de la impresora
        descargarPagina("$esq://$ip/", $cookies, $err);
        if ($err !== null && stripos($err, 'HTTP') === false) { $ultimoErr = $err; continue; }
        foreach ($rutas as $ruta) {
            $err = null;
            $html = descargarPagina("$esq://$ip$ruta", $cookies, $err);
            if ($html === null) { $ultimoErr = $err; continue; }
            $r = parsearContadores($html);
            $ultimoTxt = mb_substr(trim(preg_replace('/\s+/u', ' ', strip_tags($html))), 0, 1500);
            if (count(array_filter($r['campos'], fn($v) => $v !== null)) > 0) return [$r, null, $ultimoTxt];
            $ultimoErr = 'La impresora respondió, pero no se reconocieron los contadores.';
        }
    }
    return [null, $ultimoErr ?: 'No se pudo conectar con la impresora.', $ultimoTxt];
}

/** Ultima lectura de cada impresora y la suma de todas (contador total y cada categoria). */
function totalesImpresoras(PDO $pdo): array {
    $campos = ['copia_color','copia_bn','copia_color_pers','copia_dos_colores','impresion_color','impresion_bn',
               'impresion_color_pers','impresion_dos_colores','escaneo_color','escaneo_bn','contador_total'];
    [$filtro, $ips] = filtroImpresoras();
    $st = $pdo->prepare("SELECT i.id, i.nombre, i.ip, l.fecha_hora, " . implode(', ', array_map(fn($c) => "l.$c", $campos)) . "
        FROM impresoras i
        LEFT JOIN impresora_lecturas l ON l.id = (SELECT x.id FROM impresora_lecturas x WHERE x.impresora_id = i.id ORDER BY x.fecha_hora DESC, x.id DESC LIMIT 1)
        WHERE $filtro
        ORDER BY i.nombre");
    $st->execute($ips);
    $rows = $st->fetchAll();
    $suma = array_fill_keys($campos, 0);
    $conLectura = 0;
    foreach ($rows as $r) {
        if ($r['fecha_hora'] !== null) $conLectura++;
        foreach ($campos as $c) $suma[$c] += (int)($r[$c] ?? 0);
    }
    return ['impresoras' => $rows, 'suma' => $suma, 'con_lectura' => $conLectura];
}

// ---------------------------------------------------------------------------
// Requisiciones de compra (formato MA-PC-FT-001). Listas en php/requisiciones.php
// ---------------------------------------------------------------------------
const REQ_MAX_ITEMS = 60;   // ítems por requisición (el PDF pone 15 por página)

function requisicionesConfig(): array {
    static $cfg = null;
    if ($cfg === null) $cfg = require __DIR__ . '/requisiciones.php';
    return $cfg;
}

function asegurarTablasRequisiciones(PDO $pdo): void {
    static $listo = false;
    if ($listo) return;
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `requisiciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consecutivo` int(11) NOT NULL,
  `codigo` varchar(80) NOT NULL,
  `fecha` date NOT NULL,
  `prioridad` varchar(20) NOT NULL,
  `empresa_cod` varchar(20) NOT NULL,
  `empresa_nombre` varchar(200) NOT NULL,
  `area_cod` varchar(20) NOT NULL,
  `area_nombre` varchar(150) NOT NULL,
  `solicitante_cod` int(11) NOT NULL,
  `solicitante_nombre` varchar(200) NOT NULL,
  `autorizado_por` varchar(200) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `sol_cedula` varchar(30) DEFAULT NULL,
  `sol_cargo` varchar(150) DEFAULT NULL,
  `firma_nombre` varchar(200) DEFAULT NULL,
  `creado_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_req_consecutivo` (`consecutivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS `requisicion_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `requisicion_id` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `unidad` varchar(30) DEFAULT NULL,
  `articulo` text NOT NULL,
  `proveedor` varchar(200) DEFAULT NULL,
  `centro_trabajo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reqitem_req` (`requisicion_id`),
  CONSTRAINT `fk_reqitem_req` FOREIGN KEY (`requisicion_id`) REFERENCES `requisiciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    // Instalaciones que crearon la tabla antes de existir la columna
    if (!$pdo->query("SHOW COLUMNS FROM requisiciones LIKE 'firma_nombre'")->fetch()) {
        $pdo->exec("ALTER TABLE requisiciones ADD COLUMN `firma_nombre` varchar(200) DEFAULT NULL AFTER `sol_cargo`");
    }
    $listo = true;
}

function siguienteConsecutivo(PDO $pdo, bool $bloquear = false): int {
    $ini = max(1, (int)(requisicionesConfig()['consecutivo_inicial'] ?? 1));
    $max = $pdo->query("SELECT MAX(consecutivo) FROM requisiciones" . ($bloquear ? " FOR UPDATE" : ""))->fetchColumn();
    return $max === null ? $ini : max($ini, (int)$max + 1);
}

/** Valida el formulario. Devuelve [datos, items, error]. */
function validarRequisicion(array $b): array {
    $cfg = requisicionesConfig();
    $empCod = (string)($b['empresa_cod'] ?? '');
    $areaCod = (string)($b['area_cod'] ?? '');
    $solCod = (int)($b['solicitante_cod'] ?? 0);
    $aut = trim((string)($b['autorizado_por'] ?? ''));
    $pri = (string)($b['prioridad'] ?? '');
    if (!isset($cfg['empresas'][$empCod]))          return [null, null, 'Selecciona la empresa.'];
    if (!isset($cfg['areas'][$areaCod]))            return [null, null, 'Selecciona el área.'];
    if (!isset($cfg['solicitantes'][$solCod]))      return [null, null, 'Selecciona el solicitante.'];
    if (!in_array($aut, $cfg['autorizado_por'], true)) return [null, null, 'Selecciona quién autoriza.'];
    if (!in_array($pri, $cfg['prioridades'], true)) return [null, null, 'Selecciona la prioridad.'];

    $items = [];
    $filas = is_array($b['items'] ?? null) ? $b['items'] : [];
    if (count($filas) > REQ_MAX_ITEMS) return [null, null, 'Se admiten máximo ' . REQ_MAX_ITEMS . ' ítems por requisición.'];

    // Fecha: la de hoy si no se envía; si se envía, debe ser una fecha real
    $fecha = trim((string)($b['fecha'] ?? ''));
    if ($fecha === '') $fecha = date('Y-m-d');
    if (!fechaValida($fecha) || $fecha < '2000-01-01' || $fecha > '2100-12-31') return [null, null, 'La fecha no es válida.'];

    // Consecutivo: automático si viene vacío; si se escribe a mano, entero positivo
    $consTxt = trim((string)($b['consecutivo'] ?? ''));
    $consecutivo = null;
    if ($consTxt !== '') {
        if (!ctype_digit($consTxt) || (int)$consTxt < 1 || (int)$consTxt > 999999) return [null, null, 'El consecutivo debe ser un número entero mayor que 0.'];
        $consecutivo = (int)$consTxt;
    }
    foreach ($filas as $i => $f) {
        if (!is_array($f)) continue;
        $cant = trim(str_replace(',', '.', (string)($f['cantidad'] ?? '')));
        $art = textoOpcional($f['articulo'] ?? '', 1000);
        $uni = textoOpcional($f['unidad'] ?? '', 30);
        $prov = textoOpcional($f['proveedor'] ?? '', 200);
        $cen = textoOpcional($f['centro_trabajo'] ?? '', 100);
        if ($cant === '' && $art === null && $uni === null && $prov === null && $cen === null) continue; // fila vacía
        $n = $i + 1;
        if ($art === null) return [null, null, "Fila $n: escribe el artículo o servicio."];
        if ($cant === '' || !is_numeric($cant) || (float)$cant <= 0) return [null, null, "Fila $n: la cantidad debe ser un número mayor que 0."];
        if ($uni !== null && !in_array($uni, $cfg['unidades'], true)) return [null, null, "Fila $n: unidad no válida."];
        if ($cen !== null && !in_array($cen, $cfg['centros_trabajo'], true)) return [null, null, "Fila $n: centro de trabajo no válido."];
        $items[] = ['cantidad' => round((float)$cant, 2), 'unidad' => $uni, 'articulo' => $art, 'proveedor' => $prov, 'centro_trabajo' => $cen];
    }
    if (!$items) return [null, null, 'Agrega al menos un artículo o servicio.'];

    return [[
        'fecha' => $fecha,
        'consecutivo' => $consecutivo,
        'prioridad' => $pri,
        'empresa_cod' => $empCod, 'empresa_nombre' => $cfg['empresas'][$empCod],
        'area_cod' => $areaCod, 'area_nombre' => $cfg['areas'][$areaCod],
        'solicitante_cod' => $solCod, 'solicitante_nombre' => $cfg['solicitantes'][$solCod],
        'autorizado_por' => $aut,
        'observaciones' => textoOpcional($b['observaciones'] ?? '', 2000),
        // La firma del solicitante es fija (php/requisiciones.php); se ignora lo que llegue del formulario
        'firma_nombre' => $cfg['firma_solicitante']['nombre'] ?? null,
        'sol_cedula' => $cfg['firma_solicitante']['cedula'] ?? null,
        'sol_cargo' => $cfg['firma_solicitante']['cargo'] ?? null,
    ], $items, null];
}

function obtenerRequisicion(PDO $pdo, int $id): ?array {
    $st = $pdo->prepare("SELECT * FROM requisiciones WHERE id = ?");
    $st->execute([$id]);
    $r = $st->fetch();
    if (!$r) return null;
    $it = $pdo->prepare("SELECT item, cantidad, unidad, articulo, proveedor, centro_trabajo FROM requisicion_items WHERE requisicion_id = ? ORDER BY item");
    $it->execute([$id]);
    $r['items'] = $it->fetchAll();
    return $r;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$body = [];
if (in_array($method, ['POST','PUT'])) {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true) ?? [];
}

// Solo estas acciones son de consulta; todas las demás modifican datos y deben llegar por POST
const ACCIONES_LECTURA = ['responsables','responsable','get_equipo','buscar_equipos','papelera','tareas','vigencias',
    'get_vigencia','impresoras','impresoras_totales','lecturas_impresora','stats',
    'requisicion_listas','requisiciones','requisicion','listado_ip'];
if ($action !== '' && !in_array($action, ACCIONES_LECTURA, true) && $method !== 'POST') {
    responderError(405, 'Esta acción solo acepta peticiones POST.');
    exit;
}

switch ($action) {
    case 'responsables':
        $q   = trim($_GET['q'] ?? '');
        $pdo = getDB();
        echo json_encode(['responsables' => agruparResponsables($pdo, $q)]);
        break;

    case 'responsable':
        $resp = trim($_GET['nombre'] ?? '');
        $pdo  = getDB();
        asegurarTablaVigencias($pdo);

        $stmt = $pdo->prepare("SELECT * FROM equipos WHERE LOWER(TRIM(responsable)) = LOWER(?) AND componente = 'Equipo de computo' AND equipo_padre_id IS NULL AND deleted_at IS NULL ORDER BY item ASC");
        $stmt->execute([$resp]);
        $equipos = $stmt->fetchAll();

        $stmt2 = $pdo->prepare("SELECT * FROM equipos WHERE LOWER(TRIM(responsable)) = LOWER(?) AND componente <> 'Equipo de computo' AND deleted_at IS NULL ORDER BY item ASC");
        $stmt2->execute([$resp]);
        $sueltos = $stmt2->fetchAll();

        if (empty($equipos)) {
            $equipos = $sueltos;
            $sueltos = [];
        }

        foreach ($equipos as &$eq) {
            $eq['perifericos'] = [];
            $sw = $pdo->prepare("SELECT id, programa, version FROM software WHERE equipo_id = ? AND deleted_at IS NULL ORDER BY programa");
            $sw->execute([$eq['id']]);
            $eq['software'] = $sw->fetchAll();
            $vg = $pdo->prepare("SELECT id, tipo, nombre, proveedor, referencia, fecha_inicio, fecha_vencimiento FROM vigencias WHERE equipo_id = ? AND deleted_at IS NULL ORDER BY fecha_vencimiento");
            $vg->execute([$eq['id']]);
            $eq['vigencias'] = conEstadoVigencia($vg->fetchAll());
        }
        unset($eq);

        $idsEquipos = array_column($equipos, 'id');
        foreach ($sueltos as $p) {
            $destino = null;
            if (!empty($p['equipo_padre_id']) && in_array($p['equipo_padre_id'], $idsEquipos)) {
                $destino = $p['equipo_padre_id'];
            } elseif (count($equipos) === 1) {
                $destino = $equipos[0]['id'];
            } elseif (count($equipos) > 1) {
                $candidato = null;
                foreach ($equipos as $eq) {
                    if ((int)$eq['item'] <= (int)$p['item']) $candidato = $eq;
                }
                $destino = $candidato ? $candidato['id'] : $equipos[0]['id'];
            }
            if ($destino !== null) {
                foreach ($equipos as &$eq) {
                    if ($eq['id'] == $destino) { $eq['perifericos'][] = $p; break; }
                }
                unset($eq);
            }
        }

        echo json_encode(['items' => $equipos]);
        break;

    // Endpoint necesario para poblar el formulario de edición antes de modificar
    case 'get_equipo':
        $id = (int)($_GET['id'] ?? 0);
        $eq = equipoActivo(getDB(), $id);
        if (!$eq) { responderError(404, 'El equipo no existe o está en la papelera.'); break; }
        echo json_encode(['equipo' => $eq]);
        break;

    case 'update_equipo':
        if ($method !== 'POST') { http_response_code(405); break; }
        $id = (int)($body['id'] ?? 0);
        
        $campos  = ['sede','area','responsable','componente','tipo_equipo','usuario_actual',
                    'nombre_completo','grupo_usuario','marca','modelo','serial','procesador',
                    'disco_total','tipo_disco','disco_usado','tipo_ram','ram_total','ram_usada',
                    'sistema_op','ip_local','conexion','observaciones','office'];
        $pdo = getDB();
        $actual = equipoActivo($pdo, $id);
        if (!$actual) { responderError(404, 'El equipo no existe o está en la papelera.'); break; }

        $datos = [];
        foreach ($campos as $c) {
            if (array_key_exists($c, $body)) $datos[$c] = valorEquipo($c, $body[$c]);
        }
        if (array_key_exists('responsable', $datos) && $datos['responsable'] === null) {
            responderError(400, 'El responsable es obligatorio.'); break;
        }
        $comp = array_key_exists('componente', $datos) ? $datos['componente'] : $actual['componente'];
        // Se acepta el componente que ya tenía el registro aunque no esté en la lista (datos antiguos)
        if ($comp !== $actual['componente'] && !in_array($comp, COMPONENTES_VALIDOS, true)) {
            responderError(400, 'Componente no válido.'); break;
        }
        $serial = array_key_exists('serial', $datos) ? $datos['serial'] : $actual['serial'];
        // Solo se revisa si cambió el serial o el componente (no bloquea registros antiguos ya repetidos)
        if ($serial !== $actual['serial'] || $comp !== $actual['componente']) {
            if ($err = serialRepetido($pdo, $serial, $comp, $id)) { responderError(409, $err); break; }
        }
        if (!$datos) { echo json_encode(['ok' => true]); break; }

        $sets = []; $valores = [];
        foreach ($datos as $c => $v) { $sets[] = "`$c` = ?"; $valores[] = $v; }
        $valores[] = $id;
        $pdo->prepare("UPDATE equipos SET " . implode(', ', $sets) . " WHERE id = ?")->execute($valores);
        echo json_encode(['ok' => true]);
        break;

    // Se mantiene create_equipo SOLO para crear periféricos desde dentro de un perfil
    case 'create_equipo':
        if ($method !== 'POST') { http_response_code(405); break; }
        $pdo = getDB();
        $padreId = (int)($body['equipo_padre_id'] ?? 0);
        if ($padreId > 0) {
            $padre = equipoActivo($pdo, $padreId);
            if (!$padre || $padre['componente'] !== 'Equipo de computo') {
                responderError(400, 'El equipo al que se quiere agregar el periférico no existe o está en la papelera.'); break;
            }
        } else {
            $padreId = null;
        }
        $nuevo = [];
        foreach (array_keys(EQUIPO_LARGOS) as $c) $nuevo[$c] = valorEquipo($c, $body[$c] ?? null);
        $nuevo['componente'] = $nuevo['componente'] ?? 'Mouse';
        if (!in_array($nuevo['componente'], COMPONENTES_VALIDOS, true)) { responderError(400, 'Componente no válido.'); break; }
        if ($padreId !== null && $nuevo['componente'] === 'Equipo de computo') {
            responderError(400, 'Un periférico no puede ser un equipo de cómputo.'); break;
        }
        if ($nuevo['responsable'] === null) { responderError(400, 'El responsable es obligatorio.'); break; }
        if ($err = serialRepetido($pdo, $nuevo['serial'], $nuevo['componente'])) { responderError(409, $err); break; }
        $body = $nuevo;
        $maxItem = $pdo->query("SELECT COALESCE(MAX(item),0)+1 FROM equipos")->fetchColumn();
        
        $pdo->prepare("
            INSERT INTO equipos (item, equipo_padre_id, sede, area, responsable, componente, tipo_equipo, usuario_actual,
              nombre_completo, marca, modelo, serial, procesador, disco_total, tipo_disco,
              disco_usado, tipo_ram, ram_total, ram_usada, sistema_op, ip_local, conexion, observaciones,
              office, fecha_registro, hora_registro)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,CURDATE(),CURTIME())
        ")->execute([
            $maxItem,
            $padreId,
            $body['sede'] ?? null,
            $body['area'] ?? null,
            $body['responsable'] ?? null,
            $body['componente'],
            $body['tipo_equipo'] ?? null,
            $body['usuario_actual'] ?? null,
            $body['nombre_completo'] ?? null,
            $body['marca'] ?? null,
            $body['modelo'] ?? null,
            $body['serial'] ?? null,
            $body['procesador'] ?? null,
            $body['disco_total'] ?? null,
            $body['tipo_disco'] ?? null,
            $body['disco_usado'] ?? null,
            $body['tipo_ram'] ?? null,
            $body['ram_total'] ?? null,
            $body['ram_usada'] ?? null,
            $body['sistema_op'] ?? null,
            $body['ip_local'] ?? null,
            $body['conexion'] ?? null,
            $body['observaciones'] ?? null,
            $body['office'] ?? null,
        ]);
        echo json_encode(['ok' => true]);
        break;

    case 'delete_equipo':
        $id = (int)($body['id'] ?? 0);
        $pdo = getDB();
        if (!equipoActivo($pdo, $id)) { responderError(404, 'El equipo no existe o ya está en la papelera.'); break; }
        asegurarTablaVigencias($pdo);
        // Mismo instante para todo lo que se va junto, así al restaurar vuelve exactamente lo mismo
        $ahora = $pdo->query("SELECT NOW()")->fetchColumn();
        // Si es un equipo padre, sus periféricos anidados (mouse/teclado) se van con él a la papelera
        $hijos = $pdo->prepare("SELECT id FROM equipos WHERE equipo_padre_id = ? AND deleted_at IS NULL");
        $hijos->execute([$id]);
        $todos = array_merge([$id], array_column($hijos->fetchAll(), 'id'));
        $in = implode(',', array_fill(0, count($todos), '?'));
        $pdo->beginTransaction();
        $pdo->prepare("UPDATE equipos SET deleted_at = ? WHERE id IN ($in)")->execute(array_merge([$ahora], $todos));
        // Y con ellos, todo el software y las vigencias asociadas (evita huérfanos)
        $pdo->prepare("UPDATE software SET deleted_at = ? WHERE equipo_id IN ($in) AND deleted_at IS NULL")->execute(array_merge([$ahora], $todos));
        $pdo->prepare("UPDATE vigencias SET deleted_at = ? WHERE equipo_id IN ($in) AND deleted_at IS NULL")->execute(array_merge([$ahora], $todos));
        $pdo->commit();
        echo json_encode(['ok' => true]);
        break;

    // Buscar equipos de cómputo (para reasignar a otro responsable)
   // api.php (Reemplaza el case 'buscar_equipos' existente)
    case 'buscar_equipos':
        $q = trim($_GET['q'] ?? '');
        $pdo = getDB();
        if ($q !== '') {
            $like = '%' . $q . '%';
            $stmt = $pdo->prepare("SELECT id, responsable, area, sede, marca, modelo, serial, observaciones FROM equipos
                WHERE componente = 'Equipo de computo' AND deleted_at IS NULL
                AND (responsable LIKE ? OR marca LIKE ? OR modelo LIKE ? OR serial LIKE ? OR area LIKE ? OR sede LIKE ? OR observaciones LIKE ? OR tipo_equipo LIKE ?)
                ORDER BY responsable ASC LIMIT 50");
            $stmt->execute([$like,$like,$like,$like,$like,$like,$like,$like]);
        } else {
            $stmt = $pdo->query("SELECT id, responsable, area, sede, marca, modelo, serial, observaciones FROM equipos
                WHERE componente = 'Equipo de computo' AND deleted_at IS NULL
                ORDER BY responsable ASC LIMIT 200");
        }
        echo json_encode(['equipos' => $stmt->fetchAll()]);
        break;

    // Reasignar un equipo existente a otro responsable (el anterior lo pierde automáticamente,
    // sus periféricos como mouse/teclado NO se tocan)
    case 'asignar_equipo':
        if ($method !== 'POST') { http_response_code(405); break; }
        $id = (int)($body['id'] ?? 0);
        $nuevoResp = trim($body['responsable'] ?? '');
        if ($id <= 0 || $nuevoResp === '') { responderError(400, 'Datos incompletos.'); break; }
        $pdo = getDB();
        $eq = equipoActivo($pdo, $id);
        if (!$eq || $eq['componente'] !== 'Equipo de computo') { responderError(404, 'El equipo no existe o está en la papelera.'); break; }
        $pdo->prepare("UPDATE equipos SET responsable = ? WHERE id = ?")->execute([mb_substr($nuevoResp, 0, 150), $id]);
        echo json_encode(['ok' => true]);
        break;

    case 'add_software':
        $pdo = getDB();
        $eqId = (int)($body['equipo_id'] ?? 0);
        if (!equipoActivo($pdo, $eqId)) { responderError(404, 'El equipo no existe o está en la papelera.'); break; }
        $programa = textoOpcional($body['programa'] ?? '', 300);
        $version  = textoOpcional($body['version'] ?? '', 100);
        if ($programa === null) { responderError(400, 'Escribe el nombre del programa.'); break; }
        $dup = $pdo->prepare("SELECT 1 FROM software WHERE equipo_id = ? AND deleted_at IS NULL
            AND LOWER(TRIM(programa)) = LOWER(?) AND COALESCE(LOWER(TRIM(version)),'') = COALESCE(LOWER(?),'')");
        $dup->execute([$eqId, $programa, $version]);
        if ($dup->fetchColumn()) { responderError(409, 'Ese programa ya está registrado en este equipo.'); break; }
        $pdo->prepare("INSERT INTO software (equipo_id,programa,version) VALUES (?,?,?)")
            ->execute([$eqId, $programa, $version]);
        echo json_encode(['ok'=>true, 'id'=>(int)$pdo->lastInsertId()]);
        break;

    case 'delete_software':
        getDB()->prepare("UPDATE software SET deleted_at = NOW() WHERE id = ?")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok'=>true]);
        break;

    case 'papelera':
        $pdo = getDB();
        asegurarTablaVigencias($pdo);
        // Solo se listan sueltos los equipos/periféricos que NO son hijos de un equipo
        // que también está en la papelera (si el padre está, el hijo va incluido/anidado en él)
        $equipos = $pdo->query("
            SELECT e.id, e.componente, e.tipo_equipo, e.marca, e.modelo, e.serial, e.responsable, e.deleted_at,
                   (SELECT COUNT(*) FROM software s WHERE s.equipo_id = e.id AND s.deleted_at IS NOT NULL) AS software_incluido,
                   (SELECT COUNT(*) FROM equipos c WHERE c.equipo_padre_id = e.id AND c.deleted_at IS NOT NULL) AS perifericos_incluidos,
                   (SELECT COUNT(*) FROM vigencias v WHERE v.equipo_id = e.id AND v.deleted_at >= e.deleted_at) AS vigencias_incluidas
            FROM equipos e
            WHERE e.deleted_at IS NOT NULL
              AND (e.equipo_padre_id IS NULL OR NOT EXISTS (
                    SELECT 1 FROM equipos p WHERE p.id = e.equipo_padre_id AND p.deleted_at IS NOT NULL
              ))
            ORDER BY e.deleted_at DESC")->fetchAll();

        // Adjunta el listado real (no solo el conteo) del software que tenía cada equipo,
        // para poder verlo directamente desde la papelera sin tener que restaurar nada
        if ($equipos) {
            $idsEquipos = array_column($equipos, 'id');
            $in = implode(',', array_fill(0, count($idsEquipos), '?'));
            $swStmt = $pdo->prepare("SELECT id, equipo_id, programa, version FROM software WHERE equipo_id IN ($in) AND deleted_at IS NOT NULL ORDER BY programa");
            $swStmt->execute($idsEquipos);
            $swPorEquipo = [];
            foreach ($swStmt->fetchAll() as $sw) {
                $swPorEquipo[$sw['equipo_id']][] = $sw;
            }
            foreach ($equipos as &$eq) {
                $eq['software_lista'] = $swPorEquipo[$eq['id']] ?? [];
            }
            unset($eq);
        }

        // Solo se listan sueltos los software eliminados de forma independiente:
        // aquellos cuyo equipo NO está también en la papelera (o ya no existe)
        $software = $pdo->query("
            SELECT s.id, s.programa, s.version, s.deleted_at, e.responsable AS responsable
            FROM software s LEFT JOIN equipos e ON e.id = s.equipo_id
            WHERE s.deleted_at IS NOT NULL AND (e.id IS NULL OR e.deleted_at IS NULL)
            ORDER BY s.deleted_at DESC")->fetchAll();

        $tareas = $pdo->query("
            SELECT id, titulo, descripcion, deleted_at
            FROM tareas WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC")->fetchAll();
        // Las vigencias de un equipo que está en la papelera van incluidas en él, no sueltas
        $vigencias = $pdo->query("
            SELECT v.id, v.tipo, v.nombre, v.proveedor, v.fecha_vencimiento, v.deleted_at
            FROM vigencias v LEFT JOIN equipos e ON e.id = v.equipo_id
            WHERE v.deleted_at IS NOT NULL AND (e.id IS NULL OR e.deleted_at IS NULL)
            ORDER BY v.deleted_at DESC")->fetchAll();
        echo json_encode(['equipos' => $equipos, 'software' => $software, 'tareas' => $tareas, 'vigencias' => $vigencias]);
        break;

    case 'restore_equipo':
        $id = (int)($body['id'] ?? 0);
        $pdo = getDB();

        // Si es un periférico (mouse/teclado) cuyo equipo padre sigue en la papelera,
        // no tiene sentido restaurarlo solo: quedaría huérfano de nuevo.
        $row = $pdo->prepare("SELECT equipo_padre_id FROM equipos WHERE id = ?");
        $row->execute([$id]);
        $padreId = $row->fetchColumn();
        if (!empty($padreId)) {
            $padre = $pdo->prepare("SELECT deleted_at FROM equipos WHERE id = ?");
            $padre->execute([$padreId]);
            $padreDeletedAt = $padre->fetchColumn();
            if ($padreDeletedAt) {
                http_response_code(409);
                echo json_encode(['error' => 'Este periférico pertenece a un equipo que sigue en la papelera. Restaura primero el equipo.']);
                break;
            }
        }

        // Restaura el equipo y, si es un equipo padre, sus periféricos + todo el software asociado
        asegurarTablaVigencias($pdo);
        $cuando = $pdo->prepare("SELECT deleted_at FROM equipos WHERE id = ?");
        $cuando->execute([$id]);
        $borradoEn = $cuando->fetchColumn();
        if (!$borradoEn) { responderError(404, 'El equipo no está en la papelera.'); break; }
        $hijos = $pdo->prepare("SELECT id FROM equipos WHERE equipo_padre_id = ?");
        $hijos->execute([$id]);
        $todos = array_merge([$id], array_column($hijos->fetchAll(), 'id'));
        $in = implode(',', array_fill(0, count($todos), '?'));
        $pdo->beginTransaction();
        $pdo->prepare("UPDATE equipos SET deleted_at = NULL WHERE id IN ($in)")->execute($todos);
        $pdo->prepare("UPDATE software SET deleted_at = NULL WHERE equipo_id IN ($in)")->execute($todos);
        // Solo las vigencias que se fueron junto con el equipo (las borradas antes a mano se quedan en la papelera)
        $pdo->prepare("UPDATE vigencias SET deleted_at = NULL WHERE equipo_id IN ($in) AND deleted_at >= ?")->execute(array_merge($todos, [$borradoEn]));
        $pdo->commit();
        echo json_encode(['ok'=>true]);
        break;

    case 'restore_software':
        $id = (int)($body['id'] ?? 0);
        $pdo = getDB();
        // No se puede restaurar un software de forma huérfana mientras su equipo siga en la papelera
        $stmt = $pdo->prepare("
            SELECT e.deleted_at FROM software s LEFT JOIN equipos e ON e.id = s.equipo_id
            WHERE s.id = ?");
        $stmt->execute([$id]);
        $equipoDeletedAt = $stmt->fetchColumn();
        if ($equipoDeletedAt !== null && $equipoDeletedAt !== false) {
            http_response_code(409);
            echo json_encode(['error' => 'Este software pertenece a un equipo que sigue en la papelera. Restaura primero el equipo.']);
            break;
        }
        $pdo->prepare("UPDATE software SET deleted_at = NULL WHERE id = ?")->execute([$id]);
        echo json_encode(['ok'=>true]);
        break;

    // Borrado permanente individual (desde la papelera, item por item)
    case 'delete_equipo_permanente':
        if ($method !== 'POST') { http_response_code(405); break; }
        $id = (int)($body['id'] ?? 0);
        $pdo = getDB();
        $check = $pdo->prepare("SELECT id FROM equipos WHERE id = ? AND deleted_at IS NOT NULL");
        $check->execute([$id]);
        if (!$check->fetch()) { echo json_encode(['ok' => true]); break; }

        $hijos = $pdo->prepare("SELECT id FROM equipos WHERE equipo_padre_id = ?");
        $hijos->execute([$id]);
        $todos = array_merge([$id], array_column($hijos->fetchAll(), 'id'));
        $in = implode(',', array_fill(0, count($todos), '?'));
        asegurarTablaVigencias($pdo);
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM vigencias WHERE equipo_id IN ($in) AND deleted_at IS NOT NULL")->execute($todos);
        $pdo->prepare("DELETE FROM software WHERE equipo_id IN ($in)")->execute($todos);
        $pdo->prepare("DELETE FROM equipos WHERE id IN ($in)")->execute($todos);
        $pdo->commit();
        echo json_encode(['ok' => true]);
        break;

    case 'delete_software_permanente':
        if ($method !== 'POST') { http_response_code(405); break; }
        getDB()->prepare("DELETE FROM software WHERE id = ? AND deleted_at IS NOT NULL")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok' => true]);
        break;

    case 'delete_tarea_permanente':
        if ($method !== 'POST') { http_response_code(405); break; }
        getDB()->prepare("DELETE FROM tareas WHERE id = ? AND deleted_at IS NOT NULL")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok' => true]);
        break;

    case 'vaciar_papelera':
        $pdo = getDB();
        asegurarTablaVigencias($pdo);
        $pdo->beginTransaction();
        $pdo->exec("DELETE FROM vigencias WHERE deleted_at IS NOT NULL");
        $pdo->exec("DELETE FROM software WHERE deleted_at IS NOT NULL");
        $pdo->exec("DELETE FROM equipos WHERE deleted_at IS NOT NULL");
        $pdo->exec("DELETE FROM tareas WHERE deleted_at IS NOT NULL");
        $pdo->commit();
        echo json_encode(['ok' => true]);
        break;

    case 'tareas':
        $pdo = getDB();
        $tareas = $pdo->query("SELECT * FROM tareas WHERE deleted_at IS NULL ORDER BY orden ASC, creado_at ASC")->fetchAll();
        echo json_encode(['tareas' => $tareas]);
        break;

    case 'crear_tarea':
        if ($method !== 'POST') { http_response_code(405); break; }
        $pdo = getDB();
        $titulo = textoOpcional($body['titulo'] ?? '', 200);
        if ($titulo === null) { responderError(400, 'Escribe un título para la tarea.'); break; }
        $color = in_array($body['color'] ?? '', COLORES_TAREA, true) ? $body['color'] : 'verde';
        $maxOrden = $pdo->query("SELECT COALESCE(MAX(orden),0)+1 FROM tareas WHERE estado='pendiente'")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO tareas (titulo, descripcion, estado, color, orden) VALUES (?,?,?,?,?)");
        $stmt->execute([
            $titulo,
            textoOpcional($body['descripcion'] ?? '', 5000),
            'pendiente',
            $color,
            $maxOrden,
        ]);
        echo json_encode(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
        break;

    case 'actualizar_tarea':
        if ($method !== 'POST') { http_response_code(405); break; }
        $id = (int)($body['id'] ?? 0);
        $pdo = getDB();

        $existe = $pdo->prepare("SELECT 1 FROM tareas WHERE id = ? AND deleted_at IS NULL");
        $existe->execute([$id]);
        if (!$existe->fetchColumn()) { responderError(404, 'La tarea no existe o está en la papelera.'); break; }

        $sets = []; $valores = [];
        if (array_key_exists('estado', $body)) {
            if (!in_array($body['estado'], ESTADOS_TAREA, true)) { responderError(400, 'Estado de tarea no válido.'); break; }
            // La fecha de completado solo cambia si la tarea cambia de estado (va antes de "estado = ?"
            // porque MySQL evalúa las asignaciones en orden)
            $sets[] = "completado_at = CASE WHEN estado = ? THEN completado_at WHEN ? = 'completada' THEN NOW() ELSE NULL END";
            $valores[] = $body['estado']; $valores[] = $body['estado'];
            $sets[] = "estado = ?";
            $valores[] = $body['estado'];
        }
        if (array_key_exists('titulo', $body)) {
            $t = textoOpcional($body['titulo'], 200);
            if ($t === null) { responderError(400, 'Escribe un título para la tarea.'); break; }
            $sets[] = "titulo = ?"; $valores[] = $t;
        }
        if (array_key_exists('descripcion', $body))  { $sets[] = "descripcion = ?";  $valores[] = textoOpcional($body['descripcion'], 5000); }
        if (array_key_exists('color', $body)) {
            if (!in_array($body['color'], COLORES_TAREA, true)) { responderError(400, 'Color no válido.'); break; }
            $sets[] = "color = ?"; $valores[] = $body['color'];
        }
        if (array_key_exists('orden', $body))        { $sets[] = "orden = ?";        $valores[] = (int)$body['orden']; }
        if (!$sets) { echo json_encode(['ok' => true]); break; }
        $valores[] = $id;
        $pdo->prepare("UPDATE tareas SET " . implode(', ', $sets) . " WHERE id = ?")->execute($valores);
        echo json_encode(['ok' => true]);
        break;

    // Enviar tarea a la papelera (borrado suave, ya no se elimina definitivamente al instante)
    case 'eliminar_tarea':
        if ($method !== 'POST') { http_response_code(405); break; }
        getDB()->prepare("UPDATE tareas SET deleted_at = NOW() WHERE id = ?")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok' => true]);
        break;

    case 'restore_tarea':
        getDB()->prepare("UPDATE tareas SET deleted_at = NULL WHERE id = ?")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok'=>true]);
        break;

    // ----- Vigencias -----
    case 'vigencias':
        $pdo = getDB();
        asegurarTablaVigencias($pdo);
        $rows = $pdo->query(VIGENCIA_SELECT . " WHERE v.deleted_at IS NULL ORDER BY v.fecha_vencimiento ASC, v.nombre ASC")->fetchAll();
        echo json_encode(['vigencias' => conEstadoVigencia($rows), 'alerta_dias' => VIGENCIA_ALERTA_DIAS]);
        break;

    case 'get_vigencia':
        $pdo = getDB();
        asegurarTablaVigencias($pdo);
        $st = $pdo->prepare("SELECT * FROM vigencias WHERE id = ? AND deleted_at IS NULL");
        $st->execute([(int)($_GET['id'] ?? 0)]);
        $r = $st->fetch();
        if (!$r) { responderError(404, 'Vigencia no encontrada.'); break; }
        echo json_encode(['vigencia' => $r]);
        break;

    case 'crear_vigencia':
    case 'actualizar_vigencia':
        if ($method !== 'POST') { http_response_code(405); break; }
        $pdo = getDB();
        asegurarTablaVigencias($pdo);
        [$d, $err] = validarVigencia($pdo, $body);
        if ($err) { responderError(400, $err); break; }
        if ($action === 'crear_vigencia') {
            $pdo->prepare("INSERT INTO vigencias (equipo_id,tipo,nombre,proveedor,referencia,fecha_inicio,fecha_vencimiento,observaciones) VALUES (?,?,?,?,?,?,?,?)")
                ->execute(array_values($d));
            echo json_encode(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
        } else {
            $id = (int)($body['id'] ?? 0);
            $st = $pdo->prepare("UPDATE vigencias SET equipo_id=?,tipo=?,nombre=?,proveedor=?,referencia=?,fecha_inicio=?,fecha_vencimiento=?,observaciones=? WHERE id=? AND deleted_at IS NULL");
            $st->execute(array_merge(array_values($d), [$id]));
            echo json_encode(['ok' => true]);
        }
        break;

    case 'eliminar_vigencia':
        if ($method !== 'POST') { http_response_code(405); break; }
        $pdo = getDB();
        asegurarTablaVigencias($pdo);
        $pdo->prepare("UPDATE vigencias SET deleted_at = NOW() WHERE id = ?")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok' => true]);
        break;

    case 'restore_vigencia':
        $pdo = getDB();
        asegurarTablaVigencias($pdo);
        $st = $pdo->prepare("SELECT e.deleted_at FROM vigencias v LEFT JOIN equipos e ON e.id = v.equipo_id WHERE v.id = ?");
        $st->execute([(int)($body['id'] ?? 0)]);
        $eqBorrado = $st->fetchColumn();
        if ($eqBorrado) { responderError(409, 'Esta vigencia pertenece a un equipo que sigue en la papelera. Restaura primero el equipo.'); break; }
        $pdo->prepare("UPDATE vigencias SET deleted_at = NULL WHERE id = ?")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok' => true]);
        break;

    case 'delete_vigencia_permanente':
        if ($method !== 'POST') { http_response_code(405); break; }
        $pdo = getDB();
        asegurarTablaVigencias($pdo);
        $pdo->prepare("DELETE FROM vigencias WHERE id = ? AND deleted_at IS NOT NULL")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok' => true]);
        break;

    // ----- Impresoras -----
    case 'impresoras':
        $pdo = getDB();
        asegurarTablasImpresoras($pdo);
        [$filtro, $ips] = filtroImpresoras();
        $st = $pdo->prepare("SELECT i.id, i.nombre, i.ip, i.ubicacion,
            (SELECT MAX(l.fecha_hora) FROM impresora_lecturas l WHERE l.impresora_id = i.id) AS ultima_lectura
            FROM impresoras i WHERE $filtro ORDER BY i.nombre");
        $st->execute($ips);
        $rows = $st->fetchAll();
        echo json_encode(['impresoras' => $rows]);
        break;

    // Las impresoras solo se agregan o quitan editando php/impresoras.php
    case 'crear_impresora':
    case 'eliminar_impresora':
        responderError(403, 'Las impresoras se administran desde el archivo php/impresoras.php.');
        break;

    // Consulta la impresora en este momento y guarda la lectura
    case 'consultar_impresora':
        if ($method !== 'POST') { http_response_code(405); break; }
        $pdo = getDB();
        asegurarTablasImpresoras($pdo);
        [$filtro, $ips] = filtroImpresoras('ip');
        $st = $pdo->prepare("SELECT id, ip FROM impresoras WHERE id = ? AND $filtro");
        $st->execute(array_merge([(int)($body['id'] ?? 0)], $ips));
        $imp = $st->fetch();
        if (!$imp) { responderError(404, 'Impresora no encontrada.'); break; }
        [$r, $err, $txt] = consultarImpresora($imp['ip']);
        if ($r === null) {
            http_response_code(502);
            echo json_encode(['error' => $err, 'detalle' => $txt]);
            break;
        }
        $c = $r['campos'];
        $cols = ['copia_color','copia_bn','copia_color_pers','copia_dos_colores','impresion_color','impresion_bn',
                 'impresion_color_pers','impresion_dos_colores','escaneo_color','escaneo_bn',
                 'contador_total','total_color','total_bn','total_color_pers','total_dos_colores'];
        $vals = [$imp['id']];
        foreach ($cols as $k) $vals[] = $c[$k];
        $vals[] = json_encode($r['pares'], JSON_UNESCAPED_UNICODE);
        $pdo->prepare("INSERT INTO impresora_lecturas (impresora_id, " . implode(', ', $cols) . ", crudo) VALUES (" . implode(',', array_fill(0, count($cols) + 2, '?')) . ")")
            ->execute($vals);
        $lec = $pdo->prepare("SELECT * FROM impresora_lecturas WHERE id = ?");
        $lec->execute([(int)$pdo->lastInsertId()]);
        $fila = $lec->fetch();
        $fila['pares'] = $r['pares'];
        unset($fila['crudo']);
        echo json_encode(['ok' => true, 'lectura' => $fila]);
        break;

    case 'impresoras_totales':
        $pdo = getDB();
        asegurarTablasImpresoras($pdo);
        echo json_encode(totalesImpresoras($pdo));
        break;

    case 'borrar_lectura':
        if ($method !== 'POST') { http_response_code(405); break; }
        $pdo = getDB();
        asegurarTablasImpresoras($pdo);
        $pdo->prepare("DELETE FROM impresora_lecturas WHERE id = ?")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok' => true]);
        break;

    case 'vaciar_lecturas':
        if ($method !== 'POST') { http_response_code(405); break; }
        $pdo = getDB();
        asegurarTablasImpresoras($pdo);
        $pdo->prepare("DELETE FROM impresora_lecturas WHERE impresora_id = ?")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok' => true]);
        break;

    case 'lecturas_impresora':
        $pdo = getDB();
        asegurarTablasImpresoras($pdo);
        $st = $pdo->prepare("SELECT id, fecha_hora, copia_color, copia_bn, copia_color_pers, copia_dos_colores, impresion_color, impresion_bn,
            impresion_color_pers, impresion_dos_colores, escaneo_color, escaneo_bn, contador_total
            FROM impresora_lecturas WHERE impresora_id = ? ORDER BY fecha_hora DESC, id DESC LIMIT 100");
        $st->execute([(int)($_GET['id'] ?? 0)]);
        echo json_encode(['lecturas' => $st->fetchAll()]);
        break;

    case 'stats':
        $pdo = getDB();
        $grupos = agruparResponsables($pdo);

        $resp = count($grupos);
        // Exclusivo computadoras nativamente
        $equipos = $pdo->query("SELECT COUNT(*) FROM equipos WHERE componente='Equipo de computo' AND deleted_at IS NULL")->fetchColumn();
        $perifericos = $pdo->query("SELECT COUNT(*) FROM equipos WHERE componente<>'Equipo de computo' AND deleted_at IS NULL")->fetchColumn();

        $lista_resp = array_map(fn($g) => ['responsable' => $g['responsable'], 'area' => $g['area']], $grupos);
        // Se agrega 'modelo' para que el resumen por componente (modal de KPIs) muestre marca + modelo
        $lista_eq = $pdo->query("SELECT id, responsable, componente, marca, modelo, serial FROM equipos WHERE deleted_at IS NULL ORDER BY responsable")->fetchAll();

        // Áreas registradas centralizadas y normalizadas
        $areaRows = $pdo->query("SELECT area, sede FROM equipos WHERE deleted_at IS NULL")->fetchAll();
        $areasNorm = [];
        foreach ($areaRows as $a) {
            if (trim($a['area'] ?? '') === '') continue; // ignora áreas vacías: no deben contarse ni listarse
            $normArea = normalizarArea($a['area']);
            $key = mb_strtolower($normArea);
            if (!isset($areasNorm[$key])) {
                $areasNorm[$key] = ['area' => $normArea, 'sede' => trim($a['sede'])];
            }
        }
        $lista_areas = array_values($areasNorm);
        usort($lista_areas, fn($a, $b) => strcasecmp($a['area'], $b['area']));
        $areas = count($lista_areas);

        // Distribución de equipos de cómputo por área 
        //(Filtra en query por 'Equipo de computo' de manera segura)
        $porAreaNorm = [];
        $pcAreaRows = $pdo->query("SELECT area FROM equipos WHERE deleted_at IS NULL AND componente='Equipo de computo'")->fetchAll();
        foreach ($pcAreaRows as $a) {
            if (trim($a['area'] ?? '') === '') continue;
            $normArea = normalizarArea($a['area']);
            $key = mb_strtolower($normArea);
            if (!isset($porAreaNorm[$key])) $porAreaNorm[$key] = ['area' => $normArea, 'total' => 0];
            $porAreaNorm[$key]['total']++;
        }
        $por_area = array_values($porAreaNorm);
        usort($por_area, fn($a, $b) => $b['total'] <=> $a['total']);

        // Componentes Generales
        $por_componente = $pdo->query("
            SELECT componente, COUNT(*) AS total
            FROM equipos
            WHERE deleted_at IS NULL
            GROUP BY componente
            ORDER BY total DESC
        ")->fetchAll();

        asegurarTablaVigencias($pdo);
        $vig = conEstadoVigencia($pdo->query("SELECT fecha_vencimiento FROM vigencias WHERE deleted_at IS NULL")->fetchAll());
        $vigencias = ['total' => count($vig), 'vencidas' => 0, 'por_vencer' => 0, 'vigentes' => 0];
        foreach ($vig as $v) {
            if ($v['estado'] === 'vencida') $vigencias['vencidas']++;
            elseif ($v['estado'] === 'por_vencer') $vigencias['por_vencer']++;
            else $vigencias['vigentes']++;
        }

        asegurarTablasImpresoras($pdo);
        $impresoras = totalesImpresoras($pdo);

        echo json_encode(compact('resp','equipos','areas','perifericos','lista_resp','lista_areas','lista_eq','por_area','por_componente','vigencias','impresoras'));
        break;

    // ----- Listado de IPs de la red (php/ips.php) -----
    case 'listado_ip':
        $archivo = __DIR__ . '/ips.php';
        $cfg = is_file($archivo) ? (require $archivo) : [];
        // Enlace de cada equipo: el 'url' del archivo (solo http/https) o http://IP
        $conEnlace = function (array $filas): array {
            $out = [];
            foreach ($filas as $f) {
                $ip = trim((string)($f['ip'] ?? ''));
                if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) continue;
                $url = trim((string)($f['url'] ?? ''));
                $f['ip'] = $ip;
                $f['url'] = preg_match('#^https?://#i', $url) ? $url : "http://$ip";
                $out[] = $f;
            }
            return $out;
        };
        echo json_encode([
            'administrativa' => $conEnlace($cfg['administrativa'] ?? []),
            'planta' => $conEnlace($cfg['planta'] ?? []),
        ]);
        break;

    // ----- Requisiciones de compra -----
    case 'requisicion_listas':
        $pdo = getDB();
        asegurarTablasRequisiciones($pdo);
        $cfg = requisicionesConfig();
        $par = fn($arr) => array_map(fn($k, $v) => ['cod' => (string)$k, 'nombre' => $v], array_keys($arr), $arr);
        echo json_encode([
            'formato' => $cfg['formato'],
            'empresas' => $par($cfg['empresas']),
            'areas' => $par($cfg['areas']),
            'solicitantes' => $par($cfg['solicitantes']),
            'autorizado_por' => $cfg['autorizado_por'],
            'prioridades' => $cfg['prioridades'],
            'unidades' => $cfg['unidades'],
            'centros_trabajo' => $cfg['centros_trabajo'],
            'max_items' => REQ_MAX_ITEMS,
            'siguiente' => siguienteConsecutivo($pdo),
            'hoy' => $pdo->query("SELECT CURDATE()")->fetchColumn(),
            'firma_solicitante' => $cfg['firma_solicitante'] ?? ['nombre' => '', 'cedula' => '', 'cargo' => ''],
        ]);
        break;

    case 'requisiciones':
        $pdo = getDB();
        asegurarTablasRequisiciones($pdo);
        $q = trim($_GET['q'] ?? '');
        // orden = número de la requisición en el historial (1, 2, 3… según se fueron creando),
        // independiente del consecutivo del REQ N° (que puede escribirse a mano)
        $sql = "SELECT r.id, (SELECT COUNT(*) FROM requisiciones o WHERE o.id <= r.id) AS orden, r.consecutivo, r.codigo, r.fecha, r.prioridad, r.area_nombre, r.solicitante_nombre, r.creado_at,
                    (SELECT COUNT(*) FROM requisicion_items i WHERE i.requisicion_id = r.id) AS total_items,
                    (SELECT i.articulo FROM requisicion_items i WHERE i.requisicion_id = r.id ORDER BY i.item LIMIT 1) AS primer_item
                FROM requisiciones r";
        $params = [];
        if ($q !== '') {
            $like = '%' . $q . '%';
            $sql .= " WHERE r.codigo LIKE ? OR r.solicitante_nombre LIKE ? OR r.area_nombre LIKE ? OR r.observaciones LIKE ?
                      OR CAST(r.consecutivo AS CHAR) = ?
                      OR EXISTS (SELECT 1 FROM requisicion_items i WHERE i.requisicion_id = r.id AND (i.articulo LIKE ? OR i.proveedor LIKE ?))";
            $params = [$like, $like, $like, $like, $q, $like, $like];
        }
        $st = $pdo->prepare($sql . " ORDER BY r.id DESC LIMIT 500");
        $st->execute($params);
        echo json_encode(['requisiciones' => $st->fetchAll()]);
        break;

    case 'requisicion':
        $pdo = getDB();
        asegurarTablasRequisiciones($pdo);
        $r = obtenerRequisicion($pdo, (int)($_GET['id'] ?? 0));
        if (!$r) { responderError(404, 'Requisición no encontrada.'); break; }
        echo json_encode(['requisicion' => $r]);
        break;

    // Borra una requisición del historial (sus ítems se borran con ella)
    case 'eliminar_requisicion':
        $pdo = getDB();
        asegurarTablasRequisiciones($pdo);
        $st = $pdo->prepare("DELETE FROM requisiciones WHERE id = ?");
        $st->execute([(int)($body['id'] ?? 0)]);
        if (!$st->rowCount()) { responderError(404, 'La requisición ya no existe.'); break; }
        echo json_encode(['ok' => true]);
        break;

    // Borra todo el historial de requisiciones
    case 'vaciar_requisiciones':
        $pdo = getDB();
        asegurarTablasRequisiciones($pdo);
        $pdo->beginTransaction();
        $pdo->exec("DELETE FROM requisicion_items");
        $pdo->exec("DELETE FROM requisiciones");
        $pdo->commit();
        echo json_encode(['ok' => true]);
        break;

    // Guarda la requisición y le asigna el siguiente consecutivo (nunca se repite)
    case 'crear_requisicion':
        $pdo = getDB();
        asegurarTablasRequisiciones($pdo);
        [$d, $items, $err] = validarRequisicion($body);
        if ($err) { responderError(400, $err); break; }
        $manual = $d['consecutivo'] !== null;
        if ($manual) {
            $usado = $pdo->prepare("SELECT codigo FROM requisiciones WHERE consecutivo = ?");
            $usado->execute([$d['consecutivo']]);
            if ($c = $usado->fetchColumn()) { responderError(409, "El consecutivo {$d['consecutivo']} ya está usado (requisición $c). Escribe otro o déjalo como estaba."); break; }
        }
        $nuevoId = null;
        $duplicadoManual = false;
        for ($intento = 0; $intento < 6 && $nuevoId === null && !$duplicadoManual; $intento++) {
            if ($intento > 0) usleep(random_int(20000, 120000));   // espera corta antes de reintentar
            try {
                $pdo->beginTransaction();
                $cons = $manual ? $d['consecutivo'] : siguienteConsecutivo($pdo, true);
                $codigo = "{$d['empresa_cod']}-{$d['area_cod']}-{$d['solicitante_cod']}-$cons";
                $pdo->prepare("INSERT INTO requisiciones (consecutivo, codigo, fecha, prioridad, empresa_cod, empresa_nombre, area_cod, area_nombre,
                        solicitante_cod, solicitante_nombre, autorizado_por, observaciones, sol_cedula, sol_cargo, firma_nombre)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
                    ->execute([$cons, $codigo, $d['fecha'], $d['prioridad'], $d['empresa_cod'], $d['empresa_nombre'], $d['area_cod'], $d['area_nombre'],
                        $d['solicitante_cod'], $d['solicitante_nombre'], $d['autorizado_por'], $d['observaciones'], $d['sol_cedula'], $d['sol_cargo'], $d['firma_nombre']]);
                $id = (int)$pdo->lastInsertId();
                $ins = $pdo->prepare("INSERT INTO requisicion_items (requisicion_id, item, cantidad, unidad, articulo, proveedor, centro_trabajo) VALUES (?,?,?,?,?,?,?)");
                foreach ($items as $n => $it) {
                    $ins->execute([$id, $n + 1, $it['cantidad'], $it['unidad'], $it['articulo'], $it['proveedor'], $it['centro_trabajo']]);
                }
                $pdo->commit();
                $nuevoId = $id;
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                // Dos personas exportando al mismo tiempo: consecutivo duplicado (1062), bloqueo mutuo (1213)
                // o espera de bloqueo (1205). Se reintenta con el siguiente número; cualquier otro error se reporta.
                $codErr = (int)($e->errorInfo[1] ?? 0);
                if (!in_array($codErr, [1062, 1213, 1205], true)) throw $e;
                // Un consecutivo escrito a mano que otra persona acaba de usar no se cambia solo
                if ($manual && $codErr === 1062) $duplicadoManual = true;
            }
        }
        if ($duplicadoManual) { responderError(409, "El consecutivo {$d['consecutivo']} acaba de ser usado por otra requisición. Escribe otro."); break; }
        if ($nuevoId === null) { responderError(409, 'No se pudo asignar el consecutivo. Intenta de nuevo.'); break; }
        echo json_encode(['ok' => true, 'requisicion' => obtenerRequisicion($pdo, $nuevoId)]);
        break;

    // Edita una requisición ya guardada (mismos datos e ítems; el código se rearma)
    case 'actualizar_requisicion':
        $pdo = getDB();
        asegurarTablasRequisiciones($pdo);
        $id = (int)($body['id'] ?? 0);
        $actual = obtenerRequisicion($pdo, $id);
        if (!$actual) { responderError(404, 'La requisición ya no existe.'); break; }
        [$d, $items, $err] = validarRequisicion($body);
        if ($err) { responderError(400, $err); break; }
        $cons = $d['consecutivo'] ?? (int)$actual['consecutivo'];
        $usado = $pdo->prepare("SELECT codigo FROM requisiciones WHERE consecutivo = ? AND id <> ?");
        $usado->execute([$cons, $id]);
        if ($c = $usado->fetchColumn()) { responderError(409, "El consecutivo $cons ya está usado (requisición $c). Escribe otro."); break; }
        $codigo = "{$d['empresa_cod']}-{$d['area_cod']}-{$d['solicitante_cod']}-$cons";
        try {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE requisiciones SET consecutivo=?, codigo=?, fecha=?, prioridad=?, empresa_cod=?, empresa_nombre=?, area_cod=?, area_nombre=?,
                    solicitante_cod=?, solicitante_nombre=?, autorizado_por=?, observaciones=?, sol_cedula=?, sol_cargo=?, firma_nombre=? WHERE id=?")
                ->execute([$cons, $codigo, $d['fecha'], $d['prioridad'], $d['empresa_cod'], $d['empresa_nombre'], $d['area_cod'], $d['area_nombre'],
                    $d['solicitante_cod'], $d['solicitante_nombre'], $d['autorizado_por'], $d['observaciones'], $d['sol_cedula'], $d['sol_cargo'], $d['firma_nombre'], $id]);
            $pdo->prepare("DELETE FROM requisicion_items WHERE requisicion_id = ?")->execute([$id]);
            $ins = $pdo->prepare("INSERT INTO requisicion_items (requisicion_id, item, cantidad, unidad, articulo, proveedor, centro_trabajo) VALUES (?,?,?,?,?,?,?)");
            foreach ($items as $n => $it) {
                $ins->execute([$id, $n + 1, $it['cantidad'], $it['unidad'], $it['articulo'], $it['proveedor'], $it['centro_trabajo']]);
            }
            $pdo->commit();
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ((int)($e->errorInfo[1] ?? 0) === 1062) { responderError(409, "El consecutivo $cons acaba de ser usado por otra requisición. Escribe otro."); break; }
            throw $e;
        }
        echo json_encode(['ok' => true, 'requisicion' => obtenerRequisicion($pdo, $id)]);
        break;

    default:
        http_response_code(400); echo json_encode(['error' => 'Acción no válida']);
}