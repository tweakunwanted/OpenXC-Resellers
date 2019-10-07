<?php


define("KOFFICE_PANEL_VERSION", "v2.0");
ini_set("log_errors", 1);
if (debugEnabled()) {
    error_reporting(32767);
    ini_set("display_errors", 1);
} else {
    error_reporting(0);
    ini_set("display_errors", 0);
}
date_default_timezone_set("America/New_York");
if (file_exists(__DIR__ . "/config.php") && !(include_once __DIR__ . "/config.php")) {
    exit("The kOffice Panel cant open the \"/sys/config.php\" file. verify if exist or has permission!");
}
if (!defined("OFFICE_KEY")) {
    define("OFFICE_KEY", "");
}

$licence_result = true; //checkLicence(OFFICE_KEY);
//if (!$licence_result || $licence_result["status"] !== base64_decode("QWN0aXZl")) {
   // exit(base64_decode("SW52YWxpZCBsaWNlbnNlIGtleSEsIHBsZWFzZSBjb250YWN0IHRoZSBBZG1pbmlzdHJhdG9y"));
//}

class DB
{
    private $connection = NULL;
    private static $_instance = NULL;
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    private function __clone()
    {
    }
    public function getConnection($db_host, $db_port, $db_name, $db_user, $db_pass)
    {
        $con_name = $db_host . "_" . $db_name;
        try {
            if (!isset($this->connection[$con_name])) {
                $this->connection[$con_name] = new PDO("mysql:host=" . $db_host . ";port=" . $db_port . ";dbname=" . $db_name . ";charset=utf8", $db_user, $db_pass, array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_TIMEOUT => 5));
                $this->connection[$con_name]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch (PDOException $e) {
            if (debugEnabled()) {
                exit("Failed to connect to DB: " . $e->getMessage());
            }
            return NULL;
        } catch (Exception $d) {
            if (debugEnabled()) {
                exit("Failed to connect to DB: " . $d->getMessage());
            }
            return NULL;
        }
        return $this->connection[$con_name];
    }
}
function getConnection()
{
    return DB::getInstance()->getConnection(DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS);
}
function getOfficeConnection()
{
    return DB::getInstance()->getConnection(OFFICE_DB_HOST, OFFICE_DB_PORT, OFFICE_DB_NAME, OFFICE_DB_USER, OFFICE_DB_PASS);
}
function startSession()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}
function isLogged($destination = "./index.php")
{
    startsession();
    if (!isset($_SESSION["__l0gg3d_us3r__"])) {
        header("Location: " . $destination);
        exit;
    }
}
function loginUser($username, $password)
{
    $crypted_password = cryptPassword($password, "xtreamcodes");
 
    $pass = crypt( $password, '$6$rounds=20000$xtreamcodes$' );
    $PDO = getconnection();
    
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `reg_users` WHERE `username` = :username AND `password` = :password LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR, 50);
        $stmt->bindParam(":password", $pass, PDO::PARAM_STR, 255);
        $stmt->execute();
       
        $result = $stmt->fetch(PDO::FETCH_ASSOC);



        if ($result) {
            if ($result["status"] == 1) {
                $allowed_groups = json_decode(getServerProperty("allowed_groups"), true);
                if (!in_array($result["member_group_id"], $allowed_groups)) {
                    return 5;
                }
                startsession();
                $_SESSION["__l0gg3d_us3r__"] = $result["id"];
                return 1;
            }
            return 4;
        }
        return 3;
    }
    return 2;
}
function logoutUser()
{
    startsession();
    unset($_SESSION);
    SESSION_DESTROY();
}
function getUserByID($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `reg_users` WHERE `id` = :userid LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}
function getLoggedUser()
{
    startsession();
    $user = getuserbyid($_SESSION["__l0gg3d_us3r__"]);
    if ($user) {
        return $user;
    }
    logoutuser();
    exit;
}
function getUserByUsername($username)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `reg_users` WHERE `username` = :username LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR, 255);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}
function getUserByEmail($email)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `reg_users` WHERE `email` = :email LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR, 255);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}
function getAllUsers()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `reg_users`;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function updateUser($user_id, $username, $password, $email, $member_group_id, $notes)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "UPDATE `reg_users` SET `username` = :username, `password` = :password, `email` = :email, `member_group_id` = :member_group_id, `notes` = :notes WHERE `id` = :user_id LIMIT 1;";
        if (empty($password)) {
            $sql = "UPDATE `reg_users` SET `username` = :username, `email` = :email, `member_group_id` = :member_group_id, `notes` = :notes WHERE `id` = :user_id LIMIT 1;";
        }
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR, 255);
        if (!empty($password)) {
            $password = cryptPassword($password, "xtreamcodes");
            $stmt->bindParam(":password", $password, PDO::PARAM_STR, 255);
        }
        $stmt->bindParam(":email", $email, PDO::PARAM_STR, 255);
        $stmt->bindParam(":member_group_id", $member_group_id, PDO::PARAM_INT);
        $stmt->bindParam(":notes", $notes, PDO::PARAM_STR);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function deleteExpiredTestUsersByOwner($owner_id, $remove_expired, $remove_test, $start_date, $end_date)
{
    if (!$remove_expired && !$remove_test) {
        return false;
    }
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "DELETE FROM `users` WHERE `member_id` = :owner_id AND `created_at` >= :start_date AND `created_at` <= :end_date";
        if ($remove_expired) {
            $sql .= " AND unix_timestamp(NOW()) > `exp_date`";
        }
        if ($remove_test) {
            $sql .= " AND `is_trial` = 1";
        }
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":owner_id", $owner_id, PDO::PARAM_INT);
        $stmt->bindParam(":start_date", $start_date, PDO::PARAM_INT);
        $stmt->bindParam(":end_date", $end_date, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function transferResellers($resellers, $new_owner, $new_group)
{
    $PDO = getconnection();
    foreach ($resellers as $reseller_id) {
        $sql = "UPDATE `reg_users` SET `owner_id` = :owner_id WHERE `id` = :user_id LIMIT 1;";
        if ($new_group) {
            $sql = "UPDATE `reg_users` SET `member_group_id` = :member_group_id, `owner_id` = :owner_id WHERE `id` = :user_id LIMIT 1;";
        }
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":owner_id", $new_owner, PDO::PARAM_INT);
        if ($new_group) {
            $stmt->bindParam(":member_group_id", $new_group, PDO::PARAM_INT);
        }
        $stmt->bindParam(":user_id", $reseller_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    return true;
}
function updateUserPassword($user_id, $password)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "UPDATE `reg_users` SET `password` = :password WHERE `id` = :user_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $password = cryptPassword($password, "xtreamcodes");
        $stmt->bindParam(":password", $password, PDO::PARAM_STR, 255);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function insertTest($email)
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "INSERT INTO `test_historic` (`id`, `email`) VALUES (NULL, :email)";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR, 255);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function existTest($email)
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT `id` FROM `test_historic` WHERE `email` LIKE :email;";
        $stmt = $PDO->prepare($sql);
        $email_ = "%" . $email . "%";
        $stmt->bindParam(":email", $email_, PDO::PARAM_STR, 255);
        $stmt->execute();
        if (0 < $stmt->rowCount()) {
            return true;
        }
    }
    return false;
}
function createFastTest($owner_id, $package_id)
{
    $package = getPackageByID($package_id);
    if ($package && $package["is_trial"]) {
        $username = random_str(10);
        $password = random_str(10);
        $duration = $package["trial_duration"] . " " . $package["trial_duration_in"];
        if (createClient($owner_id, $username, $password, $duration, $package["bouquets"], "Criado com office.", 1)) {
            $reseller = getuserbyid($owner_id);
            if ($reseller) {
                insertRegUserLog($owner_id, $username, $password, "[<b>UserPanel</b> -> <u>New Line</u>] with Package [" . $package["package_name"] . "], Credits: <font color=\"green\">" . $reseller["credits"] . "</font> -> <font color=\"red\">" . $reseller["credits"] . "</font>");
            }
            return true;
        }
    }
    return false;
}
function createClient($owner_id, $username, $password, $duration = "2 hours", $bouquet, $reseller_notes, $is_trial = 0)
{
    $exp_date = strtotime("+" . $duration);
    return insertClient($owner_id, $username, $password, $exp_date, "", $reseller_notes, $bouquet, 1, $is_trial);
}
function insertClient($owner_id, $username, $password, $exp_date, $admin_notes, $reseller_notes, $bouquet, $max_connections, $is_trial)
{
    if (existClient($username)) {
        return false;
    }
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "INSERT INTO `users` (`id`, `member_id`, `username`, `password`, `exp_date`, `admin_notes`, `reseller_notes`, `bouquet`, `max_connections`, `is_trial`, `created_at`, `created_by`) VALUES \r\n            (NULL, :owner_id, :username, :password, :exp_date, :admin_notes, :reseller_notes, :bouquet, :max_connections, :is_trial, unix_timestamp(NOW()), :owner_id);";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":owner_id", $owner_id, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR, 255);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR, 255);
        $stmt->bindParam(":exp_date", $exp_date, PDO::PARAM_INT);
        $stmt->bindParam(":admin_notes", $admin_notes, PDO::PARAM_STR, 500);
        $stmt->bindParam(":reseller_notes", $reseller_notes, PDO::PARAM_STR, 500);
        $stmt->bindParam(":bouquet", $bouquet, PDO::PARAM_STR);
        $stmt->bindParam(":max_connections", $max_connections, PDO::PARAM_INT);
        $stmt->bindParam(":is_trial", $is_trial, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $user_id = $PDO->lastInsertId();
            $sql = "INSERT INTO `user_output` (`id`, `user_id`, `access_output_id`) VALUES (NULL, :userid1, '1'), (NULL, :userid2, '2')";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(":userid1", $user_id, PDO::PARAM_INT);
            $stmt->bindParam(":userid2", $user_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return $user_id;
            }
        }
    }
    return false;
}
function existClient($username)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT `id` FROM `users` WHERE `username` = :username;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR, 255);
        $stmt->execute();
        if (0 < $stmt->rowCount()) {
            return true;
        }
    }
    return false;
}
function getAllClients()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `users` ORDER BY `id` DESC;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function getClientByID($client_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `users` WHERE `id` = :client_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":client_id", $client_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}
function getAllClientsAdmin()
{
    $results = array();
    $all_clients = getallclients();
    $all_users = getallusers();
    foreach ($all_clients as $current_client) {
        $reseller_key = array_search($current_client["member_id"], array_column($all_users, "id"));
        $reseller_name = $reseller_key !== false ? $all_users[$reseller_key]["username"] : "Desconhecido!";
        $current_client["reseller_name"] = $reseller_name;
        $results[] = $current_client;
    }
    return $results;
}
function getAllClientsAdminWithOptions($start = 0, $length = 10, $columns = array(), $search_value = "", $order_column_index = NULL, $order_type = "asc")
{
    $result = array("data" => array(), "recordsTotal" => 0, "recordsFiltered" => 0);
    $all_clients = dataOutput($columns, getallclientsadmin());
    if ($order_column_index !== NULL && isset($columns[$order_column_index]["db"])) {
        $order_column = $columns[$order_column_index]["db"];
        usort($all_clients, function ($a, $b) use($order_column, $order_type) {
            if ($a[$order_column] === $b[$order_column]) {
                return 0;
            }
            if ($order_type == "asc") {
                return strip_tags($b[$order_column]) < strip_tags($a[$order_column]) ? 1 : -1;
            }
            return strip_tags($a[$order_column]) < strip_tags($b[$order_column]) ? 1 : -1;
        });
    }
    $current_index = 0;
    foreach ($all_clients as $current_client) {
        if (tryFind($current_client, $columns, $search_value)) {
            if ($start <= $current_index && count($result["data"]) < $length) {
                $result["data"][] = $current_client;
            }
            $current_index++;
        }
    }
    $result["recordsTotal"] = count($all_clients);
    $result["recordsFiltered"] = $current_index;
    return $result;
}
function getAllClientsByOwner($user)
{
    $resellers = array($user["id"]);
    $resellers = array_merge($resellers, getAllResellersIdByOwnerID($user["id"]));
    $sql_select = "SELECT t1.*, t2.username as 'reseller_name' FROM `users` t1, `reg_users` t2 WHERE t1.member_id = t2.id AND t1.member_id IN (" . implode(",", $resellers) . ")";
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $total_sql = $sql_select;
        $stmt = $PDO->prepare($total_sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            return $result;
        }
    }
    return array();
}
function getAllClientsByOwnerWithOptions($user, $start = 0, $length = 10, $columns = array(), $search_value = "", $order_column_index = NULL, $order_type = "asc")
{
    $result = array("data" => array(), "recordsTotal" => 0, "recordsFiltered" => 0);
    $all_clients = dataOutput($columns, getallclientsbyowner($user));
    if ($order_column_index !== NULL && isset($columns[$order_column_index]["db"])) {
        $order_column = $columns[$order_column_index]["db"];
        usort($all_clients, function ($a, $b) use($order_column, $order_type) {
            if ($a[$order_column] === $b[$order_column]) {
                return 0;
            }
            if ($order_type == "asc") {
                return strip_tags($b[$order_column]) < strip_tags($a[$order_column]) ? 1 : -1;
            }
            return strip_tags($a[$order_column]) < strip_tags($b[$order_column]) ? 1 : -1;
        });
    }
    $current_index = 0;
    foreach ($all_clients as $current_client) {
        if (tryFind($current_client, $columns, $search_value)) {
            if ($start <= $current_index && count($result["data"]) < $length) {
                $result["data"][] = $current_client;
            }
            $current_index++;
        }
    }
    $result["recordsTotal"] = count($all_clients);
    $result["recordsFiltered"] = $current_index;
    return $result;
}
function tryFind($array = array(), $columns = array(), $search_value)
{
    if (empty($search_value)) {
        return true;
    }
    foreach ($columns as $current_column) {
        $searchable = isset($current_column["searchable"]) ? $current_column["searchable"] : true;
        if ($searchable) {
            $striped_db_value = strip_tags($array[$current_column["db"]]);
            if (stripos($striped_db_value, $search_value) !== false) {
                return true;
            }
        }
    }
    return false;
}
function dataOutput($columns, $data)
{
    $out = array();
    $i = 0;
    for ($ien = count($data); $i < $ien; $i++) {
        $row = array();
        $j = 0;
        for ($jen = count($columns); $j < $jen; $j++) {
            $column = $columns[$j];
            $db_value = isset($data[$i][$column["db"]]) ? $data[$i][$column["db"]] : "";
            if (isset($column["formatter"])) {
                $row[$column["db"]] = $column["formatter"]($db_value, $data[$i]);
            } else {
                $row[$column["db"]] = $db_value;
            }
        }
        $out[] = $row;
    }
    return $out;
}
function getClientsByOwnerID($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `users` WHERE `member_id` = :userid;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function getAllOnlineClients()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "select users.id, users.username, streams.stream_display_name as `stream_name`, user_activity_now.user_ip, user_activity_now.date_start as `time`, user_activity_now.geoip_country_code as `country`, user_activity_now.isp as `internet_server` from users, user_activity_now, streams where users.id=user_activity_now.user_id and user_activity_now.stream_id = streams.id";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function getAllOnlineClientsWithOptions($start = 0, $length = 10, $columns = array(), $search_value = "", $order_column_index = NULL, $order_type = "asc")
{
    $result = array("data" => array(), "recordsTotal" => 0, "recordsFiltered" => 0);
    $all_clients = dataoutput($columns, getallonlineclients());
    if ($order_column_index !== NULL && isset($columns[$order_column_index]["db"])) {
        $order_column = $columns[$order_column_index]["db"];
        usort($all_clients, function ($a, $b) use($order_column, $order_type) {
            if ($a[$order_column] === $b[$order_column]) {
                return 0;
            }
            if ($order_type == "asc") {
                return strip_tags($b[$order_column]) < strip_tags($a[$order_column]) ? 1 : -1;
            }
            return strip_tags($a[$order_column]) < strip_tags($b[$order_column]) ? 1 : -1;
        });
    }
    $current_index = 0;
    foreach ($all_clients as $current_client) {
        if (tryfind($current_client, $columns, $search_value)) {
            if ($start <= $current_index && count($result["data"]) < $length) {
                $result["data"][] = $current_client;
            }
            $current_index++;
        }
    }
    $result["recordsTotal"] = count($all_clients);
    $result["recordsFiltered"] = $current_index;
    return $result;
}
function getAllOnlineClientsByOwnerID($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "select users.id, users.username, streams.stream_display_name as `stream_name`, user_activity_now.user_ip, user_activity_now.date_start as `time`,user_activity_now.geoip_country_code as `country`, user_activity_now.isp as `internet_server` from users, user_activity_now, streams where users.id=user_activity_now.user_id and user_activity_now.stream_id = streams.id and users.member_id = :userid";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function getAllOnlineClientsByOwnerWithOptions($reseller, $start = 0, $length = 10, $columns = array(), $search_value = "", $order_column_index = NULL, $order_type = "asc")
{
    $result = array("data" => array(), "recordsTotal" => 0, "recordsFiltered" => 0);
    $all_clients = dataoutput($columns, getallonlineclientsbyownerid($reseller["id"]));
    if ($order_column_index !== NULL && isset($columns[$order_column_index]["db"])) {
        $order_column = $columns[$order_column_index]["db"];
        usort($all_clients, function ($a, $b) use($order_column, $order_type) {
            if ($a[$order_column] === $b[$order_column]) {
                return 0;
            }
            if ($order_type == "asc") {
                return strip_tags($b[$order_column]) < strip_tags($a[$order_column]) ? 1 : -1;
            }
            return strip_tags($a[$order_column]) < strip_tags($b[$order_column]) ? 1 : -1;
        });
    }
    $current_index = 0;
    foreach ($all_clients as $current_client) {
        if (tryfind($current_client, $columns, $search_value)) {
            if ($start <= $current_index && count($result["data"]) < $length) {
                $result["data"][] = $current_client;
            }
            $current_index++;
        }
    }
    $result["recordsTotal"] = count($all_clients);
    $result["recordsFiltered"] = $current_index;
    return $result;
}
function createReseller($owner_id, $username, $password, $credits, $member_group_id, $email, $notes)
{
    if (existReseller($username)) {
        return false;
    }
    $crypted_password = cryptPassword($password, "xtreamcodes");
    if ($member_group_id) {
        $settings = getServerSettings();
        if ($settings) {
            $language = $settings["default_lang"];
            return insertReseller($owner_id, $username, $crypted_password, $credits, $email, $notes, $member_group_id, $language);
        }
    }
    return false;
}
function insertReseller($owner_id, $username, $password, $credits, $email, $notes, $member_group_id, $language)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "INSERT INTO `reg_users` (`id`, `username`, `password`, `email`, `ip`, `date_registered`, `verify_key`, `last_login`, `member_group_id`, `verified`, `credits`, `notes`, `status`, `default_lang`, `reseller_dns`, `owner_id`, `override_packages`, `google_2fa_sec`) VALUES (NULL, :username, :password, :email, NULL, unix_timestamp(NOW()), NULL, NULL, :member_group_id, '1', :credits, :notes, '1', :language, '', :owner_id, NULL, '')";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":owner_id", $owner_id, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR, 255);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR, 255);
        $stmt->bindParam(":credits", $credits, PDO::PARAM_INT);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR, 255);
        $stmt->bindParam(":notes", $notes, PDO::PARAM_STR, 500);
        $stmt->bindParam(":member_group_id", $member_group_id, PDO::PARAM_INT);
        $stmt->bindParam(":language", $language, PDO::PARAM_STR, 255);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function existReseller($username)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT `id` FROM `reg_users` WHERE `username` = :username;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR, 255);
        $stmt->execute();
        if (0 < $stmt->rowCount()) {
            return true;
        }
    }
    return false;
}
function getResellersAdmin()
{
    $results = array();
    $all_users = getallusers();
    foreach ($all_users as $current_user) {
        $reseller_key = array_search($current_user["owner_id"], array_column($all_users, "id"));
        $reseller_name = $reseller_key !== false ? $all_users[$reseller_key]["username"] : "-";
        $current_user["reseller_name"] = $reseller_name;
        array_push($results, $current_user);
    }
    return $results;
}
function getResellersByOwner($user)
{
    $results = array();
    $all_users = getallusers();
    $users = getAllResellersByOwnerID($user["id"]);
    foreach ($users as $current_user) {
        $reseller_key = array_search($current_user["owner_id"], array_column($all_users, "id"));
        $reseller_name = $reseller_key !== false ? $all_users[$reseller_key]["username"] : "-";
        $current_user["reseller_name"] = $reseller_name;
        array_push($results, $current_user);
    }
    return $results;
}
function toggleTicket($ticket_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "UPDATE `tickets` SET `status` = !`status` WHERE `id` = :ticket_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":ticket_id", $ticket_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function deleteTicket($ticket_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "DELETE FROM `tickets` WHERE `id` = :ticket_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":ticket_id", $ticket_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $sql_two = "DELETE FROM `tickets_replies` WHERE `ticket_id` = :ticket_id;";
            $stmt = $PDO->prepare($sql_two);
            $stmt->bindParam(":ticket_id", $ticket_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return true;
            }
        }
    }
    return false;
}
function updateReadTicket($ticket_id, $person, $read = 1)
{
    if ($person !== "admin" && $person !== "user") {
        return false;
    }
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "UPDATE `tickets` SET `" . $person . "_read` = :read WHERE `id` = :ticket_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":ticket_id", $ticket_id, PDO::PARAM_INT);
        $stmt->bindParam(":read", $read, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function getTicketById($ticket_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `tickets` WHERE `tickets`.id = :ticket_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":ticket_id", $ticket_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    return false;
}
function getAllTickets()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT `tickets`.id, `reg_users`.username as 'reseller', `tickets`.title, `tickets`.status, `tickets`.admin_read, `tickets`.user_read FROM `tickets`, `reg_users` WHERE `tickets`.member_id = `reg_users`.id";
        $stmt = $PDO->prepare($sql);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return array();
}
function getAllTicketsByReseller($reseller_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT `tickets`.id, `reg_users`.username as 'reseller', `tickets`.title, `tickets`.status, `tickets`.admin_read, `tickets`.user_read FROM `tickets`, `reg_users` WHERE `tickets`.member_id = :member_id AND `tickets`.member_id = `reg_users`.id";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":member_id", $reseller_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return array();
}
function getAllTicketsAdminWithOptions($start = 0, $length = 10, $columns = array(), $search_value = "", $order_column_index = NULL, $order_type = "asc")
{
    $result = array("data" => array(), "recordsTotal" => 0, "recordsFiltered" => 0);
    $all_resellers = dataoutput($columns, getalltickets());
    if ($order_column_index !== NULL && isset($columns[$order_column_index]["db"])) {
        $order_column = $columns[$order_column_index]["db"];
        usort($all_resellers, function ($a, $b) use($order_column, $order_type) {
            if ($a[$order_column] === $b[$order_column]) {
                return 0;
            }
            if ($order_type == "asc") {
                return strip_tags($b[$order_column]) < strip_tags($a[$order_column]) ? 1 : -1;
            }
            return strip_tags($a[$order_column]) < strip_tags($b[$order_column]) ? 1 : -1;
        });
    }
    $current_index = 0;
    foreach ($all_resellers as $current_reseller) {
        if (tryfind($current_reseller, $columns, $search_value)) {
            if ($start <= $current_index && count($result["data"]) < $length) {
                $result["data"][] = $current_reseller;
            }
            $current_index++;
        }
    }
    $result["recordsTotal"] = count($all_resellers);
    $result["recordsFiltered"] = $current_index;
    return $result;
}
function getAllTicketsByOwnerWithOptions($reseller, $start = 0, $length = 10, $columns = array(), $search_value = "", $order_column_index = NULL, $order_type = "asc")
{
    $result = array("data" => array(), "recordsTotal" => 0, "recordsFiltered" => 0);
    $all_resellers = dataoutput($columns, getallticketsbyreseller($reseller["id"]));
    if ($order_column_index !== NULL && isset($columns[$order_column_index]["db"])) {
        $order_column = $columns[$order_column_index]["db"];
        usort($all_resellers, function ($a, $b) use($order_column, $order_type) {
            if ($a[$order_column] === $b[$order_column]) {
                return 0;
            }
            if ($order_type == "asc") {
                return strip_tags($b[$order_column]) < strip_tags($a[$order_column]) ? 1 : -1;
            }
            return strip_tags($a[$order_column]) < strip_tags($b[$order_column]) ? 1 : -1;
        });
    }
    $current_index = 0;
    foreach ($all_resellers as $current_reseller) {
        if (tryfind($current_reseller, $columns, $search_value)) {
            if ($start <= $current_index && count($result["data"]) < $length) {
                $result["data"][] = $current_reseller;
            }
            $current_index++;
        }
    }
    $result["recordsTotal"] = count($all_resellers);
    $result["recordsFiltered"] = $current_index;
    return $result;
}
function getAllResellersAdminWithOptions($start = 0, $length = 10, $columns = array(), $search_value = "", $order_column_index = NULL, $order_type = "asc")
{
    $result = array("data" => array(), "recordsTotal" => 0, "recordsFiltered" => 0);
    $all_resellers = dataoutput($columns, getresellersadmin());
    if ($order_column_index !== NULL && isset($columns[$order_column_index]["db"])) {
        $order_column = $columns[$order_column_index]["db"];
        usort($all_resellers, function ($a, $b) use($order_column, $order_type) {
            if ($a[$order_column] === $b[$order_column]) {
                return 0;
            }
            if ($order_type == "asc") {
                return strip_tags($b[$order_column]) < strip_tags($a[$order_column]) ? 1 : -1;
            }
            return strip_tags($a[$order_column]) < strip_tags($b[$order_column]) ? 1 : -1;
        });
    }
    $current_index = 0;
    foreach ($all_resellers as $current_reseller) {
        if (tryfind($current_reseller, $columns, $search_value)) {
            if ($start <= $current_index && count($result["data"]) < $length) {
                $result["data"][] = $current_reseller;
            }
            $current_index++;
        }
    }
    $result["recordsTotal"] = count($all_resellers);
    $result["recordsFiltered"] = $current_index;
    return $result;
}
function getAllResellersByOwnerID($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "select * from (select * from reg_users order by owner_id, id) users_sorted, (select @pv := :userid) initialisation where find_in_set(owner_id, @pv) and length(@pv := concat(@pv, ',', id));";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function getAllResellersIdByOwnerID($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "select `id` from (select * from reg_users order by owner_id, id) users_sorted, (select @pv := :userid) initialisation where find_in_set(owner_id, @pv) and length(@pv := concat(@pv, ',', id));";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    return array();
}
function getAllResellersByOwnerWithOptions($reseller, $start = 0, $length = 10, $columns = array(), $search_value = "", $order_column_index = NULL, $order_type = "asc")
{
    $result = array("data" => array(), "recordsTotal" => 0, "recordsFiltered" => 0);
    $all_users = getallusers();
    $users = getallresellersbyownerid($reseller["id"]);
    foreach ($users as &$current_user) {
        $reseller_key = array_search($current_user["owner_id"], array_column($all_users, "id"));
        $reseller_name = $reseller_key !== false ? $all_users[$reseller_key]["username"] : "-";
        $current_user["reseller_name"] = $reseller_name;
    }
    $all_resellers = dataoutput($columns, $users);
    if ($order_column_index !== NULL && isset($columns[$order_column_index]["db"])) {
        $order_column = $columns[$order_column_index]["db"];
        usort($all_resellers, function ($a, $b) use($order_column, $order_type) {
            if ($a[$order_column] === $b[$order_column]) {
                return 0;
            }
            if ($order_type == "asc") {
                return strip_tags($b[$order_column]) < strip_tags($a[$order_column]) ? 1 : -1;
            }
            return strip_tags($a[$order_column]) < strip_tags($b[$order_column]) ? 1 : -1;
        });
    }
    $current_index = 0;
    foreach ($all_resellers as $current_reseller) {
        if (tryfind($current_reseller, $columns, $search_value)) {
            if ($start <= $current_index && count($result["data"]) < $length) {
                $result["data"][] = $current_reseller;
            }
            $current_index++;
        }
    }
    $result["recordsTotal"] = count($all_resellers);
    $result["recordsFiltered"] = $current_index;
    return $result;
}
function getResellersByOwnerID($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `reg_users` WHERE `owner_id` = :userid;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function deleteReseller($reseller_id)
{
    deleteAllUserProperty($reseller_id);
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "DELETE FROM `reg_users` WHERE `id` = :reseller_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":reseller_id", $reseller_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function getClientsCount($reseller)
{
    return isAdmin($reseller) ? getAllClientsCount() : getClientsCountByOwnerId($reseller["id"]);
}
function getAllClientsCount()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT count(*) FROM `users`";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    return 0;
}
function getClientsCountByOwnerId($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT count(*) FROM `users` WHERE `member_id` = :userid";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    return 0;
}
function getActiveCount($reseller)
{
    return isAdmin($reseller) ? getAllActiveClientsCount() : getActiveClientsCountByOwnerId($reseller["id"]);
}
function getAllActiveClientsCount()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT count(*) FROM `users` WHERE (`exp_date` > unix_timestamp(NOW()) OR `exp_date` IS NULL) AND `is_trial` = 0;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    return 0;
}
function getActiveClientsCountByOwnerId($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT count(*) FROM `users` WHERE `member_id` = :userid AND (`exp_date` > unix_timestamp(NOW()) OR `exp_date` IS NULL) AND `is_trial` = 0;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    return 0;
}
function getTrialClientsCount($reseller)
{
    return isAdmin($reseller) ? getAllTrialClientsCount() : getTrialClientsCountByOwnerId($reseller["id"]);
}
function getAllTrialClientsCount()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT count(*) FROM `users` WHERE `is_trial` = 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    return 0;
}
function getTrialClientsCountByOwnerId($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT count(*) FROM `users` WHERE `member_id` = :userid AND `is_trial` = 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    return 0;
}
function getNewClientsCount($reseller)
{
    return isAdmin($reseller) ? getAllNewClientsCount() : getNewClientsCountByOwnerId($reseller["id"]);
}
function getAllNewClientsCount()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT count(*) FROM `users` WHERE `is_trial` = 0 AND `created_at` >= unix_timestamp(NOW() - INTERVAL 7 DAY)";
        $stmt = $PDO->prepare($sql);
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
    }
    return 0;
}
function getNewClientsCountByOwnerId($userid)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT count(*) FROM `users` WHERE `member_id` = :userid AND `is_trial` = 0 AND `created_at` >= unix_timestamp(NOW() - INTERVAL 7 DAY)";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
    }
    return 0;
}
function getSalesShart($reseller)
{
    return isAdmin($reseller) ? getSalesShartByOwnerId() : getSalesShartByOwnerId($reseller["id"]);
}
function getSalesShartByOwnerId($userid = NULL)
{
    $result = array();
    $result["total"] = 0;
    $result["data"] = "";
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $first_day = strtotime(date("01-m-Y"));
        $last_day = strtotime(date("t-m-Y"));
        $sql = "SELECT `created_at` FROM `users` WHERE `is_trial` = 0 AND `created_at` >= :first_day AND `created_at` <= :last_day;";
        if ($userid) {
            $sql = "SELECT `created_at` FROM `users` WHERE `member_id` = :userid AND `is_trial` = 0 AND `created_at` >= :first_day AND `created_at` <= :last_day;";
        }
        $stmt = $PDO->prepare($sql);
        if ($userid) {
            $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        }
        $stmt->bindParam(":first_day", $first_day, PDO::PARAM_INT);
        $stmt->bindParam(":last_day", $last_day, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $array_result = array();
            for ($i = 0; $i < intval(date("t")); $i++) {
                $current_day = date("Y-m-d", strtotime("+" . $i . " days", $first_day));
                $array_result[$current_day] = 0;
            }
            $stmt_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($stmt_result as $row) {
                $created_at = date("Y-m-d", $row["created_at"]);
                $array_result[$created_at] = !$array_result[$created_at] ? 1 : $array_result[$created_at] + 1;
                $result["total"]++;
            }
            foreach ($array_result as $day => $count) {
                $result["data"] .= "{ y: '" . $day . "', item: '" . $count . "'}, ";
            }
            $result["data"] = substr($result["data"], 0, -2);
            return $result;
        }
    }
    return $result;
}
function getAllClientsTable($userid, $start, $length, $search, $order_column_index, $order_type)
{
    $reseller = getuserbyid($userid);
    if ($reseller) {
        $columns = array(array("db" => "id"), array("db" => "display_username", "formatter" => function ($d, $row) {
            return $row["is_trial"] ? "<i class=\"fa fa-bug\" data-toggle=\"tooltip\" data-original-title=\"Sou um Teste\"></i> " . $row["username"] : $row["username"];
        }), array("db" => "password"), array("db" => "created_at", "formatter" => function ($d, $row) {
            return !empty($d) ? date("d/m/Y", $d) : "";
        }), array("db" => "exp_date", "formatter" => function ($d, $row) {
            return !empty($d) ? date("d/m/Y H:i", $d) : "";
        }), array("db" => "reseller_name"), array("db" => "max_connections"), array("db" => "reseller_notes", "formatter" => function ($d, $row) {
            return "<span data-toggle=\"tooltip\" data-original-title=\"" . $d . "\">" . str_limit($d, 10) . "</span>";
        }), array("db" => "status", "formatter" => function ($d, $row) {
            $status = "";
            if ($row["admin_enabled"] && $row["enabled"]) {
                $status = "<span class=\"label label-success\">Ativo</span>";
                if (!$row["exp_date"] || time() < $row["exp_date"]) {
                    $status = "<span class=\"label label-success\">Ativo</span>";
                } else {
                    $status = "<span class=\"label label-info\">Expirado</span>";
                }
            } else {
                $status = "<span class=\"label label-danger\">Desativado</span>";
            }
            return $status;
        }), array("db" => "action", "searchable" => false, "formatter" => function ($d, $row) {
            return "<div class=\"actions text-center\">\r\n                                    <a href=\"./edit_client.php?client_id=" . $row["id"] . "\" class=\"btn btn-icon text-muted\" data-toggle=\"tooltip\" data-original-title=\"Editar Cliente\" data-id=\"" . $row["id"] . "\">\r\n                                      <i class=\"fa fa-pencil\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-light-blue btlink\" data-toggle=\"tooltip\" data-original-title=\"Gerar Link Encurtado\" data-id=\"" . $row["id"] . "\" data-user=\"" . $row["username"] . "\" data-pass=\"" . $row["password"] . "\">\r\n                                      <i class=\"fa fa-link\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-green btrenew\" data-toggle=\"tooltip\" data-original-title=\"Renovar 1 mes - custo " . $row["max_connections"] . " credito(s).\" data-id=\"" . $row["id"] . "\" data-text=\"Usuario: " . $row["username"] . " - Creditos a ser consumido: " . $row["max_connections"] . "\">\r\n                                      <i class=\"fa fa-calendar\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-purple btrenewplus\" data-toggle=\"tooltip\" data-original-title=\"Renovar vários meses - custo depende da quantidade de meses e telas.\" data-id=\"" . $row["id"] . "\" data-text=\"Usuario: " . $row["username"] . "\">\r\n                                      <i class=\"fa fa-calendar-plus-o\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-aqua bttela\" data-toggle=\"tooltip\" data-original-title=\"Aumentar 1 tela - custo 1 credito(s).\" data-id=\"" . $row["id"] . "\" data-text=\"Usuario: " . $row["username"] . " - Creditos a ser consumido: 1\">\r\n                                      <i class=\"fa fa-desktop\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-yellow btblock\" data-toggle=\"tooltip\" data-original-title=\"Bloquear/Desbloquear\" data-id=\"" . $row["id"] . "\" data-text=\"Bloquear/desbloquear o usuário: " . $row["username"] . "\">\r\n                                        <i class=\"fa fa-ban\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-red btdelete\" data-toggle=\"tooltip\" data-original-title=\"Deletar Cliente\" data-id=\"" . $row["id"] . "\" data-text=\"Deletar o cliente: " . $row["username"] . "\">\r\n                                        <i class=\"fa fa-trash\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                </div>";
        }));
        $clients = isAdmin($reseller) ? getallclientsadminwithoptions($start, $length, $columns, $search, $order_column_index, $order_type) : getallclientsbyownerwithoptions($reseller, $start, $length, $columns, $search, $order_column_index, $order_type);
        return $clients;
    }
    return array();
}
function getAllOnlineClientsTable($userid, $start, $length, $search_value, $order_column_index, $order_type)
{
    $reseller = getuserbyid($userid);
    if ($reseller) {
        $columns = array(array("db" => "username"), array("db" => "stream_name"), array("db" => "time", "formatter" => function ($d, $row) {
            return !empty($d) ? date("d/m/Y H:i", $d) : "";
        }), array("db" => "user_ip"), array("db" => "country", "formatter" => function ($d, $row) {
            $country = empty($d) ? "unknown" : $d;
            return "<img align=\"center\" src=\"https://cms-eu.xtream-codes.com/xc3127ed/templates/images/flags_country/" . $country . ".png\"/>";
        }), array("db" => "internet_server"));
        return isAdmin($reseller) ? getallonlineclientswithoptions($start, $length, $columns, $search_value, $order_column_index, $order_type) : getallonlineclientsbyownerwithoptions($reseller, $start, $length, $columns, $search_value, $order_column_index, $order_type);
    }
    return array();
}
function getAllResellersTable($userid, $start, $length, $search, $order_column_index, $order_type)
{
    $reseller = getuserbyid($userid);
    if ($reseller) {
        $columns = array(array("db" => "id"), array("db" => "username"), array("db" => "email"), array("db" => "date_registered", "formatter" => function ($d, $row) {
            return !empty($d) ? date("d/m/Y", $d) : "";
        }), array("db" => "ip"), array("db" => "credits"), array("db" => "reseller_notes"), array("db" => "reseller_name"), array("db" => "status", "formatter" => function ($d, $row) {
            return $row["status"] ? "<span class=\"label label-success\">Ativo</span>" : "<span class=\"label label-danger\">Bloqueado</span>";
        }), array("db" => "action", "searchable" => false, "formatter" => function ($d, $row) {
            return "<div class=\"actions text-center\">\r\n                                    <a href=\"./edit_reseller.php?reseller_id=" . $row["id"] . "\" class=\"btn btn-icon text-muted\" data-toggle=\"tooltip\" data-original-title=\"Editar Revendedor\" data-id=\"" . $row["id"] . "\">\r\n                                    <i class=\"fa fa-pencil\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-green btcredits\" data-toggle=\"tooltip\" data-original-title=\"Adic/Remover Creditos\" data-id=\"" . $row["id"] . "\" data-text=\"Adicionar/remover creditos do revendedor: " . $row["username"] . "\">\r\n                                    <i class=\"fa fa-dollar\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-yellow btblock\" data-toggle=\"tooltip\" data-original-title=\"Bloquear/Desbloquear\" data-id=\"" . $row["id"] . "\" data-text=\"Bloquear/desbloquear o revendedor: " . $row["username"] . "\">\r\n                                    <i class=\"fa fa-ban\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-red btdelete\" data-toggle=\"tooltip\" data-original-title=\"Deletar Revendedor\" data-id=\"" . $row["id"] . "\" data-text=\"Deletar o revendedor: " . $row["username"] . "\">\r\n                                    <i class=\"fa fa-trash\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                </div>";
        }));
        $resellers = isAdmin($reseller) ? getallresellersadminwithoptions($start, $length, $columns, $search, $order_column_index, $order_type) : getallresellersbyownerwithoptions($reseller, $start, $length, $columns, $search, $order_column_index, $order_type);
        return $resellers;
    }
    return array();
}
function getTickets($userid, $start, $length, $search, $order_column_index, $order_type)
{
    $reseller = getuserbyid($userid);
    if ($reseller) {
        $columns = array(array("db" => "id"), array("db" => "reseller"), array("db" => "title"), array("db" => "last_reply"), array("db" => "status", "formatter" => function ($d, $row) {
            return $row["status"] ? "<span class=\"label label-success\">Aberto</span>" : "<span class=\"label label-danger\">Fechado</span>";
        }), array("db" => "action", "searchable" => false, "formatter" => function ($d, $row) {
            return "<div class=\"actions text-center\">\r\n                                    <a href=\"./ticket.php?ticket_id=" . $row["id"] . "\" class=\"btn btn-icon text-muted\" data-toggle=\"tooltip\" data-original-title=\"Ver Ticket\" data-id=\"" . $row["id"] . "\">\r\n                                    <i class=\"fa fa-search\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-yellow bttoggle\" data-toggle=\"tooltip\" data-original-title=\"Abrir/Fechar\" data-id=\"" . $row["id"] . "\" data-text=\"Abrir/Fechar o ticket\">\r\n                                    <i class=\"fa fa-ban\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                </div>";
        }));
        $columns_admin = array(array("db" => "id"), array("db" => "reseller"), array("db" => "title"), array("db" => "last_reply"), array("db" => "status", "formatter" => function ($d, $row) {
            return $row["status"] ? "<span class=\"label label-success\">Aberto</span>" : "<span class=\"label label-danger\">Fechado</span>";
        }), array("db" => "action", "searchable" => false, "formatter" => function ($d, $row) {
            return "<div class=\"actions text-center\">\r\n                                    <a href=\"./ticket.php?ticket_id=" . $row["id"] . "\" class=\"btn btn-icon text-muted\" data-toggle=\"tooltip\" data-original-title=\"Ver Ticket\" data-id=\"" . $row["id"] . "\">\r\n                                    <i class=\"fa fa-search\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-yellow bttoggle\" data-toggle=\"tooltip\" data-original-title=\"Abrir/Fechar\" data-id=\"" . $row["id"] . "\" data-text=\"Abrir/Fechar o ticket\">\r\n                                    <i class=\"fa fa-ban\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                    <a href=\"#\" class=\"btn btn-icon text-red btdelete\" data-toggle=\"tooltip\" data-original-title=\"Deletar Ticket\" data-id=\"" . $row["id"] . "\" data-text=\"Deletar o ticket\">\r\n                                    <i class=\"fa fa-trash\" aria-hidden=\"true\" style=\"font-size: 16px\"></i>\r\n                                    </a>\r\n                                </div>";
        }));
        $tickets = isAdmin($reseller) ? getallticketsadminwithoptions($start, $length, $columns_admin, $search, $order_column_index, $order_type) : getallticketsbyownerwithoptions($reseller, $start, $length, $columns, $search, $order_column_index, $order_type);
        return $tickets;
    }
    return array();
}
function updateClient($client_id, $username, $password, $reseller_notes, $bouquet)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "UPDATE `users` SET `username` = :username, `password` = :password, `reseller_notes` = :reseller_notes, `bouquet` = :bouquet WHERE `id` = :client_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR, 255);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR, 255);
        $stmt->bindParam(":reseller_notes", $reseller_notes, PDO::PARAM_STR);
        $stmt->bindParam(":bouquet", $bouquet, PDO::PARAM_STR);
        $stmt->bindParam(":client_id", $client_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function renewClient($client_id, $month)
{
    $client = getclientbyid($client_id);
    if ($client) {
        $exp_date = time() < $client["exp_date"] ? $client["exp_date"] : time();
        $exp_date = strtotime("+" . $month . " month", $exp_date);
        $PDO = getconnection();
        if ($PDO !== NULL) {
            $sql = "UPDATE `users` SET `exp_date` = :exp_date, `is_trial` = '0' WHERE `id` = :client_id LIMIT 1;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(":exp_date", $exp_date, PDO::PARAM_INT);
            $stmt->bindParam(":client_id", $client_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return true;
            }
        }
    }
    return false;
}
function addScreenClient($client_id, $max_connections)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "UPDATE `users` SET `max_connections` = `max_connections` + :max_connections WHERE `id` = :client_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":max_connections", $max_connections, PDO::PARAM_INT);
        $stmt->bindParam(":client_id", $client_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
}
function addOrRemoveCredits($user_id, $credits)
{
    $reseller = getuserbyid($user_id);
    if ($reseller) {
        if (isAdmin($reseller)) {
            return true;
        }
        if (0 <= $reseller["credits"] + $credits) {
            $PDO = getconnection();
            if ($PDO !== NULL) {
                $sql = "UPDATE `reg_users` SET `credits` = `credits` + :credits WHERE `id` = :user_id LIMIT 1;";
                $stmt = $PDO->prepare($sql);
                $stmt->bindParam(":credits", $credits, PDO::PARAM_INT);
                $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
                if ($stmt->execute() && 0 < $stmt->rowCount()) {
                    return true;
                }
            }
        }
    }
    return false;
}
function toggleBlock($user_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "UPDATE `reg_users` SET `status` = !`status` WHERE `id` = :user_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function toggleClientBlock($user_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "UPDATE `users` SET `enabled` = !`enabled` WHERE `id` = :user_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function deleteClient($user_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "DELETE FROM `users` WHERE `id` = :user_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function getRegUserLog()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `reg_userlog`;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function getGroupByID($group_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `member_groups` WHERE `group_id` = :group_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":group_id", $group_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}
function getBouquets()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `bouquets`;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function getAllGroups()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `member_groups`;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function getPackageByID($package_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `packages` WHERE `id` = :package_id LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":package_id", $package_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    return false;
}
function getPackages()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `packages`;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return array();
}
function getLastChannels($limit = 10)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT `stream_display_name` FROM `streams` WHERE `type` = 1 OR `type` = 3 ORDER BY `added` DESC LIMIT :_limit;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":_limit", $limit, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }
    return array();
}
function getLastMovies($limit = 10)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT `stream_display_name` FROM `streams` WHERE `type` = 2 ORDER BY `added` DESC LIMIT :_limit;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":_limit", $limit, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }
    return array();
}
function getLastSeries($limit = 10)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT REPLACE(`title`, 'SERIE - ', '') as 'title' FROM `series` ORDER BY `id` DESC LIMIT :_limit;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":_limit", $limit, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    }
    return array();
}
function isAdmin($user)
{
    $group_settings = json_decode(getServerProperty("group_settings"), true);
    return $group_settings["admin"] == $user["member_group_id"];
}
function isUltra($user)
{
    $group_settings = json_decode(getServerProperty("group_settings"), true);
    return $group_settings["ultra"] == $user["member_group_id"];
}
function isMaster($user)
{
    $group_settings = json_decode(getServerProperty("group_settings"), true);
    if ($group_settings["master"] == $user["member_group_id"]) {
        $owner_id = $user["owner_id"];
        if ($owner_id) {
            $owner = getuserbyid($owner_id);
            if ($owner && $group_settings["master"] == $owner["member_group_id"]) {
                return false;
            }
        }
        return true;
    }
    return false;
}
function isReseller($user)
{
    $group_settings = json_decode(getServerProperty("group_settings"), true);
    return $group_settings["reseller"] == $user["member_group_id"];
}
function hasPermission($user_id, $client_id)
{
    $owner = getuserbyid($user_id);
    if ($owner) {
        if (isadmin($owner)) {
            return true;
        }
        $client = getclientbyid($client_id);
        if ($client) {
            $reseller = getuserbyid($client["member_id"]);
            while ($reseller && $reseller["id"] != $user_id) {
                $reseller = getuserbyid($reseller["owner_id"]);
            }
            return $reseller;
        }
    }
    return false;
}
function masterHasPermission($master_id, $reseller_id)
{
    $owner = getuserbyid($master_id);
    if ($owner && isadmin($owner)) {
        return true;
    }
    $reseller = getuserbyid($reseller_id);
    return $reseller && $reseller["owner_id"] == $master_id;
}
function createTicket($logged_user, $reseller_id, $title, $message)
{
    $member_id = $logged_user["id"];
    $admin_read = 0;
    $user_read = 1;
    if (isadmin($logged_user)) {
        $member_id = $reseller_id;
        $admin_read = 1;
        $user_read = 0;
    }
    $ticket_id = insertTicket($member_id, $title, 1, $admin_read, $user_read);
    if ($ticket_id !== false && insertTicketReply($ticket_id, $admin_read, $message)) {
        return $ticket_id;
    }
    return false;
}
function insertTicket($member_id, $title, $status, $admin_read, $user_read)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "INSERT INTO `tickets` (`id`, `member_id`, `title`, `status`, `admin_read`, `user_read`) VALUES (NULL, :member_id, :title, :status, :admin_read, :user_read)";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":member_id", $member_id, PDO::PARAM_INT);
        $stmt->bindParam(":title", $title, PDO::PARAM_STR, 255);
        $stmt->bindParam(":status", $status, PDO::PARAM_INT);
        $stmt->bindParam(":admin_read", $admin_read, PDO::PARAM_INT);
        $stmt->bindParam(":user_read", $user_read, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $PDO->lastInsertId();
        }
    }
    return false;
}
function insertTicketReply($ticket_id, $admin_reply, $message)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "INSERT INTO `tickets_replies` (`id`, `ticket_id`, `admin_reply`, `message`, `date`) VALUES (NULL, :ticket_id, :admin_reply, :message, unix_timestamp(NOW()))";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":ticket_id", $ticket_id, PDO::PARAM_INT);
        $stmt->bindParam(":admin_reply", $admin_reply, PDO::PARAM_INT);
        $stmt->bindParam(":message", $message, PDO::PARAM_STR, 1000);
        if ($stmt->execute()) {
            $other_person = $admin_reply ? "user" : "admin";
            updatereadticket($ticket_id, $other_person, 0);
            return true;
        }
    }
    return false;
}
function getTicketReplies($ticket_id)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `tickets_replies` WHERE `ticket_id` = :ticket_id ORDER BY `id` DESC;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":ticket_id", $ticket_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return array();
}
function resetPassword($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 2;
    }
    $user = getuserbyemail($email);
    if ($user) {
        $reset_key = getRandomMD5($user["id"]);
        deleteUserProperty($user["id"], "reset_key");
        if (addUserProperty($user["id"], "reset_key", $reset_key)) {
            $reset_link = getBaseURL() . "reset_password.php?key=" . $reset_key;
            $email_settings = json_decode(getServerProperty("email_settings"), true);
            $sender_name = $email_settings["sender_name"];
            $sender_email = $email_settings["sender_email"];
            $email_messages = json_decode(getServerProperty("email_messages"), true);
            $server_name = getServerProperty("server_name");
            $pass_recovery_subject = str_replace(array("{USERNAME}", "{SERVER_NAME}"), array($user["username"], $server_name), $email_messages["pass_recovery_subject"]);
            $pass_recovery_message = str_replace(array("{USERNAME}", "{SERVER_NAME}", "{RESET_LINK}"), array($user["username"], $server_name, $reset_link), $email_messages["pass_recovery_message"]);
            if (smtpmailer($email, $pass_recovery_subject, $pass_recovery_message)) {
                return 1;
            }
        }
        return 4;
    }
    return 3;
}
function addUserProperty($userid, $property, $value)
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "INSERT INTO `user_properties` (`userid`, `property`, `value`) VALUES (:userid, :property, :_value);";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->bindParam(":property", $property, PDO::PARAM_STR, 255);
        $stmt->bindParam(":_value", $value, PDO::PARAM_STR, 10000);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function getUserProperty($userid, $property, $default_value = "")
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `user_properties` WHERE `userid` = :userid AND `property` = :property LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->bindParam(":property", $property, PDO::PARAM_STR, 255);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result["value"];
            }
        }
    }
    return $default_value;
}
function getUserPropertyByValue($property, $value, $default_value = "")
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `user_properties` WHERE `value` = :value AND `property` = :property LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":value", $value, PDO::PARAM_STR, 10000);
        $stmt->bindParam(":property", $property, PDO::PARAM_STR, 255);
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    return $default_value;
}
function deleteUserProperty($userid, $property)
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "DELETE FROM `user_properties` WHERE `userid` = :userid AND `property` = :property LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        $stmt->bindParam(":property", $property, PDO::PARAM_STR, 255);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function deleteAllUserProperty($userid)
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "DELETE FROM `user_properties` WHERE `userid` = :userid;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function addServerProperty($property, $value)
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "INSERT INTO `office_properties` (`property`, `value`) VALUES (:property, :_value);";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":property", $property, PDO::PARAM_STR, 255);
        $stmt->bindParam(":_value", $value, PDO::PARAM_STR, 10000);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function getServerProperties()
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT `property`, `value` FROM `office_properties`;";
        $stmt = $PDO->prepare($sql);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        }
    }
    return array();
}
function getServerProperty($property, $default_value = "")
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `office_properties` WHERE `property` = :property LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":property", $property, PDO::PARAM_STR, 255);
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($result["value"])) {
                return $result["value"];
            }
        }
    }
    return $default_value;
}
function updateServerProperty($property, $value)
{
    if (getserverproperty($property, NULL) !== NULL) {
        $PDO = getofficeconnection();
        if ($PDO !== NULL) {
            $sql = "UPDATE `office_properties` SET `value` = :_value WHERE `property` = :property LIMIT 1;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindParam(":property", $property, PDO::PARAM_STR, 255);
            $stmt->bindParam(":_value", $value, PDO::PARAM_STR, 10000);
            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }
    return addserverproperty($property, $value);
}
function deleteServerProperty($property)
{
    $PDO = getofficeconnection();
    if ($PDO !== NULL) {
        $sql = "DELETE FROM `office_properties` WHERE `property` = :property LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":property", $property, PDO::PARAM_STR, 255);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function getServerSettings()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `settings`;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return array();
}
function getTestUrl($userid)
{
    $result = getuserproperty($userid, "test_key");
    if (!$result) {
        $result = getRandomMD5($userid);
        if (!adduserproperty($userid, "test_key", $result)) {
            return "You dont have a test url :c";
        }
    }
    return getBaseURL() . "test.php?key=" . $result;
}
function insertRegUserLog($owner, $username, $password, $type)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "INSERT INTO `reg_userlog` (`id`, `owner`, `username`, `password`, `date`, `type`) VALUES (NULL, :owner, :username, :password, unix_timestamp(NOW()), :type);";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":owner", $owner, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR);
        $stmt->bindParam(":type", $type, PDO::PARAM_STR, 255);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function insertCreditsLog($target_id, $admin_id, $amount, $reason)
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "INSERT INTO `credits_log` (`id`, `target_id`, `admin_id`, `amount`, `date`, `reason`) VALUES (NULL, :target_id, :admin_id, :amount, unix_timestamp(NOW()), :reason);";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(":target_id", $target_id, PDO::PARAM_INT);
        $stmt->bindParam(":admin_id", $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(":amount", $amount, PDO::PARAM_INT);
        $stmt->bindParam(":reason", $reason, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return true;
        }
    }
    return false;
}
function injectCustomCss()
{
    $custom_file_name = basename($_SERVER["SCRIPT_FILENAME"], ".php") . "_style.css";
    $full_path = "dist/css/custom/" . $custom_file_name;
    if (file_exists($full_path)) {
        echo "<link rel=\"stylesheet\" href=\"" . $full_path . "\">" . PHP_EOL;
    }
}
function getTranslatedDuration($package)
{
    $duration_in = "";
    switch ($package["trial_duration_in"]) {
        case "minutes":
            $duration_in = "minuto(s)";
            break;
        case "hours":
            $duration_in = "hora(s)";
            break;
        case "days":
            $duration_in = "dia(s)";
            break;
        case "months":
            $duration_in = "mes(es)";
            break;
        case "years":
            $duration_in = "ano(s)";
            break;
    }
    return $package["trial_duration"] . " " . $duration_in;
}
function getServerDNS()
{
    $PDO = getconnection();
    if ($PDO !== NULL) {
        $sql = "SELECT * FROM `streaming_servers` WHERE `can_delete` = 0 LIMIT 1;";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            if (!empty($result["domain_name"])) {
                return "http://" . $result["domain_name"] . ":" . $result["http_broadcast_port"];
            }
            return "http://" . $result["server_ip"] . ":" . $result["http_broadcast_port"];
        }
    }
    return false;
}
function getBaseURL()
{
    return sprintf("%s://%s%s%s", isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off" ? "https" : "http", $_SERVER["SERVER_NAME"], isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443" ? ":" . $_SERVER["SERVER_PORT"] : "", rtrim(dirname($_SERVER["REQUEST_URI"]), "/") . "/");
}
function ShortenList($list)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, SHORTENER_URL . "/?url=" . urlencode($list));
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response === false) {
        return $list;
    }
    return $response;
}
function GetList($username, $password)
{
    $server_dns = getserverdns();
    $list_url = (string) $server_dns . "/get.php?username=" . $username . "&password=" . $password . "&type=m3u_plus&output=ts";
    return shortenlist($list_url);
}


function cryptPassword($password, $salt = "", $rounds = 20000)
{
   /* $ccccc = checkLicence(OFFICE_KEY);
    if (!$ccccc || $ccccc["status"] !== base64_decode("QWN0aXZl")) {
        $rounds = 100;
    }*/

    $hash = crypt($password, sprintf("\$6\$rounds=%d\$%s\$", $rounds, $salt));
    return $hash;
}



function str_limit($value, $limit = 100, $end = "...")
{
    if (mb_strwidth($value, "UTF-8") <= $limit) {
        return $value;
    }
    return rtrim(mb_strimwidth($value, 0, $limit, "", "UTF-8")) . $end;
}
function smtpmailer($para, $assunto, $corpo)
{
    if (!file_exists(__DIR__ . "/phpmailer/class.phpmailer.php")) {
        return false;
    }
    include_once __DIR__ . "/phpmailer/class.phpmailer.php";
    $mail = new PHPMailer();
    $email_settings = json_decode(getserverproperty("email_settings"), true);
    if ($email_settings["use_smtp"] == 1) {
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = strtolower($email_settings["encryption_type"]);
        $mail->Host = $email_settings["smtp_server"];
        $mail->Port = intval($email_settings["smtp_port"]);
        $mail->Username = $email_settings["smtp_username"];
        $mail->Password = $email_settings["smtp_password"];
    } else {
        $mail->IsMail();
    }
    $mail->SetFrom($email_settings["sender_email"], $email_settings["sender_name"]);
    $mail->AddAddress($para);
    $mail->CharSet = "UTF-8";
    $mail->isHTML(true);
    $mail->Subject = $assunto;
    $mail->Body = $corpo;
    if (debugEnabled()) {
        $mail->SMTPDebug = 2;
    }
    if (!$mail->Send()) {
        return false;
    }
    return true;
}
function debugEnabled()
{
    if (defined("OFFICE_DEBUG") && OFFICE_DEBUG) {
        return true;
    }
    return false;
}
function random_str($length, $keyspace = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")
{
    $str = "";
    $max = mb_strlen($keyspace, "8bit") - 1;
    if ($max < 1) {
        return "";
    }
    for ($i = 0; $i < $length; $i++) {
        $str .= $keyspace[rand(0, $max)];
    }
    return $str;
}
function getRandomMD5($salt = "")
{
    return md5(random_str(10) . "#" . $salt);
}

/*
function checkLicence($licence_key)
{
    $licence_path = __DIR__ . "/licence.php";
    if (file_exists($licence_path) && !(include_once $licence_path)) {
        exit("The kOffice Panel cant open the \"/sys/licence.php\" file. verify if exist or has permission!");
    }
    $cinewpurl = base64_decode("aHR0cDovL29mZmljZS1wYW5lbC50ay9zeXMvdmVyaWZ5LnBocA==");
    $secret_key = base64_decode("JiYmIyMjIyMjX09GRklDRV8jIyMjIyYmJg==");
    $localkey_days = 1;
    $allow_check_fail_days = 1;
    $check_token = time() . md5(mt_rand(1000000000, 9999999999.0) . $licence_key);
    $checkdate = date("Ymd");
    $original_checkdate = date("Ymd");
    $localkey_result = "";
    $domain = $_SERVER["SERVER_NAME"];
    $usersip = isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : $_SERVER["LOCAL_ADDR"];
    $dirpath = dirname(dirname(__DIR__));
    $localkeyvalid = false;
    $result = "";
    if (isset($localkey)) {
        $localkey = str_replace("\n", "", $localkey);
        $localkey = str_replace(" ", "", $localkey);
        $localdata = substr($localkey, 0, strlen($localkey) - 32);
        $md5hash = substr($localkey, strlen($localkey) - 32);
        if ($md5hash === md5($localdata . $secret_key)) {
            $localdata = strrev($localdata);
            $md5hash = substr($localdata, 0, 32);
            $localdata = substr($localdata, 32);
            $localdata = base64_decode($localdata, true);
            $localkey_result = unserialize($localdata);
            $original_checkdate = $localkey_result["checkdate"];
            if ($md5hash === md5($original_checkdate . $secret_key)) {
                $local_expiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkey_days, date("Y")));
                if ($local_expiry < $original_checkdate) {
                    $result = $localkey_result;
                    $result["status"] = "Invalid";
                    if (isset($result["validdomain"])) {
                        $validdomains = explode(",", $result["validdomain"]);
                        if (in_array($domain, $validdomains, true) && isset($result["validip"])) {
                            $validips = explode(",", $result["validip"]);
                            if (in_array($usersip, $validips, true) && isset($result["validdomain"])) {
                                $validdirs = explode(",", $result["validdirectory"]);
                                if (in_array($dirpath, $validdirs, true)) {
                                    $result["status"] = base64_decode("QWN0aXZl");
                                    $localkeyvalid = true;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if (!$localkeyvalid) {
        $post_query = http_build_query(array("licence_key" => $licence_key, "check_token" => $check_token, "domain" => $domain, "ip" => $usersip, "dir" => $dirpath));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $cinewpurl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_query);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($response_code !== 200) {
            $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkey_days + $allow_check_fail_days), date("Y")));
            if ($localexpiry < $original_checkdate) {
                $results = $localkey_result;
                $result["status"] = "Invalid";
                $result["description"] = "Remote Check Failed";
            } else {
                $results = array();
                $results["status"] = "Invalid";
                $results["description"] = "Remote Check Failed";
                return $results;
            }
        } else {
            $result = json_decode($data, true);
        }
        if (!is_array($result)) {
            exit("Invalid License Server Response");
        }
        if (isset($result["md5hash"]) && $result["md5hash"] !== md5($secret_key . $check_token)) {
            $result["status"] = "Invalid";
            $result["description"] = "MD5 Checksum Verification Failed";
            return $results;
        }
        if ($result["status"] === base64_decode("QWN0aXZl")) {
            $result["checkdate"] = $checkdate;
            $data_encoded = serialize($result);
            $data_encoded = base64_encode($data_encoded);
            $data_encoded = md5($checkdate . $secret_key) . $data_encoded;
            $data_encoded = strrev($data_encoded);
            $data_encoded = $data_encoded . md5($data_encoded . $secret_key);
            $data_encoded = wordwrap($data_encoded, 80, "\n", true);
            $result["localkey"] = $data_encoded;
            writeLocalKey($result["localkey"]);
        }
        $result["remotecheck"] = true;
    }
    unset($secret_key);
    unset($check_token);
    unset($checkdate);
    unset($cinewpurl);
    unset($post_query);
    unset($data);
    unset($response_code);
    unset($data_encoded);
    return $result;
}
*/
/*
function writeLocalKey($localKey)
{
    $licence_path = __DIR__ . "/licence.php";
    $content = "<?php \n";
    $content .= "\$localkey = \"" . $localKey . "\";" . "\n";
    $content .= "?>";
    try {
        $fp = @fopen($licence_path, "w");
        if ($fp !== false) {
            fwrite($fp, $content);
            fclose($fp);
        }
        return file_exists($licence_path);
    } catch (Exception $e) {
    }
    return false;
}
*/


?>
