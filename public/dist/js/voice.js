/**
 * 接口封装
 */
var api = (function () {
    /**
     * 初始化获取openid
     */
    var getopenid = function (cb) {
        var url = window.location.href;
        var data = {
            url: url
        }
        $.post('/api/voice/jssdk', data, function (response) {
            console.log(response);
            if (response.signPackage) {
                var signPackage = response.signPackage;
                wx.config({
                    debug: false,
                    appId: signPackage["appId"],
                    timestamp: signPackage["timestamp"],
                    nonceStr: signPackage["nonceStr"],
                    signature: signPackage["signature"],
                    jsApiList: [
                        // 所有要调用的 API 都要加到这个列表中
                        'chooseImage',
                        'previewImage',
                        'uploadImage',
                        'translateVoice',
                        'playVoice',
                        'stopRecord',
                        'startRecord'
                    ]
                });
                wx.ready(function () {
                    voice.init();
                });
            }
            if (response.code == '0') {
                if (cb) {
                    cb(response);
                }
            } else {
                window.location.href = response.url;
            }
        });
    };
    return {
        getopenid: getopenid
    }
}());

/**
 * 初始化
 */
$(function () {
    function init() {
        api.getopenid(function () {
            // setJSAPI();
        });       
    }
    init();
});


/**
 * 测试语音识别
 */
var voice = (function () {
    var localId;
    // 匹配字符
    var match = function (data) {
        var str = data['str'];
        var patt1 = new RegExp(data['str2']);
        var result = patt1.test(str);
        data.cb(result);
    }
    var translate = function (cbk) {
        // alert(localId);
        wx.translateVoice({
            localId: localId, // 需要识别的音频的本地Id，由录音相关接口获得
            isShowProgressTips: 1, // 默认为1，显示进度提示
            success: function (res) {
                // alert(res.translateResult);
                match({
                    str: res.translateResult,
                    str2: '开灯',
                    cb: function (flag) {
                        if (flag) {
                            weui.topTips('开灯', 500);
                            var data = {
                                light: 1
                            }
                            $.post('/api/voice/light', data, function (response) {
                                weui.topTips(JSON.stringify(response), 500);
                                cbk();
                            });
                        } else {
                            match({
                                str: res.translateResult,
                                str2: '关灯',
                                cb: function (flag2) {
                                    if (flag2) {
                                        weui.topTips('关灯', 500);
                                        var data = {
                                            light: 0
                                        }
                                        $.post('/api/voice/light', data, function (response) {
                                            weui.topTips(JSON.stringify(response), 500);
                                            cbk();
                                        });
                                    } else {
                                        weui.topTips(0, 500);
                                        cbk();
                                    }
                                }
                            });
                        }
                    }
                });
                //alert(res.translateResult); // 语音识别的结果
            },
            fail: function(res){
                weui.topTips(JSON.stringify(res), 500);
                cbk();
            }
        });
    }
    var play = function () {
        wx.playVoice({
            localId: localId // 需要播放的音频的本地ID，由stopRecord接口获得
        });
    }
    var stop = function (cb) {
        wx.stopRecord({
            success: function (res) {
                localId = res.localId;
                cb();
            }
        });
    }
    var start = function () {
        wx.startRecord();
    }
    var init = function () {
        start();
        $('#ceshiluy').html('录音中……');
        setTimeout(function () {
            $('#ceshiluy').html('录音停止');
            stop(function () {
                translate(function () {
                    init();
                });
            });
        }, 3000);
    }
    return {
        init: init
    }
}());

