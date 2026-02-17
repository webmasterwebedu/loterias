<?php
include 'config.php';

try {
    // Busca os dados da Quina
    $stmt = $pdo->query("SELECT dezenas FROM resultados_quina");
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_concursos = count($resultados);

    // Inicializa o array de 1 a 80
    $frequencia = array_fill(1, 80, 0);
    foreach ($resultados as $res) {
        $dezenas = explode(',', $res['dezenas']);
        foreach ($dezenas as $num) {
            $n = (int)$num;
            if ($n >= 1 && $n <= 80) $frequencia[$n]++;
        }
    }

    // Identifica o máximo para calcular a cor proporcional
    $max_freq = max($frequencia);
    
    // Pega os top 3 para o destaque
    arsort($frequencia);
    $top_dezenas = array_slice($frequencia, 0, 3, true);

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas Quina - HGWebEdu</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; padding: 15px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        /* Grid da Quina (10 colunas) */
        .grid-quina { 
            display: grid; 
            grid-template-columns: repeat(10, 1fr); 
            gap: 6px; 
            margin: 20px 0;
        }
        
        .dezena-box { 
            aspect-ratio: 1 / 1; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
            border-radius: 6px; 
            font-size: 14px; 
            font-weight: bold; 
            border: 1px solid #ddd;
            transition: transform 0.2s;
        }
        
        .dezena-box:hover { transform: scale(1.1); z-index: 10; cursor: help; }
        .dezena-box small { font-size: 9px; opacity: 0.8; }

        .destaques { display: flex; gap: 10px; margin-bottom: 20px; }
        .card-destaque { flex: 1; background: #8e44ad; color: white; padding: 15px; border-radius: 10px; text-align: center; }
        
        @media (max-width: 600px) {
            .grid-quina { grid-template-columns: repeat(5, 1fr); }
        }
    </style>
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="container">
        <h2 style="color: #8e44ad; text-align: center;">Mapa de Calor - Quina</h2>
        <p style="text-align: center; color: #666;">Frequência baseada em <?php echo $total_concursos; ?> concursos</p>

        <div class="destaques">
            <?php foreach($top_dezenas as $num => $qtd): ?>
            <div class="card-destaque">
                <small>🔥 TOP <?php echo str_pad($num, 2, "0", STR_PAD_LEFT); ?></small>
                <div style="font-size: 20px; font-weight: bold;"><?php echo $qtd; ?>x</div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="grid-quina">
            <?php
            // Volta a ordem para 1 a 80 para desenhar a cartela
            ksort($frequencia);
            foreach ($frequencia as $num => $qtd) {
                // Cálculo de cor: quanto mais perto do máximo, mais roxo escuro
                $opacidade = ($qtd / $max_freq);
                // Escala de Roxo: de um lilás claro para um roxo forte
                $bg = "rgba(142, 68, 173, $opacidade)";
                $cor_texto = ($opacidade > 0.5) ? "white" : "#333";
                
                echo "<div class='dezena-box' style='background: $bg; color: $cor_texto;' title='Sorteada $qtd vezes'>";
                echo str_pad($num, 2, "0", STR_PAD_LEFT);
                echo "<small>$qtd</small>";
                echo "</div>";
            }
            ?>
        </div>

        <div style="background: #f9f9f9; padding: 10px; border-radius: 8px; font-size: 12px; color: #777; text-align: center;">
            💡 <strong>Dica:</strong> Os quadrados mais escuros representam os números que mais saíram historicamente.
        </div>
    </div>

</body>
</html>