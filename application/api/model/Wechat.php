<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/8 0008
 * Time: 17:31
 */

namespace app\api\model;


use app\lib\exception\WechatCodeException;
use app\lib\Qcloud\Jssdk;
use think\Model;
use think\Session;
use app\lib\Qcloud\Openid as OpenidModel;
use think\Db;

class Wechat extends Model
{
    protected $table = 't_members';
    /**
     *  初始化获取openid
     */
    public function openid($code,$urlSkip){
        if(!$code){
            $redirect_uri = $urlSkip;
            $appId = config('we_appId');
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
            throw new WechatCodeException($url);
        }else{
            $openid = Session::get('openid');
//            var_dump($openid);
            if(!$openid){
                $code = input('code');
                $openid = OpenidModel::get($code);
                if($openid){
                    Session::set('openid', $openid);
                }
            }
            return $openid;
        }
    }
    /*
     * 初始化获取accessToken
     */
    public function getAcessToken(){
        $jssdk = new Jssdk(config('we_appId'), config('we_appSecret'));
        $accessToken = $jssdk->getAccessToken();
        return $accessToken;
    }
    /**
     * 初始化获取jsSdk权限
     */
    public function jsSdk($url){
        $jssdk = new Jssdk(config('we_appId'), config('we_appSecret'));
        $signPackage = $jssdk->GetSignPackage($url);
        return $signPackage;
    }
    /**
     * 清空文件夹下的所有图片
     */
    public function delFile($dirName){
        $files = glob($dirName.'*');
        if($files){
            foreach($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
    /*
     * 获取openid的用户信息
     * 在获取用户信息前必须先缓存accessToken 和 openid
     * 也就是说要先进行jssdk的初始化和
     */
    public function getUserInfo(){
        /**
         * 获取用户信息
         */
        $jssdk = new Jssdk(config('we_appId'), config('we_appSecret'));
        $access_token = $jssdk->getAccessToken();
        $openid = Session::get('openid');
        $d = '{}';
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid;
        $result = $this->https_post($url,$d);
        return json_decode($result);
    }
    /*
     * 获取客服信息
     */
    public function getonlinekflist($token){
        $url = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='.$token;
        $data = $this->curl_post($url);
        return $data;
    }
    /**
     * 发送图片的客服消息的接口
     */
    public function sendNews($openid, $media_id){
//        var_dump($openid);
        $data = '{
        "touser": "'.$openid.'",
        "msgtype": "image",
        "image":
            {
                 "media_id": "'.$media_id.'"
            }
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".Session::get('accessToken');
        $result = $this->https_post($url,$data);
        $final = json_decode($result);
        return $final;
    }
    /*
     * 向微信服务器发送图片素材
     * 生成图片id以便发送客服消息
     */
    public function sendMsg($imgUrl,$TOKEN){
//        var_dump($imgUrl);
//        $TOKEN = Session::get('accessToken');
        // $imgUrl = 'E:\xampp\htdocs\zerg\public\/uploads/sharecard/od-XDwNudw1DUOgVB-DFgrjNqfhw.png';

        if(!file_exists($imgUrl)){
            return '不存在图片';
//            exit();
        }
        $URL ='http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$TOKEN.'&type=image';
//        $data = array('media'=>'@'.$imgUrl);
        $mediaFile = realpath($imgUrl);
        $data   = array('media' => '@'.$mediaFile);
        $miniType = mime_content_type($mediaFile);
        if(class_exists('CurlFile')){
            $media = new \CURLFile($mediaFile);
            $media->setMimeType($miniType);
            $data = array('media' => $media);
        }
        $res = json_decode($this->curl_post($URL, $data), true);
//        var_dump($res);
//        $result = $this->curl_post($URL,$data);
//        $data = json_decode($result,true);
        return $res['media_id'];
    }
    /**
     * 生成发送的二维码图片
     */
    public function qrcode($url,$size=5,$level=3)
    {
        $openid = Session::get('openid');
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        // 生成二维码图片
        $object = new \QRcode();
        $QR = ROOT_PATH . 'public/' . DS . '/uploads/ewm/'.$openid.'.jpg';
        $object->png($url, $QR, $errorCorrectionLevel, $matrixPointSize, 2);
        // 查询当前openid的头像地址
        $logo = Db::table('t_members')->where('openid',$openid)->value('headimgurl');
        $dh = ROOT_PATH . 'public/' . DS . '/uploads/ewm/ewmlogo/'.$openid.'.jpg';  //二维码图片地址
        if($logo !== FALSE){
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        $result = imagepng($QR,$dh);  //跟logo合并之后的地址
        return $result;
    }
    /**
     * 通过微信服务器，获取该微信公众号的二维码
     */
    public function weCode($openid, $TOKEN){
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$TOKEN;
        $data = '{
            "expire_seconds": 2592000,
            "action_name": "QR_STR_SCENE", 
            "action_info": 
                {"scene": 
                    {"scene_str": "'.$openid.'"}
                }
        }';
        $result = $this->api_notice_increment($url, $data);
        $result = json_decode($result, true);
        if($result['url']) {
            return $result['url'];
        }else{
            return '生成微信公众号的二维码失败！';
        }
    }
    /**
     * 通过微信服务器，获取该微信公众号的二维码
     */
    public function weCode2($openid, $TOKEN){
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$TOKEN;
        $data = '{
            "expire_seconds": 2592000,
            "action_name": "QR_STR_SCENE", 
            "action_info": 
                {"scene": 
                    {"scene_str": "'.$openid.'"}
                }
        }';
        $result = $this->api_notice_increment($url, $data);
        $result = json_decode($result, true);
        if($result) {
            return $result;
        }else{
            return '生成微信公众号的二维码失败！';
        }
    }

    /**
     * @param $url
     * @param $data
     * @return mixed|string
     * 获取用户头图地址
     */
    public function getHeadImg($openid, $headimgurl){
        // 获取头图
        $pathHead = ROOT_PATH . '/public/uploads/headimg/' . $openid . '.png';
        if(!file_exists($pathHead)){
            $this->download($headimgurl, $pathHead);
        }
        return config('host') .'/uploads/headimg/' . $openid . '.png';
    }

    /**
     * @param $url
     * @param $data
     * @return mixed|string
     * 获取二维码
     */
    public function getEwm($openid, $Token){
        // 获取二维码
        $pathHead = ROOT_PATH . '/public/uploads/catchewm/' . $openid;
        $data = json_decode($this->get_php_file(ROOT_PATH . '\\public\\uploads\\catchewm\\'.$openid.'.php'));
        if($data->expire_seconds < time()){
            $codeResult = $this->weCode2($openid,$Token);
            $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$codeResult['ticket'];
            $file=file_get_contents($url);
            $resource = fopen($pathHead,'a');
            fwrite($resource, $file);
            fclose($resource);
            $ticket = $codeResult['ticket'];
            $expire_time = $codeResult['expire_seconds'];
            if ($ticket) {
                $codeResult['expire_seconds'] = time() + ($expire_time/2);
                $codeResult['ticket'] = $ticket;
                $this->set_php_file(ROOT_PATH . '\\public\\uploads\\catchewm\\'.$openid.'.php', json_encode($codeResult));
            }
        }
        return config('host') .'/uploads/catchewm/' . $openid;
    }
    // 获取php文件数据
    private function get_php_file($filename) {
        if(!file_exists($filename)){
            $data = array('expire_seconds' => 0);
            $data = json_encode($data);
            return $data;
        }else{
            return trim(substr(file_get_contents($filename), 15));
        }
    }
    // 存放php数据
    private function set_php_file($filename, $content) {
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
    }
    private function api_notice_increment($url, $data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'Errno'.curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }
    // 下载图片到本地
    private function download($url, $path)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        $resource = fopen($path,'a');
        fwrite($resource, $file);
        fclose($resource);
    }
    /**
     * 发送模板消息
     */
    public function sendTemplate($token, $data){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$token;
        $result = $this->https_post($url,$data);
        $final = json_decode($result);
        return $final;
    }
    /**
     * 向微信服务器发送信息的方法
     */
    private function https_post($url,$data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'Errno'.curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }
    public function curl_post($url, $data = null){
        //创建一个新cURL资源
        $curl = curl_init();
        //设置URL和相应的选项
        curl_setopt($curl, CURLOPT_URL, $url);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行curl，抓取URL并把它传递给浏览器
        $output = curl_exec($curl);
        //关闭cURL资源，并且释放系统资源
        curl_close($curl);
        return $output;
    }
}