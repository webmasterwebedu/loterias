<?php
include 'config.php';

$stmt = $pdo->query("SELECT concurso, dezenas FROM resultados_quina ORDER BY concurso DESC");
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = count($todos);
$ultimo = ($total > 0) ? $todos[0]['concurso'] : "Nenhum";

$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['meu_jogo'])) {
    $meu_jogo = explode(',', $_POST['meu_jogo']);
    $premios = [3 => 0, 4 => 0, 5 => 0];

    foreach ($todos as $res) {
        $acertos = count(array_intersect($meu_jogo, explode(',', $res['dezenas'])));
        if ($acertos >= 3) $premios[$acertos]++;
    }

    $mensagem = "<div style='border:2px solid #8e44ad; padding:15px; border-radius:10px; background:#f5eef8;'>
                    <h3 style='margin:0; color:#8e44ad;'>Simulação Quina:</h3>
                    <p>🏆 5 ACERTOS: {$premios[5]} <br /><br /> 💰 4 ACERTOS: {$premios[4]} <br /><br /> ✅ 3 ACERTOS: {$premios[3]}</p>
                 </div>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador Quina - HGWebEdu</title>
    <style>
        body { font-family: sans-serif; max-width: 450px; margin: 20px auto; background: #f4f7f6; padding: 15px; }
        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 15px; font-size: 24px; border: 2px solid #ddd; border-radius: 10px; box-sizing: border-box; text-align: center; }
        button { width: 100%; padding: 15px; margin-top: 15px; border: none; border-radius: 10px; font-weight: bold; background: #8e44ad; color: white; cursor: pointer; }
        button:disabled { background: #ccc; }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    <div class="card">
        <h2 style="color:#8e44ad; text-align:center;">Simulador Quina</h2>
        <p style="text-align:center; font-size:12px; color:#666;">Concursos no BD: <?php echo $total; ?></p>
        <?php echo $mensagem; ?>
        <form method="post">
            <input type="text" name="meu_jogo" id="meu_jogo" placeholder="051532..." maxlength="14">
            <span id="contador" style="display:block; text-align:center; margin-top:10px; color:red;">Digite 5 dezenas</span>
            <button type="submit" id="btn" disabled>SIMULAR NA QUINA</button>
        </form>
    </div>

    <script>
    const input = document.getElementById('meu_jogo');
    const btn = document.getElementById('btn');
    const cont = document.getElementById('contador');

    input.addEventListener('input', function() {
        let v = this.value.replace(/\D/g, '');
        if (v.length > 2) v = v.match(/.{1,2}/g).join(',');
        this.value = v;

        const dezenas = v.split(',').filter(d => d.length === 2 && parseInt(d) <= 80 && parseInt(d) > 0);
        const unique = [...new Set(dezenas)];
        
        cont.innerText = `Válidos: ${unique.length} / 5`;
        if (unique.length === 5) {
            btn.disabled = false;
            cont.style.color = "green";
        } else {
            btn.disabled = true;
            cont.style.color = "red";
        }
    });
    </script>
</body>
</html>