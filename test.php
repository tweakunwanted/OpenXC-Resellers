<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

include_once "./sys/functions.php";
startSession();
if (!getServerProperty("automatic_test", 0)) {
    exit;
}
if (!isset($_GET["key"])) {
    exit;
}
$key = $_GET["key"];
$result = getUserPropertyByValue("test_key", $key);
if (!$result) {
    exit;
}
$reseller = getUserByID($result["userid"]);
if (!$reseller) {
    exit;
}
if (!isAdmin($reseller) && $reseller["credits"] < getServerProperty("automatic_test_min_credits", 0)) {
    exit;
}
$server_name = getServerProperty("server_name");
$automatic_test_packages = json_decode(getServerProperty("automatic_test_packages", json_encode(array())), true);
$random_name = getServerProperty("random_name_automatic_test", 0);
if (isset($_POST["email"]) && isset($_POST["package_id"])) {
    $username = random_str(6);
    if (!$random_name) {
        if (!isset($_POST["username"])) {
            header("location: ?key=" . $key . "&result=invalid_username");
            exit;
        }
        $username = $_POST["username"];
        $username = str_replace(" ", "_", $username);
        if (strlen($username) < 6 || 255 < strlen($username)) {
            header("location: ?key=" . $key . "&result=invalid_username");
            exit;
        }
    }
    $email = $_POST["email"];
    $package_id = intval($_POST["package_id"]);
    $package = getPackageByID($package_id);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location: ?key=" . $key . "&result=invalid_email");
        exit;
    }
    if (getServerProperty("only_valid_emails_automatic_test", 0) && strpos($email, "@hotmail") === false && strpos($email, "@outlook") === false && strpos($email, "@gmail") === false && strpos($email, "@icloud") === false) {
        header("location: ?key=" . $key . "&result=just_verified_emails");
        exit;
    }
    if ($package && $package["is_trial"]) {
        if (!in_array($email, unserialize(ALLOWED_EMAILS)) && existTest($email)) {
            header("location: ?key=" . $key . "&result=used_email");
            exit;
        }
        if (insertTest($email)) {
            $duration = $package["trial_duration"] . " " . $package["trial_duration_in"];
            $password = random_str(6);
            if (createClient($result["userid"], $username, $password, $duration, $package["bouquets"], "", 1)) {
                insertRegUserLog($reseller["id"], $username, $password, "[<b>UserPanel</b> -> <u>New Line</u>] with Package [" . $package["package_name"] . "], Credits: <font color=\"green\">" . $reseller["credits"] . "</font> -> <font color=\"red\">" . $reseller["credits"] . "</font>");
                $list_link = GetList($username, $password);
                $email_messages = json_decode(getServerProperty("email_messages"), true);
                $whatsapp = getUserProperty($result["userid"], "whatsapp");
                $telegram = getUserProperty($result["userid"], "telegram");
                $auto_test_subject = str_replace(array("{USERNAME}", "{PASSWORD}", "{SERVER_NAME}"), array($username, $password, $server_name), $email_messages["auto_test_subject"]);
                $auto_test_message = str_replace(array("{USERNAME}", "{PASSWORD}", "{LIST_LINK}", "{SERVER_NAME}", "{RESELLER_EMAIL}", "{WHATSAPP}", "{TELEGRAM}"), array($username, $password, $list_link, $server_name, $reseller["email"], $whatsapp, $telegram), $email_messages["auto_test_message"]);
                if (smtpmailer($email, $auto_test_subject, $auto_test_message)) {
                    header("location: ?key=" . $key . "&result=success");
                }
            } else {
                header("location: ?key=" . $key . "&result=exist_user");
            }
        }
    }
}
echo "<!DOCTYPE html>\n<html>\n  <head>\n    <meta charset=\"utf-8\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <title>";
echo $server_name;
echo " :: Gerador de teste</title>\n    <!-- Tell the browser to be responsive to screen width -->\n    <meta content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" name=\"viewport\">\n    <!-- Bootstrap 3.3.7 -->\n    <link rel=\"stylesheet\" href=\"bower_components/bootstrap/dist/css/bootstrap.min.css\">\n    <!-- Font Awesome -->\n    <link rel=\"stylesheet\" href=\"bower_components/font-awesome/css/font-awesome.min.css\">\n    <!-- Theme style -->\n    <link rel=\"stylesheet\" href=\"dist/css/AdminLTE.min.css\">\n    <!-- iCheck -->\n    <link rel=\"stylesheet\" href=\"plugins/iCheck/square/blue.css\">\n    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->\n    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->\n    <!--[if lt IE 9]>\n    <script src=\"https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js\"></script>\n    <script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\"></script>\n    <![endif]-->\n\n    <!-- Google Font -->\n    <link rel=\"stylesheet\" href=\"https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic\">\n    ";
injectCustomCss();
echo "</head>\n  <body class=\"hold-transition login-page\" style=\"background-image: url('./dist/img/test_background.png'); background-attachment: fixed; background-repeat: no-repeat; background-size: cover;\">\n    <div class=\"container\">\n      <div class=\"login-box\">\n        <div class=\"login-logo\">\n          <a href=\"index.php\"><img src=\"dist/img/logo_test.png\" style=\"max-width: 100%;\"></a>\n        </div>\n        <!-- /.login-logo -->\n        ";
if (isset($_GET["result"])) {
    $result = $_GET["result"];
    $result_message = "Aconteceu um problema, tente novamente mais tarde!";
    $result_type = "warning";
    switch ($result) {
        case "success":
            $result_type = "success";
            $result_message = "Teste criado com sucesso, verifique seu e-mail!";
            break;
        case "invalid_email":
            $result_message = "O e-mail escolhido é invalido!";
            break;
        case "just_verified_emails":
            $result_message = "Apenas e-mails válidos(@gmail, @hotmail, @outlook e @icloud)!";
            break;
        case "used_email":
            $result_message = "Você só pode criar um teste!";
            break;
        case "exist_user":
            $result_message = "Já existe um usuário com este nome, tente novamente.";
            break;
    }
    echo "            <div class=\"callout callout-";
    echo $result_type;
    echo "\">\n              ";
    echo $result_message;
    echo "            </div>\n            ";
}
echo "        <div class=\"login-box-body\">\n        ";
$current_date = date("m/d/Y");
$disabled_days = getServerProperty("disabled_days_automatic_test", "");
if (strpos($disabled_days, $current_date) !== false) {
    echo "          <p class=\"login-box-msg\">Gerador de teste automático desativado temporariamente!</p>\n        ";
} else {
    echo "          <p class=\"login-box-msg\">Crie seu teste gratuito agora mesmo!</p>\n          <form method=\"post\">\n            <div class=\"form-group has-feedback\">\n              <label for=\"email\">E-mail</label>\n              <input type=\"email\" class=\"form-control\" name=\"email\" placeholder=\"Digite seu e-mail\" maxlength=\"255\" required=\"\" autocomplete=\"off\">\n              <span class=\"fa fa-envelope form-control-feedback\"></span>\n            </div>\n          ";
    if (!$random_name) {
        echo "            <div class=\"form-group has-feedback\">\n              <label for=\"username\">Usuário</label>\n              <input type=\"text\" class=\"form-control\" name=\"username\" placeholder=\"Digite um usuário\" maxlength=\"255\" required=\"\">\n              <span class=\"fa fa-user form-control-feedback\"></span>\n            </div>\n          ";
    }
    echo "            <div class=\"form-group\">\n              <label for=\"plan_id\">Pacote</label>\n              <select class=\"form-control\" name=\"package_id\" required=\"\">\n                ";
    $packages = getPackages();
    foreach ($packages as $current_package) {
        if (in_array($current_package["id"], $automatic_test_packages)) {
            echo "                      <option value=\"";
            echo $current_package["id"];
            echo "\">";
            echo $current_package["package_name"];
            echo "</option>\n                      ";
        }
    }
    echo "              </select>\n            </div>\n            <input type=\"hidden\" name=\"key\" value=\"";
    echo $key;
    echo "\">\n            <div class=\"row\">\n              <div class=\"col-xs-8\">\n              </div>\n              <!-- /.col -->\n              <div class=\"col-xs-4\">\n                <button type=\"submit\" class=\"btn btn-danger btn-block btn-flat\">Concluir</button>\n              </div>\n              <!-- /.col -->\n            </div>\n          </form>\n\n\n        ";
}
echo "        </div>\n        <!-- /.login-box-body -->\n      </div>\n      <!-- /.login-box -->\n  </div>\n\n  <!-- jQuery 3 -->\n  <script src=\"bower_components/jquery/dist/jquery.min.js\"></script>\n  <!-- Bootstrap 3.3.7 -->\n  <script src=\"bower_components/bootstrap/dist/js/bootstrap.min.js\"></script>\n  <!-- iCheck -->\n  <script src=\"plugins/iCheck/icheck.min.js\"></script>\n  <script>\n      \$(function () {\n          \$('input').iCheck({\n              checkboxClass: 'icheckbox_square-blue',\n              radioClass: 'iradio_square-blue',\n              increaseArea: '20%' /* optional */\n          });\n          \$(\".callout\").delay(3000).slideUp(200, function() {\n              \$(this).remove();\n          });\n      });\n  </script>\n  </body>\n</html>\n";

?>