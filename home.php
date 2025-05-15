<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CriptoFarm - Sobre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/home.css" rel="stylesheet">
    
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="home.php">CriptoFarm</a>
            <div class="navbar-nav">
                <a class="nav-link" href="login.php">Login</a>
            </div>
        </div>
    </nav>

    <!-- Seção Hero -->
    <div class="hero-section">
        <div class="text-center">
            <h1 class="display-4 mb-4 text-center mx-auto" style="max-width: 800px;" color="white">Sistema de Gerenciamento Inteligente de Mineração</h1>
            <a href="login.php" class="btn btn-primary btn-lg">Acessar Painel</a>
        </div>
    </div>

    <!-- Explicação do Processo -->
    <div class="container py-5">
        <h2 class="text-center mb-5">Como Funciona Nosso Sistema</h2>
        
        <div class="row g-4">
            <!-- Card Sensores -->
            <div class="col-md-4">
                <div class="card process-card h-100">
                    <div class="card-body text-center">
                        <img src="https://img.icons8.com/ios/100/000000/thermometer.png" width="80" class="mb-3">
                        <h4 class="card-title">Monitoramento por Sensores</h4>
                        <p class="card-text">
                            Sensores virtuais coletam dados em tempo real:
                            <ul class="list-unstyled">
                                <li>🌡️ Temperatura dos Rigs</li>
                                <li>⚡ Consumo Energético</li>
                                <li>⏱️ Performance de Hardware</li>
                            </ul>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card Lógica -->
            <div class="col-md-4">
                <div class="card process-card h-100">
                    <div class="card-body text-center">
                        <img src="https://img.icons8.com/ios/100/000000/automatic.png" width="80" class="mb-3">
                        <h4 class="card-title">Lógica de Automação</h4>
                        <p class="card-text">
                            Sistema inteligente toma decisões baseado em regras:
                            <ul class="list-unstyled">
                                <li>❌ Desligamento preventivo</li>
                                <li>📈 Otimização de lucro</li>
                                <li>🔌 Gerenciamento energético</li>
                            </ul>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card Atuadores -->
            <div class="col-md-4">
                <div class="card process-card h-100">
                    <div class="card-body text-center">
                        <img src="https://img.icons8.com/ios/100/000000/engine.png" width="80" class="mb-3">
                        <h4 class="card-title">Controle de Atuadores</h4>
                        <p class="card-text">
                            Atuação remota nos dispositivos:
                            <ul class="list-unstyled">
                                <li>🔋 Ligar/Desligar Rigs</li>
                                <li>🎛️ Ajustar Velocidade</li>
                                <li>🚨 Alertas Automáticos</li>
                            </ul>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container text-center">
            <p>&copy; 2025 CriptoFarm - Sistema de Gerenciamento de Fazendas de Criptos</p>
        </div>
    </footer>
</body>
</html>