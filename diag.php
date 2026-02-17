<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
echo "=== DIAGNÓSTICO ===\n\n";

// Teste PHP
echo "PHP OK - Versão: " . phpversion() . "\n\n";

// Teste DNS do domínio
$dominio = 'seusite.com.br';
$ip = gethostbyname($dominio);

if ($ip === $dominio) {
    echo "DNS ERRO: domínio NÃO resolve\n";
} else {
    echo "DNS OK: $dominio -> $ip\n";
}

echo "\n";

// Teste MySQL
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=",
        "",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]
    );
    echo "MySQL OK - Conectado com sucesso\n";
} catch (PDOException $e) {
    echo "MySQL ERRO: " . $e->getMessage() . "\n";
}

echo "\n=== FIM ===";
echo "</pre>";
