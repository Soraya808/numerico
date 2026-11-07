
<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/conexion.php';

try {

    // Solo aceptar solicitudes POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'ok' => false,
            'mensaje' => 'Método no permitido. Se requiere POST.'
        ]);
        exit;
    }

    // Recibir los datos enviados desde JavaScript
    $datos = json_decode(file_get_contents('php://input'), true);

    if (!is_array($datos)) {
        throw new Exception('No se recibieron datos válidos.');
    }

    // Validar los datos principales
    $campos = [
        'escenario', 'V0', 'A', 'C', 'k', 'Vm', 'T',
        'h1', 'h2', 'aporteNeto', 'exactaFinal',
        'heunFinal', 'rk4Final', 'errorHeun', 'errorRK4'
    ];

    foreach ($campos as $campo) {
        if (!isset($datos[$campo]) ||
            !is_numeric($datos[$campo]) && $campo !== 'escenario') {
            if ($campo === 'escenario' && isset($datos[$campo])) {
                continue;
            }
            throw new Exception("Falta el dato: " . $campo);
        }
    }

    if (empty($datos['escenario'])) {
        throw new Exception('El escenario es obligatorio.');
    }

    // Iniciar transacción
    $pdo->beginTransaction();

    // Insertar simulación principal
    $sql = "INSERT INTO simulaciones (
        escenario, V0, A, C, k, Vm, T, h1, h2,
        aporte_neto, dia_critico,
        exacta_final, heun_final, rk4_final,
        error_heun, error_rk4
    ) VALUES (
        :escenario, :V0, :A, :C, :k, :Vm, :T, :h1, :h2,
        :aporte_neto, :dia_critico,
        :exacta_final, :heun_final, :rk4_final,
        :error_heun, :error_rk4
    )";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':escenario' => $datos['escenario'],
        ':V0' => $datos['V0'],
        ':A' => $datos['A'],
        ':C' => $datos['C'],
        ':k' => $datos['k'],
        ':Vm' => $datos['Vm'],
        ':T' => $datos['T'],
        ':h1' => $datos['h1'],
        ':h2' => $datos['h2'],
        ':aporte_neto' => $datos['aporteNeto'],
        ':dia_critico' => $datos['diaCritico'] ?? null,
        ':exacta_final' => $datos['exactaFinal'],
        ':heun_final' => $datos['heunFinal'],
        ':rk4_final' => $datos['rk4Final'],
        ':error_heun' => $datos['errorHeun'],
        ':error_rk4' => $datos['errorRK4']
    ]);

    // Obtener ID de la simulación
    $id = $pdo->lastInsertId();

    // Preparar inserción del detalle
    $sqlDetalle = "INSERT INTO detalle_simulacion (
        simulacion_id, paso, numero_paso,
        tiempo, exacta, heun, rk4
    ) VALUES (
        :simulacion_id, :paso, :numero_paso,
        :tiempo, :exacta, :heun, :rk4
    )";

    $detalleStmt = $pdo->prepare($sqlDetalle);

    // Guardar resultados de h1 y h2
    foreach (['h1' => 'detalleH1', 'h2' => 'detalleH2'] as $paso => $campo) {

        if (!isset($datos[$campo]) || !is_array($datos[$campo])) {
            throw new Exception("Falta el detalle de " . $paso);
        }

        foreach ($datos[$campo] as $numero => $fila) {

            $detalleStmt->execute([
                ':simulacion_id' => $id,
                ':paso' => $paso,
                ':numero_paso' => $numero,
                ':tiempo' => $fila['t'],
                ':exacta' => $fila['ex'],
                ':heun' => $fila['he'],
                ':rk4' => $fila['rk']
            ]);
        }
    }

    // Confirmar guardado
    $pdo->commit();

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Simulación guardada correctamente.',
        'id' => $id
    ]);

} catch (Throwable $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => $e->getMessage()
    ]);
}
