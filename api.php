<?php
// 1. Cabeceras CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Conexión segura a SQLite
try {
    $db = new PDO('sqlite:q3jKvTbpGJ12UY.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "error" => "Error de conexión: " . $e->getMessage()]);
    exit();
}

// 3. Leer y validar JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);
if (!is_array($input)) $input = [];



// --- LOG EVENTO ACUMULADO ------------------------------------------------
date_default_timezone_set('America/La_Paz'); // Hora de La Paz
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) mkdir($logDir, 0777, true);

// Archivo de log del día
$logFile = $logDir . '/' . date("Y-m-d") . '.json';

// Preparar el evento
$evento = [
    "fecha_completa" => date("Y-m-d H:i:s"), // año-mes-día hora:min:seg
    "dia_semana" => date("l"),               // lunes, martes, ...
    "ip_cliente" => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    "payload" => $input
];

// Leer log existente
$logData = [];
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $logData = json_decode($content, true);
    if (!is_array($logData)) $logData = [];
}

// Agregar el nuevo evento
$logData[] = $evento;

// Guardar todo de nuevo en el archivo
file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// --- LOG EVENTO ACUMULADO  FIN ------------------------------------------------------

// 🔐 Validar password
if (!isset($input['password']) || $input['password'] !== '0SiShHPgNwbYe5') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "NO TIENES PERMISO."]);
    exit();
}

// 4. Campos de búsqueda válidos
$camposBusqueda = [
    "carnet_identidad", "carnet_rango_min", "carnet_rango_max",
    "nombre", "paterno", "materno", "genero",
    "fecha_nacimiento_inicio", "fecha_nacimiento_fin"
];

// Verificar si hay al menos un filtro válido
$todosVacios = true;
foreach ($camposBusqueda as $campo) {
    if (!empty($input[$campo])) {
        if ($campo === 'carnet_rango_min' && $input[$campo] === '0') continue;
        if ($campo === 'carnet_rango_max' && $input[$campo] === '18000000') continue;
        $todosVacios = false;
        break;
    }
}

if ($todosVacios) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Debe enviar al menos un parámetro de búsqueda no predeterminado."]);
    exit();
}



// --- CONFIGURACIÓN DE LISTA NEGRA (BLACKLIST) ###########################################################


$ciFile = __DIR__ . '/ci_blockeds.txt';
$namesFile = __DIR__ . '/names_blocked.txt';

// Crear archivos vacíos si no existen
if (!file_exists($ciFile)) {
    file_put_contents($ciFile, '');
}

if (!file_exists($namesFile)) {
    file_put_contents($namesFile, '');
}

// Leer archivos
$ci_blockeds = file($ciFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$names_blocked = file($namesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);


$nombresProhibidos = [];

foreach ($names_blocked as $linea) {
    $partes = explode(' ', strtoupper(trim($linea)));

    // Eliminar segundos nombres (opcional)
    if (count($partes) > 3) {
        $partes = [$partes[0], $partes[count($partes)-2], $partes[count($partes)-1]];
    }

    $nombresProhibidos[] = $partes;
}

// --- VALIDACIÓN DE LISTA NEGRA -----------------------------------------------

// 1. Validar Carnet (Individual o Rango)
$ciBuscado = $input['carnet_identidad'] ?? '';
if (in_array($ciBuscado, $ci_blockeds)) {
    echo json_encode(["status" => "success", "total" => 0, "personas" => []]);
    exit();
}

// 2. Validar Combinaciones de Nombres
$nombreInput = strtoupper(
    trim(($input['nombre'] ?? '') . ' ' . ($input['paterno'] ?? '') . ' ' . ($input['materno'] ?? ''))
);

// Convertimos el nombre buscado en array
$palabrasInput = explode(' ', $nombreInput);

foreach ($nombresProhibidos as $personaBloqueada) {

    // Contar coincidencias por persona
    $coincidencias = count(array_intersect($palabrasInput, $personaBloqueada));

    // Si hay 2 o más coincidencias → bloquear
    if ($coincidencias >= 2) {
        echo json_encode([
            "status" => "success",
            "total" => 0,
            "personas" => []
        ]);
        exit();
    }
}
// --- FIN VALIDACIÓN LISTA NEGRA ###########################################################


// 5. Mapa estado civil
$mapEstadoCivil = [
    0 => "S/D", 1 => "CASADA/O", 2 => "DIVORCIADA/O", 3 => "VIUDA/O",
    9 => "OTRO", 10 => "SOLTERA/O", 11 => "CONVIVE"
];

// 6. Construir SQL dinámico con parámetros
$sql = "SELECT carnet_identidad, complemento, paterno, materno, nombre, fecha_nacimiento,
        codigo_rude, estado_civil_tipo_id, nacimiento_localidad, observacion,
        fecha_registro, fecha_modificacion, tiene_discapacidad, telefono, email,
        genero, materno_idioma_tipo, comunidad, municipio, provincia, departamento,
        pais, ci_consultado, timestamp_consulta
        FROM personas WHERE 1=1";

$params = [];

// Carnet
if (!empty($input['carnet_rango_min']) || !empty($input['carnet_rango_max'])) {
    $min = !empty($input['carnet_rango_min']) ? intval($input['carnet_rango_min']) : 0;
    $max = !empty($input['carnet_rango_max']) ? intval($input['carnet_rango_max']) : 18000000;
    if ($min > $max) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "El valor mínimo del rango de carnet no puede ser mayor al máximo."]);
        exit();
    }
    $sql .= " AND carnet_identidad BETWEEN :min_ci AND :max_ci";
    $params[':min_ci'] = $min;
    $params[':max_ci'] = $max;
} elseif (!empty($input['carnet_identidad'])) {
    $sql .= " AND carnet_identidad LIKE :ci";
    $params[':ci'] = $input['carnet_identidad'] . "%";
}

// Nombre, paterno, materno
foreach (['nombre', 'paterno', 'materno'] as $campo) {
    if (!empty($input[$campo])) {
        $sql .= " AND $campo LIKE :$campo";
        $params[":$campo"] = "%" . strtoupper($input[$campo]) . "%";
    }
}

// Género
if (!empty($input['genero'])) {
    $sql .= " AND genero = :genero";
    $params[':genero'] = $input['genero'];
}

// Fechas
if (!empty($input['fecha_nacimiento_inicio'])) {
    $sql .= " AND fecha_nacimiento >= :ini";
    $params[':ini'] = $input['fecha_nacimiento_inicio'];
}
if (!empty($input['fecha_nacimiento_fin'])) {
    $sql .= " AND fecha_nacimiento <= :fin";
    $params[':fin'] = $input['fecha_nacimiento_fin'];
}

$sql .= " ORDER BY paterno ASC LIMIT 1000";

// 7. Ejecutar de forma segura
try {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error al ejecutar la consulta: " . $e->getMessage()]);
    exit();
}

// 8. Procesar resultados
foreach ($resultados as &$p) {
    $idEstado = $p["estado_civil_tipo_id"];
    $p["estado_civil"] = $mapEstadoCivil[$idEstado] ?? "DESCONOCIDO";
}

// 9. Respuesta
echo json_encode([
    "status" => "success",
    "total" => count($resultados),
    "personas" => $resultados
]);
?>
