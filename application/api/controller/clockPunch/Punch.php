<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/15 0015
 * Time: 10:06
 */

namespace app\api\controller\clockPunch;


use app\api\model\Wechat;
use app\api\validate\OpenidValidate;
use app\api\model\Punch as PunchModel;
use think\Db;
use think\Session;

class Punch
{
    /*
     * 点击打卡按钮的接口
     * 传入的参数openid
     * 查询t_punch表
     * 查询今日有没有打卡，如果没有打卡，插入数据
     */
    public function punchDay(){
        (new OpenidValidate())->goCheck();
        $openid = input('openid');
        // 判断今天有没有打卡
        $punch = new PunchModel($openid);
        $flag = $punch->isTodayPunch();
        $count = $punch->countPunch();
        $backData = [
            'err_code' => 0,
            'count' => $count
        ];
        if($flag){
            $backData['err_code'] = 2;
            $backData['msg'] = '今天已经打过卡了。';
            $backData['data'] = $flag;
            $backData['createTime'] = $flag['punchTime'];
            return json($backData);
        }else{
            // 添加打卡数据
            $punchData = $punch->toPunch();
            if($punchData[0]['punchTime']){
                $backData['msg'] = '打卡成功。';
                $backData['createTime'] = $punchData[0]['punchTime'];
                $backData['punchDays'] = $punchData[0]['punchDays'];
                $backData['count'] = ($count + 1);
            }else{
                $backData['err_code'] = 1;
                $backData['msg'] = '打卡失败。';
            }
            return json($backData);
        }
    }
    /**
     * 初始化早起打卡页面
     */
    public function punchInit(){
        (new OpenidValidate())->goCheck();
        $weChat = new Wechat();
        $openid = input('openid');
        // 渲染数据
        $data = [
            'error_code' => 0
        ];
        // 获取用户信息
        $userInfo = $weChat
            ->where('openid', $openid)
            ->find();
        $data['userInfo'] = $userInfo;
        // 获取打卡天数，有无打卡，如果已经打卡，获取打卡时间
        $punch = new PunchModel($openid);
        $count = $punch->countPunch();
        $data['count'] = $count;
        $flag = $punch->isTodayPunch();
        if($flag) {
            $data['createTime'] = $flag['punchTime'];
        }
        // 获取今天的被赞次数
        $Time = time();
        $day = date("Y-m-d", $Time);
        $like_count = Db::table('t_like')
            ->where('otheropenid', $openid)
            ->where('likeDate', $day)
            ->count();
        $data['like_count'] = $like_count;
        return $data;
    }
}