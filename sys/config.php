<?php
	/* SERVER SETTINGS */ /* CONFIGURAÇÕES DO SERVIDOR */
	define("OFFICE_KEY", "YOUR_LICENCE_KEY");

	/* OFFICE PANEL DATABASE SETTINGS */ /* CONFIGURAÇÕES DO BANCO DE DADOS DO PAINEL OFFICE */
    define("OFFICE_DB_HOST", "127.0.0.1");
    define("OFFICE_DB_PORT", "3306");
    define("OFFICE_DB_NAME", "office_panel");
    define("OFFICE_DB_USER", "root");
    define("OFFICE_DB_PASS", "");

 	/* XTREAM PANEL DATABASE SETTINGS */ /* CONFIGURAÇÕES DO BANCO DE DADOS DO SERVIDOR XTREAM CODES */
	define("DB_HOST", "127.0.0.1");
	define("DB_PORT", "7999");
	define("DB_NAME", "xtream_iptvpro");
	define("DB_USER", "root");
	define("DB_PASS", "");

	/* SHORTENER SETTINGS */ /* CONFIGURAÇÕES DO ENCURTADOR DE LINK */
	define("SHORTENER_URL", "http://n.localhost.com");

	/* ALLOWED EMAILS FOR TESTS */ /* EMAILS PERMITIDOS PARA TESTES */
	define ("ALLOWED_EMAILS", serialize (array ("root@localhost.com", "exemplo2@gmail.com")));

	/* ENABLE DEBUG LOG */
	define("OFFICE_DEBUG", 0);
?>