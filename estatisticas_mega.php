<?php
include 'config.php';

try {
    $stmt = $pdo->query("SELECT dezenas FROM resultados_megasena");
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = count($resultados);

    // Inicializa o array com TODOS os 60 números para não pular ninguém
    $frequencia = array_fill(1, 60, 0);

    foreach ($resultados as $res) {
        $dezenas = explode(',', $res['dezenas']);
        foreach ($dezenas as $num) {
            $n = (int)$num;
            if ($n >= 1 && $n <= 60) {
                $frequencia[$n]++;
            }
        }
    }

    // Identifica a maior e a menor frequência para as cores
    $max_frequencia = max($frequencia);
    $min_frequencia = min($frequencia);
    
    // ACHAR A MAIS SORTEADA (Para o painel de destaque)
    arsort($frequencia); 
    $dezena_campea = key($frequencia);
    $vezes_campea = current($frequencia);
    
    // Reorganiza por ordem numérica (01 a 60) para o gráfico
    ksort($frequencia);

    $labels = [];
    $valores = [];
    $cores = [];

    foreach ($frequencia as $num => $qtd) {
        $labels[] = str_pad($num, 2, "0", STR_PAD_LEFT);
        $valores[] = $qtd;
        
        // Lógica de Cores: Verde para os top, Cinza para o resto, Vermelho para os piores
        if ($qtd == $max_frequencia) {
            $cores[] = '#27ae60'; // Verde destaque
        } elseif ($qtd <= ($min_frequencia + 5)) {
            $cores[] = '#e74c3c'; // Vermelho (frios)
        } else {
            $cores[] = '#3498db'; // Azul padrão
        }
    }

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas Mega - HGWebEdu</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; padding: 15px; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .topo-stats { display: flex; justify-content: space-around; margin-bottom: 30px; gap: 15px; }
        .stat-card { flex: 1; padding: 20px; border-radius: 12px; text-align: center; color: white; }
        .chart-box { height: 400px; margin-top: 20px; }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 5px; }
    </style>
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="container">
        <h2>Ranking de Dezenas Mega-Sena</h2>
        <p style="text-align:center; color:#7f8c8d; margin-bottom:25px;">Análise de <?php echo $total; ?> concursos</p>

        <div class="topo-stats">
            <div class="stat-card" style="background: #27ae60;">
                <small>🏆 MAIS SORTEADA</small>
                <div style="font-size: 32px; font-weight: bold;">Dezena <?php echo str_pad($dezena_campea, 2, "0", STR_PAD_LEFT); ?></div>
                <small>Apareceu <?php echo $vezes_campea; ?> vezes</small>
            </div>
        </div>

        <div class="chart-box">
            <canvas id="megaChart"></canvas>
        </div>
        
        <p style="text-align:center; font-size:12px; color:#999; margin-top:10px;">
            <span style="color:#27ae60">■</span> Mais Sorteada | <span style="color:#3498db">■</span> Frequência Normal | <span style="color:#e74c3c">■</span> Menos Sorteadas
        </p>
    </div>

    <script>
        const ctx = document.getElementById('megaChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Vezes Sorteada',
                    data: <?php echo json_encode($valores); ?>,
                    backgroundColor: <?php echo json_encode($cores); ?>,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { font: { size: 9 }, autoSkip: false } },
                    y: { beginAtZero: false }
                }
            }
        });
    </script>
</body>
</html>