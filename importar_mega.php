<?php
include 'config.php';
set_time_limit(600);

try {
    echo "<h2>Iniciando Importação Mega-Sena...</h2>";
    $url = "https://loteriascaixa-api.herokuapp.com/api/megasena/";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $json = curl_exec($ch);
    curl_close($ch);

    $resultados = json_decode($json, true);
    $stmt = $pdo->prepare("INSERT IGNORE INTO resultados_megasena (concurso, data_sorteio, dezenas) VALUES (?, ?, ?)");

    $pdo->beginTransaction();
    $count = 0;
    foreach ($resultados as $res) {
        $dataArray = explode('/', $res['data']);
        $dataFormatada = $dataArray[2] . '-' . $dataArray[1] . '-' . $dataArray[0];
        $dezenas = implode(',', $res['dezenas']);
        $stmt->execute([$res['concurso'], $dataFormatada, $dezenas]);
        $count++;
    }
    $pdo->commit();

    echo "✅ Sucesso! <strong>$count</strong> concursos da Mega-Sena importados.";
} catch (Exception $e) {
    if($pdo->inTransaction()) $pdo->rollBack();
    die("Erro: " . $e->getMessage());
}
?>