<?php
include 'config.php';

function atualizar_inteligente($url, $tabela, $nome, $pdo) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/121.0.0.0');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $json = curl_exec($ch);
    curl_close($ch);

    $lista = json_decode($json, true);
    // Se não for lista, coloca em uma para o foreach
    if (isset($lista['concurso'])) { $lista = [$lista]; }

    if ($lista && is_array($lista)) {
        $novos = 0;
        foreach ($lista as $res) {
            if (!isset($res['concurso'])) continue;

            // TRATAMENTO DE DATA: Garante o formato AAAA-MM-DD para o MySQL
            $dataOriginal = $res['data']; // Ex: 14/02/2026
            $d = explode('/', $dataOriginal);
            $dataSql = (count($d) == 3) ? "{$d[2]}-{$d[1]}-{$d[0]}" : date('Y-m-d');

            $dezenas = is_array($res['dezenas']) ? implode(',', $res['dezenas']) : $res['dezenas'];

            // INSERT IGNORE: Se o concurso já existir, ele pula.
            $sql = "INSERT IGNORE INTO $tabela (concurso, data_sorteio, dezenas) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$res['concurso'], $dataSql, $dezenas]);

            if ($stmt->rowCount() > 0) {
                $novos++;
            }
        }
        return "<strong>$nome:</strong> " . ($novos > 0 ? "<span style='color:green;'>$novos novos inseridos!</span>" : "Sem novidades na API.");
    }
    return "<strong>$nome:</strong> Erro na API.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sincronização HG</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; padding: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .item { padding: 10px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
<div class="card">
    <h2 style="color:#FF6B00">🔄 Sincronização HG</h2>
    <div class="item"><?php echo atualizar_inteligente("https://loteriascaixa-api.herokuapp.com/api/lotofacil/", "resultados_lotofacil", "Lotofácil", $pdo); ?></div>
    <div class="item"><?php echo atualizar_inteligente("https://loteriascaixa-api.herokuapp.com/api/megasena/", "resultados_megasena", "Mega-Sena", $pdo); ?></div>
    <div class="item"><?php echo atualizar_inteligente("https://loteriascaixa-api.herokuapp.com/api/quina/", "resultados_quina", "Quina", $pdo); ?></div>
    <br><a href="index.php" style="color:#FF6B00">Voltar</a>
</div>
</body>
</html>