webpackJsonp([17],{642:function(e,t,n){n(722);var o=n(34)(n(673),n(755),null,null);o.options.__file="C:\\Users\\29423\\Documents\\progress\\zaoqidaka\\CMS\\iview-admin\\src\\views\\advanced-router\\component\\shopping-info.vue",o.esModule&&Object.keys(o.esModule).some(function(e){return"default"!==e&&"__esModule"!==e})&&console.error("named exports are not supported in *.vue files."),o.options.functional&&console.error("[vue-loader] shopping-info.vue: functional components are not supported with templates, they should use render functions."),e.exports=o.exports},673:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default={name:"shopping-info",data:function(){return{showInfo:!1,shopping_col:[{title:"购买物品名称",key:"name",align:"center"},{title:"购买时间aaa",key:"time",align:"center"},{title:"价格",key:"price",align:"center"}],shopping_data:[]}},methods:{init:function(){var e="",t="",n="";switch(this.$route.query.shopping_id.toString()){case"100001":e="《vue.js实战》",t="2017年11月12日 13：33：24",n="79";break;case"100002":e="面包",t="2017年11月5日 19：13：24",n="10";break;case"100003":e="咖啡",t="2017年11月8日 10：39：24",n="57";break;case"100004":e="超级豪华土豪金牙签",t="2017年11月9日 11：45：24",n="200"}var o={name:e,time:t,price:n};this.shopping_data=[o]}},mounted:function(){this.init()},watch:{$route:function(){this.init()}}}},722:function(e,t){},755:function(e,t,n){e.exports={render:function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",[n("Row",[n("Card",[n("p",{attrs:{slot:"title"},slot:"title"},[n("Icon",{attrs:{type:"compose"}}),e._v("\n                订单详情\n            ")],1),e._v(" "),n("Table",{attrs:{columns:e.shopping_col,data:e.shopping_data}})],1)],1)],1)},staticRenderFns:[]},e.exports.render._withStripped=!0}});