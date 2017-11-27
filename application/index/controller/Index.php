<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        /**
         * 跳转到微信开发的提交表单
         */
        $redirect_uri = config('host').'/dist/html/index.html';
        $redirect_uri = urlencode($redirect_uri);
        $appId = config('we_appId');
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
        return redirect($url);

//        return 'd';
    }
}
