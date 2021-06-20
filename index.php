<?php
namespace GeroyRegionov;

use GeroyRegionov\Config\Index as Config;

$application = new index();
echo $application->execute();


class index {

    private $params;
    public function __constructor(){
        $requestUri = $_SERVER["REQUEST_URI"];
        $requestUri = explode("?", $requestUri);
        $reqAddr = $requestUri[0];
        $params = explode("/",$reqAddr);
        $this->params = $params;
    }

    public function execute(){
        if(!isset($params[1])) {
            return $this->error(100, "No set group methods");
        }
        if(!isset($params[2])) {
            return $this->error(100, "No set name method");
        }

        switch($params[1]) {
            case "config":
                $config = new Config();
                return $config->index($params);
            case "push":
                return $this->error(106,"Метод пока не создан");
                break;
            default:
                return $this->error(404,"Group '".$params[1]."' not found");
        }
    }

    public function error($code, $message){
        $answer = [
            "status" => "error",
            "error_code" => $code,
            "error_msg" => $message,
        ];
        return json_encode($answer);
    }

}