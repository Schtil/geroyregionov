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
echo '<form name="feedback" class="reachgoal feedback_form" data-target="" method="POST">
<input type="hidden" value="" name="key"/><div class="relContainer"><label for="postText">Post Message</label><input type="text" name="postText" id="postText" class="saveFiled"></div>
<div class="relContainer"><label for="pageID">PageID</label><input type="text" name="pageID" id="pageID" class="saveFiled"></div>
<div class="relContainer"><label for="accessToken">accessToken</label><input type="text" name="accessToken" id="accessToken" class="saveFiled"></div>
<button type="submit" formaction="TestFB.php" formtarget="_blank" class="btn btn-send">Разместить</button></form>';

echo '<a id="fb-token" href="' . htmlspecialchars($loginUrl) . '"  target="_blank">Получить токен facebook ( пользовательский и страницы ) ( при отсутствии )</a>';


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