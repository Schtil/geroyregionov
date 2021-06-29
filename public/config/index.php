<?php
namespace GeroyRegionov\Config;
use \RedBeanPHP\R;

class index
{
    private $params;

    public function __construct()
    {
        R::setup( "mysql:host=".$this->ENV("MYSQL_HOST","localhost").";dbname=".$this->ENV("MYSQL_DATABASE")."", $this->ENV("MYSQL_USER"), $this->ENV("MYSQL_PASS") );
    }

    public function index($method, $db){

        switch($method) {
            case "set":
                return "ok";
            case "get":

            default:
                return $this->error("404", "Method '".$method."' not found.");
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