<?php
    
    print"Pagina / função  parada por enquanto.";
    die;
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(600); // Aumentei para 10 minutos para garantir

// 1. INCLUDE DA CONEXÃO (Já que criamos o config.php)
include 'config.php';

try {
    echo "<h2>Iniciando Processo...</h2>";
    echo "Conectado ao banco. Aguarde, buscando dados da API (isso pode demorar)...<br>";

    // 2. BUSCA DOS DADOS
    $url = "https://loteriascaixa-api.herokuapp.com/api/lotofacil/";
    
    // Usando uma alternativa mais forte ao file_get_contents
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $json = curl_exec($ch);
    curl_close($ch);

    $resultados = json_decode($json, true);

    if (!$resultados) {
        die("<b style='color:red'>Erro:</b> Não foi possível ler os dados da API. Tente ativar o 'allow_url_fopen' no cPanel ou contate o suporte da hospedagem.");
    }

    // 3. INSERÇÃO NO BANCO
    $stmt = $pdo->prepare("INSERT IGNORE INTO resultados_lotofacil (concurso, data_sorteio, dezenas) VALUES (?, ?, ?)");

    $count = 0;
    $pdo->beginTransaction(); // Deixa a inserção muito mais rápida

    foreach ($resultados as $res) {
        if(!isset($res['data'])) continue;

        $dataArray = explode('/', $res['data']);
        if(count($dataArray) == 3) {
            $dataFormatada = $dataArray[2] . '-' . $dataArray[1] . '-' . $dataArray[0];
            $dezenas = implode(',', $res['dezenas']);
            
            $stmt->execute([$res['concurso'], $dataFormatada, $dezenas]);
            $count++;
        }
    }

    $pdo->commit();

    echo "<div style='background:#d4edda; color:#155724; padding:20px; border-radius:10px; margin-top:20px;'>";
    echo "<h3>✅ IMPORTAÇÃO CONCLUÍDA!</h3>";
    echo "Total de concursos processados: <strong>$count</strong>";
    echo "<br><br><a href='conferir.php' style='display:inline-block; padding:10px 20px; background:#155724; color:white; text-decoration:none; border-radius:5px;'>VOLTAR PARA O SIMULADOR</a>";
    echo "</div>";

} catch (Exception $e) {
    if($pdo->inTransaction()) $pdo->rollBack();
    die("<b style='color:red'>Erro Fatal:</b> " . $e->getMessage());
}
?>