<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/28 0028
 * Time: 14:07
 */

namespace app\api\controller\report;

class Report
{
    public function startReport(){
        $redirect_uri = config('host').'/dist/html/index.html';
        $redirect_uri = urlencode($redirect_uri);
        $appId = config('we_appId');
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
        return redirect($url);
    }
}