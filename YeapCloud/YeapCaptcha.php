<?php

class YeapCaptcha {
    private static $app = [];
    public function __construct($id, $sk, $type){
        self::$app = [
            'id'=>$id,
            'sk'=>$sk,
            'type'=>$type,
            'api'=>'https://captcha_api.yeapcloud.cn/api.php'
            ];
    }
    private static function sk($arr){
        $app = self::$app;
        ksort($arr);
        $str = [];
        foreach($arr as $k=>$v){
            $str[] = $k.'='.$v;
        }
        $q = implode('&', $str);
        $str = $app['id'] . $q . $app['sk'];
        $key = md5($str);
        return [$q, $key];
    }
    public static function challenge($userid, $ip){
        $app = self::$app;
        $arr = ['action'=>'challenge', 'type'=>$app['type'], 'appId'=>$app['id'], 'userid'=>$userid, 'ip'=>$ip, 'client_type'=>'web'];
        $key = self::sk($arr);
        $url = $app['api'] . '?' . $key[0] . '&key=' . $key[1];
        $data = json_decode(file_get_contents($url), true);// var_dump($url);
        if($data['status']){
            return $data;
        }
        return false;
    }
    
    public static function verify($challenge, $userid, $ip, $query = false){
        $app = self::$app;
        $arr = ['action'=>'verify', 'appId'=>$app['id'], 'challenge'=>$challenge, 'userid'=>$userid, 'ip'=>$ip];
        $key = self::sk($arr);
        $url = $app['api'] . '?' . $key[0] . '&key=' . $key[1];
        $data = file_get_contents($url);//var_dump($data);
        $data = json_decode($data, true);
        if($data['status']){
            if($query){
                if($data['query']) return false;
            }
            return $data['success'];
        }
        return false;
    }
    
    public static function get_client_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] as $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }
}
