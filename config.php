<?php
// config.php
   // 1. Forçar exibição de erros (Fundamental para descobrir o problema)
error_reporting(E_ALL);
ini_set('display_errors', 1); 
   
$host = 'localhost';
$db   = 'banco';
$user = 'suser';
$pass = 'senha aqui';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro crítico de conexão: " . $e->getMessage());
}
?>
