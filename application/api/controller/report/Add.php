<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/27 0027
 * Time: 10:57
 */

namespace app\api\controller\report;

use app\api\validate\TelValidate;

use app\api\model\AddReport as AddReportModel;
class Add
{
    public function addData(){
        // TODO 验证获取的数据
        $data = input();
        $result = AddReportModel::addData($data);
        return json($result);
    }
}