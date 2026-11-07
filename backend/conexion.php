<?php
declare(strict_types=1);
$host='localhost'; $db='bd_represa'; $user='root'; $pass=''; // Ajusta si tu MySQL tiene contraseña.
$dsn="mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
  $pdo=new PDO($dsn,$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
} catch(PDOException $e) {
  http_response_code(500); header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false,'mensaje'=>'No se pudo conectar con MySQL. Revisa backend/conexion.php y que MySQL esté iniciado.']); exit;
}
