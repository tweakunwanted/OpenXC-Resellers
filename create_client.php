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
$allowed_bouquets = json_decode(getServerProperty("allowed_bouquets"), true);
$fast_packages = json_decode(getServerProperty("fast_packages"), true);
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["reseller_notes"]) && isset($_POST["bouquet"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $reseller_notes = $_POST["reseller_notes"];
    $bouquet = $_POST["bouquet"];
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $send_email = isset($_POST["send_email"]);
    if (is_array($bouquet)) {
        foreach ($bouquet as $a => $b) {
            if (!in_array($b, $allowed_bouquets)) {
                header("location: ?result=invalid_bouquet");
                exit;
            }
        }
        if (strlen($username) < 6 || 255 < strlen($username)) {
            header("location: ?result=invalid_username");
            exit;
        }
        if (strlen($password) < 6 || 255 < strlen($password)) {
            header("location: ?result=invalid_password");
            exit;
        }
        if (500 < strlen($reseller_notes)) {
            header("location: ?result=invalid_notes");
            exit;
        }
        $bouquet = json_encode($bouquet);
        if ($send_email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("location: ?key=" . $key . "&result=invalid_email");
            exit;
        }
        if (addOrRemoveCredits($logged_user["id"], -1)) {
            if (createClient($logged_user["id"], $username, $password, "1 months", $bouquet, $reseller_notes, 0)) {
                $old_credits = $logged_user["credits"];
                $logged_user = getLoggedUser();
                $now_credits = $logged_user["credits"];
                insertRegUserLog($logged_user["id"], $username, $password, "[<b>UserPanel</b> -> <u>New Line</u>] with Package [Custom Package], Credits: <font color=\"green\">" . $old_credits . "</font> -> <font color=\"red\">" . $now_credits . "</font>");
                if ($send_email) {
                    $list_link = GetList($username, $password);
                    $email_messages = json_decode(getServerProperty("email_messages"), true);
                    $whatsapp = getUserProperty($logged_user["id"], "whatsapp");
                    $telegram = getUserProperty($logged_user["id"], "telegram");
                    $auto_test_subject = str_replace(array("{USERNAME}", "{PASSWORD}", "{SERVER_NAME}"), array($username, $password, $server_name), $email_messages["auto_test_subject"]);
                    $auto_test_message = str_replace(array("{USERNAME}", "{PASSWORD}", "{LIST_LINK}", "{SERVER_NAME}", "{RESELLER_EMAIL}", "{WHATSAPP}", "{TELEGRAM}"), array($username, $password, $list_link, $server_name, $logged_user["email"], $whatsapp, $telegram), $email_messages["auto_test_message"]);
                    if (smtpmailer($email, $auto_test_subject, $auto_test_message)) {
                        header("location: ?key=" . $key . "&result=success");
                        exit;
                    }
                    header("location: ?result=cant_send_email");
                    exit;
                }
                header("location: ?result=success");
                exit;
            }
            addOrRemoveCredits($logged_user["id"], 1);
            header("location: ?result=exist_client");
            exit;
        }
        header("location: ?result=insufficient_credits");
        exit;
    }
}
echo "<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\"utf-8\">\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n  <title>";
echo $server_name;
echo " :: Office</title>\n  <!-- Tell the browser to be responsive to screen width -->\n  <meta content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" name=\"viewport\">\n  <!-- Bootstrap 3.3.7 -->\n  <link rel=\"stylesheet\" href=\"bower_components/bootstrap/dist/css/bootstrap.min.css\">\n  <!-- Font Awesome -->\n  <link rel=\"stylesheet\" href=\"bower_components/font-awesome/css/font-awesome.min.css\">\n  <!-- Theme style -->\n  <link rel=\"stylesheet\" href=\"dist/css/AdminLTE.min.css\">\n  <!-- AdminLTE Skins. Choose a skin from the css/skins\n       folder instead of downloading all of them to reduce the load. -->\n  <link rel=\"stylesheet\" href=\"dist/css/skins/_all-skins.min.css\">\n  <!-- iCheck for checkboxes and radio inputs -->\n  <link rel=\"stylesheet\" href=\"./plugins/iCheck/all.css\">\n  <!-- Morris chart -->\n  <link rel=\"stylesheet\" href=\"bower_components/morris.js/morris.css\">\n  <!-- MultiSelect -->\n  <link rel=\"stylesheet\" href=\"bower_components/multiselect/css/multi-select.css\">\n  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->\n  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->\n  <!--[if lt IE 9]>\n  <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>\n  <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>\n  <![endif]-->\n\n  <!-- Google Font -->\n  <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic\">\n  ";
injectCustomCss();
echo "</head>\n<body class=\"hold-transition ";
echo getServerProperty("theme_color", "skin-red");
echo " sidebar-mini\">\n<div class=\"wrapper\">\n  <header class=\"main-header\">\n    <!-- Logo -->\n    <a href=\"dashboard.php\" class=\"logo\">\n      <!-- mini logo for sidebar mini 50x50 pixels -->\n      <span class=\"logo-mini\"><img src=\"dist/img/logo_small.png\" width=\"50\" height=\"50\"></span>\n      <!-- logo for regular state and mobile devices -->\n      <span class=\"logo-lg\"><img src=\"dist/img/logo_medium.png\" height=\"50\"></span>\n    </a>\n    <!-- Header Navbar: style can be found in header.less -->\n    <nav class=\"navbar navbar-static-top\">\n      <!-- Sidebar toggle button-->\n      <a href=\"#\" class=\"sidebar-toggle\" data-toggle=\"push-menu\" role=\"button\">\n        <span class=\"sr-only\">Toggle navigation</span>\n      </a>\n\n      <div class=\"navbar-custom-menu\">\n        <ul class=\"nav navbar-nav\">\n          <!-- User Credits -->\n          <li class=\"dropdown messages-menu\">\n            <a href=\"#\" class=\"dropdown-toggle\">\n              <i class=\"fa fa-dollar\"></i>\n              <span class=\"label label-success\">";
echo $logged_user["credits"];
echo "</span>\n            </a>\n          </li>\n          <!-- Control Sidebar Toggle Button -->\n        ";
if (isAdmin($logged_user)) {
    echo "          <li>\n            <a href=\"settings.php\"><i class=\"fa fa-gears\"></i></a>\n          </li>\n        ";
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
echo "        <li class=\"treeview active\">\n          <a href=\"#\">\n            <i class=\"fa fa-users\"></i>\n            <span>Usuários</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"online.php\"><i class=\"fa fa-circle\"></i> Usuários Online</a></li>\n            <li><a href=\"clients.php\"><i class=\"fa fa-cogs\"></i> Gerir Usuários</a></li>\n            <li class=\"active\"><a href=\"create_client.php\"><i class=\"fa fa-user-plus\"></i> Criar Usuário</a></li>\n          </ul>\n        </li>\n        <li>\n          <a href=\"shortener.php\">\n            <i class=\"fa fa-link\"></i> <span>Encurtador</span>\n          </a>\n        </li>\n        <li>\n          <a href=\"tools.php\">\n            <i class=\"fa fa-wrench\"></i> <span>Ferramentas</span>\n          </a>\n        </li>\n        <li class=\"treeview\">\n          <a href=\"#\">\n            <i class=\"fa fa-film\"></i>\n            <span>Conteúdo Novo</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"new_channels.php\"><i class=\"fa fa-circle-o\"></i> Novos Canais</a></li>\n            <li><a href=\"new_movies.php\"><i class=\"fa fa-circle-o\"></i> Novos Filmes</a></li>\n            <li><a href=\"new_series.php\"><i class=\"fa fa-circle-o\"></i> Novas Series</a></li>\n          </ul>\n        </li>\n        <li class=\"treeview\">\n          <a href=\"#\">\n            <i class=\"fa fa-ticket\"></i>\n            <span>Ticket Suporte</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"create_ticket.php\"><i class=\"fa fa-circle-o\"></i> Criar Ticket</a></li>\n            <li><a href=\"manage_tickets.php\"><i class=\"fa fa-circle-o\"></i> Gerenciar Tickets</a></li>\n          </ul>\n        </li>\n        <li>\n          <a href=\"profile.php\">\n            <i class=\"fa fa-user-circle\"></i> <span>Perfil</span>\n          </a>\n        </li>\n        <li>\n          <a href=\"logout.php\">\n            <i class=\"fa fa-power-off\"></i> <span>Desconectar</span>\n          </a>\n        </li>\n      </ul>\n    </section>\n    <!-- /.sidebar -->\n  </aside>\n\n  <!-- Content Wrapper. Contains page content -->\n  <div class=\"content-wrapper\">\n    <!-- Content Header (Page header) -->\n    <section class=\"content-header\">\n      <h1>\n        Criar usuário\n        <small>A criação do login oficial, tem o custo de 1 crédito e duração de 30 dias.</small>\n      </h1>\n      <ol class=\"breadcrumb\">\n        <li><a href=\"dashboard.php\"><i class=\"fa fa-dashboard\"></i> Painel</a></li>\n        <li class=\"active\">Criar usuário</li>\n      </ol>\n    </section>\n    <!-- Main content -->\n    <section class=\"content\">\n      <!-- Main row -->\n      <div class=\"row\">\n        <!-- Left col -->\n        <section class=\"col-md-12 connectedSortable\">\n          ";
if (isset($_GET["result"])) {
    $result = $_GET["result"];
    $result_message = "Aconteceu um problema, tente novamente mais tarde!";
    $result_type = "warning";
    switch ($result) {
        case "success":
            $result_message = "O usuário foi criado com sucesso.";
            $result_type = "success";
            break;
        case "invalid_username":
            $result_message = "O nome de usuário escolhido é invalido!, deve ter no mínimo 6 caracteres.";
            break;
        case "invalid_password":
            $result_message = "A senha escolhida é invalida!, deve ter no mínimo 6 caracteres.";
            break;
        case "invalid_notes":
            $result_message = "A observação escolhida é invalida!, deve ter no máximo 500 caracteres.";
            break;
        case "invalid_bouquet":
            $result_message = "Os pacotes escolhidos são inválidos.";
            break;
        case "invalid_email":
            $result_message = "O e-mail escolhido é invalido!";
            break;
        case "insufficient_credits":
            $result_message = "Você não tem créditos suficiente, recarregue seu painel.";
            break;
        case "exist_client":
            $result_message = "Já existe um usuário com este nome de usuário, escolha outro.";
            break;
        case "cant_send_email":
            $result_message = "O usuário foi criado com sucesso, mas não foi possível enviar o e-mail com os dados de acesso.";
            break;
    }
    echo "            <div class=\"alert alert-";
    echo $result_type;
    echo " alert-dismissible\">\n              <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>\n              <i class=\"icon fa fa-check\"></i>\n              ";
    echo $result_message;
    echo "            </div>\n          ";
}
echo "          <div class=\"box\">\n            <div class=\"box-body\">\n              <div class=\"row\">\n                <div class=\"col-md-6\">\n                  <form autocomplete=\"off\" action=\"#\" method=\"post\" name=\"frm1\">\n                    <div class=\"row\">\n                      <div class=\"form-group col-md-6\">\n                        <label class=\"form-control-label\">Login</label>\n                        <div class=\"input-group\">\n                          <span class=\"input-group-addon\"><i class=\"fa fa-user\"></i></span>\n                          <input type=\"text\" class=\"form-control\" required=\"\" placeholder=\"Login\" autocomplete=\"off\" name=\"username\" data-minlength=\"6\" minlength=\"6\">\n                        </div>\n                        <span class=\"text-help\">Minimo 6 caracteres</span>\n                      </div>\n                      <div class=\"form-group col-md-6\">\n                        <label class=\"form-control-label\">Senha</label>\n                        <div class=\"input-group\">\n                          <span class=\"input-group-addon\"><i class=\"fa fa-key\"></i></span>\n                          <input type=\"text\" class=\"form-control\" required=\"\" placeholder=\"Senha\" autocomplete=\"off\" value=\"\" name=\"password\" data-minlength=\"6\" minlength=\"6\">\n                        </div>\n                        <span class=\"text-help\">Minimo 6 caracteres</span>\n                      </div>\n                    </div>\n                    <div class=\"form-group\">\n                      <label class=\"form-control-label\">E-mail</label>\n                      <div class=\"input-group\">\n                          <span class=\"input-group-addon\"><i class=\"fa fa-envelope\"></i></span>\n                          <input type=\"email\" class=\"form-control\" placeholder=\"E-mail do usuário\" autocomplete=\"off\" value=\"\" name=\"email\">\n                        </div>\n                      <label>\n                      <input type=\"checkbox\" class=\"flat-red\" name=\"send_email\" value=\"\">\n                        Enviar dados do usuário por e-mail\n                      </label>\n                    </div>\n                    <div class=\"form-group\">\n                      <label class=\"form-control-label\">Observações</label>\n                      <textarea class=\"form-control\" placeholder=\"Adicione informações relevantes\" name=\"reseller_notes\"></textarea>\n                    </div>\n                    <div class=\"form-group\">\n                      <label class=\"form-control-label\">Definir Pacotes <span class=\"text-danger\"> *Selecione apenas os pacotes desejados.</span></label>\n                      <div class=\"row\">\n                        <div class=\"col-md-12\">\n                          <select name=\"bouquet[]\" id=\"multiselect\" class=\"form-control\" size=\"10\" multiple=\"multiple\" required=\"\" style=\"overflow: auto\" title=\"Pacotes\">\n                            ";
$bouquets = getBouquets();
foreach ($bouquets as $bouquet) {
    if (in_array($bouquet["id"], $allowed_bouquets)) {
        echo "                                  <option value=\"";
        echo $bouquet["id"];
        echo "\">";
        echo $bouquet["bouquet_name"];
        echo "</option>\n                                ";
    }
}
echo "                          </select>\n                        </div>\n                      </div>\n                    </div>\n                    <div class=\"form-group\">\n                      <button type=\"submit\" class=\"btn btn-success\">Criar Usuário</button>\n                    </div>\n                  </form>\n                </div>\n              </div>\n            </div>\n            <!-- /.box-body -->\n          </div>\n        </section>\n      </div>\n      <!-- /.box -->\n    </section>\n    <!-- /.content -->\n  </div>\n  <!-- /.content-wrapper -->\n  <footer class=\"main-footer\">\n    <div class=\"row\">\n      <div class=\"text-left col-md-6\">\n        <strong>Copyright &copy; ";
echo date("Y");
echo " <a href=\"#\">";
echo $server_name;
echo "</a>.</strong> All rights reserved.\n      </div>\n      <div class=\"text-right col-md-6\">Painel Office. ";
echo KOFFICE_PANEL_VERSION;
echo " - www.paineloffice.top</div>\n    </div>\n  </footer>\n  <!-- Add the sidebar's background. This div must be placed\n       immediately after the control sidebar -->\n  <div class=\"control-sidebar-bg\"></div>\n</div>\n<!-- ./wrapper -->\n\n<!-- jQuery 3 -->\n<script src=\"bower_components/jquery/dist/jquery.min.js\"></script>\n<!-- jQuery UI 1.11.4 -->\n<script src=\"bower_components/jquery-ui/jquery-ui.min.js\"></script>\n<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->\n<script>\n    \$.widget.bridge('uibutton', \$.ui.button);\n</script>\n<!-- Bootstrap 3.3.7 -->\n<script src=\"bower_components/bootstrap/dist/js/bootstrap.min.js\"></script>\n<!-- Morris.js charts -->\n<script src=\"bower_components/raphael/raphael.min.js\"></script>\n<script src=\"bower_components/morris.js/morris.min.js\"></script>\n<!-- FastClick -->\n<script src=\"bower_components/fastclick/lib/fastclick.js\"></script>\n<!-- iCheck 1.0.1 -->\n<script src=\"./plugins/iCheck/icheck.min.js\"></script>\n<!-- MultiSelect -->\n<script src=\"bower_components/multiselect/js/jquery.multi-select.js\"></script>\n<!-- AdminLTE App -->\n<script src=\"dist/js/adminlte.min.js\"></script>\n<!-- AdminLTE dashboard demo (This is only for demo purposes) -->\n<script src=\"dist/js/pages/dashboard.js\"></script>\n<!-- AdminLTE for demo purposes -->\n<script src=\"dist/js/demo.js\"></script>\n\n<script type=\"text/javascript\">\n    \$('#multiselect').multiSelect({\n      sort: false,\n      keepOrder: true\n    });\n    \$('input[type=\"checkbox\"].flat-red, input[type=\"radio\"].flat-red').iCheck({\n        checkboxClass: 'icheckbox_flat-green',\n        radioClass   : 'iradio_flat-green'\n    });\n    \$(\".alert\").delay(3000).slideUp(200, function() {\n      \$(this).alert('close');\n    });\n</script>\n</body>\n</html>";

?>