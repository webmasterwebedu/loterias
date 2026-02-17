<?php
include 'config.php';

// Função rápida para buscar o último concurso de qualquer tabela
function getUltimo($pdo, $tabela) {
    $stmt = $pdo->query("SELECT * FROM $tabela ORDER BY concurso DESC LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$loto = getUltimo($pdo, 'resultados_lotofacil');
$mega = getUltimo($pdo, 'resultados_megasena');
$quina = getUltimo($pdo, 'resultados_quina');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferir Resultados da Lotofácil, Mega-Sena e Quina Online</title>

    <meta name="description" content="Consulte todos os resultados da Lotofácil, Mega-Sena e Quina. Digite seus números e descubra se já ganhou em algum concurso. Conferência online grátis.">

    <meta name="keywords" content="resultados lotofácil, resultados mega sena, resultados quina, conferir lotofácil, conferir mega sena, conferir quina, verificar números da loteria, loterias caixa resultados, ver se já ganhei na lotofácil">

    <meta name="robots" content="index, follow">

    <meta name="author" content="HG WebEdu">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="canonical" href="https://hgwebedu.unaux.com/">

    <!-- Open Graph (Facebook, WhatsApp, etc) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Conferir Resultados da Lotofácil, Mega-Sena e Quina">
    <meta property="og:description" content="Digite seus números e descubra se já ganhou na Lotofácil, Mega-Sena ou Quina. Veja todos os resultados históricos.">
    <meta property="og:url" content="https://hgwebedu.unaux.com/">
    <meta property="og:site_name" content="Resultados de Loterias">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Resultados da Lotofácil, Mega-Sena e Quina">
    <meta name="twitter:description" content="Confira resultados históricos da Lotofácil, Mega-Sena e Quina e veja se seus números já foram sorteados.">


    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; margin: 0; padding: 15px; }
        .dashboard { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 1100px; margin: 0 auto; }
        .card-home { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 8px solid #ddd; }
        .card-loto { border-color: #1a73e8; }
        .card-mega { border-color: #27ae60; }
        .card-quina { border-color: #8e44ad; }
        .dezenas { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 15px; justify-content: center; }
        .bola { background: #eee; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; font-size: 14px; }
        .btn-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; padding: 10px; background: #f8f9fa; border-radius: 8px; color: #333; font-weight: bold; border: 1px solid #ddd; }
        h3 { margin: 0; text-align: center; }
    </style>
</head>
<body>

    <?php include 'menu.php'; ?>

    <h1 style="text-align:center; color:#2c3e50;">Resultados Recentes</h1>

    <div class="dashboard">
        <div class="card-home card-loto">
            <h3>🍀 Lotofácil</h3>
            <p style="text-align:center; color:#666;">Concurso: <?php echo $loto['concurso']; ?></p>
            <div class="dezenas">
                <?php foreach(explode(',', $loto['dezenas']) as $d) echo "<div class='bola' style='background:#e8f0fe; color:#1a73e8;'>$d</div>"; ?>
            </div>
            <a href="conferir.php" class="btn-link">Simular Jogos</a>
        </div>

        <div class="card-home card-mega">
            <h3>💰 Mega-Sena</h3>
            <p style="text-align:center; color:#666;">Concurso: <?php echo $mega['concurso']; ?></p>
            <div class="dezenas">
                <?php foreach(explode(',', $mega['dezenas']) as $d) echo "<div class='bola' style='background:#e6f4ea; color:#1e7e34;'>$d</div>"; ?>
            </div>
            <a href="conferir_mega.php" class="btn-link">Simular Jogos</a>
        </div>

        <div class="card-home card-quina">
            <h3>💜 Quina</h3>
            <p style="text-align:center; color:#666;">Concurso: <?php echo $quina['concurso']; ?></p>
            <div class="dezenas">
                <?php foreach(explode(',', $quina['dezenas']) as $d) echo "<div class='bola' style='background:#f3e5f5; color:#7b1fa2;'>$d</div>"; ?>
            </div>
            <a href="conferir_quina.php" class="btn-link">Simular Jogos</a>
        </div>
    </div>

    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- lateral-showeb -->
<ins class="adsbygoogle"
     style="display:inline-block;width:300px;height:600px"
     data-ad-client="ca-pub-7805341505350601"
     data-ad-slot="6509207978"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
    
</body>
</html>