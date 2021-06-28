<?php
include('vendor/autoload.php');
use Telegram\Bot\Api;

$telegram = new Api(ENV("TG_API_KEY"));
$result = $telegram -> getWebhookUpdates();

$botIsAdmin = "нет";
if(isset($result["message"]["forward_from_chat"])) {
    $infoChat = json_decode(
        file_get_contents("https://api.telegram.org/bot".ENV("TG_API_KEY")."/getChat?chat_id=".$result["message"]["forward_from_chat"]["id"]),
        1
    );
    if($infoChat["ok"]) {
        $botIsAdmin = "да";
    } else {
        $botIsAdmin = "нет";
    }
    $telegram->sendMessage([
        'chat_id' => $result["message"]["chat"]["id"],
        "text" => "Channel Title: ".$result["message"]["forward_from_chat"]["title"]."\n".
            "Channel ID: ".$result["message"]["forward_from_chat"]["id"]."\n".
            "Данный бот администратор в этом канале - ". $botIsAdmin,
    ]);
}
$telegram->sendMessage([
    'chat_id' => $result["message"]["chat"]["id"],
    "text" => "Привет! Перешли мне сообщение из любого канала и я выведу информацию о нем",
]);

function ENV($index, $default = NULL)
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