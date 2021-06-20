<?php
namespace GeroyRegionov\Config;

class index
{
    private $params;

    public function index($params){
        switch($params[2]) {
            case "set":
                return "ok";
            case "get":
                return "get";
            default:
                return $this->error("404", "Method '".$params[2]."' not found.");
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