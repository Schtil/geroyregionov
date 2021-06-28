<?php


namespace GeroyRegionov\Providers;


use CURLFile;

class ok extends provider
{
    private $token;
    public function push() {
        $fields = $this->getProviderFields();
        $photo_url = null;
        $group_id = null;
        $message = null;
        $token = null;
        foreach($fields as $field) {
            if($field->field_name == "token"){
                $token = $field->field_value;
            }
            if($field->field_name == "group_id"){
                $group_id = $field->field_value;
            }
        }
        if(isset($_GET["message"])) {
            $message = urldecode($_GET["message"]);
        }
        if(isset($_GET["photo_url"])) {
            $photo_url = urldecode($_GET["photo_url"]);
        }

        $checkResult = $this->checkParams([
            ["group_id",$group_id],
            ["message", $message],
            ["token", $token],
        ]);
        if($checkResult["status"] != "ok") {
            $this->error("120", "Some required fields were not specified. The field ".$checkResult["param"]." cannot be empty");
        }

        $this->token = $token;

        $attach = [
            "media" => [
                [
                    "type" => "text",
                    "text" => $message,
                ],
            ],
        ];
        if($photo_url != null) {
            $photoToken = $this->sendImage($photo_url, $group_id);
            $attach["media"][] =
                [
                    "type" => "photo",
                    "list" => [
                        [
                            "id" => $photoToken,
                        ],
                    ],
                ];
        }
        $answerOK = $this->request("mediatopic/post", [
            "gid"           => $group_id,
            "type"          => "GROUP_THEME",
            "attachment"    => json_encode($attach),
        ]);
        return $this->answer($answerOK);
    }

    private function sendImage($local_file_path, $group_id)
    {
        $uploadData = $this->getDocumentServer($group_id);
        $photoID = $uploadData["photo_ids"][0];
        $answer_ok = json_decode($this->sendFiles($uploadData["upload_url"], $local_file_path), true);
        return $answer_ok['photos'][$photoID]["token"];
    }

    private function sendFiles($url, $photo_url) {
        file_put_contents(md5($photo_url).".jpg", file_get_contents($photo_url));
        $post_fields = array(
            "file" => new CURLFile(md5($photo_url).".jpg")
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        unlink(md5($photo_url).".jpg");
        return $output;
    }

    private function getDocumentServer($group_id){
        return $this->request('photosV2/getUploadUrl',array('gid'=>$group_id));
    }

    private function request($method, $params)
    {
        $url = "https://api.ok.ru/api/" . $method;
        $params["application_key"] = $this->ENV("OK_CLIENT_PUBLIC");
        $params["application_id"] = $this->ENV("OK_CLIENT_ID");
        $params["application_secret_key"] = $this->ENV("OK_CLIENT_SECRET");
        $params["access_token"] = $this->token;
        $params["sig"] = $this->calcSignature($params);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
            ),
        ));
        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        if (!isset($response['error_code'])) {
            return $response;
        }
        else {
            $this->error(110, "An error occurred while executing the OK API method", $response);
            return "error";
        }
    }

    private function calcSignature($params): string
    {
        $secretKey = md5($this->token.$this->ENV("OK_CLIENT_SECRET"));
        $tmp = $params;
        if(isset($tmp["access_token"])) {
            unset($tmp["access_token"]);
        }
        ksort($tmp);
        $strParams = "";
        foreach($tmp as $key => $value) {
            $strParams .= $key."=".$value;
        }
        return md5($strParams.$secretKey);
    }
}