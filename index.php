<?php

include_once "./sys/config.php";


include_once "./sys/functions.php";


$server_name = getServerProperty("server_name");
startSession();
if (isset($_SESSION["__l0gg3d_us3r__"])) {
    header("Location: ./dashboard.php");
    exit;
}
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $result = loginUser($username, $password);
    switch ($result) {
        case 1:
            header("location: ./dashboard.php");
            exit;
        case 2:
            header("location: ?result=cant_connect");
            exit;
        case 3:
            header("location: ?result=invalid_user_or_pass");
            exit;
        case 4:
            header("location: ?result=blocked");
            exit;
        case 5:
            header("location: ?result=insufficient_permission");
            exit;
    }
}
echo "<!DOCTYPE html>\n<html>\n  <head>\n    <meta charset=\"utf-8\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <title>";
echo $server_name;
echo " :: Office</title>\n    <!-- Tell the browser to be responsive to screen width -->\n    <meta content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" name=\"viewport\">\n    <!-- Bootstrap 3.3.7 -->\n    <link rel=\"stylesheet\" href=\"bower_components/bootstrap/dist/css/bootstrap.min.css\">\n    <!-- Font Awesome -->\n    <link rel=\"stylesheet\" href=\"bower_components/font-awesome/css/font-awesome.min.css\">\n    <!-- Theme style -->\n    <link rel=\"stylesheet\" href=\"dist/css/AdminLTE.min.css\">\n    <!-- iCheck -->\n    <link rel=\"stylesheet\" href=\"plugins/iCheck/square/blue.css\">\n\n    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->\n    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->\n    <!--[if lt IE 9]>\n    <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>\n    <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>\n    <![endif]-->\n\n    <!-- Google Font -->\n    <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic\">\n    ";
injectCustomCss();
echo "</head>\n  <body class=\"hold-transition login-page\" style=\"background-image: url('./dist/img/login_background.png'); background-attachment: fixed; background-repeat: no-repeat; background-size: cover;\">\n    <div class=\"container\">\n      <div class=\"login-box\">\n        ";
if (isset($_GET["result"])) {
    $result = $_GET["result"];
    $result_message = "Aconteceu um problema, tente novamente mais tarde!";
    $result_type = "warning";
    switch ($result) {
        case "cant_connect":
            $result_message = "Não é possível se conectar agora, tente novamente em alguns minutos!";
            break;
        case "invalid_user_or_pass":
            $result_message = "Usuário ou/e senha incorreto(s).";
            break;
        case "blocked":
            $result_type = "danger";
            $result_message = "Usuário bloqueado, contacte seu revendedor.";
            break;
        case "insufficient_permission":
            $result_type = "danger";
            $result_message = "Você não tem permissão para acessar o painel office!";
            break;
        case "password_changed":
            $result_type = "success";
            $result_message = "Senha alterada com sucesso, conecte-se.";
            break;
    }
    echo "            <div class=\"callout callout-";
    echo $result_type;
    echo "\">\n              ";
    echo $result_message;
    echo "            </div>\n            ";
}
echo "        <div class=\"login-box-body\" style=\"border-radius: 4px;\">\n          <div class=\"login-logo\">\n            <a href=\"index.php\"><img src=\"dist/img/logo_giant.png\" style=\"max-width: 100%;\"></a>\n          </div>\n          <!-- /.login-logo -->\n          <form action=\"index.php\" method=\"post\">\n            <div class=\"form-group has-feedback\">\n              <label for=\"username\">Usuário</label>\n              <input type=\"text\" class=\"form-control\" style=\"border: none; border-bottom: 1px solid #ccc;\" name=\"username\" placeholder=\"Digite seu usuário\" maxlength=\"255\">\n              <span class=\"fa fa-user form-control-feedback\"></span>\n            </div>\n            <div class=\"form-group has-feedback\">\n              <label for=\"password\">Senha</label>\n              <input type=\"password\" class=\"form-control\" style=\"border: none; border-bottom: 1px solid #ccc;\" name=\"password\" placeholder=\"Digite sua senha\" maxlength=\"255\">\n              <span class=\"fa fa-lock form-control-feedback\"></span>\n            </div>\n            <div class=\"form-group\">\n               <button type=\"submit\" class=\"btn btn-danger btn-block btn-flat\" style=\"padding: 10px 0; border-radius: 2px;\">Entrar</button>\n            </div>\n          </form>\n          <a href=\"forget_password.php\" style=\"color: #666;\">Esqueci minha senha.</a><br>\n        </div>\n        <!-- /.login-box-body -->\n    </div>\n  </div>\n  <!-- /.login-box -->\n\n  <!-- jQuery 3 -->\n  <script src=\"bower_components/jquery/dist/jquery.min.js\"></script>\n  <!-- Bootstrap 3.3.7 -->\n  <script src=\"bower_components/bootstrap/dist/js/bootstrap.min.js\"></script>\n  <!-- iCheck -->\n  <script src=\"plugins/iCheck/icheck.min.js\"></script>\n  <script>\n      \$(function () {\n          \$('input').iCheck({\n              checkboxClass: 'icheckbox_square-blue',\n              radioClass: 'iradio_square-blue',\n              increaseArea: '20%' /* optional */\n          });\n          \$(\".callout\").delay(3000).slideUp(200, function() {\n              \$(this).remove();\n          });\n      });\n  </script>\n  </body>\n</html>\n";

?>