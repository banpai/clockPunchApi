<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/26 0026
 * Time: 17:38
 */

namespace app\api\controller\report;
use app\api\model\Yzm as YzmModel;
use app\api\validate\TelValidate;

class Yzm
{
    /**
     *  获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的id号
     */
    public function getYzm(){
        (new TelValidate())->goCheck();
        $tel = input('tel');
        $yzm = YzmModel::getYzm($tel);
        $data = [
            'code'=>0,
            'msg'=> '短信发送成功！'
        ];
        if($yzm->result != 0){
            $data['code'] = 1;
            $data['msg'] = '短信发送失败！';
        }
        return json($data);
    }
}