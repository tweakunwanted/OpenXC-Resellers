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
$fast_packages = json_decode(getServerProperty("fast_packages"), true);
if (!isset($_GET["ticket_id"])) {
    exit;
}
$ticket_id = intval($_GET["ticket_id"]);
$ticket = getTicketByID($ticket_id);
if (!$ticket) {
    exit;
}
if ($ticket["member_id"] !== $logged_user["id"] && !isAdmin($logged_user)) {
    exit;
}
if (isset($_POST["send_message"]) && isset($_POST["message"]) && $ticket["status"]) {
    $message = $_POST["message"];
    $admin_reply_ = isAdmin($logged_user) ? 1 : 0;
    insertTicketReply($ticket_id, $admin_reply_, $message);
    $other_person = isAdmin($logged_user) ? "user" : "admin";
    updateReadTicket($ticket_id, $other_person, 0);
    header("location: ?ticket_id=" . $ticket_id . "&result=success");
    exit;
}
$reseller = getUserById($ticket["member_id"]);
$reseller_name = $reseller ? $reseller["username"] : "Desconhecido(Não encontrado)";
$ticket_replies = getTicketReplies($ticket_id);
$person = isAdmin($logged_user) ? "admin" : "user";
updateReadTicket($ticket_id, $person, 1);
echo "<!DOCTYPE html>\n<html>\n<head>\n  <meta charset=\"utf-8\">\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n  <title>";
echo $server_name;
echo " :: Office</title>\n  <!-- Tell the browser to be responsive to screen width -->\n  <meta content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" name=\"viewport\">\n  <!-- Bootstrap 3.3.7 -->\n  <link rel=\"stylesheet\" href=\"bower_components/bootstrap/dist/css/bootstrap.min.css\">\n  <!-- DataTables -->\n  <link rel=\"stylesheet\" href=\"bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css\">\n  <!-- Font Awesome -->\n  <link rel=\"stylesheet\" href=\"bower_components/font-awesome/css/font-awesome.min.css\">\n  <!-- Theme style -->\n  <link rel=\"stylesheet\" href=\"dist/css/AdminLTE.min.css\">\n  <!-- AdminLTE Skins. Choose a skin from the css/skins\n       folder instead of downloading all of them to reduce the load. -->\n  <link rel=\"stylesheet\" href=\"dist/css/skins/_all-skins.min.css\">\n  <!-- Morris chart -->\n  <link rel=\"stylesheet\" href=\"bower_components/morris.js/morris.css\">\n  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->\n  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->\n  <!--[if lt IE 9]>\n  <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>\n  <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>\n  <![endif]-->\n\n  <!-- Google Font -->\n  <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic\">\n\n  <style type=\"text/css\">\n    .direct-chat-name {\n      font-size: 14px;\n      text-transform: uppercase;\n    }\n\n    .direct-chat-text {\n      padding: 10px;\n      margin: 0 !important;\n      word-break: break-all;\n    }\n\n    .direct-chat-text:after, .direct-chat-text:before {\n      display: none;\n    }\n\n    .direct-chat-primary .right>.direct-chat-text {\n      text-align: right;\n    }\n  </style>\n  ";
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
echo "        <li class=\"treeview\">\n          <a href=\"#\">\n            <i class=\"fa fa-users\"></i>\n            <span>Usuários</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"online.php\"><i class=\"fa fa-circle\"></i> Usuários Online</a></li>\n            <li><a href=\"clients.php\"><i class=\"fa fa-cogs\"></i> Gerir Usuários</a></li>\n            <li><a href=\"create_client.php\"><i class=\"fa fa-user-plus\"></i> Criar Usuário</a></li>\n          </ul>\n        </li>\n        <li>\n          <a href=\"shortener.php\">\n            <i class=\"fa fa-link\"></i> <span>Encurtador</span>\n          </a>\n        </li>\n        <li>\n          <a href=\"tools.php\">\n            <i class=\"fa fa-wrench\"></i> <span>Ferramentas</span>\n          </a>\n        </li>\n        <li class=\"treeview\">\n          <a href=\"#\">\n            <i class=\"fa fa-film\"></i>\n            <span>Conteúdo Novo</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"new_channels.php\"><i class=\"fa fa-circle-o\"></i> Novos Canais</a></li>\n            <li><a href=\"new_movies.php\"><i class=\"fa fa-circle-o\"></i> Novos Filmes</a></li>\n            <li><a href=\"new_series.php\"><i class=\"fa fa-circle-o\"></i> Novas Series</a></li>\n          </ul>\n        </li>\n        <li class=\"treeview active\">\n          <a href=\"#\">\n            <i class=\"fa fa-ticket\"></i>\n            <span>Ticket Suporte</span>\n            <span class=\"pull-right-container\">\n              <i class=\"fa fa-angle-left pull-right\"></i>\n            </span>\n          </a>\n          <ul class=\"treeview-menu\">\n            <li><a href=\"create_ticket.php\"><i class=\"fa fa-circle-o\"></i> Criar Ticket</a></li>\n            <li class=\"active\"><a href=\"manage_tickets.php\"><i class=\"fa fa-circle-o\"></i> Gerenciar Tickets</a></li>\n          </ul>\n        </li>\n        <li>\n          <a href=\"profile.php\">\n            <i class=\"fa fa-user-circle\"></i> <span>Perfil</span>\n          </a>\n        </li>\n        <li>\n          <a href=\"logout.php\">\n            <i class=\"fa fa-power-off\"></i> <span>Desconectar</span>\n          </a>\n        </li>\n      </ul>\n    </section>\n    <!-- /.sidebar -->\n  </aside>\n\n  <!-- Content Wrapper. Contains page content -->\n  <div class=\"content-wrapper\">\n    <!-- Content Header (Page header) -->\n    <section class=\"content-header\">\n      <h1>\n        Ticket\n      </h1>\n      <ol class=\"breadcrumb\">\n        <li><a href=\"dashboard.php\"><i class=\"fa fa-dashboard\"></i> Painel</a></li>\n        <li class=\"active\">Gerenciar Tickets</li>\n      </ol>\n    </section>\n    <!-- Main content -->\n    <section class=\"content\">\n      <!-- Main row -->\n      <div class=\"row\">\n        <!-- Left col -->\n        <section class=\"col-md-12\">\n          ";
if (isset($_GET["result"])) {
    $result = $_GET["result"];
    $result_message = "Aconteceu um problema, tente novamente mais tarde!";
    $result_type = "warning";
    switch ($result) {
        case "success":
            $result_message = "O ticket foi enviado com sucesso.";
            $result_type = "success";
            break;
    }
    echo "            <div class=\"alert alert-";
    echo $result_type;
    echo " alert-dismissible\">\n              <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button>\n              <i class=\"icon fa fa-check\"></i>\n              ";
    echo $result_message;
    echo "            </div>\n          ";
}
echo "          <!-- DIRECT CHAT PRIMARY -->\n          <div class=\"box direct-chat direct-chat-primary\">\n            <div class=\"box-header with-border\">\n              <h3 class=\"box-title\">Ticket: ";
echo $ticket["title"];
echo "</h3>\n            </div>\n            <!-- /.box-header -->\n            <div class=\"box-body\">\n              <!-- Conversations are loaded here -->\n              <div class=\"direct-chat-messages\">\n                ";
foreach ($ticket_replies as $current_reply) {
    $is_owner = isAdmin($logged_user) == $current_reply["admin_reply"];
    echo "                <!-- direct-chat-msg -->\n                <div class=\"direct-chat-msg ";
    if ($is_owner) {
        echo "right";
    }
    echo "\">\n                  <div class=\"direct-chat-info clearfix\">\n                    <span class=\"direct-chat-name ";
    echo $is_owner ? "pull-right" : "pull-left";
    echo "\">";
    echo $current_reply["admin_reply"] ? "Admin" : $reseller_name;
    echo "</span>\n                    <span class=\"direct-chat-timestamp ";
    echo $is_owner ? "pull-left" : "pull-right";
    echo "\">";
    echo date("d/m/Y H:i:s", $current_reply["date"]);
    echo "</span>\n                  </div>\n                  <!-- /.direct-chat-info -->\n                  <div class=\"direct-chat-text\">\n                    ";
    echo $current_reply["message"];
    echo "                  </div>\n                  <!-- /.direct-chat-text -->\n                </div>\n                <!-- /.direct-chat-msg -->\n\n                ";
}
echo "              </div>\n              <!--/.direct-chat-messages-->\n            </div>\n            <!-- /.box-body -->\n            <div class=\"box-footer\">\n              <form action=\"#\" method=\"post\">\n                <div class=\"input-group\">\n                  <input type=\"text\" name=\"message\" placeholder=\"Digite sua mensagem aqui ...\" class=\"form-control\" ";
if (!$ticket["status"]) {
    echo "disabled";
}
echo ">\n                  <span class=\"input-group-btn\">\n                    <button type=\"submit\" name=\"send_message\" class=\"btn btn-success btn-flat\" ";
if (!$ticket["status"]) {
    echo "disabled";
}
echo ">Enviar</button>\n                  </span>\n                </div>\n              </form>\n            </div>\n            <!-- /.box-footer-->\n          </div>\n          <!--/.direct-chat -->\n        </section>\n      </div>\n      <!-- /.box -->\n    </section>\n    <!-- /.content -->\n  </div>\n  <!-- /.content-wrapper -->\n  <footer class=\"main-footer\">\n    <div class=\"row\">\n      <div class=\"text-left col-md-6\">\n        <strong>Copyright &copy; ";
echo date("Y");
echo " <a href=\"#\">";
echo $server_name;
echo "</a>.</strong> All rights reserved.\n      </div>\n      <div class=\"text-right col-md-6\">Painel Office. ";
echo KOFFICE_PANEL_VERSION;
echo " - www.paineloffice.top</div>\n    </div>\n  </footer>\n  <!-- Add the sidebar's background. This div must be placed\n       immediately after the control sidebar -->\n  <div class=\"control-sidebar-bg\"></div>\n</div>\n<!-- ./wrapper -->\n\n<!-- jQuery 3 -->\n<script src=\"bower_components/jquery/dist/jquery.min.js\"></script>\n<!-- jQuery UI 1.11.4 -->\n<script src=\"bower_components/jquery-ui/jquery-ui.min.js\"></script>\n<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->\n<script>\n  \$.widget.bridge('uibutton', \$.ui.button);\n</script>\n<!-- Bootstrap 3.3.7 -->\n<script src=\"bower_components/bootstrap/dist/js/bootstrap.min.js\"></script>\n<!-- Bootbox -->\n<script src=\"bower_components/bootbox.min.js\"></script>\n<!-- Morris.js charts -->\n<script src=\"bower_components/raphael/raphael.min.js\"></script>\n<script src=\"bower_components/morris.js/morris.min.js\"></script>\n<!-- FastClick -->\n<script src=\"bower_components/fastclick/lib/fastclick.js\"></script>\n<!-- DataTables -->\n<script src=\"bower_components/datatables.net/js/jquery.dataTables.min.js\"></script>\n<script src=\"bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js\"></script>\n<!-- AdminLTE App -->\n<script src=\"dist/js/adminlte.min.js\"></script>\n<!-- AdminLTE dashboard demo (This is only for demo purposes) -->\n<script src=\"dist/js/pages/dashboard.js\"></script>\n<!-- AdminLTE for demo purposes -->\n<script src=\"dist/js/demo.js\"></script>\n<script type=\"text/javascript\">\n    \$(\".alert\").delay(3000).slideUp(200, function() {\n      \$(this).alert('close');\n    });\n  </script>\n</body>\n</html>\n";

?>