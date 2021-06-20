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

$helper = $fb->getRedirectLoginHelper() ;
$permissions = ['pages_manage_posts'];
$loginUrl = $helper->getLoginUrl('https://geroyregionov.schtil.com/CallbackFB.php', $permissions);


echo '<p style="color: blue;"> Текст вашего поста </p>';
echo '<span id="post-text"> ' . "Hello world!" . '</span>';
echo '<p> Введите здесь ID страницы, куда будет размещаться пост. Он потребуется как для размещения поста, так и для получения токена доступа Facebook </p>';
echo '<input id="pageid-fb"></br>';

echo '<a id="fb-token" href="' . htmlspecialchars($loginUrl) . '"  target="_blank">Получить токен facebook ( пользовательский и страницы ) ( при отсутствии )</a>';
echo '<p> Если у вас нет токена для вашей страницы Facebook, получите его по ссылке выше. Если у вас есть токен, просто введите его в поле ниже. </p>';
echo '<p> Впишите сюда ваш токен страницы Facebook </p>';
echo '<input id="accesstoken-fb"></br>';
echo '<button id="facebook-post"> Разместить пост в Facebook </button>';

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