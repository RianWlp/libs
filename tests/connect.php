<?php

$dsn = 'pgsql:host=localhost;port=5432;dbname=oilcar;';
$user = 'oilcar';
$pass = 'oilcar';

try {
    $pdo = new \PDO($dsn, $user, $pass);
    echo "Conectado ao PostgreSQL com sucesso!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
