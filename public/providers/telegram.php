<?php
namespace GeroyRegionov\Providers;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramOtherException;

class telegram extends provider
{
    public function push() {
        $fields = $this->getProviderFields();
        $photo_url = null;
        $channel_id = null;
        $message = null;
        foreach($fields as $field) {
            if($field->field_name == "channel_id"){
                $channel_id = $field->field_value;
            }
        }
        if(isset($_GET["channel_id"])) {
            $channel_id = urldecode($_GET["channel_id"]);
        }
        if(isset($_GET["message"])) {
            $message = urldecode($_GET["message"]);
        }
        if(isset($_GET["photo_url"])) {
            $photo_url = urldecode($_GET["photo_url"]);
        }

        $checkResult = $this->checkParams([
            ["channel_id",$channel_id],
            ["message", $message],
        ]);
        if($checkResult["status"] != "ok") {
            $this->error("120", "Some required fields were not specified. The field ".$checkResult["param"]." cannot be empty");
        }
        $telegram = new Api($this->ENV("TG_API_KEY"));
        $status = ["chat" => null];
        try {
            $status = $telegram->sendMessage(["chat_id" => $channel_id, "text" => $message]);
        } catch (\Exception $exception) {
            $this->error("116", "An error occurred while executing the Telegram API method", $exception->getMessage());
            return "error";
        }
        if($photo_url != null) {
            try {
                $telegram->sendPhoto(['chat_id' => $channel_id, 'photo' => $photo_url]);
            } catch (\Exception $exception) {
                $this->error("116", "An error occurred while executing the Telegram API method", $exception->getMessage());
                return "error";
            }
        }
        return $this->answer($status);
    }
}