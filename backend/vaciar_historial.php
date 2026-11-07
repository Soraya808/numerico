
<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/conexion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new Exception('Método no permitido.');
    }

    $pdo->exec("DELETE FROM simulaciones");

    echo json_encode([
        'ok' => true,
        'mensaje' => 'Todo el historial ha sido eliminado.'
    ]);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'mensaje' => $e->getMessage()
    ]);
}
