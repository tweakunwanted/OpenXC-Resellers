<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

include_once "./sys/functions.php";
startSession();
if (isset($_SESSION["__l0gg3d_us3r__"])) {
    header("Location: ./dashboard.php");
    exit;
}
$server_name = getServerProperty("server_name");
if (isset($_POST["email"])) {
    $email = $_POST["email"];
    $result = resetPassword($email);
    switch ($result) {
        case 1:
            header("location: ?result=success");
            exit;
        case 2:
            header("location: ?result=invalid_email");
            exit;
        case 3:
            header("location: ?result=email_not_found");
            exit;
    }
}
echo "<!DOCTYPE html>\n<html>\n  <head>\n    <meta charset=\"utf-8\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <title>";
echo $server_name;
echo " :: Office</title>\n    <!-- Tell the browser to be responsive to screen width -->\n    <meta content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" name=\"viewport\">\n    <!-- Bootstrap 3.3.7 -->\n    <link rel=\"stylesheet\" href=\"bower_components/bootstrap/dist/css/bootstrap.min.css\">\n    <!-- Font Awesome -->\n    <link rel=\"stylesheet\" href=\"bower_components/font-awesome/css/font-awesome.min.css\">\n    <!-- Theme style -->\n    <link rel=\"stylesheet\" href=\"dist/css/AdminLTE.min.css\">\n    <!-- iCheck -->\n    <link rel=\"stylesheet\" href=\"plugins/iCheck/square/blue.css\">\n\n    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->\n    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->\n    <!--[if lt IE 9]>\n    <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>\n    <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>\n    <![endif]-->\n\n    <!-- Google Font -->\n    <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic\">\n    ";
injectCustomCss();
echo "</head>\n  <body class=\"hold-transition login-page\" style=\"background-image: linear-gradient(to bottom, #607D8B 0%, #232121 100%); background-attachment: fixed;\">\n    <div class=\"container\">\n      <div class=\"login-box\">\n        <div class=\"login-logo\">\n          <a href=\"index.php\"><img src=\"dist/img/logo_giant.png\" style=\"max-width: 100%;\"></a>\n        </div>\n        <!-- /.login-logo -->\n        ";
if (isset($_GET["result"])) {
    $result = $_GET["result"];
    $result_message = "Aconteceu um problema, tente novamente mais tarde!";
    $result_type = "warning";
    switch ($result) {
        case "success":
            $result_type = "success";
            $result_message = "As instruções para você alterar sua senha foram enviadas ao seu email!";
            break;
        case "invalid_email":
            $result_message = "E-mail inválido, verifique se o email esta correto e tente novamente.";
            break;
        case "email_not_found":
            $result_message = "Não existe nenhum usuário cadastrado com este email!";
            break;
    }
    echo "            <div class=\"callout callout-";
    echo $result_type;
    echo "\">\n              ";
    echo $result_message;
    echo "            </div>\n            ";
}
echo "        <div class=\"login-box-body\">\n          <p class=\"login-box-msg\">Esqueci minha senha</p>\n          <form method=\"post\">\n            <div class=\"form-group has-feedback\">\n              <label for=\"email\">Enviaremos um email com instruções de como redefinir sua senha.</label>\n              <input type=\"text\" class=\"form-control\" name=\"email\" placeholder=\"usuario@exemplo.com\" maxlength=\"255\" autocomplete=\"off\">\n              <span class=\"fa fa-evelope form-control-feedback\"></span>\n            </div>\n            <div class=\"row\">\n              <div class=\"col-xs-12\">\n                <button type=\"submit\" class=\"btn btn-danger btn-block btn-flat\">Enviar por email</button>\n              </div>\n              <!-- /.col -->\n            </div>\n          </form>\n          <a href=\"index.php\" style=\"color: #666;\">Clique para voltar.</a><br>\n        </div>\n        <!-- /.login-box-body -->\n      </div>\n      <!-- /.login-box -->\n    </div>\n  <!-- jQuery 3 -->\n  <script src=\"bower_components/jquery/dist/jquery.min.js\"></script>\n  <!-- Bootstrap 3.3.7 -->\n  <script src=\"bower_components/bootstrap/dist/js/bootstrap.min.js\"></script>\n  <!-- iCheck -->\n  <script src=\"plugins/iCheck/icheck.min.js\"></script>\n  <script>\n      \$(function () {\n          \$('input').iCheck({\n              checkboxClass: 'icheckbox_square-blue',\n              radioClass: 'iradio_square-blue',\n              increaseArea: '20%' /* optional */\n          });\n          \$(\".callout\").delay(3000).slideUp(200, function() {\n              \$(this).remove();\n          });\n      });\n  </script>\n  </body>\n</html>\n";

?>