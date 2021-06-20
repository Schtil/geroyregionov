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
echo "<head>
  <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js\"></script>
</head>";
echo "<script>
$(document).ready(function(){
	$('form[name=feedback]').each(function(){
		var text_send = 'Отправить';
		var message_compleate = 'Спасибо, ваше сообщение отправлено!';
		var err = false;
		var _this = $(this);
		var btn_send = _this.find('.btn.btn-send');
		var fields = _this.find('input[type=text], textarea, select');
		//проверка на обязательность
		fields.each(function(){
			if ($(this).hasClass('required')){
				if ($(this).val()==''){
					$(this).addClass('error').focus();
					err = true;
					return false;
				}
			}
		});
		if(err==true){
		   return false;
		}
		var fdata = $(_this).serialize();
				//Отправка
		$.ajax({
			type: 'POST',
			url: window.location.origin +'/' + 'TestFB.php', //путь к файлу
			data: fdata,
			success: function(msg) {
				alert(msg);
						$(btn_send).removeAttr('disabled','disabled').removeClass('disabled').html(text_send);
 			}
		});
						return false;
			});
   });
</script>";
echo '<form name="feedback" class="reachgoal feedback_form" data-target="" method="POST">
<input type="hidden" value="" name="key"/><div class="relContainer"><label for="postText">Post Message</label><input type="text" name="postText" id="postText" class="saveFiled"></div>
<div class="relContainer"><label for="pageID">PageID</label><input type="text" name="pageID" id="pageID" class="saveFiled"></div>
<div class="relContainer"><label for="accessToken">accessToken</label><input type="text" name="accessToken" id="accessToken" class="saveFiled"></div>
<button type="submit" class="btn btn-send">Разместить</button></form>';

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