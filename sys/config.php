<?php
	/* SERVER SETTINGS */ /* CONFIGURAÇÕES DO SERVIDOR */
	define("OFFICE_KEY", "YOUR_LICENCE_KEY");

	/* RESELLERS PANEL DATABASE SETTINGS */ /* CONFIGURAÇÕES DO BANCO DE DADOS DO PAINEL REVENDEDOR */
       define("OFFICE_DB_HOST", "localhost");
       define("OFFICE_DB_PORT", "3306");
       define("OFFICE_DB_NAME", "openxc_resellers");
       define("OFFICE_DB_USER", "root");
       define("OFFICE_DB_PASS", "");

 	/* XTREAM PANEL DATABASE SETTINGS */ /* CONFIGURAÇÕES DO BANCO DE DADOS DO SERVIDOR XTREAM CODES */
	define("DB_HOST", "127.0.0.1");
	define("DB_PORT", "7999");
	define("DB_NAME", "xtream_iptvpro");
	define("DB_USER", "root");
	define("DB_PASS", "");

	/* REPLACE WITH YOUR DOMAIN OR IP BUT KEEP /i */ /* SUBSTITUA COM O SEU ENDEREÇO IP OU DOMINIO MATENHA O /i */
	define("SHORTENER_URL", "http://domain.com/i");

	/* ALLOWED EMAILS FOR TESTS */ /* EMAILS PERMITIDOS PARA TESTES */
	define ("ALLOWED_EMAILS", serialize (array ("root@localhost.com", "exemplo2@gmail.com")));

	/* ENABLE DEBUG LOG */
	define("OFFICE_DEBUG", 0);
?>
