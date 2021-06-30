<?php
namespace GeroyRegionov;

use Exception;
use GeroyRegionov\Providers;
use RedBeanPHP\R;

spl_autoload_register(function ($class_name) {
    $class_name = mb_strtolower(str_replace(__NAMESPACE__ ."\\", "", $class_name));
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        $class_name = str_replace("\\", "/", $class_name);
    }
    if(file_exists($class_name.".php")) {
        include $class_name . '.php';
    }
});
include("vendor/autoload.php");

$application = new index();
echo $application->execute();


class index {

    private $params;

    public function __construct(){
        R::setup( "mysql:host=".$this->ENV("MYSQL_HOST","localhost").";dbname=".$this->ENV("MYSQL_DATABASE")."", $this->ENV("MYSQL_USER"), $this->ENV("MYSQL_PASS") );
        if(isset($_GET["mode"])) {
            if($_GET["mode"] == "debug") {
                $this->params = explode("/","/".$_GET["method"]);
                return;
            }
        }
        $requestUri = $_SERVER["REQUEST_URI"];
        $requestUri = explode("?", $requestUri);
        $reqAddr = $requestUri[0];
        $params = explode("/",$reqAddr);
        $this->params = $params;

    }

    public function execute(){
        if(!isset($_GET["access_token"])){
            return $this->error(103, "Access token must be set");
        }
        $token = R::findOne("tokens", "token = ?", [$_GET["access_token"]]);
        if(!$token) {
            return $this->error(104, "Such a access token does not exist");
        }
        $params = $this->params;
        if(!isset($params[1])) {
            return $this->error(105, "No set name method");
        }
        if(!isset($_GET["provider"])) {
            return $this->error(106,"No set provider");
        }
        $providerName = $_GET["provider"];

        $classCall = "GeroyRegionov\\providers\\".$providerName;
        if(!class_exists($classCall)) {
            return $this->error(101,"Not found provider '".$providerName."'");
        }
        $provider = new $classCall();
        $methodName = $params[1];
        if(!method_exists($provider,$methodName)) {
            return $this->error(102,"Not found method '".$methodName."' in provider '".$providerName."'");
        }
        return $provider->$methodName();
    }

    public function error($code, $message){
        $answer = [
            "status" => "error",
            "error_code" => $code,
            "error_msg" => $message,
        ];
        return json_encode($answer);
    }

    private function ENV($index, $default = NULL)
    {
        $file = file_get_contents("../.env");
        $file = explode(PHP_EOL, $file);
        $params = [];
        foreach($file as $item)
        {
            $item = explode("=", $item);
            $params[trim($item[0])] = trim($item[1]);
        }
        if(isset($params[$index])) {
            return $params[$index];
        }
        return $default;
    }




}
