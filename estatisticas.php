<?php
// 1. Conexão e Menu
include 'config.php';

try {
    // 2. Buscar todas as dezenas de todos os concursos
    $stmt = $pdo->query("SELECT dezenas FROM resultados_lotofacil");
    $todos_resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_concursos = count($todos_resultados);
    
    // 3. Contabilizar a frequência de cada número (01 a 25)
    $frequencia = array_fill(1, 25, 0); // Cria um array de 1 a 25 zerado

    foreach ($todos_resultados as $res) {
        $dezenas = explode(',', $res['dezenas']);
        foreach ($dezenas as $num) {
            $frequencia[(int)$num]++;
        }
    }

    // Preparar dados para o gráfico (Labels e Valores)
    $labels = [];
    $valores = [];
    for ($i = 1; $i <= 25; $i++) {
        $labels[] = str_pad($i, 2, "0", STR_PAD_LEFT);
        $valores[] = $frequencia[$i];
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
    <title>Estatísticas Lotofácil - HGWebEdu</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #1a73e8; text-align: center; }
        .stats-info { text-align: center; color: #666; margin-bottom: 30px; }
        .chart-container { position: relative; height: 400px; width: 100%; }
    </style>
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="container">
        <h2>Frequência das Dezenas</h2>
        <p class="stats-info">Análise baseada em <strong><?php echo $total_concursos; ?></strong> concursos cadastrados.</p>

        <div class="chart-container">
            <canvas id="graficoFrequencia"></canvas>
        </div>

        <div style="margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="background: #e6fffa; padding: 15px; border-radius: 8px; border-left: 5px solid #38b2ac;">
                <h4 style="margin:0">🔥 Números Quentes</h4>
                <small>Os que mais saíram</small>
                <?php 
                    arsort($frequencia); 
                    $top5 = array_slice($frequencia, 0, 5, true);
                    echo "<ul>";
                    foreach($top5 as $num => $qtd) echo "<li>Dezena <strong>".str_pad($num, 2, "0", STR_PAD_LEFT)."</strong> ($qtd vezes)</li>";
                    echo "</ul>";
                ?>
            </div>
            <div style="background: #fff5f5; padding: 15px; border-radius: 8px; border-left: 5px solid #e53e3e;">
                <h4 style="margin:0">❄️ Números Frios</h4>
                <small>Os que menos saíram</small>
                <?php 
                    asort($frequencia); 
                    $bottom5 = array_slice($frequencia, 0, 5, true);
                    echo "<ul>";
                    foreach($bottom5 as $num => $qtd) echo "<li>Dezena <strong>".str_pad($num, 2, "0", STR_PAD_LEFT)."</strong> ($qtd vezes)</li>";
                    echo "</ul>";
                ?>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('graficoFrequencia').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Vezes Sorteada',
                    data: <?php echo json_encode($valores); ?>,
                    backgroundColor: '#1a73e8',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    </script>
</body>
</html>