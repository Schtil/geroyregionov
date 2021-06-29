<?php
include("vendor/autoload.php");
use Facebook\Facebook;

If (!session_id()) {
    session_start();
}

$fb = new Facebook([
    'app_id' => ENV("FACEBOOK_ID"),
    'app_secret' => ENV("FACEBOOK_SECRET"),
]);
$pageID = $_POST["pageID"];
$postText = $_POST["postText"];
$accessToken = $_POST["accessToken"];
$str_page = '/' . $pageID . '/feed';
$feed = array('message' => $postText);
try {
	$response = $fb->post($str_page, $feed, $accessToken);
}
catch (Exception $e) {
	echo 'Facebook SDK вернул ошибку: ' . $e->getMessage();
	exit;
}
$graphNode = $response->getGraphNode();
echo 'Опубликован пост, id: ' . $graphNode['id'];

function ENV($index, $default = NULL)
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