<?php
// 1. Conexão e lógica de banco
include 'config.php';

try {
    // Busca o total de concursos da Mega para a simulação
    $stmt = $pdo->query("SELECT concurso, dezenas FROM resultados_megasena ORDER BY concurso DESC");
    $todos_resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_concursos = count($todos_resultados);
    $ultimo_concurso = ($total_concursos > 0) ? $todos_resultados[0]['concurso'] : "Nenhum";
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['meu_jogo'])) {
    $meu_jogo = explode(',', $_POST['meu_jogo']);
    $premios = [4 => 0, 5 => 0, 6 => 0]; // Quadra, Quina e Sena
    $historico_ganhos = "";
    $count_ganhos = 0;

    foreach ($todos_resultados as $res) {
        $dezenas_sorteadas = explode(',', $res['dezenas']);
        $acertos = count(array_intersect($meu_jogo, $dezenas_sorteadas));

        if ($acertos >= 4) {
            $premios[$acertos]++;
            if ($count_ganhos < 5) {
                $historico_ganhos .= "Conc. {$res['concurso']} ({$acertos} pts) | ";
                $count_ganhos++;
            }
        }
    }

    $mensagem = "<div class='result-box' style='border-color: #f1c40f;'>
                    <h3 style='margin:0 0 10px 0; color:#f39c12;'>Simulação Mega-Sena:</h3>
                    <ul style='list-style:none; padding:0; margin:0;'>
                        <li>🌟 <strong>6 ACERTOS (SENA):</strong> {$premios[6]} vezes</li>
                        <li>💰 <strong>5 ACERTOS (QUINA):</strong> {$premios[5]} vezes</li>
                        <li>✅ <strong>4 ACERTOS (QUADRA):</strong> {$premios[4]} vezes</li>
                    </ul>
                    <p style='font-size:11px; margin-top:10px; color:#666;'><strong>Últimos ganhos:</strong> $historico_ganhos</p>
                 </div>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador Mega-Sena - HGWebEdu</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 450px; margin: 20px auto; background: #f4f7f6; padding: 15px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #7f8c8d; font-size: 0.85em; margin-bottom: 20px; }
        input { width: 100%; padding: 15px; font-size: 24px; border: 2px solid #bdc3c7; border-radius: 10px; box-sizing: border-box; text-align: center; font-weight: bold; color: #2c3e50; }
        button { width: 100%; padding: 15px; margin-top: 15px; border: none; border-radius: 10px; font-weight: bold; font-size: 16px; cursor: pointer; color: white; background: #ccc; }
        .contador { display: block; text-align: center; margin: 10px 0; font-weight: bold; }
        .result-box { background: #fffdf2; border: 2px solid #f1c40f; padding: 15px; margin-bottom: 20px; border-radius: 10px; }
        li { padding: 8px 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>

    <?php include 'menu.php'; ?>

    <div class="card">
        <h2>Conferir Mega-Sena</h2>
        <p class="subtitle">Analisando <strong><?php echo $total_concursos; ?></strong> sorteios (Último: <?php echo $ultimo_concurso; ?>)</p>

        <?php echo $mensagem; ?>

        <form method="post" id="megaForm">
            <input type="text" name="meu_jogo" id="meu_jogo" placeholder="051224..." maxlength="17">
            <span id="display-contador" class="contador" style="color:#e74c3c;">Digite 6 dezenas</span>
            <button type="submit" id="btn-conferir" disabled>SIMULAR NA MEGA-SENA</button>
        </form>
    </div>

    <script>
    const input = document.getElementById('meu_jogo');
    const contador = document.getElementById('display-contador');
    const botao = document.getElementById('btn-conferir');

    input.addEventListener('input', function(e) {
        let v = this.value.replace(/\D/g, ''); 
        if (v.length > 2) v = v.match(/.{1,2}/g).join(',');
        this.value = v;

        const dezenas = v.split(',').filter(d => d.length === 2 && parseInt(d) <= 60 && parseInt(d) > 0);
        const unique = [...new Set(dezenas)]; 
        const qtd = unique.length;
        
        contador.innerText = `Dezenas válidas: ${qtd} / 6`;

        if (qtd === 6) {
            contador.style.color = "#27ae60";
            botao.disabled = false;
            botao.style.background = "#27ae60";
        } else {
            contador.style.color = "#e74c3c";
            botao.disabled = true;
            botao.style.background = "#ccc";
        }
    });
    </script>
</body>
</html>