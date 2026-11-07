<?php
declare(strict_types=1); header('Content-Type: application/json; charset=utf-8'); require __DIR__.'/conexion.php';
try{$q=$pdo->query('SELECT id,fecha,escenario,V0,A,C,k,Vm,T,h1,h2,aporte_neto,dia_critico,exacta_final,heun_final,rk4_final,error_heun,error_rk4 FROM simulaciones ORDER BY id DESC LIMIT 100');echo json_encode(['ok'=>true,'datos'=>$q->fetchAll()]);}catch(Throwable $e){http_response_code(500);echo json_encode(['ok'=>false,'mensaje'=>'No se pudo consultar el historial']);}
