<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/6 0006
 * Time: 14:01
 */

namespace app\api\controller\clockPunch;


use app\api\model\FriendShip;
use app\api\model\Wechat;
use app\api\validate\OpenidValidate;
use think\Db;
use think\Session;
class Init
{
    /**
     * 配置微信服务器通信
     */
    public function checkSignature(){
        //检验签名的合法性
        if($this->_checkSignature()){
            echo input('echostr');
            //签名合法，告知微信公众平台服务器
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                $obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
                $data = json_decode(json_encode($obj), true);
                $EventKey = $data['EventKey'];
                $FromUserName = $data['FromUserName'];
                $Event = $data['Event'];
                if($EventKey){
                    $strA = substr($EventKey, 0, 8);
                    if($strA== 'qrscene_'){
                        $len = strlen($EventKey) - 8;
                        $EventKey = substr($EventKey, 8, $len);
                    }
                }
                // 根据$data处理自己所要的逻辑
//                $filename = ROOT_PATH . '\\public\\static\\mm.php';
//                $fp = fopen($filename, "w");
//                $str = json_encode($data);
//                fwrite($fp,$EventKey);
//                fclose($fp);
                if(($Event == 'SCAN' || $Event == 'subscribe') && $EventKey && ($EventKey != $FromUserName)){
                    $addFriend = new FriendShip($FromUserName, $EventKey);
                    $flag = $addFriend->isFriendShip();
                    if(!$flag){
                        $addFriend->addFriends();
                    }
                    $addFriend = new FriendShip($EventKey, $FromUserName);
                    $flag = $addFriend->isFriendShip();
                    if(!$flag){
                        $addFriend->addFriends();
                    }
                }
            }
        }
    }
    /**
     * 验证签名
     * @return bool
     */
    private function _checkSignature()
    {
        //获得由微信公众平台请求的验证数据
        $signature = input("signature");
        $timestamp = input("timestamp");
        $nonce = input("nonce");
        //将时间戳，随机字符串，token按照字母顺序排序，病并连接
        $token = 'pamtest';
        $tmp_arr = array($token, $timestamp, $nonce);
        sort($tmp_arr, SORT_STRING);//字典顺序
        $tmp_str = implode($tmp_arr);//连接
        $tmp_str = sha1($tmp_str);//sha1加密
        if ($signature == $tmp_str) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断是不是参加了该活动，有没有openid
     */
    public function isHasOpenid(){
        $weChat = new Wechat();
        // 获取openid
        $code = input('code');
        $urlSkip = input('baseurl');
        $openid = input('openid');
        if($openid){
            Session::set('openid', $openid);
        }else{
            $openid = $weChat->openid($code, $urlSkip);
        }
        $flag = $weChat->where('openid',$openid)->value('flag');
        $data = [
            'error_code' => 400088,
            'url' => '',
            'openid' => $openid,
            'flag' => $flag
        ];
        return $data;
    }
    /**
     * 第一次参加活动,点击参加保存数据
     */
    public function joinProject(){
        (new OpenidValidate())->goCheck();
        $weChat = new Wechat();
        $openid = input('openid');
        if($openid){
            Session::set('openid', $openid);
        }
        // 获取用户信息
        $result = $weChat->getUserInfo();
        // 数据库操作
        $Time = time();
        $createTime = date("Y-m-d H:i:s", $Time);
        $flag = $weChat->where('openid',$result->openid)->find();
        if(!$flag){
            $weChat->nickname = $result->nickname;
            $weChat->sex = $result->sex;
            $weChat->headimgurl = $result->headimgurl;
            $weChat->openid = $result->openid;
            $weChat->country = $result->country;
            $weChat->province = $result->province;
            $weChat->city = $result->city;
            $weChat->createTime = $createTime;
            $weChat->updateTime = $createTime;
            $weChat->flag = 1;
            $weChat->save();
        }else{
            $weChat->save([
                'flag'  => 1,
                'updateTime' => $createTime
            ],['openid' => $openid]);
        }
        $data = [
            'err_code' => 0,
            'msg' => '成功！',
            'userInfo' => $result
        ];
        return $data;
    }
    /**
     * 初始化JSSDK
     */
    public function getJssdk(){
        $weChat = new Wechat();

        // 获取openid
        $code = input('code');
        $urlSkip = input('baseurl');
        if(!$urlSkip){
            $urlSkip = config('host') . '/dist/html/index.html';
        }
        $openid = input('openid');
        if($openid){
            Session::set('openid', $openid);
        }else{
          $openid = $weChat->openid($code, $urlSkip);
        }

        // 获取jsSDK
        $url = input('baseurl');
        $signPackage = $weChat->jsSdk($url);

        // 获取用户信息
        $result = $weChat->getUserInfo();

        // 数据库操作
        $Time = time();
        $createTime = date("Y-m-d H:i:s", $Time);
        $flag = $weChat->where('openid',$result->openid)->find();
        if(!$flag){
            $weChat->nickname = $result->nickname;
            $weChat->sex = $result->sex;
            $weChat->headimgurl = $result->headimgurl;
            $weChat->openid = $result->openid;
            $weChat->createTime = $createTime;
            $weChat->updateTime = $createTime;
            $weChat->save();
        }

        $data = [
            'code' => '0',
            'url' => ''
        ];
        if(is_array($signPackage)){
            $data['signPackage'] = $signPackage;
            $data['accessToken'] = Session::get('accessToken');
            $data['userInfo'] = $result;
        }
        return $data;
    }
    /*
     * 获取我和我的好友列表
     */
    public function discList(){
        (new OpenidValidate())->goCheck();
        $openid = input('openid');

//        $openid = 'od-XDwBgurkh43bqzyAPiMzZJemk';
        $Time = time();
        $day = date("Y-m-d", $Time);
        $sql = 'select distinct b.id,b.openid,b.headimgurl,b.nickname,c.punchTime,d.likestatus ';
        $sql = $sql . 'from t_friendship as a ';
        $sql = $sql . 'left join t_members as b ';
        $sql = $sql . 'on b.openid IN (a.otheropenid, a.openid) ';
        $sql = $sql . 'left join t_like as d ';
        $sql = $sql . 'ON d.otheropenid = b.openid and d.likeDate=? ';
        $sql = $sql . 'AND d.openid =? ';
        $sql = $sql . 'left join t_punch as c ';
        $sql = $sql . 'ON b.openid = c.openid and c.punchDate=? ';
        $sql = $sql . 'where a.openid=? ';
        $sql = $sql . 'ORDER BY c.punchTime IS NULL,c.punchTime ASC ';
        $result = Db::query($sql,[$day,$openid,$day,$openid]);
        $data = [
            'err_code' => 0
        ];
        $selfData = array();
        $selfLike =array();
        if(sizeof($data) === 1){
            $selfData = Db::table('t_punch')
                ->where('openid', $openid)
                ->where('punchDate', $day)
                ->find();
            $selfLike = Db::table('t_like')
                ->where('openid', $openid)
                ->where('otheropenid', $openid)
                ->where('likeDate', $day)
                ->find();
        }
        if(is_array($result)){
            $openidArr = array();
            foreach ($result as $k=>$v) {
                array_push($openidArr, $v['openid']);
            }
            // 先查询出自己和朋友的所有打卡记录
            $punchDays = Db::table('t_punch')
                ->where('openid','in', $openidArr)
                ->select();
            $data['data'] = $result;
            $data['msg'] = '列表查询成功！';
            $data['date'] = $day;
            $data['punchdays'] = $punchDays;
            $data['selfdata'] = $selfData;
            $data['selflike'] = $selfLike;
            return json($data);
        }else{
            $data['err_code'] = 1;
            $data['msg'] = '列表查询失败！';
            return json($data);
        }
    }
    /**
     * 发送客服消息的接口
     */
    public  function sendNews(){
        $data = '{
        "touser":"od-XDwNudw1DUOgVB-DFgrjNqfhw",
        "msgtype":"image",
        "image":
            {
                 "media_id": "45ILumF_IsZvS4zsxT46gWZ98HAI2STTxmbX-JN3JwBl-_tMTTHN_CdgTp4lYS3E"
            }
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".Session::get('accessToken');
        $result = $this->https_post($url,$data);
        $final = json_decode($result);
        return $final;
    }
    /**
     * 发送文本客服消息
     */
    public function sendTextNews(){
        $content = "今早已经打过早安卡拉，棒棒哒，明天别忘了继续早起呢~\n\n"."<a href='http://summer.natapp1.cc/view/clockpunch/wechat/dist/html/voice.html'>去打卡aa</a>";
        $data = '{
        "touser":"od-XDwNudw1DUOgVB-DFgrjNqfhw",
        "msgtype":"text",
        "text":
            {
                 "content": "'.$content.'"
            }
        }';
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".Session::get('accessToken');
        $result = $this->https_post($url,$data);
        $final = json_decode($result);
        return $final;
    }
    /**
     * 发送模板消息
     */
    public function sendTemplate(){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.Session::get('accessToken');
        $data = '{
        "touser":"od-XDwNudw1DUOgVB-DFgrjNqfhw",
        "template_id":"ZrWtJ9z1fItRo-o-xWLG6t29MeiUH6JOmjhlSqo4kVE",
        "data":{
                "first":{
                     "value": "恭喜你购买成功！",
                      "color":"#173177"
                }
            } 
        }';
        $result = $this->https_post($url,$data);
        $final = json_decode($result);
        return $final;
    }
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
}