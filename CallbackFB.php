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
$helper = $fb->getRedirectLoginHelper();
$permissions = ['pages_manage_posts'];
$loginUrl = $helper->getLoginUrl('https://geroyregionov.schtil.com/CallbackFB.php', $permissions);

if(!session_id()) {
    session_start();
}
$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
}

catch(Exception $e) {
    echo 'Graph вернул ошибку: ' . $e->getMessage();
    exit;

}

catch(Exception $e) {
    echo 'Facebook SDK вернул ошибку: ' . $e->getMessage();
    exit;
}

if (isset($accessToken))
    $_SESSION['facebook_access_token'] = (string) $accessToken;

elseif ($helper->getError())
    exit;

try {
    $response = $fb->get('/me/accounts', $_SESSION['facebook_access_token']);
    $response = $response->getDecodedBody();
}

catch (Exception $e) {
    echo 'Graph вернул ошибку: ' . $e->getMessage();
    exit;
}

catch (Exception $e) {
    echo 'Facebook SDK вернул ошибку: ' . $e->getMessage();
    exit;
}

//Токен страницы
print_r($response);
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