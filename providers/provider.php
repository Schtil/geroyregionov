<?php


namespace GeroyRegionov\Providers;


use RedBeanPHP\R;

class provider
{
    public $class;
    public function __construct(){
        $this->class = explode("\\",get_class($this))[2];
        R::setup( "mysql:host=".$this->ENV("MYSQL_HOST","localhost").";dbname=".$this->ENV("MYSQL_DATABASE")."", $this->ENV("MYSQL_USER"), $this->ENV("MYSQL_PASS") );
    }
    public function list(){
        $fieldsData = $this->getProviderFields();
        $fields = [];
        foreach($fieldsData as $fieldData) {
            $fields[] = [
                "field_name" => $fieldData->field_name,
                "field_value" => $fieldData->field_value,
                "field_info" => $fieldData->field_info,
            ];
        }
        $answer = [
            "provider" => $this->class,
            "fields" => $fields,
        ];
        return $this->answer($answer);
    }

    protected function getProviderFields(): array
    {
        return R::find("providers", "provider_name = ?", [$this->class]);
    }

    protected function checkParams($params): array
    {
        foreach($params as $param) {
            if($param[1] == null or $param[1] == '') {
                return ["status" => "false", "param" => $param[0]];
            }
        }
        return ["status" => "ok"];
    }

    public function get($field = null){

        if(isset($_GET["field"]) and $field == null) {
            $field = urldecode($_GET["field"]);
        }
        $checkResult = $this->checkParams([
            ["field",$field],
        ]);
        if($checkResult["status"] != "ok") {
            $this->error("120", "Some required fields were not specified. The field {".$checkResult["param"]."} cannot be empty");
        }
        $value = null;
        $info = null;
        $link = null;
        $fields = $this->getProviderFields();
        foreach ($fields as $fieldDB) {
            if($fieldDB->field_name == $field) {
                $value = $fieldDB->field_value;
                $link = $fieldDB->field_link;
                $info = $fieldDB->field_info;
            }
        }
        if(is_null($value)) {
            $this->error("404", "Field {".$field."} not found from provider {".$this->class);
            return "error";
        } else {
           return $this->answer(["field" => $field, "value" => $value, "info" => $info, "link" => $link]);
        }
    }

    public function set(){
        $field = null;
        $value = null;
        if(isset($_GET["field"])) {
            $field = urldecode($_GET["field"]);
        }
        if(isset($_GET["value"])) {
            $value = urldecode($_GET["value"]);
        }
        $checkResult = $this->checkParams([
            ["field",$field],
            ["value",$value],
        ]);
        if($checkResult["status"] != "ok") {
            $this->error("120", "Some required fields were not specified. The field {".$checkResult["param"]."} cannot be empty");
        }

        $fields = $this->getProviderFields();
        foreach ($fields as $fieldDB) {
            if($fieldDB->field_name == $field) {
                $fieldDB->field_value = $value;
                R::store($fieldDB);
            }
        }
        if(is_null($value)) {
            $this->error("404", "Field {".$field."} not found from provider {".$this->class);
            return "error";
        } else {
            return $this->get($field);
        }
    }

    public function answer($data){
        $answer = [
            "status" => "ok",
            "data" => $data,
        ];
        return json_encode($answer);
    }

    public function error($code, $message, $data = false){
        $answer = [
            "status" => "error",
            "error_code" => $code,
            "error_msg" => $message,
        ];
        if($data) {
            $answer["error_details"] = $data;
        }
        echo json_encode($answer);
        exit(200);
    }

    protected function ENV($index, $default = NULL)
    {
        $file = file_get_contents(".env");
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