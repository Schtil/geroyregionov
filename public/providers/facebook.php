<?php


namespace GeroyRegionov\Providers;


class facebook extends provider
{
    public function push(){
        $fb = new \Facebook\Facebook([
            'app_id' => $this->env("FACEBOOK_ID"),
            'app_secret' => $this->env("FACEBOOK_SECRET"),
        ]);
        $helper = $fb->getRedirectLoginHelper() ;
        $permissions = ['manage_pages','publish_pages'];
        $loginUrl = $helper->getLoginUrl('https://your-url.ru/callback.php', $permissions);

    }
}