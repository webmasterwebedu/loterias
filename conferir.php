<?php
// 1. Conexão e lógica de banco
include 'config.php';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    
    // Pegamos todos os resultados de uma vez para a simulação
    $stmt = $pdo->query("SELECT concurso, dezenas FROM resultados_lotofacil ORDER BY concurso DESC");
    $todos_resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_concursos = count($todos_resultados);
    
    // Definimos o último concurso com segurança para não dar o erro do seu chefe
    $ultimo_concurso_num = ($total_concursos > 0) ? $todos_resultados[0]['concurso'] : "Nenhum sorteio no banco";

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['meu_jogo'])) {
    $meu_jogo = explode(',', $_POST['meu_jogo']);
    $premios = [11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0];
    $historico_ganhos = "";
    $count_ganhos = 0;

    foreach ($todos_resultados as $res) {
        $dezenas_sorteadas = explode(',', $res['dezenas']);
        $acertos = count(array_intersect($meu_jogo, $dezenas_sorteadas));

        if ($acertos >= 11) {
            $premios[$acertos]++;
            if ($count_ganhos < 5) {
                $historico_ganhos .= "Conc. {$res['concurso']} ({$acertos} pts) | ";
                $count_ganhos++;
            }
        }
    }

    $mensagem = "<div class='result-box'>
                    <h3 style='margin:0 0 10px 0'>Simulação em $total_concursos concursos:</h3>
                    <ul style='list-style:none; padding:0; margin:0;'>
                        <li>🏆 15 Pontos: <strong>{$premios[15]}</strong></li>
                        <li>💰 14 Pontos: <strong>{$premios[14]}</strong></li>
                        <li>✅ 13 Pontos: <strong>{$premios[13]}</strong></li>
                        <li>✅ 12 Pontos: <strong>{$premios[12]}</strong></li>
                        <li>✅ 11 Pontos: <strong>{$premios[11]}</strong></li>
                    </ul>
                    <p style='font-size:11px; margin-top:10px; color:#666;'><strong>Últimos acertos:</strong> $historico_ganhos</p>
                 </div>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Lotofácil - HGWebEdu</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 450px; margin: 20px auto; background: #f0f2f5; padding: 15px; }
        .card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid #e1e4e8; }
        h2 { color: #1a73e8; text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #5f6368; font-size: 0.9em; margin-bottom: 20px; }
        input { width: 100%; padding: 15px; font-size: 22px; border: 2px solid #dfe1e5; border-radius: 10px; box-sizing: border-box; text-align: center; outline: none; transition: border 0.3s; }
        input:focus { border-color: #1a73e8; }
        button { width: 100%; padding: 15px; margin-top: 15px; border: none; border-radius: 10px; font-weight: bold; font-size: 16px; cursor: pointer; color: white; transition: 0.3s; }
        .contador { display: block; text-align: center; margin: 10px 0; font-weight: bold; }
        .invalido { color: #d93025; }
        .valido { color: #188038; }
        .result-box { background: #fff; border: 2px solid #1a73e8; padding: 15px; margin-bottom: 20px; border-radius: 10px; }
        li { padding: 5px 0; border-bottom: 1px solid #eee; }
        li:last-child { border: none; }
    </style>
</head>
<body>
<?php include 'menu.php'; ?>
    <div class="card">
        <h2>Conferir Loto Fácil</h2>
        <p class="subtitle">Último sorteio processado: <strong><?php echo $ultimo_concurso_num; ?></strong></p>

        <?php echo $mensagem; ?>

        <form method="post" id="lotoForm">
            <input type="text" name="meu_jogo" id="meu_jogo" placeholder="01020304..." maxlength="44">
            <span id="display-contador" class="contador invalido">Digite 15 dezenas</span>
            <button type="submit" id="btn-conferir" style="background:#dadce0" disabled>SIMULAR EM TODO O HISTÓRICO</button>
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

        const dezenas = v.split(',').filter(d => d.length === 2);
        const unique = [...new Set(dezenas)]; 
        const qtd = unique.length;
        
        contador.innerText = `Dezenas: ${qtd} / 15`;

        if (qtd === 15) {
            contador.className = 'contador valido';
            botao.disabled = false;
            botao.style.background = "#1a73e8";
        } else {
            contador.className = 'contador invalido';
            botao.disabled = true;
            botao.style.background = "#dadce0";
        }
    });
    </script>
</body>
</html>