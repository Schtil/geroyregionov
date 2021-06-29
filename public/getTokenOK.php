<?php
$client_id = ENV("OK_CLIENT_ID");
$client_secret = ENV("OK_CLIENT_SECRET");
if(!isset($_GET["code"])) {
    header('location: https://connect.ok.ru/oauth/authorize?client_id='.$client_id.'&scope=VALUABLE_ACCESS;LONG_ACCESS_TOKEN;GROUP_CONTENT;PHOTO_CONTENT&response_type=code&redirect_uri='.ENV("HOST").'/getTokenOK.php');
}
$code = $_GET["code"];

$params = [
    "code" => $code,
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "redirect_uri" => ENV("HOST").'/getTokenOK.php',
    "grant_type" => "authorization_code",
];
$tokenData = request("oauth/token.do", $params);
echo "<h2>Ваш токен: ".$tokenData["access_token"]."</h2>";

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

function request($method,$params=array())
{
    $url = 'https://api.ok.ru/'.$method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type:multipart/form-data"
    ));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
    $result = json_decode(curl_exec($ch), True);
    curl_close($ch);
    return $result;
}