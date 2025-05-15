-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/05/2025 às 19:36
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `criptofarm`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(255) DEFAULT NULL,
  `valor` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`) VALUES
(1, 'automacao', '1');

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_lucro`
--

CREATE TABLE `historico_lucro` (
  `id` int(11) NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_energia` int(11) NOT NULL,
  `temperatura_media` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `historico_lucro`
--

INSERT INTO `historico_lucro` (`id`, `valor`, `data_registro`, `total_energia`, `temperatura_media`) VALUES
(1239, 5.00, '2025-05-14 17:35:58', 0, 0.00),
(1240, 5.00, '2025-05-14 17:35:58', 1006, 70.00),
(1241, 3.00, '2025-05-14 17:36:07', 0, 0.00),
(1242, 3.00, '2025-05-14 17:36:07', 1587, 66.33),
(1243, 1.00, '2025-05-14 17:36:16', 0, 0.00),
(1244, 1.00, '2025-05-14 17:36:17', 2155, 67.00),
(1245, 2.00, '2025-05-14 17:36:25', 0, 0.00),
(1246, 2.00, '2025-05-14 17:36:25', 2258, 72.00),
(1247, 4.00, '2025-05-14 17:36:34', 0, 0.00),
(1248, 4.00, '2025-05-14 17:36:34', 1810, 60.67);

-- --------------------------------------------------------

--
-- Estrutura para tabela `rigs`
--

CREATE TABLE `rigs` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `status` enum('ligado','desligado') DEFAULT 'ligado',
  `temperatura` int(11) DEFAULT NULL,
  `watts` int(11) DEFAULT NULL,
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `rigs`
--

INSERT INTO `rigs` (`id`, `nome`, `status`, `temperatura`, `watts`, `data_atualizacao`) VALUES
(1, 'Rig GPU #1', 'ligado', 71, 1444, '2025-05-14 17:36:34'),
(2, 'Rig GPU #2', 'ligado', 79, 976, '2025-05-14 17:36:34'),
(3, 'Rig ASIC #1', 'ligado', 72, 1095, '2025-05-14 17:36:34');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `historico_lucro`
--
ALTER TABLE `historico_lucro`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `rigs`
--
ALTER TABLE `rigs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;

--
-- AUTO_INCREMENT de tabela `historico_lucro`
--
ALTER TABLE `historico_lucro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1249;

--
-- AUTO_INCREMENT de tabela `rigs`
--
ALTER TABLE `rigs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
