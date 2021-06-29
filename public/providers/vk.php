<?php


namespace GeroyRegionov\Providers;


use CURLFile;
use RedBeanPHP\R;

class vk extends provider
{
    private $token;
    private float $v = 5.131;

    public function push() {
        $fields = $this->getProviderFields();
        $photo_url = null;
        $group_id = null;
        $message = null;
        $token = null;
        $copyright = null;
        $attach = null;
        foreach($fields as $field) {
            if($field->field_name == "group_id"){
                $group_id = $field->field_value;
            }
            if($field->field_name == "token"){
                $token = $field->field_value;
            }
        }
        if(isset($_GET["group_id"])) {
            $group_id = urldecode($_GET["group_id"]);
        }
        if(isset($_GET["token"])) {
            $token = urldecode($_GET["token"]);
        }
        if(isset($_GET["message"])) {
            $message = urldecode($_GET["message"]);
        }
        if(isset($_GET["photo_url"])) {
            $photo_url = urldecode($_GET["photo_url"]);
        }
        if(isset($_GET["copyright"])) {
            $copyright = urldecode($_GET["copyright"]);
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

        if($photo_url != null) {
            $attach = $this->sendImage($photo_url, $group_id);
        }
        $answerVK = $this->request("wall.post", [
            "owner_id"      => "-".$group_id,
            "from_group"    => 1,
            "message"       => $message,
            "attachments"   => $attach,
            "copyright"     => $copyright,
        ]);
        return $this->answer($answerVK);
    }

    private function sendImage($local_file_path, $group_id)
    {
        $upload_url = $this->getDocumentServer($group_id)['upload_url'];

        $answer_vk = json_decode($this->sendFiles($upload_url, $local_file_path, 'photo'), true);

        $upload_file = $this->savePhoto($answer_vk['photo'], $answer_vk['server'], $answer_vk['hash'], $group_id);

        return "photo" . $upload_file[0]['owner_id'] . "_" . $upload_file[0]['id'];
    }

    private function sendFiles($url, $photo_url, $type = 'file') {

        file_put_contents(md5($photo_url).".jpg", file_get_contents($photo_url));
        $post_fields = array(
            $type => new CURLFile(md5($photo_url).".jpg")
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);
        unlink(md5($photo_url).".jpg");
        return $output;
    }

    private function getDocumentServer($group_id){
        return $this->request('photos.getWallUploadServer',array('group_id'=>$group_id));
    }

    private function savePhoto($photo, $server, $hash, $group_id){
        return $this->request('photos.saveWallPhoto',array('photo'=>$photo, 'server'=>$server, 'hash' => $hash, "group_id" => $group_id));
    }

    private function request($method,$params=array())
    {
        $url = 'https://api.vk.com/method/'.$method;

        $params['access_token']=$this->token;
        $params['v']=$this->v;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
        $result = json_decode(curl_exec($ch), True);
        curl_close($ch);
        if (isset($result['response'])) {
            return $result['response'];
        }
        else {
            $this->error(110, "An error occurred while executing the VK API method", $result);
        }
    }
}