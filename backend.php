<?php
require("./YeapCloud/YeapCaptcha.php");
header('Access-Control-Allow-Credentials:true');
header('Access-Control-Allow-Origin:*');

$act = !empty($_GET['act']) ? $_GET['act'] : '';
$type = !empty($_GET['type']) ? $_GET['type'] : 'rotate';
$app['type'] = $type;

$YeapCaptcha = new YeapCaptcha('Your Yeap Captcha APP ID','Your Yeap Captcha Secrect Key','rotate');

if($act == 'challenge'){
    $res = $YeapCaptcha->challenge('test', YeapCaptcha::get_client_ip());
    if($res){
        exit(json_encode(['status'=>1] + $res));
    }else{
        exit(json_encode(['status'=>0]));
    }
}elseif($act == 'verify'){
    $challenge = !empty($_GET['challenge']) ? $_GET['challenge'] : exit(json_encode(['status'=>-1]));
    $res = $YeapCaptcha->verify($challenge, 'test', YeapCaptcha::get_client_ip(), true);
    if($res){
        exit(json_encode(['status'=>1, 'success'=>$res]));
    }else{
        exit(json_encode(['status'=>0]));
    }
}
