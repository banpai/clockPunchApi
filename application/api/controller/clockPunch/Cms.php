<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/7 0007
 * Time: 10:02
 */

namespace app\api\controller\clockPunch;
use app\api\model\Kefu;
use app\api\model\User as UserModel;
use app\lib\exception\UserException;
use app\api\model\Kefu as KefuModel;
use app\api\model\WarnTime as WarnTimeModel;
use app\api\model\Wechat as WechatModel;
use think\Db;

class Cms
{
    /**
     * 后端CMS的登录接口
     * 接受的参数，user 和 password
     */
    public function login(){
        $password = input('password');
        $user= input('user');
        $data = UserModel::get([
            'name'=>$user,
            'password'=>$password
        ]);
        if (!$data){
            throw new UserException();
        }
        $backData = [
            'code' => 0,
            'message' => '登录成功'
        ];
        return json($backData);
    }
    /**
     * 修改用户名
     */
    public function changeName(){
        $password = input('password');
        $user= input('user');
        $data = UserModel::get([
            'name'=>$user,
            'password'=>$password
        ]);
        if (!$data){
            throw new UserException();
        }
        if(input('newUser')){
            $data->name = input('newUser');
            $data->save();
            return $user;
        }else{
            throw new UserException();
        }
    }
    /**
     * 修改密码
     */
    public function changePassword(){
        $password = input('password');
        $user= input('user');
        $data = UserModel::get([
            'name'=>$user,
            'password'=>$password
        ]);
        if (!$data){
            throw new UserException();
        }
        if(input('newPassword')){
            $data->password = input('newPassword');
            $data->save();
            return $user;
        }else{
            throw new UserException();
        }
    }
    /**
     * 添加早起群发模板消息的时间设定
     */
    public function uploadTime(){
        $time = input('time');
        $data = [
            'err_code' => 0,
            'msg' => '成功'
        ];
        if($time){
            // 存表
            $runTime = WarnTimeModel::get(1);
            if(!$runTime){
                $runTime = new WarnTimeModel();
            }
            $Time = time();
            $createTime = date("Y-m-d H:i:s", $Time);
            $runTime->createTime = $createTime;
            $runTime->updateTime = $createTime;
            if($time == -1){
                $runTime->time = null;
            }else{
                $runTime->time = $time;
            }
            $runTime->save();
        }else{
            $data['err_code'] = 1;
            $data['msg'] = '失败';
        }
        return $data;
    }
    /**
     * 获取早起群发模板消息的时间的数据
     */
    public function getTime(){
        $data = WarnTimeModel::get(1);
        $data['host'] = config('host');
        return json($data);
    }
    /**
     * 添加客服的头像
     */
    public function uploadKefuImg(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
        $data = [];
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $data['url'] = '/uploads/' . $info->getSaveName();
                $data['name'] = $info->getFilename();
                // 存表
                $kefu = KefuModel::get(1);
                if(!$kefu){
                    $kefu = new KefuModel();
                }
                $Time = time();
                $createTime = date("Y-m-d H:i:s", $Time);
                $kefu->createTime = $createTime;
                $kefu->updateTime = $createTime;
                $kefu->image = '/uploads/' . $info->getSaveName();
                $kefu->save();
            }else{
                $data['msg'] = $file->getError();
            }
        }
//        $temp = new WechatModel();
//        $Token = $temp->getAcessToken();
//        $kefudata = $temp->getonlinekflist($Token);
//        var_dump($kefudata);
        return json($data);
    }
    /**
     * 获取客服的头像
     */
    public function getKefuData(){
        $data = KefuModel::get(1);
        $data['host'] = config('host');
        return json($data);
    }
    /**
     * 定时任务调用的接口
     */
    public function runTime(){
        $Time = time();
        $nowTime = date("H:i:s", $Time);
        $m1 = strtotime($nowTime);
        $timeOld = WarnTimeModel::get(1);
        $timeOld = $timeOld['time'];
        $m2 = strtotime($timeOld);
        $kk = $m2 - $m1;
        $result = 5 * 60;
        if($kk <= $result && $kk > 0){
            $this->sendAllTemplet();
        }
        $filename = ROOT_PATH . '\\public\\static\\kk.php';
        $fp = fopen($filename, "w");
        fwrite($fp,'$zaiciceshihhh=' . $m1 .'&&$m2='.$m2 . '&&$kk=' .$kk);
        fclose($fp);
    }
    /**
     * 群发模板消息
     */
    public function sendAllTemplet(){
        $sql = 'select distinct a.openid ';
        $sql = $sql . 'from t_members as a';
        $result = Db::query($sql);
        $flag =false;
        if(is_array($result)){
            $template_id = 'kxTuhXMf05D3oYeoES7wZ5gcxCCGPTjlN6-vYoIYWqs';
            $temp = new WechatModel();
            $acessToken = $temp->getAcessToken();
            foreach ($result as $value) {
                echo $value['openid'];
                $openid = $value['openid'];
                $data = '{
                "touser":"'.$openid.'",
                "template_id":"'.$template_id.'",
                "url":"'.config('weChatHost').'",
                "data":{
                        "first":{
                             "value": "智新会",
                              "color":"#173177"
                        }
                    }
                }';
                if($acessToken){
                    $flag = $temp->sendTemplate($acessToken,$data);
                }else{
                    $flag = '获取accesstoken出错！';
                }
            }
        }
//        return $flag;
    }
}