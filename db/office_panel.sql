-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 11-Fev-2019 às 20:50
-- Versão do servidor: 10.1.34-MariaDB
-- PHP Version: 5.6.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `office_panel`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `office_properties`
--

CREATE TABLE `office_properties` (
  `id` int(11) NOT NULL,
  `property` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `value` text COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Extraindo dados da tabela `office_properties`
--

INSERT INTO `office_properties` (`id`, `property`, `value`) VALUES
(1, 'email_settings', '{\"sender_name\":\"SERVER NAME\",\"sender_email\":\"no-reply@yourdomain.com.br\",\"use_smtp\":\"0\",\"smtp_server\":\"127.0.0.1\",\"smtp_port\":\"25\",\"smtp_username\":\"root\",\"smtp_password\":\"password\",\"encryption_type\":\"\"}'),
(2, 'email_messages', '{\"auto_test_subject\":\"Seu teste gratuito\",\"auto_test_message\":\"<p>Ol&aacute; {USERNAME},<\\/p>\\r\\n\\r\\n<p>Voc&ecirc; solicitou um teste gratuito, segue seus dados de acesso:<\\/p>\\r\\n\\r\\n<p>Usu&aacute;rio: {USERNAME}<br \\/>\\r\\nSenha: {PASSWORD}<br \\/>\\r\\nLink da Lista: {LIST_LINK}<\\/p>\\r\\n\\r\\n<p>Atenciosamente, {SERVER_NAME}.<\\/p>\\r\\n\",\"pass_recovery_subject\":\"Recupera\\u00e7\\u00e3o de senha\",\"pass_recovery_message\":\"<p>Ol&aacute; {USERNAME},<\\/p>\\r\\n\\r\\n<p>Algu&eacute;m solicitou uma nova senha. Para alterar sua senha, clique no seguinte link: {RESET_LINK}<\\/p>\\r\\n\\r\\n<p>Atenciosamente, {SERVER_NAME}.<\\/p>\\r\\n\"}'),
(3, 'fixed_informations', '<p><span style=\"color:#c0392b\"><span style=\"font-size:20px\"><span style=\"font-family:Tahoma,Geneva,sans-serif\">Coloque informa&ccedil;&otilde;es importantes aqui!</span></span></span></p>\r\n\r\n<p><span style=\"font-family:Tahoma,Geneva,sans-serif\"><span style=\"font-size:12px\"><span style=\"color:#16a085\">Obrigado por comprar :)</span></span></span></p>\r\n'),
(17, 'allowed_bouquets', '[]'),
(20, 'fast_packages', '[]'),
(21, 'server_name', 'NOME DO SERVIDOR'),
(22, 'group_settings', '{}'),
(23, 'allowed_groups', '[]');

-- --------------------------------------------------------

--
-- Estrutura da tabela `test_historic`
--

CREATE TABLE `test_historic` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_properties`
--

CREATE TABLE `user_properties` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `property` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `value` text COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `office_properties`
--
ALTER TABLE `office_properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_historic`
--
ALTER TABLE `test_historic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_properties`
--
ALTER TABLE `user_properties`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `office_properties`
--
ALTER TABLE `office_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `test_historic`
--
ALTER TABLE `test_historic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `user_properties`
--
ALTER TABLE `user_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
