<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    // 使用するAPIのベースURL
    private $api_base_url = 'http://localhost/EC_exercise_Phalcon/';

    // 使用するAPIのベースURLを取得するメソッド
    public function getBaseURL_ofAPI(){
        return $this->api_base_url;
    }
}
