/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for openxc_resellers
CREATE DATABASE IF NOT EXISTS `openxc_resellers` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `openxc_resellers`;

-- Dumping structure for table openxc_resellers.office_properties
CREATE TABLE IF NOT EXISTS `office_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `value` text COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table openxc_resellers.office_properties: ~8 rows (approximately)
/*!40000 ALTER TABLE `office_properties` DISABLE KEYS */;
INSERT INTO `office_properties` (`id`, `property`, `value`) VALUES
	(1, 'email_settings', '{"sender_name":"SERVER NAME","sender_email":"no-reply@yourdomain.com.br","use_smtp":"0","smtp_server":"127.0.0.1","smtp_port":"25","smtp_username":"root","smtp_password":"password","encryption_type":""}'),
	(2, 'email_messages', '{"auto_test_subject":"Seu teste gratuito","auto_test_message":"<p>Ol&aacute; {USERNAME},<\\/p>\\r\\n\\r\\n<p>Voc&ecirc; solicitou um teste gratuito, segue seus dados de acesso:<\\/p>\\r\\n\\r\\n<p>Usu&aacute;rio: {USERNAME}<br \\/>\\r\\nSenha: {PASSWORD}<br \\/>\\r\\nLink da Lista: {LIST_LINK}<\\/p>\\r\\n\\r\\n<p>Atenciosamente, {SERVER_NAME}.<\\/p>\\r\\n","pass_recovery_subject":"Recupera\\u00e7\\u00e3o de senha","pass_recovery_message":"<p>Ol&aacute; {USERNAME},<\\/p>\\r\\n\\r\\n<p>Algu&eacute;m solicitou uma nova senha. Para alterar sua senha, clique no seguinte link: {RESET_LINK}<\\/p>\\r\\n\\r\\n<p>Atenciosamente, {SERVER_NAME}.<\\/p>\\r\\n"}'),
	(3, 'fixed_informations', '<p><span style="color:#c0392b"><span style="font-size:20px"><span style="font-family:Tahoma,Geneva,sans-serif">Coloque informa&ccedil;&otilde;es importantes aqui!</span></span></span></p>\r\n\r\n<p><span style="font-family:Tahoma,Geneva,sans-serif"><span style="font-size:12px"><span style="color:#16a085">Obrigado por comprar :)</span></span></span></p>\r\n'),
	(17, 'allowed_bouquets', '[]'),
	(20, 'fast_packages', '[]'),
	(21, 'server_name', 'NOME DO SERVIDOR'),
	(22, 'group_settings', '{}'),
	(23, 'allowed_groups', '[]');
/*!40000 ALTER TABLE `office_properties` ENABLE KEYS */;

-- Dumping structure for table openxc_resellers.test_historic
CREATE TABLE IF NOT EXISTS `test_historic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table openxc_resellers.test_historic: ~0 rows (approximately)
/*!40000 ALTER TABLE `test_historic` DISABLE KEYS */;
/*!40000 ALTER TABLE `test_historic` ENABLE KEYS */;

-- Dumping structure for table openxc_resellers.user_properties
CREATE TABLE IF NOT EXISTS `user_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `property` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `value` text COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Dumping data for table openxc_resellers.user_properties: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_properties` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
