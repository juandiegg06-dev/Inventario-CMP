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

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$body = [];
if (in_array($method, ['POST','PUT'])) {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true) ?? [];
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
        $stmt = getDB()->prepare("SELECT * FROM equipos WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['equipo' => $stmt->fetch()]);
        break;

    case 'update_equipo':
        if ($method !== 'POST') { http_response_code(405); break; }
        $id = (int)($body['id'] ?? 0);
        
        $campos  = ['sede','area','responsable','componente','tipo_equipo','usuario_actual',
                    'nombre_completo','grupo_usuario','marca','modelo','serial','procesador',
                    'disco_total','tipo_disco','disco_usado','tipo_ram','ram_total','ram_usada',
                    'sistema_op','ip_local','conexion','observaciones','office'];
        $sets = []; $valores = [];
        foreach ($campos as $c) {
            if (array_key_exists($c, $body)) {
                $sets[] = "`$c` = ?";
                $valores[] = $body[$c] === '' ? null : $body[$c];
            }
        }
        $valores[] = $id;
        $pdo = getDB();
        $pdo->prepare("UPDATE equipos SET " . implode(', ', $sets) . " WHERE id = ?")->execute($valores);
        echo json_encode(['ok' => true]);
        break;

    // Se mantiene create_equipo SOLO para crear periféricos desde dentro de un perfil
    case 'create_equipo':
        if ($method !== 'POST') { http_response_code(405); break; }
        $pdo = getDB();
        $maxItem = $pdo->query("SELECT COALESCE(MAX(item),0)+1 FROM equipos")->fetchColumn();
        
        $pdo->prepare("
            INSERT INTO equipos (item, equipo_padre_id, sede, area, responsable, componente, tipo_equipo, usuario_actual,
              nombre_completo, marca, modelo, serial, procesador, disco_total, tipo_disco,
              disco_usado, tipo_ram, ram_total, ram_usada, sistema_op, ip_local, conexion, observaciones,
              office, fecha_registro, hora_registro)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,CURDATE(),CURTIME())
        ")->execute([
            $maxItem,
            !empty($body['equipo_padre_id']) ? $body['equipo_padre_id'] : null,
            $body['sede'] ?? null,
            $body['area'] ?? null,
            $body['responsable'] ?? null,
            $body['componente'] ?? 'Mouse',
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
        // Si es un equipo padre, sus periféricos anidados (mouse/teclado) se van con él a la papelera
        $hijos = $pdo->prepare("SELECT id FROM equipos WHERE equipo_padre_id = ? AND deleted_at IS NULL");
        $hijos->execute([$id]);
        $todos = array_merge([$id], array_column($hijos->fetchAll(), 'id'));
        $in = implode(',', array_fill(0, count($todos), '?'));
        $pdo->prepare("UPDATE equipos SET deleted_at = NOW() WHERE id IN ($in)")->execute($todos);
        // Y con ellos, todo el software asociado (evita huérfanos)
        $pdo->prepare("UPDATE software SET deleted_at = NOW() WHERE equipo_id IN ($in) AND deleted_at IS NULL")->execute($todos);
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
        if ($id <= 0 || $nuevoResp === '') { http_response_code(400); echo json_encode(['error' => 'Datos incompletos']); break; }
        getDB()->prepare("UPDATE equipos SET responsable = ? WHERE id = ? AND componente = 'Equipo de computo'")->execute([$nuevoResp, $id]);
        echo json_encode(['ok' => true]);
        break;

    case 'add_software':
        $pdo = getDB();
        $pdo->prepare("INSERT INTO software (equipo_id,programa,version) VALUES (?,?,?)")
            ->execute([(int)$body['equipo_id'], $body['programa'] ?? '', $body['version'] ?? null]);
        echo json_encode(['ok'=>true, 'id'=>(int)$pdo->lastInsertId()]);
        break;

    case 'delete_software':
        getDB()->prepare("UPDATE software SET deleted_at = NOW() WHERE id = ?")->execute([(int)($body['id'] ?? 0)]);
        echo json_encode(['ok'=>true]);
        break;

    case 'papelera':
        $pdo = getDB();
        // Solo se listan sueltos los equipos/periféricos que NO son hijos de un equipo
        // que también está en la papelera (si el padre está, el hijo va incluido/anidado en él)
        $equipos = $pdo->query("
            SELECT e.id, e.componente, e.tipo_equipo, e.marca, e.modelo, e.serial, e.responsable, e.deleted_at,
                   (SELECT COUNT(*) FROM software s WHERE s.equipo_id = e.id AND s.deleted_at IS NOT NULL) AS software_incluido,
                   (SELECT COUNT(*) FROM equipos c WHERE c.equipo_padre_id = e.id AND c.deleted_at IS NOT NULL) AS perifericos_incluidos
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
        echo json_encode(['equipos' => $equipos, 'software' => $software, 'tareas' => $tareas]);
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
        $hijos = $pdo->prepare("SELECT id FROM equipos WHERE equipo_padre_id = ?");
        $hijos->execute([$id]);
        $todos = array_merge([$id], array_column($hijos->fetchAll(), 'id'));
        $in = implode(',', array_fill(0, count($todos), '?'));
        $pdo->prepare("UPDATE equipos SET deleted_at = NULL WHERE id IN ($in)")->execute($todos);
        $pdo->prepare("UPDATE software SET deleted_at = NULL WHERE equipo_id IN ($in)")->execute($todos);
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
        $pdo->prepare("DELETE FROM software WHERE equipo_id IN ($in)")->execute($todos);
        $pdo->prepare("DELETE FROM equipos WHERE id IN ($in)")->execute($todos);
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
        $pdo->exec("DELETE FROM software WHERE deleted_at IS NOT NULL");
        $pdo->exec("DELETE FROM equipos WHERE deleted_at IS NOT NULL");
        $pdo->exec("DELETE FROM tareas WHERE deleted_at IS NOT NULL");
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
        $maxOrden = $pdo->query("SELECT COALESCE(MAX(orden),0)+1 FROM tareas WHERE estado='pendiente'")->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO tareas (titulo, descripcion, estado, color, orden) VALUES (?,?,?,?,?)");
        $stmt->execute([
            trim($body['titulo'] ?? ''),
            $body['descripcion'] ?? null,
            'pendiente',
            $body['color'] ?? 'verde',
            $maxOrden,
        ]);
        echo json_encode(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
        break;

    case 'actualizar_tarea':
        if ($method !== 'POST') { http_response_code(405); break; }
        $id = (int)($body['id'] ?? 0);
        $pdo = getDB();

        $sets = []; $valores = [];
        if (array_key_exists('titulo', $body))       { $sets[] = "titulo = ?";       $valores[] = $body['titulo']; }
        if (array_key_exists('descripcion', $body))  { $sets[] = "descripcion = ?";  $valores[] = $body['descripcion']; }
        if (array_key_exists('color', $body))        { $sets[] = "color = ?";        $valores[] = $body['color']; }
        if (array_key_exists('orden', $body))        { $sets[] = "orden = ?";        $valores[] = (int)$body['orden']; }
        if (array_key_exists('estado', $body)) {
            $sets[] = "estado = ?";
            $valores[] = $body['estado'];
            $sets[] = "completado_at = " . ($body['estado'] === 'completada' ? "NOW()" : "NULL");
        }
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

        echo json_encode(compact('resp','equipos','areas','perifericos','lista_resp','lista_areas','lista_eq','por_area','por_componente'));
        break;

    default:
        http_response_code(400); echo json_encode(['error' => 'Acción no válida']);
}