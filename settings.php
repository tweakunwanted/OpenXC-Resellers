<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

include_once "./sys/functions.php";
isLogged();
$logged_user = getLoggedUser();
$server_name = getServerProperty("server_name");
$theme_color = getServerProperty("theme_color", "red-skin");
if (!isAdmin($logged_user)) {
    exit;
}
if (isset($_POST["save_geral_settings"]) && isset($_POST["theme_color"])) {
    $theme_color = $_POST["theme_color"];
    updateServerProperty("theme_color", $theme_color);
    header("location: ?result=geral_settings_saved");
    exit;
}
if (isset($_POST["save_fixed_informations"]) && isset($_POST["fixed_informations"])) {
    $fixed_informations = $_POST["fixed_informations"];
    $result1 = updateServerProperty("fixed_informations", $fixed_informations);
    if ($result1) {
        header("location: ?result=fixed_informations_saved");
        exit;
    }
    header("location: ?result=failed");
    exit;
}
if (isset($_POST["save_allowed_groups"]) && isset($_POST["allowed_groups"])) {
    $allowed_groups = json_encode($_POST["allowed_groups"]);
    $result1 = updateServerProperty("allowed_groups", $allowed_groups);
    if ($result1) {
        header("location: ?result=allowed_groups_saved");
        exit;
    }
    header("location: ?result=failed");
    exit;
}
if (isset($_POST["save_allowed_bouquets"]) && isset($_POST["allowed_bouquets"])) {
    $allowed_bouquets = json_encode($_POST["allowed_bouquets"]);
    $result1 = updateServerProperty("allowed_bouquets", $allowed_bouquets);
    if ($result1) {
        header("location: ?result=allowed_bouquets_saved");
        exit;
    }
    header("location: ?result=failed");
    exit;
}
if (isset($_POST["save_manual_test"]) && isset($_POST["fast_packages"]) && isset($_POST["test_time"]) && isset($_POST["min_credits"])) {
    $fast_packages = json_encode($_POST["fast_packages"]);
    updateServerProperty("fast_packages", $fast_packages);
    $test_time = intval($_POST["test_time"]);
    updateServerProperty("test_time", $test_time);
    $min_credits = intval($_POST["min_credits"]);
    updateServerProperty("test_min_credits", $min_credits);
    header("location: ?result=manual_test_saved");
    exit;
}
if (isset($_POST["save_automatic_test"]) && isset($_POST["disabled_days_automatic_test"]) && isset($_POST["automatic_test_packages"]) && isset($_POST["automatic_test_min_credits"])) {
    $automatic_test = isset($_POST["automatic_test"]) ? 1 : 0;
    updateServerProperty("automatic_test", $automatic_test);
    $random_name_automatic_test = isset($_POST["random_name_automatic_test"]) ? 1 : 0;
    updateServerProperty("random_name_automatic_test", $random_name_automatic_test);
    $only_valid_emails_automatic_test = isset($_POST["only_valid_emails_automatic_test"]) ? 1 : 0;
    updateServerProperty("only_valid_emails_automatic_test", $only_valid_emails_automatic_test);
    $disabled_days_automatic_test = $_POST["disabled_days_automatic_test"];
    updateServerProperty("disabled_days_automatic_test", $disabled_days_automatic_test);
    $automatic_test_packages = json_encode($_POST["automatic_test_packages"]);
    updateServerProperty("automatic_test_packages", $automatic_test_packages);
    $automatic_test_min_credits = intval($_POST["automatic_test_min_credits"]);
    updateServerProperty("automatic_test_min_credits", $automatic_test_min_credits);
    header("location: ?result=automatic_test_saved");
    exit;
}
if (isset($_POST["change_resellers"]) && isset($_POST["selected_resellers"]) && isset($_POST["new_owner"]) && isset($_POST["new_group_name"])) {
    $selected_resellers = $_POST["selected_resellers"];
    $new_owner = intval($_POST["new_owner"]);
    $new_group = $_POST["new_group_name"];
    if (is_array($selected_resellers)) {
        $group_settings = json_decode(getServerProperty("group_settings"), true);
        $group_id = isset($group_settings[$new_group]) ? $group_settings[$new_group] : 0;
        if (transferResellers($selected_resellers, $new_owner, $group_id)) {
            header("location: ?result=resellers_changed");
            exit;
        }
    }
    header("location: ?result=failed");
    exit;
}
if (isset($_POST["save_email_settings"]) && isset($_POST["encryption_type"]) && isset($_POST["sender_name"]) && isset($_POST["sender_email"]) && isset($_POST["use_smtp"]) && isset($_POST["smtp_server"]) && isset($_POST["smtp_port"]) && isset($_POST["smtp_username"]) && isset($_POST["smtp_password"])) {
    $email_settings = $_POST;
    unset($email_settings["save_email_settings"]);
    $email_settings = json_encode($email_settings);
    $result1 = updateServerProperty("email_settings", $email_settings);
    if ($result1) {
        header("location: ?result=email_settings_saved");
        exit;
    }
    header("location: ?result=failed");
    exit;
}
if (isset($_POST["save_email_messages"]) && isset($_POST["auto_test_subject"]) && isset($_POST["auto_test_message"]) && isset($_POST["pass_recovery_subject"]) && isset($_POST["pass_recovery_message"])) {
    $email_messages = $_POST;
    unset($email_messages["save_email_messages"]);
    $email_messages = json_encode($email_messages);
    $result1 = updateServerProperty("email_messages", $email_messages);
    if ($result1) {
        header("location: ?result=email_messages_saved");
        exit;
    }
    header("location: ?result=failed");
    exit;
}
$settings = getServerProperties();
$fixed_informations = $settings["fixed_informations"];
$allowed_groups = isset($settings["allowed_groups"]) ? json_decode($settings["allowed_groups"], true) : array();
$allowed_bouquets = isset($settings["allowed_bouquets"]) ? json_decode($settings["allowed_bouquets"], true) : array();
$fast_packages = isset($settings["fast_packages"]) ? json_decode($settings["fast_packages"], true) : array();
$automatic_test_packages = isset($settings["automatic_test_packages"]) ? json_decode($settings["automatic_test_packages"], true) : array();
$email_settings = json_decode($settings["email_settings"], true);
$email_messages = json_decode($settings["email_messages"], true);
echo "<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\"utf-8\">\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n  <title>";
echo $server_name;
echo " :: Office</title>\n  <!-- Tell the browser to be responsive to screen width -->\n  <meta content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" name=\"viewport\">\n  <!-- Bootstrap 3.3.7 -->\n  <link rel=\"stylesheet\" href=\"bower_components/bootstrap/dist/css/bootstrap.min.css\">\n  <!-- Font Awesome -->\n  <link rel=\"stylesheet\" href=\"bower_components/font-awesome/css/font-awesome.min.css\">\n  <!-- Theme style -->\n  <link rel=\"stylesheet\" href=\"dist/css/AdminLTE.min.css\">\n  <!-- AdminLTE Skins. Choose a skin from the css/skins\n       folder instead of downloading all of them to reduce the load. -->\n  <link rel=\"stylesheet\" href=\"dist/css/skins/_all-skins.min.css\">\n  <!-- iCheck for checkboxes and radio inputs -->\n  <link rel=\"stylesheet\" href=\"./plugins/iCheck/all.css\">\n  <!-- bootstrap datepicker -->\n  <link rel=\"stylesheet\" href=\"bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css\">\n  <!-- Morris chart -->\n  <link rel=\"stylesheet\" href=\"bower_components/morris.js/morris.css\">\n  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->\n  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->\n  <!--[if lt IE 9]>\n  <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>\n  <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>\n  <![endif]-->\n\n  <!-- Google Font -->\n  <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic\">\n  ";
injectCustomCss();
echo "</head>\n<body class=\"hold-transition ";
echo getServerProperty("theme_color", "skin-red");
echo " sidebar-mini\">\n<div class=\"wrapper\">\n  <header class=\"main-header\">\n    <!-- Logo -->\n    <a href=\"dashboard.php\" class=\"logo\">\n      <!-- mini logo for sidebar mini 50x50 pixels -->\n      <span class=\"logo-mini\"><img src=\"dist/img/logo_small.png\" width=\"50\" height=\"50\"></span>\n      <!-- logo for regular state and mobile devices -->\n      <span class=\"logo-lg\"><img src=\"dist/img/logo_medium.png\" height=\"50\"></span>\n    </a>\n    <!-- Header Navbar: style can be found in header.less -->\n    <nav class=\"navbar navbar-static-top\">\n      <!-- Sidebar toggle button-->\n      <a href=\"#\" class=\"sidebar-toggle\" data-toggle=\"push-menu\" role=\"button\">\n        <span class=\"sr-only\">Toggle navigation</span>\n      </a>\n\n      <div class=\"navbar-custom-menu\">\n        <ul class=\"nav navbar-nav\">\n          <!-- User Credits -->\n          <li class=\"dropdown messages-menu\">\n            <a href=\"#\" class=\"dropdown-toggle\">\n              <i class=\"fa fa-dollar\"></i>\n              <span class=\"label label-success\">";
echo $logged_user["credits"];
echo "</span>\n            </a>\n          </li>\n          <!-- Control Sidebar Toggle Button -->\n        ";
if (isAdmin($logged_user)) {
    echo "          <li class=\"active\">\n            <a href=\"settings.php\"><i class=\"fa fa-gears\"></i></a>\n          </li>\n        ";
}
echo "        </ul>\n      </div>\n    </nav>\n  </header>\n  <!-- Left side column. contains the logo and sidebar -->\n  <aside class=\"main-sidebar\">\n    <!-- sidebar: style can be found in sidebar.less -->\n    <section class=\"sidebar\">\n      <!-- sidebar menu: : style can be found in sidebar.less -->\n      <ul class=\"sidebar-menu\" data-widget=\"tree\">\n        <li class=\"header\">MENU PRINCIPAL</li>\n        <li>\n          <a href=\"dashboard.php\">\n            <i class=\"fa fa-dashboard\"></i> <span>Painel</span>\n          </a>\n        </li>\n        <li>\n          <a href=\"informations.php\">\n            <i class=\"fa fa-align-left\"></i> <span>Informações</span>\n          </a>\n        </li>\n        <li class=\"treeview\">\n          <a href=\"#\">\n            <i class=\"fa fa-bug\"></i>\n            <span>Criar teste</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"create_test.php\"><i class=\"fa fa-circle-o\"></i> Customizado</a></li>\n            ";
$packages = getPackages();
foreach ($fast_packages as $package_id) {
    $package_key = array_search($package_id, array_column($packages, "id"));
    if ($package_key !== false) {
        $current_package = $packages[$package_key];
        if ($current_package["is_trial"] == 1) {
            echo "                    <li><a href=\"./sys/API.php?action=create_test&package_id=";
            echo $current_package["id"];
            echo "\"><i class=\"fa fa-circle-o\"></i> ";
            echo $current_package["package_name"];
            echo "</a></li>\n                    ";
        }
    }
}
echo "          </ul>\n        </li>\n        ";
if (isAdmin($logged_user) || isUltra($logged_user) || isMaster($logged_user)) {
    echo "        <li class=\"treeview\">\n          <a href=\"#\">\n            <i class=\"fa fa-users\"></i>\n            <span>Sub-Revendas</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"resellers.php\"><i class=\"fa fa-cogs\"></i> Gerir Revendas</a></li>\n            <li><a href=\"create_reseller.php\"><i class=\"fa fa-user-plus\"></i> Criar Revenda</a></li>\n          </ul>\n        </li>\n        ";
}
echo "        <li class=\"treeview\">\n          <a href=\"#\">\n            <i class=\"fa fa-users\"></i>\n            <span>Usuários</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"online.php\"><i class=\"fa fa-circle\"></i> Usuários Online</a></li>\n            <li><a href=\"clients.php\"><i class=\"fa fa-cogs\"></i> Gerir Usuários</a></li>\n            <li><a href=\"create_client.php\"><i class=\"fa fa-user-plus\"></i> Criar Usuário</a></li>\n          </ul>\n        </li>\n        <li>\n          <a href=\"shortener.php\">\n            <i class=\"fa fa-link\"></i> <span>Encurtador</span>\n          </a>\n        </li>\n        <li>\n          <a href=\"tools.php\">\n            <i class=\"fa fa-wrench\"></i> <span>Ferramentas</span>\n          </a>\n        </li>\n        <li class=\"treeview\">\n          <a href=\"#\">\n            <i class=\"fa fa-film\"></i>\n            <span>Conteúdo Novo</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"new_channels.php\"><i class=\"fa fa-circle-o\"></i> Novos Canais</a></li>\n            <li><a href=\"new_movies.php\"><i class=\"fa fa-circle-o\"></i> Novos Filmes</a></li>\n            <li><a href=\"new_series.php\"><i class=\"fa fa-circle-o\"></i> Novas Series</a></li>\n          </ul>\n        </li>\n        <li class=\"treeview\">\n          <a href=\"#\">\n            <i class=\"fa fa-ticket\"></i>\n            <span>Ticket Suporte</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"create_ticket.php\"><i class=\"fa fa-circle-o\"></i> Criar Ticket</a></li>\n            <li><a href=\"manage_tickets.php\"><i class=\"fa fa-circle-o\"></i> Gerenciar Tickets</a></li>\n          </ul>\n        </li>\n        <li>\n          <a href=\"profile.php\">\n            <i class=\"fa fa-user-circle\"></i> <span>Perfil</span>\n          </a>\n        </li>\n        <li>\n          <a href=\"logout.php\">\n            <i class=\"fa fa-power-off\"></i> <span>Desconectar</span>\n          </a>\n        </li>\n      </ul>\n    </section>\n    <!-- /.sidebar -->\n  </aside>\n\n  <!-- Content Wrapper. Contains page content -->\n  <div class=\"content-wrapper\">\n    <!-- Content Header (Page header) -->\n    <section class=\"content-header\">\n      <h1>\n        Configurações\n        <small>Configure seu Painel Office.</small>\n      </h1>\n      <ol class=\"breadcrumb\">\n        <li><a href=\"dashboard.php\"><i class=\"fa fa-dashboard\"></i> Painel</a></li>\n        <li class=\"active\">Configurações</li>\n      </ol>\n    </section>\n    <!-- Main content -->\n    <section class=\"content\">\n      <!-- Main row -->\n      <div class=\"row\">\n        <!-- Left col -->\n        <section class=\"col-md-12\">\n          ";
if (isset($_GET["result"])) {
    $result = $_GET["result"];
    $result_message = "Aconteceu um problema, tente novamente mais tarde!";
    $result_type = "warning";
    switch ($result) {
        case "geral_settings_saved":
            $result_message = "Configurações gerais salvadas com sucesso.";
            $result_type = "success";
            break;
        case "fixed_informations_saved":
            $result_message = "Informações Fixas salvadas com sucesso.";
            $result_type = "success";
            break;
        case "allowed_groups_saved":
            $result_message = "Grupos permitidos salvados com sucesso.";
            $result_type = "success";
            break;
        case "allowed_bouquets_saved":
            $result_message = "Listas permitidas salvadas com sucesso.";
            $result_type = "success";
            break;
        case "manual_test_saved":
            $result_message = "As configurações de teste manual foram salvadas com sucesso.";
            $result_type = "success";
            break;
        case "automatic_test_saved":
            $result_message = "As configurações do gerador de teste automático foram salvadas com sucesso.";
            $result_type = "success";
            break;
        case "resellers_changed":
            $result_message = "Os revendedores foram transferidos com sucesso.";
            $result_type = "success";
            break;
        case "email_messages_saved":
            $result_message = "Mensagens de email salvadas com sucesso.";
            $result_type = "success";
            break;
        case "email_settings_saved":
            $result_message = "Configurações de email salvadas com sucesso.";
            $result_type = "success";
            break;
    }
    echo "            <div class=\"alert alert-";
    echo $result_type;
    echo " alert-dismissible\">\n              <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>\n              <i class=\"icon fa fa-check\"></i>\n              ";
    echo $result_message;
    echo "            </div>\n          ";
}
echo "        </section>\n        <div class=\"col-md-12\">\n          <!-- Custom Tabs -->\n          <div class=\"nav-tabs-custom\">\n            <ul class=\"nav nav-tabs\">\n              <li class=\"active\"><a href=\"#tab_1\" data-toggle=\"tab\">Geral</a></li>\n              <li><a href=\"#tab_2\" data-toggle=\"tab\">Informações Fixas</a></li>\n              <li><a href=\"#tab_3\" data-toggle=\"tab\">Grupos Permitidos</a></li>\n              <li><a href=\"#tab_4\" data-toggle=\"tab\">Listas Permitidos</a></li>\n              <li><a href=\"#tab_5\" data-toggle=\"tab\">Teste Manual</a></li>\n              <li><a href=\"#tab_6\" data-toggle=\"tab\">Teste Automático</a></li>\n              <li><a href=\"#tab_7\" data-toggle=\"tab\">Configurações de email</a></li>\n              <li><a href=\"#tab_8\" data-toggle=\"tab\">Mensagens de email</a></li>\n              <li><a href=\"#tab_9\" data-toggle=\"tab\">Utilidades</a></li>\n            </ul>\n            <div class=\"tab-content\">\n              <div class=\"tab-pane active\" id=\"tab_1\">\n                <form autocomplete=\"off\" action=\"#\" method=\"post\">\n                  <label>Selecione o tema do Painel Office.</label>\n                  <div class=\"form-group\">\n                    <select multiple id=\"theme_color\" name=\"theme_color\" class=\"form-control\">\n                      <option value=\"skin-blue\" ";
echo $theme_color == "skin-blue" ? "selected" : "";
echo " >Azul</option>\n                      <option value=\"skin-blue-light\" ";
echo $theme_color == "skin-blue-light" ? "selected" : "";
echo " >Azul Light</option>\n\n                      <option value=\"skin-yellow\" ";
echo $theme_color == "skin-yellow" ? "selected" : "";
echo " >Amarelo</option>\n                      <option value=\"skin-yellow-light\" ";
echo $theme_color == "skin-yellow-light" ? "selected" : "";
echo " >Amarelo Light</option>\n\n                      <option value=\"skin-green\" ";
echo $theme_color == "skin-green" ? "selected" : "";
echo " >Verde</option>\n                      <option value=\"skin-green-light\" ";
echo $theme_color == "skin-green-light" ? "selected" : "";
echo " >Verde Light</option>\n\n                      <option value=\"skin-purple\" ";
echo $theme_color == "skin-purple" ? "selected" : "";
echo " >Roxo</option>\n                      <option value=\"skin-purple-light\" ";
echo $theme_color == "skin-purple-light" ? "selected" : "";
echo " >Roxo Light</option>\n\n                      <option value=\"skin-red\" ";
echo $theme_color == "skin-red" ? "selected" : "";
echo " >Vermelho</option>\n                      <option value=\"skin-red-light\" ";
echo $theme_color == "skin-red-light" ? "selected" : "";
echo " >Vermelho Light</option>\n\n                      <option value=\"skin-dark\" ";
echo $theme_color == "skin-dark" ? "selected" : "";
echo " >Preto</option>\n                      <option value=\"skin-dark-light\" ";
echo $theme_color == "skin-dark-light" ? "selected" : "";
echo " >Preto Light</option>\n                    </select>\n                  </div>\n                  <button type=\"submit\" name=\"save_geral_settings\" class=\"btn btn-flat btn-success\">Salvar</button>\n                </form>\n              </div>\n              <div class=\"tab-pane\" id=\"tab_2\">\n                <form autocomplete=\"off\" action=\"#\" method=\"post\">\n                  <label>Escreva informações importantes e úteis.</label>\n                  <div class=\"form-group\">\n                    <textarea id=\"fixed_informations\" name=\"fixed_informations\" class=\"form-control\">";
echo $fixed_informations;
echo "</textarea>\n                  </div>\n                  <button type=\"submit\" name=\"save_fixed_informations\" class=\"btn btn-flat btn-success\">Salvar</button>\n                </form>\n              </div>\n              <!-- /.tab-pane -->\n              <div class=\"tab-pane\" id=\"tab_3\">\n                <label>Selecione os grupos que tem permissão para acessar o painel office.</label>\n                <form autocomplete=\"off\" action=\"#\" method=\"post\">\n                  <div class=\"form-group\">\n                    <select multiple id=\"allowed_groups\" name=\"allowed_groups[]\" class=\"form-control\">\n                      ";
foreach (getAllGroups() as $group) {
    if (in_array($group["group_id"], $allowed_groups)) {
        echo "                            <option value=\"";
        echo $group["group_id"];
        echo "\" selected>";
        echo $group["group_name"];
        echo "</option>\n                      ";
    } else {
        echo "                            <option value=\"";
        echo $group["group_id"];
        echo "\">";
        echo $group["group_name"];
        echo "</option>\n                      ";
    }
}
echo "                    </select>\n                  </div>\n                  <button type=\"submit\" name=\"save_allowed_groups\" class=\"btn btn-flat btn-success\">Salvar</button>\n                </form>\n              </div>\n              <!-- /.tab-pane -->\n              <div class=\"tab-pane\" id=\"tab_4\">\n                <label>Selecione as listas permitidas para o painel office.</label>\n                <form autocomplete=\"off\" action=\"#\" method=\"post\">\n                  <div class=\"form-group\">\n                    <select multiple id=\"allowed_bouquets\" name=\"allowed_bouquets[]\" class=\"form-control\">\n                      ";
foreach (getBouquets() as $bouquet) {
    if (in_array($bouquet["id"], $allowed_bouquets)) {
        echo "                            <option value=\"";
        echo $bouquet["id"];
        echo "\" selected>";
        echo $bouquet["bouquet_name"];
        echo "</option>\n                      ";
    } else {
        echo "                            <option value=\"";
        echo $bouquet["id"];
        echo "\">";
        echo $bouquet["bouquet_name"];
        echo "</option>\n                      ";
    }
}
echo "                    </select>\n                  </div>\n                  <button type=\"submit\" name=\"save_allowed_bouquets\" class=\"btn btn-flat btn-success\">Salvar</button>\n                </form>\n              </div>\n              <!-- /.tab-pane -->\n              <div class=\"tab-pane\" id=\"tab_5\">\n                <form autocomplete=\"off\" action=\"#\" method=\"post\">\n                  <label>Selecione os pacotes para criação de teste rápido.</label>\n                  <div class=\"form-group\">\n                    <select multiple id=\"fast_packages\" name=\"fast_packages[]\" class=\"form-control\">\n                      ";
foreach (getPackages() as $package) {
    if ($package["is_trial"]) {
        if (in_array($package["id"], $fast_packages)) {
            echo "                              <option value=\"";
            echo $package["id"];
            echo "\" selected>";
            echo $package["package_name"];
            echo "</option>\n                      ";
        } else {
            echo "                              <option value=\"";
            echo $package["id"];
            echo "\">";
            echo $package["package_name"];
            echo "</option>\n                      ";
        }
    }
}
echo "                    </select>\n                  </div>\n\n                  <label>Defina o tempo do teste customizado.</label>\n                  <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control\" required=\"\" value=\"";
echo getServerProperty("test_time", 1);
echo "\" data-minlength=\"0\" minlength=\"0\" autocomplete=\"off\" id=\"test_time\" name=\"test_time\">\n                  </div>\n\n                  <label>Defina o minimo de créditos para a criação de testes.</label>\n                  <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control\" required=\"\" value=\"";
echo getServerProperty("test_min_credits", 0);
echo "\" data-minlength=\"0\" minlength=\"0\" autocomplete=\"off\" id=\"min_credits\" name=\"min_credits\">\n                  </div>\n\n                  <button type=\"submit\" name=\"save_manual_test\" class=\"btn btn-flat btn-success\">Salvar</button>\n                </form>\n              </div>\n              <!-- /.tab-pane -->\n              <div class=\"tab-pane\" id=\"tab_6\">\n                <form autocomplete=\"off\" action=\"#\" method=\"post\">\n                  <label>Gerador de teste automático.</label>\n                  <div class=\"form-group\">\n                    <div class=\"input-group\">\n                      <label>\n                        <input type=\"checkbox\" name=\"automatic_test\" class=\"flat-red\" ";
if (getServerProperty("automatic_test", 0)) {
    echo "checked";
}
echo "> Ativar/Desativar\n                      </label>\n                    </div>\n                  </div>\n\n                  <label>Gerar nome de usuário aleatório. *Com essa opção ativada o painel irá gerar um nome de usuário aleatorio.</label>\n                  <div class=\"form-group\">\n                    <div class=\"input-group\">\n                      <label>\n                        <input type=\"checkbox\" name=\"random_name_automatic_test\" class=\"flat-red\" ";
if (getServerProperty("random_name_automatic_test", 0)) {
    echo "checked";
}
echo "> Ativar/Desativar\n                      </label>\n                    </div>\n                  </div>\n\n                  <label>Permitir apenas e-mails válidos. *Com essa opção ativada o painel irá permitir apenas e-mails válidos(@gmail, @hotmail, @outlook e @icloud).</label>\n                  <div class=\"form-group\">\n                    <div class=\"input-group\">\n                      <label>\n                        <input type=\"checkbox\" name=\"only_valid_emails_automatic_test\" class=\"flat-red\" ";
if (getServerProperty("only_valid_emails_automatic_test", 0)) {
    echo "checked";
}
echo "> Ativar/Desativar\n                      </label>\n                    </div>\n                  </div>\n\n                  <label>Selecione os pacotes para o gerador de teste automático.</label>\n                  <div class=\"form-group\">\n                    <select multiple id=\"automatic_test_packages\" name=\"automatic_test_packages[]\" class=\"form-control\">\n                      ";
foreach (getPackages() as $package) {
    if ($package["is_trial"]) {
        if (in_array($package["id"], $automatic_test_packages)) {
            echo "                              <option value=\"";
            echo $package["id"];
            echo "\" selected>";
            echo $package["package_name"];
            echo "</option>\n                      ";
        } else {
            echo "                              <option value=\"";
            echo $package["id"];
            echo "\">";
            echo $package["package_name"];
            echo "</option>\n                      ";
        }
    }
}
echo "                    </select>\n                  </div>\n\n                  <label>Defina o minimo de créditos para a utilização do gerador de teste automático.</label>\n                  <div class=\"form-group\">\n                    <input type=\"number\" class=\"form-control\" required=\"\" value=\"";
echo getServerProperty("automatic_test_min_credits", 0);
echo "\" data-minlength=\"0\" minlength=\"0\" autocomplete=\"off\" id=\"automatic_test_min_credits\" name=\"automatic_test_min_credits\">\n                  </div>\n\n                  <label>Selecione dias para deixar o gerador de teste desativado. *Útil para desativar o gerador de teste em dia de jogo.</label>\n                  <div class=\"form-group\">\n                    <div class=\"input-group date\">\n                      <div class=\"input-group-addon\">\n                        <i class=\"fa fa-calendar\"></i>\n                      </div>\n                      <input type=\"text\" class=\"form-control pull-right\" id=\"datepicker\" name=\"disabled_days_automatic_test\" value=\"";
echo getServerProperty("disabled_days_automatic_test", "");
echo "\">\n                    </div>\n                  </div>\n\n                  <button type=\"submit\" name=\"save_automatic_test\" class=\"btn btn-flat btn-success\">Salvar</button>\n                </form>\n              </div>\n              <!-- /.tab-pane -->\n              <div class=\"tab-pane\" id=\"tab_7\">\n                <form autocomplete=\"off\" action=\"#\" method=\"post\">\n                  <div class=\"row\">\n                    <div class=\"form-group col-md-6\">\n                      <label>Nome do remetente</label>\n                      <div class=\"input-group\">\n                        <div class=\"input-group-addon\">\n                          <i class=\"fa fa-user\"></i>\n                        </div>\n                        <input type=\"text\" class=\"form-control\" name=\"sender_name\" value=\"";
echo $email_settings["sender_name"];
echo "\" placeholder=\"Nome do remetente\">\n                      </div>\n                    </div>\n                    <div class=\"form-group col-md-6\">\n                      <label>Email do remetente</label>\n                      <div class=\"input-group\">\n                        <div class=\"input-group-addon\">\n                          <i class=\"fa fa-envelope\"></i>\n                        </div>\n                        <input type=\"text\" class=\"form-control\" name=\"sender_email\" value=\"";
echo $email_settings["sender_email"];
echo "\" placeholder=\"Email do remetente\">\n                      </div>\n                    </div>\n                    <div class=\"form-group col-md-12\">\n                      <label for=\"name\">Método de Envio</label>\n                      <div class=\"input-group\">\n                        <label>\n                          <input type=\"radio\" name=\"use_smtp\" class=\"flat-red use_smtp\" value=\"0\" ";
if ($email_settings["use_smtp"] == 0) {
    echo "checked";
}
echo ">\n                          Direto do Servidor\n                        </label>\n                      </div>\n                      <div class=\"input-group\">\n                        <label>\n                          <input type=\"radio\" name=\"use_smtp\" class=\"flat-red use_smtp\" value=\"1\" ";
if ($email_settings["use_smtp"] == 1) {
    echo "checked";
}
echo ">\n                          STMP Server\n                        </label>\n                      </div>\n                    </div>\n                    <div class=\"smtp_form\">\n                      <div class=\"form-group col-md-6\">\n                        <label for=\"smtp-server\">SMTP Server</label>\n                        <div class=\"input-group\">\n                          <span class=\"input-group-addon\"><i class=\"fa fa-server\"></i></span>\n                          <input type=\"text\" class=\"form-control\" id=\"smtp_server\" name=\"smtp_server\" value=\"";
echo $email_settings["smtp_server"];
echo "\" placeholder=\"SMTP Server\" autocomplete=\"off\" maxlength=\"255\" ";
if ($email_settings["use_smtp"] == 0) {
    echo "readonly";
}
echo ">\n                        </div>\n                      </div>\n                      <div class=\"form-group col-md-6\">\n                        <label for=\"smtp-port\">SMTP Port</label>\n                        <div class=\"input-group\">\n                          <span class=\"input-group-addon\"><i class=\"fa fa-plug\"></i></span>\n                          <input type=\"text\" class=\"form-control\" id=\"smtp_port\" name=\"smtp_port\" value=\"";
echo $email_settings["smtp_port"];
echo "\" placeholder=\"SMTP Port\" autocomplete=\"off\" ";
if ($email_settings["use_smtp"] == 0) {
    echo "readonly";
}
echo ">\n                        </div>\n                      </div>\n                      <div class=\"form-group col-md-6\">\n                        <label for=\"name\">Usuário</label>\n                        <div class=\"input-group\">\n                          <span class=\"input-group-addon\"><i class=\"fa fa-user\"></i></span>\n                          <input type=\"text\" class=\"form-control\" id=\"smtp_username\" name=\"smtp_username\" placeholder=\"SMTP Username\" value=\"";
echo $email_settings["smtp_username"];
echo "\" autocomplete=\"off\" maxlength=\"100\" ";
if ($email_settings["use_smtp"] == 0) {
    echo "readonly";
}
echo ">\n                        </div>\n                      </div>\n                      <div class=\"form-group col-md-6\">\n                        <label for=\"password\">Senha</label>\n                        <div class=\"input-group\">\n                          <span class=\"input-group-addon\"><i class=\"fa fa-key\"></i></span>\n                          <input type=\"password\" class=\"form-control\" id=\"smtp_password\" name=\"smtp_password\" placeholder=\"SMTP Password\" value=\"";
echo $email_settings["smtp_password"];
echo "\" autocomplete=\"off\" maxlength=\"100\" ";
if ($email_settings["use_smtp"] == 0) {
    echo "readonly";
}
echo ">\n                        </div>\n                      </div>\n                      <div class=\"form-group col-md-6\">\n                        <label for=\"name\">Método de Segurança</label>\n                        <div class=\"input-group\">\n                           <label>\n                              <input type=\"radio\" name=\"encryption_type\" class=\"flat-red\" value=\"\" ";
if ($email_settings["encryption_type"] === "") {
    echo "checked";
}
echo " ";
if ($email_settings["use_smtp"] == 0) {
    echo "readonly";
}
echo "> Não usar\n                          </label>\n                        </div>\n                        <div class=\"input-group\">\n                          <label>\n                            <input type=\"radio\" name=\"encryption_type\" class=\"flat-red\" value=\"TLS\" ";
if ($email_settings["encryption_type"] === "TLS") {
    echo "checked";
}
echo " ";
if ($email_settings["use_smtp"] == 0) {
    echo "readonly";
}
echo "> TLS\n                          </label>\n                        </div>\n                        <div class=\"input-group\">\n                          <label>\n                            <input type=\"radio\" name=\"encryption_type\" class=\"flat-red\" value=\"SSL\" ";
if ($email_settings["encryption_type"] === "SSL") {
    echo "checked";
}
echo " ";
if ($email_settings["use_smtp"] == 0) {
    echo "readonly";
}
echo "> SSL\n                          </label>\n                        </div>\n                      </div>\n                    </div>\n                    <div class=\"col-md-12\">\n                      <button type=\"submit\" name=\"save_email_settings\" class=\"btn btn-flat btn-success\">Salvar</button>\n                    </div>\n                  </div>\n                </form>\n              </div>\n              <!-- /.tab-pane -->\n              <div class=\"tab-pane\" id=\"tab_8\">\n                <form autocomplete=\"off\" action=\"#\" method=\"post\">\n                  <div class=\"row\">\n                    <div class=\"col-md-6\">\n                      <div class=\"form-group\">\n                        <label>Mensagem de teste automático</label>\n                        <div class=\"input-group\">\n                          <div class=\"input-group-addon\">\n                            <i class=\"fa fa-envelope\"></i>\n                          </div>\n                          <input type=\"text\" class=\"form-control\" name=\"auto_test_subject\" value=\"";
echo $email_messages["auto_test_subject"];
echo "\" placeholder=\"Assunto do email\">\n                        </div>\n                      </div>\n                      <div class=\"form-group\">\n                        <textarea id=\"auto_test_message\" name=\"auto_test_message\" class=\"form-control\">";
echo $email_messages["auto_test_message"];
echo "</textarea>\n                      </div>\n                    </div>\n\n                    <div class=\"col-md-6\">\n                      <div class=\"form-group\">\n                        <label>Mensagem de recuperação de senha</label>\n                        <div class=\"input-group\">\n                          <div class=\"input-group-addon\">\n                            <i class=\"fa fa-envelope\"></i>\n                          </div>\n                          <input type=\"text\" class=\"form-control\" name=\"pass_recovery_subject\" value=\"";
echo $email_messages["pass_recovery_subject"];
echo "\" placeholder=\"Assunto do email\">\n                        </div>\n                      </div>\n                      <div class=\"form-group\">\n                        <textarea id=\"pass_recovery_message\" name=\"pass_recovery_message\" class=\"form-control\">";
echo $email_messages["pass_recovery_message"];
echo "</textarea>\n                      </div>\n                    </div>\n                    <div class=\"col-md-12\">\n                      <button type=\"submit\" name=\"save_email_messages\" class=\"btn btn-flat btn-success\">Salvar</button>\n                    </div>\n                  </div>\n                </form>\n              </div>\n              <!-- /.tab-pane -->\n              <div class=\"tab-pane\" id=\"tab_9\">\n                <div class=\"box\">\n                  <div class=\"box-header with-border\">\n                    <h3 class=\"box-title\">Transferir revendedores</h3>\n                    <div class=\"box-tools pull-right\">\n                      <button type=\"button\" class=\"btn btn-box-tool\"><i class=\"fa fa-minus\"></i></button>\n                    </div>\n                  </div>\n                  <!-- /.box-header -->\n                  <div class=\"box-body\">\n                    <div class=\"row\">\n                      <div class=\"col-md-12\">\n                        <label>Selecione os revendedores que de deseja transferir</label>\n                        <form autocomplete=\"off\" action=\"#\" method=\"post\">\n                          <div class=\"form-group\">\n                            <select multiple id=\"selected_resellers\" name=\"selected_resellers[]\" class=\"form-control\" style=\"height: 200px;\">\n                              ";
$all_users = getAllUsers();
foreach ($all_users as $user) {
    $owner_name = "-";
    if ($user["owner_id"]) {
        $user_key = array_search($user["owner_id"], array_column($all_users, "id"));
        if ($user_key !== false) {
            $owner_name = $all_users[$user_key]["username"];
        }
    }
    echo "                                  <option value=\"";
    echo $user["id"];
    echo "\">";
    echo $user["username"] . " (" . $owner_name . ")";
    echo "</option>\n                              ";
}
echo "                            </select>\n                          </div>\n                          <div class=\"form-group\">\n                            <label>Selecione o novo dono</label>\n                            <select id=\"new_owner\" name=\"new_owner\" class=\"form-control\">\n                              ";
$all_users = getAllUsers();
foreach ($all_users as $user) {
    echo "                                  <option value=\"";
    echo $user["id"];
    echo "\">";
    echo $user["username"];
    echo "</option>\n                              ";
}
echo "                            </select>\n                          </div>\n                          <div class=\"form-group\">\n                            <label>Alterar grupo</label>\n                            <select id=\"new_group_name\" name=\"new_group_name\" class=\"form-control\">\n                              ";
$group_settings = json_decode(getServerProperty("group_settings"), true);
$ultra_group = getGroupByID($group_settings["ultra"]);
$master_group = getGroupByID($group_settings["master"]);
$reseller_group = getGroupByID($group_settings["reseller"]);
echo "<option value=''>*Não alterar o grupo dos revendedores</option>";
echo "<option value='ultra'>" . $ultra_group["group_name"] . "</option>";
echo "<option value='master'>" . $master_group["group_name"] . "</option>";
echo "<option value='reseller'>" . $reseller_group["group_name"] . "</option>";
echo "                            </select>\n                          </div>\n                          <button type=\"submit\" name=\"change_resellers\" class=\"btn btn-flat btn-success\">Salvar</button>\n                        </form>\n                      </div>\n                    </div>\n                  </div>\n                </div>\n                <!-- /.box-body -->\n              </div>\n              <!-- /.tab-pane -->\n            </div>\n            <!-- /.tab-content -->\n          </div>\n          <!-- nav-tabs-custom -->\n        </div>\n        <!-- /.col -->\n      </div>\n      <!-- /.box -->\n    </section>\n    <!-- /.content -->\n  </div>\n  <!-- /.content-wrapper -->\n  <footer class=\"main-footer\">\n    <div class=\"row\">\n      <div class=\"text-left col-md-6\">\n        <strong>Copyright &copy; ";
echo date("Y");
echo " <a href=\"#\">";
echo $server_name;
echo "</a>.</strong> All rights reserved.\n      </div>\n      <div class=\"text-right col-md-6\">Painel Office. ";
echo KOFFICE_PANEL_VERSION;
echo " - www.paineloffice.top</div>\n    </div>\n  </footer>\n  <!-- Add the sidebar's background. This div must be placed\n       immediately after the control sidebar -->\n  <div class=\"control-sidebar-bg\"></div>\n</div>\n<!-- ./wrapper -->\n\n<!-- jQuery 3 -->\n<script src=\"bower_components/jquery/dist/jquery.min.js\"></script>\n<!-- jQuery UI 1.11.4 -->\n<script src=\"bower_components/jquery-ui/jquery-ui.min.js\"></script>\n<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->\n<script>\n  \$.widget.bridge('uibutton', \$.ui.button);\n</script>\n<!-- Bootstrap 3.3.7 -->\n<script src=\"bower_components/bootstrap/dist/js/bootstrap.min.js\"></script>\n<!-- Morris.js charts -->\n<script src=\"bower_components/raphael/raphael.min.js\"></script>\n<script src=\"bower_components/morris.js/morris.min.js\"></script>\n<!-- FastClick -->\n<script src=\"bower_components/fastclick/lib/fastclick.js\"></script>\n<!-- iCheck 1.0.1 -->\n<script src=\"./plugins/iCheck/icheck.min.js\"></script>\n<!-- bootstrap datepicker -->\n<script src=\"bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js\"></script>\n<!-- AdminLTE App -->\n<script src=\"dist/js/adminlte.min.js\"></script>\n<!-- AdminLTE dashboard demo (This is only for demo purposes) -->\n<script src=\"dist/js/pages/dashboard.js\"></script>\n<!-- AdminLTE for demo purposes -->\n<script src=\"dist/js/demo.js\"></script>\n<!-- CK Editor -->\n<script src=\"bower_components/ckeditor/ckeditor.js\"></script>\n<script type=\"text/javascript\">\n  \$(\".alert\").delay(3000).slideUp(200, function() {\n    \$(this).alert('close');\n  });\n\n  \$('input[type=\"checkbox\"].flat-red, input[type=\"radio\"].flat-red').iCheck({\n    checkboxClass: 'icheckbox_flat-green',\n    radioClass   : 'iradio_flat-green'\n  });\n\n  \$('.use_smtp').on('ifChecked', function(event){\n    if(\$('.use_smtp:checked').val() === '0'){\n      \$(\".smtp_form :input\").attr(\"readonly\", true);\n    } else {\n      \$(\".smtp_form :input\").removeAttr(\"readonly\");\n    }\n  });\n\n  /*\$('#checkbox_id').on('ifUnchecked', function () ({\n\n  });*/\n  CKEDITOR.config.defaultLanguage = 'pt-br';\n  CKEDITOR.config.language = 'pt-br';\n\n  CKEDITOR.replace('fixed_informations');\n\n  CKEDITOR.replace('auto_test_message');\n  CKEDITOR.replace('pass_recovery_message');\n\n  \$(\"#datepicker\").datepicker({\n    multidate: true,\n    showOtherMonths: true,\n    selectOtherMonths: true\n  });\n</script>\n</body>\n</html>\n";

?>