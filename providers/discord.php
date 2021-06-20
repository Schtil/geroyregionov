<?php


namespace GeroyRegionov\Providers;


class discord extends provider
{

    public function push() {
        $webhook_url = null;
        $message = null;
        $photo_url = null;
        $fields = $this->getProviderFields();

        foreach($fields as $field) {
            if($field->field_name == "webhook_url"){
                $webhook_url = $field->field_value;
            }
        }
        if(isset($_GET["message"])) {
            $message = urldecode($_GET["message"]);
        }
        if(isset($_GET["photo_url"])) {
            $photo_url = urldecode($_GET["photo_url"]);
        }
        $checkResult = $this->checkParams([
            ["message", $message],
        ]);
        if($checkResult["status"] != "ok") {
            $this->error("120", "Some required fields were not specified. The field ".$checkResult["param"]." cannot be empty");
        }
        if($photo_url != null) {
            $this->request($webhook_url, ["content" => $message, "embeds" => [["image" => ["url" => $photo_url]]]]);
        } else {
            $this->request($webhook_url, ["content" => $message]);
        }
    }

    private function request($url, $fields)
    {
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
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}