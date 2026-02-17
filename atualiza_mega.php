<?php
// 1. Conexão centralizada
include 'config.php';

// Forçar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // 2. URL da API específica para a Mega-Sena (Último resultado)
    $url = "https://loteriascaixa-api.herokuapp.com/api/megasena/latest";
    
    // Usando cURL para maior compatibilidade com hospedagens gratuitas
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $json = curl_exec($ch);
    curl_close($ch);

    $dados = json_decode($json, true);

    if (!$dados) {
        die("Erro ao capturar dados da Mega-Sena. Verifique a conexão com a API.");
    }

    // 3. Preparação dos dados
    $concurso = $dados['concurso'];
    
    // Converte data de DD/MM/YYYY para YYYY-MM-DD (Padrão MySQL)
    $dataArray = explode('/', $dados['data']);
    $dataFormatada = $dataArray[2] . '-' . $dataArray[1] . '-' . $dataArray[0];
    
    // Une as dezenas em uma string separada por vírgula
    $dezenas = implode(',', $dados['dezenas']);

    // 4. Inserção segura (Ignore se o concurso já existir)
    $sql = "INSERT IGNORE INTO resultados_megasena (concurso, data_sorteio, dezenas) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$concurso, $dataFormatada, $dezenas]);

    echo "<div style='font-family:sans-serif; padding:20px; border:1px solid #27ae60; background:#eafaf1; color:#27ae60; border-radius:10px;'>";
    echo "<h3>✅ Sincronização Mega-Sena</h3>";
    echo "Concurso <strong>$concurso</strong> verificado com sucesso!";
    echo "<br><br><a href='conferir_mega.php'>Voltar para o Simulador Mega</a>";
    echo "</div>";

} catch (PDOException $e) {
    die("Erro no Banco de Dados: " . $e->getMessage());
}
?>