
<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/conexion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new Exception('Se requiere el método POST.');
    }

    $datos = json_decode(file_get_contents('php://input'), true);

    if (!isset($datos['id']) || !filter_var($datos['id'], FILTER_VALIDATE_INT) || $datos['id'] <= 0) {
        throw new Exception('ID de simulación no válido.');
    }

    $stmt = $pdo->prepare(
        "DELETE FROM simulaciones WHERE id = :id"
    );

    $stmt->execute([':id' => $datos['id']]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('No se encontró esa simulación.');
    }

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Simulación eliminada correctamente.'
    ]);

} catch (Throwable $e) {
    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'mensaje' => $e->getMessage()
    ]);
}
