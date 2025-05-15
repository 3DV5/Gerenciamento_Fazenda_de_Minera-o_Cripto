<?php
session_start();
require_once 'config.php';

// OBTEM CONFIGS
function getConfig($chave, $padrao = '') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT valor FROM configuracoes WHERE chave = ?");
    $stmt->execute([$chave]);
    return $stmt->fetchColumn() ?? $padrao;
}

// ATUALIZA CONFIGS
function setConfig($chave, $valor) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO configuracoes (chave, valor) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
    $stmt->execute([$chave, $valor]);
}

// SIMULA OS DADOS
function simularDados($pdo) {
    // ATUALIZA OS RIGS
    $rigs = $pdo->query("SELECT * FROM rigs")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rigs as $rig) {
        $novaTemp = rand(60, 95);
        
        // DEFINE O LIMITE DE WATTS
        $limites = ($rig['status'] == 'ligado') 
            ? ['min' => 700, 'max' => 1500] 
            : ['min' => 50, 'max' => 100];
        
        $novoWatts = rand($limites['min'], $limites['max']);

        if ($rig['status'] == 'desligado') {
            $novaTemp = max(25, $novaTemp - rand(5, 15));
        }

        $stmt = $pdo->prepare("UPDATE rigs SET temperatura = ?, watts = ? WHERE id = ?");
        $stmt->execute([$novaTemp, $novoWatts, $rig['id']]);
    }

    // CALCULA O TOTAL DO HISTORICO
    $totalEnergia = 0;
    $totalTemperatura = 0;
    
    foreach ($rigs as $rig) {
        $totalEnergia += $rig['watts'];
        $totalTemperatura += $rig['temperatura'];
    }
    
    $temperaturaMedia = count($rigs) > 0 ? $totalTemperatura / count($rigs) : 0;

    // ATUALIZA O LUCRO
    $automacao = getConfig('automacao', 0);
    $lucro = ($automacao ? rand(1, 5) : rand(-2, 3));
    $stmt = $pdo->prepare("INSERT INTO historico_lucro (valor, data_registro) VALUES (?, NOW())");
    $stmt->execute([$lucro]);

    // ATUALIZA NO HISTORICO DO BANCO
    $stmt = $pdo->prepare("INSERT INTO historico_lucro (valor, data_registro, total_energia, temperatura_media) VALUES (?, NOW(), ?, ?)");
    $stmt->execute([$lucro, $totalEnergia, round($temperaturaMedia, 2)]);

    // MANTEM SÓ 10 REGISTROS
    $pdo->exec("DELETE FROM historico_lucro WHERE id <= (SELECT id FROM (SELECT id FROM historico_lucro ORDER BY id DESC LIMIT 1 OFFSET 10) AS temp)");
}

// PROCESSA AÇÃO
if (isset($_GET['acao'])) {
    switch ($_GET['acao']) {
        case 'toggle_rig':
            $id = intval($_GET['id']);
            $stmt = $pdo->prepare("UPDATE rigs SET status = IF(status = 'ligado', 'desligado', 'ligado') WHERE id = ?");
            $stmt->execute([$id]);
            break;

        case 'toggle_automacao':
            $novoValor = getConfig('automacao') ? 0 : 1;
            setConfig('automacao', $novoValor);
            break;
    }
}

// APLICA AS REGRAS DE AUTOMAÇÃO
if (getConfig('automacao')) {
    $rigs = $pdo->query("SELECT * FROM rigs")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rigs as $rig) {
        $novoStatus = $rig['status'];
        if ($rig['temperatura'] > 80) {
            $novoStatus = 'desligado';
        } elseif ($rig['temperatura'] < 70) {
            $novoStatus = 'ligado';
        }
        
        if ($novoStatus != $rig['status']) {
            $stmt = $pdo->prepare("UPDATE rigs SET status = ? WHERE id = ?");
            $stmt->execute([$novoStatus, $rig['id']]);
        }
    }
}

simularDados($pdo);


// OBTEM DADOS EXIBIDOS
$rigs = $pdo->query("SELECT * FROM rigs")->fetchAll(PDO::FETCH_ASSOC);
$historico = $pdo->query("SELECT valor, data_registro, total_energia, temperatura_media 
                         FROM historico_lucro 
                         ORDER BY id DESC 
                         LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
$lucroTotal = array_sum(array_column($historico, 'valor'));
$automacao = getConfig('automacao', 1);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CriptoFarm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="styles/index.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="home.php">CriptoFarm</a>
            <div class="navbar-nav">
                <a class="nav-link" href="home.php">Home</a>
                <a class="nav-link" href="?logout=1">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- PAINEL -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>🖥️ Painel de Controle</h1>
            <div class="d-flex gap-2">
                <a href="?reset=1" class="btn btn-outline-warning btn-sm">Resetar Sistema</a>
            </div>
        </div>

        <!-- CONTROLE LIGA E DESLIGA -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>⚙️ Controle Automático</h5>
                        <button onclick="window.location.href='?acao=toggle_automacao'"
                            class="btn <?= $automacao ? 'btn-success' : 'btn-danger' ?> w-100">
                            Automação: <?= $automacao ? 'LIGADA' : 'DESLIGADA' ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">💰 Lucro Total</h5>
                        <h2 class="text-center">$ <?= number_format($lucroTotal, 2) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD DO RIG -->
        <h5 class="mb-3">Gerenciamento de Equipamentos</h5>
        <div class="rigs-container">
            <?php foreach ($rigs as $rig): ?>
                <div class="rig-card card <?= $rig['status'] == 'ligado' ? 'border-success' : 'border-danger' ?>">
                    <div class="card-body">
                        <h5 class="card-title">
                            <span class="status-indicator bg-<?= $rig['status'] == 'ligado' ? 'success' : 'danger' ?>"></span>
                            <?= $rig['nome'] ?>
                        </h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="
                            ">
                                <div>
                                    <small>🌡️ Temperatura</small>
                                    <h4><?= $rig['temperatura'] ?>°C</h4>
                                </div>
                                <div>
                                    <small>⚡ Energia</small>
                                    <h4><?= $rig['watts'] ?>W</h4>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                <button onclick="window.location.href='?acao=toggle_rig&id=<?= $rig['id'] ?>'"
                                    class="btn btn-<?= $rig['status'] == 'ligado' ? 'danger' : 'success' ?> mb-2">
                                    <?= $rig['status'] == 'ligado' ? 'Desligar' : 'Ligar' ?>
                                </button>
                                <span class="badge bg-<?= $rig['status'] == 'ligado' ? 'success' : 'danger' ?>">
                                    <?= strtoupper($rig['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- GRAFICO -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">📈 Desempenho Financeiro</h5>
                        <canvas id="lucroChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HISTORICO DO LUCRO -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">📅 Histórico de Atualizações</h5>
                        <div class="historico-lucro">
                            <?php foreach ($historico as $registro): ?>
                                <div class="historico-item">
                                    <div class="historico-data">
                                        <?= date('d/m/Y H:i:s', strtotime($registro['data_registro'])) ?>
                                    </div>
                                    <div class="historico-valor <?= $registro['valor'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        $ <?= number_format($registro['valor'], 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RODAPÉ -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2025 CriptoFarm - Sistema de Gerenciamento de Fazendas de Criptos</p>
        </div>
    </footer>

    <script>
        setInterval(() => window.location.reload(), 8000);

        const ctx = document.getElementById('lucroChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_map(function($item) { 
                return date('H:i', strtotime($item['data_registro'])); 
            }, $historico)) ?>,
            datasets: [
                {
                    label: 'Lucro (USD)',
                    data: <?= json_encode(array_column($historico, 'valor')) ?>,
                    borderColor: '#00b4d8',
                    backgroundColor: 'rgba(0, 180, 216, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y',
                    fill: true
                },
                {
                    label: 'Consumo Energético (W)',
                    data: <?= json_encode(array_column($historico, 'total_energia')) ?>,
                    borderColor: '#ff4d6d',
                    backgroundColor: 'rgba(255, 77, 109, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                },
                {
                    label: 'Temperatura Média (°C)',
                    data: <?= json_encode(array_column($historico, 'temperatura_media')) ?>,
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y2'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: '#fff' }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                switch(context.datasetIndex) {
                                    case 0: 
                                        label += new Intl.NumberFormat('en-US', { 
                                            style: 'currency', 
                                            currency: 'USD' 
                                        }).format(context.parsed.y);
                                        break;
                                    case 1:
                                        label += context.parsed.y + ' W';
                                        break;
                                    case 2:
                                        label += context.parsed.y.toFixed(1) + '°C';
                                        break;
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { text: 'Lucro (USD)', display: true, color: '#fff' },
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { text: 'Energia (W)', display: true, color: '#fff' },
                    ticks: { color: '#fff' },
                    grid: { display: false }
                },
                y2: {
                    type: 'linear',
                    display: false,
                    position: 'right',
                    title: { text: 'Temp (°C)', display: true, color: '#fff' },
                    ticks: { color: '#fff' },
                    grid: { display: false }
                },
                x: {
                    ticks: { color: 'rgba(255, 255, 255, 0.7)' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' }
                }
            }
        }
    });
    </script>
</body>
</html>