<?php
session_start();

// Inicializar ou resetar dados da sessão
if (!isset($_SESSION['mineracao']) || isset($_GET['reset'])) {
    $_SESSION['mineracao'] = [
        'rigs' => [
            ['id' => 1, 'nome' => 'Rig GPU #1', 'status' => 'ligado', 'temp' => 65, 'watts' => 800],
            ['id' => 2, 'nome' => 'Rig GPU #2', 'status' => 'ligado', 'temp' => 70, 'watts' => 850],
            ['id' => 3, 'nome' => 'Rig ASIC #1', 'status' => 'ligado', 'temp' => 75, 'watts' => 1200]
        ],
        'automacao' => true,
        'lucro' => 0,
        'historico_lucro' => []
    ];
}

// Atualizar dados simulados
function simularDados() {
    foreach ($_SESSION['mineracao']['rigs'] as &$rig) {
        // Gerar dados aleatórios realistas
        $rig['temp'] = rand(60, 95);
        $rig['watts'] = rand($rig['status'] == 'ligado' ? 700 : 50, $rig['status'] == 'ligado' ? 1500 : 100);
        
        // Simular efeito de desligamento
        if ($rig['status'] == 'desligado') {
            $rig['temp'] = max(25, $rig['temp'] - rand(5, 15));
        }
    }
    
    // Atualizar lucro (valor randômico com tendência positiva)
    $_SESSION['mineracao']['lucro'] += ($_SESSION['mineracao']['automacao'] ? rand(1, 5) : rand(-2, 3));
    array_push($_SESSION['mineracao']['historico_lucro'], $_SESSION['mineracao']['lucro']);
    
    // Manter histórico de até 10 pontos
    if (count($_SESSION['mineracao']['historico_lucro']) > 10) {
        array_shift($_SESSION['mineracao']['historico_lucro']);
    }
}

// Processar ações do usuário
if (isset($_GET['acao'])) {
    switch ($_GET['acao']) {
        case 'toggle_rig':
            $id = intval($_GET['id']);
            foreach ($_SESSION['mineracao']['rigs'] as &$rig) {
                if ($rig['id'] == $id) {
                    $rig['status'] = ($rig['status'] == 'ligado') ? 'desligado' : 'ligado';
                }
            }
            break;
            
        case 'toggle_automacao':
            $_SESSION['mineracao']['automacao'] = !$_SESSION['mineracao']['automacao'];
            break;
    }
}

// Aplicar regras de automação
if ($_SESSION['mineracao']['automacao']) {
    foreach ($_SESSION['mineracao']['rigs'] as &$rig) {
        if ($rig['temp'] > 80) {
            $rig['status'] = 'desligado';
        } elseif ($rig['temp'] < 70) {
            $rig['status'] = 'ligado';
        }
    }
}

simularDados();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mining Farm Simulator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .rig-card {
            transition: all 0.3s ease;
        }
        .rig-card:hover {
            transform: scale(1.02);
        }
        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-dark text-light">
    <div class="container py-4">
        <!-- Cabeçalho -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>🖥️ Mining Farm Simulator</h1>
            <a href="?reset=1" class="btn btn-outline-warning btn-sm">Resetar Sistema</a>
        </div>

        <!-- Controles -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title">⚙️ Controle Automático</h5>
                        <button onclick="window.location.href='?acao=toggle_automacao'" 
                                class="btn <?= $_SESSION['mineracao']['automacao'] ? 'btn-success' : 'btn-danger' ?> w-100">
                            Automação: <?= $_SESSION['mineracao']['automacao'] ? 'LIGADA' : 'DESLIGADA' ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <div class="card bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title">💰 Lucro Total</h5>
                        <h2 class="text-center">$ <?= number_format($_SESSION['mineracao']['lucro'], 2) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards dos Rigs -->
        <div class="row">
            <?php foreach ($_SESSION['mineracao']['rigs'] as $rig): ?>
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <div class="card rig-card <?= $rig['status'] == 'ligado' ? 'border-success' : 'border-danger' ?>">
                    <div class="card-body">
                        <h5 class="card-title">
                            <span class="status-indicator bg-<?= $rig['status'] == 'ligado' ? 'success' : 'danger' ?>"></span>
                            <?= $rig['nome'] ?>
                        </h5>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-2">
                                    <small>🌡️ Temperatura</small>
                                    <h4><?= $rig['temp'] ?>°C</h4>
                                </div>
                                <div class="mb-2">
                                    <small>⚡ Energia</small>
                                    <h4><?= $rig['watts'] ?>W</h4>
                                </div>
                            </div>
                            <div class="col-6 text-center">
                                <div class="d-flex flex-column justify-content-between h-100">
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
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Gráficos -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-secondary">
                    <div class="card-body">
                        <canvas id="lucroChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Atualização automática a cada 5 segundos
        setInterval(() => window.location.reload(), 5000);

        // Configuração do gráfico
        const ctx = document.getElementById('lucroChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(range(1, count($_SESSION['mineracao']['historico_lucro']))) ?>,
                datasets: [{
                    label: 'Histórico de Lucro (USD)',
                    data: <?= json_encode($_SESSION['mineracao']['historico_lucro']) ?>,
                    borderColor: '#28a745',
                    tension: 0.4,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Desempenho Financeiro',
                        color: '#fff'
                    }
                },
                scales: {
                    y: {
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    x: {
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    }
                }
            }
        });
    </script>
</body>
</html>