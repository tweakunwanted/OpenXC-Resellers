<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
$ip = $_SERVER["SERVER_ADDR"];
include_once "./sys/functions.php";
if (isset($_POST["save_settings"]) && isset($_POST["server_name"]) && isset($_POST["admin_group"]) && isset($_POST["ultra_group"]) && isset($_POST["master_group"]) && isset($_POST["reseller_group"]) && isset($_POST["allowed_groups"])) {
    $server_name = $_POST["server_name"];
    $admin_group = $_POST["admin_group"];
    $ultra_group = $_POST["ultra_group"];
    $master_group = $_POST["master_group"];
    $reseller_group = $_POST["reseller_group"];
    $group_settings = json_encode(array("admin" => $admin_group, "ultra" => $ultra_group, "master" => $master_group, "reseller" => $reseller_group));
    $allowed_groups = json_encode($_POST["allowed_groups"]);
    deleteServerProperty("server_name");
    deleteServerProperty("group_settings");
    deleteServerProperty("allowed_groups");
    $result1 = addServerProperty("server_name", $server_name);
    $result2 = addServerProperty("group_settings", $group_settings);
    $result3 = addServerProperty("allowed_groups", $allowed_groups);
    if ($result1 && $result2 && $result3) {
        header("location: ?result=success");
        exit;
    }
    header("location: ?result=failed");
    exit;
}
echo "<!DOCTYPE html>\n<html>\n  <head>\n    <meta charset=\"utf-8\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <title>Painel Office</title>\n    <!-- Tell the browser to be responsive to screen width -->\n    <meta content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" name=\"viewport\">\n    <!-- Bootstrap 3.3.7 -->\n    <link rel=\"stylesheet\" href=\"bower_components/bootstrap/dist/css/bootstrap.min.css\">\n    <!-- Font Awesome -->\n    <link rel=\"stylesheet\" href=\"bower_components/font-awesome/css/font-awesome.min.css\">\n    <!-- Theme style -->\n    <link rel=\"stylesheet\" href=\"dist/css/AdminLTE.min.css\">\n    <!-- iCheck -->\n    <link rel=\"stylesheet\" href=\"plugins/iCheck/square/blue.css\">\n\n    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->\n    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->\n    <!--[if lt IE 9]>\n    <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>\n    <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>\n    <![endif]-->\n\n    <!-- Google Font -->\n    <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic\">\n    ";
injectCustomCss();
echo "</head>\n  <body class=\"hold-transition login-page\">\n  <div class=\"container\">\n    <div class=\"login-box\">\n      ";
if (isset($_GET["result"])) {
    $result = $_GET["result"];
    $result_message = "Aconteceu um problema, tente novamente mais tarde!";
    $result_type = "warning";
    switch ($result) {
        case "success":
            $result_type = "success";
            $result_message = "Configurações salvas com sucesso!<br><b>APAGUE O ARQUIVO \"install.php\"!</b>";
            break;
    }
    echo "          <div class=\"callout callout-";
    echo $result_type;
    echo "\">\n            ";
    echo $result_message;
    echo "          </div>\n          ";
}
echo "      <div class=\"login-box-body\">\n        <h2>Painel Office<br>Iniciando configurações<br>Solicite sueu serial para este IP: ".$ip."</h2>\n\n\n        <form action=\"install.php\" method=\"post\">\n        ";
$con_office = getOfficeConnection();
$con_xtream = getConnection();
if (!$con_xtream || !$con_office) {
    echo "<h4>Antes de continuar você deve corrigir os seguintes problemas:</h4>";
    if (!$con_office) {
        echo "<p style=\"color: red; font-weight: bold;\">Conexão com o banco de dados Painel Office.</p><p>Verifique as informações do banco de dados do Painel Office no arquivo /sys/config.php.</p>";
        echo PHP_EOL . "<br>" . PHP_EOL;
    }
    if (!$con_xtream) {
        echo "<p style=\"color: red; font-weight: bold;\">Conexão com o banco de dados Xtream-Codes.</p><p>Verifique as informações do banco de dados do xtream-codes no arquivo /sys/config.php.</p>";
        echo PHP_EOL . "<br>" . PHP_EOL;
    }
} else {
    echo "          <h4>Configurações gerais</h4>\n          <div class=\"form-group has-feedback\">\n            <label for=\"server_name\">Nome do Servidor</label>\n            <input type=\"text\" class=\"form-control\" name=\"server_name\" placeholder=\"Digite o nome do servidor\" maxlength=\"255\">\n          </div>\n          <h4>Configurações de grupos</h4>\n          <div class=\"form-group has-feedback\">\n            <label>Selecione o grupo dos Administradores:</label>\n            <select id=\"admin_group\" name=\"admin_group\" class=\"form-control\">\n              ";
    foreach (getAllGroups() as $group) {
        echo "                  <option value=\"";
        echo $group["group_id"];
        echo "\">";
        echo $group["group_name"];
        echo "</option>\n              ";
    }
    echo "            </select>\n            <p>*Usuários que estiverem nesse grupo teram acesso total ao office.</p>\n          </div>\n          <div class=\"form-group has-feedback\">\n            <label>Selecione o grupo dos Revendedores Ultra:</label>\n            <select id=\"ultra_group\" name=\"ultra_group\" class=\"form-control\">\n              ";
    foreach (getAllGroups() as $group) {
        echo "                  <option value=\"";
        echo $group["group_id"];
        echo "\">";
        echo $group["group_name"];
        echo "</option>\n              ";
    }
    echo "            </select>\n            <p>*Usuários que estiverem nesse grupo poderam criar revendedores masters</p>\n          </div>\n          <div class=\"form-group has-feedback\">\n            <label>Selecione o grupo dos Revendedores Master:</label>\n            <select id=\"master_group\" name=\"master_group\" class=\"form-control\">\n              ";
    foreach (getAllGroups() as $group) {
        echo "                  <option value=\"";
        echo $group["group_id"];
        echo "\">";
        echo $group["group_name"];
        echo "</option>\n              ";
    }
    echo "            </select>\n            <p>*Usuários que estiverem nesse grupo poderam criar revendedores comuns.</p>\n          </div>\n          <div class=\"form-group has-feedback\">\n            <label>Selecione o grupo dos Revendedores comuns:</label>\n            <select id=\"reseller_group\" name=\"reseller_group\" class=\"form-control\">\n              ";
    foreach (getAllGroups() as $group) {
        echo "                  <option value=\"";
        echo $group["group_id"];
        echo "\">";
        echo $group["group_name"];
        echo "</option>\n              ";
    }
    echo "            </select>\n            <p>*Usuários que estiverem nesse grupo poderam criar apenas usuários finais.</p>\n          </div>\n          <div class=\"form-group \">\n            <label>Selecione os grupos que tem permissão para acessar o painel office.</label>\n            <select multiple id=\"allowed_groups\" name=\"allowed_groups[]\" class=\"form-control\">\n              ";
    foreach (getAllGroups() as $group) {
        if (in_array($group["group_id"], $allowed_groups)) {
            echo "                    <option value=\"";
            echo $group["group_id"];
            echo "\" selected>";
            echo $group["group_name"];
            echo "</option>\n              ";
        } else {
            echo "                    <option value=\"";
            echo $group["group_id"];
            echo "\">";
            echo $group["group_name"];
            echo "</option>\n              ";
        }
    }
    echo "            </select>\n          </div>\n          <div class=\"row\">\n            <div class=\"col-xs-4\">\n              <button type=\"submit\" class=\"btn btn-primary btn-block btn-flat\" name=\"save_settings\">Concluir</button>\n            </div>\n            <!-- /.col -->\n          </div>\n        ";
}
echo "         <p style=\"padding-top: 10px; text-align: center;\">Painel Office - www.paineloffice.top<a target=\"_blank\" href=\"https://www.paineloffice.top\">Krash0.</a></p>\n        </form>\n      </div>\n      <!-- /.login-box-body -->\n    </div>\n    <!-- /.login-box -->\n  </div>\n\n  <!-- jQuery 3 -->\n  <script src=\"bower_components/jquery/dist/jquery.min.js\"></script>\n  <!-- Bootstrap 3.3.7 -->\n  <script src=\"bower_components/bootstrap/dist/js/bootstrap.min.js\"></script>\n  <!-- iCheck -->\n  <script src=\"plugins/iCheck/icheck.min.js\"></script>\n  <script>\n      \$(function () {\n          \$('input').iCheck({\n              checkboxClass: 'icheckbox_square-blue',\n              radioClass: 'iradio_square-blue',\n              increaseArea: '20%' /* optional */\n          });\n          \$(\".callout\").delay(10000).slideUp(200, function() {\n              \$(this).remove();\n          });\n      });\n  </script>\n  </body>\n</html>\n";

?>