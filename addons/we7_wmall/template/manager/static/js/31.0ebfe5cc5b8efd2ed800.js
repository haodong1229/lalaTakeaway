webpackJsonp([31],{"1nSS":function(t,i,e){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var a=e("Cz8s"),s=e("deIj"),l={data:function(){return{records:{page:1,psize:15,loading:!1,finished:!1,empty:!1,data:[]},isRefresh:!1,showPreLoading:!0,filter:{items:{pay_type:"all"}}}},components:{publicHeader:a.a},methods:{onLoad:function(t){Object(s.b)({vue:this,force:t,recordsName:"orders",url:"manage/paybill/index/list"})},onPullDownRefresh:function(){this.onLoad(!0)},onToggleStatus:function(t){this.filter.items.pay_type!=t&&(this.filter.items.pay_type=t)}},mounted:function(){this.onLoad()},watch:{filter:{handler:function(t,i){this.onLoad(!0)},deep:!0}}},n={render:function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("div",{attrs:{id:"paybill-index"}},[e("public-header",{attrs:{title:"账单"}}),t._v(" "),e("div",{staticClass:"content"},[e("div",{staticClass:"wrap-search wrap-search-input"},[e("div",{staticClass:"tab-group flex-lr border-1px-b"},[e("div",{staticClass:"tab-item",class:{active:"all"==t.filter.items.pay_type},on:{click:function(i){t.onToggleStatus("all")}}},[t._v("全部")]),t._v(" "),e("div",{staticClass:"tab-item",class:{active:"wechat"==t.filter.items.pay_type},on:{click:function(i){t.onToggleStatus("wechat")}}},[t._v("微信")]),t._v(" "),e("div",{staticClass:"tab-item",class:{active:"alipay"==t.filter.items.pay_type},on:{click:function(i){t.onToggleStatus("alipay")}}},[t._v("支付宝")]),t._v(" "),e("div",{staticClass:"tab-item",class:{active:"credit"==t.filter.items.pay_type},on:{click:function(i){t.onToggleStatus("credit")}}},[t._v("余额")])]),t._v(" "),e("van-search",{attrs:{placeholder:"请输入用户昵称"},model:{value:t.filter.items.keyword,callback:function(i){t.$set(t.filter.items,"keyword",i)},expression:"filter.items.keyword"}})],1),t._v(" "),e("van-pull-refresh",{on:{refresh:function(i){t.onPullDownRefresh()}},model:{value:t.isRefresh,callback:function(i){t.isRefresh=i},expression:"isRefresh"}},[t.records.empty?e("div",{staticClass:"no-data"},[e("img",{attrs:{src:"static/img/order_no_con.png",alt:""}}),t._v(" "),e("p",[t._v("没有符合条件的数据!")])]):e("van-list",{staticClass:"paybill-list",attrs:{finished:t.records.finished,offset:100,"immediate-check":!1},on:{load:t.onLoad},model:{value:t.records.loading,callback:function(i){t.$set(t.records,"loading",i)},expression:"records.loading"}},[t._l(t.records.data,function(i,a){return e("div",{staticClass:"paybill-item"},[e("router-link",{attrs:{to:t.util.getUrl({path:"/pages/paybill/detail",query:{id:i.id}})}},[e("div",{staticClass:"border-1px-b padding-10 flex-lr"},[e("div",{staticClass:"c-default font-16"},[t._v("#"+t._s(i.serial_sn))]),t._v(" "),e("div",{staticClass:"font-16",class:{"c-primary":"wechat"==i.pay_type,"c-info":"alipay"==i.pay_type,"c-danger":"credit"==i.pay_type}},[t._v(t._s(i.pay_type_cn))])]),t._v(" "),e("div",{staticClass:"border-1px-b padding-10 flex-lr"},[e("div",{staticClass:"flex"},[e("div",{staticClass:"paybill-img"},[e("img",{staticClass:"img-100",attrs:{src:i.avatar,alt:""}})]),t._v(" "),e("div",[t._v(t._s(i.nickname))])]),t._v(" "),e("div",{staticClass:"price"},[e("div",{staticClass:"font-14"},[t._v("顾客支付：¥"+t._s(i.final_fee))]),t._v(" "),e("div",{staticClass:"font-14"},[t._v("最终收入：¥"+t._s(i.store_final_fee))])])]),t._v(" "),e("div",{staticClass:"padding-10"},[e("div",{staticClass:"font-12 c-gray flex"},[e("div",[t._v("订单编号：")]),t._v(" "),e("div",[t._v(t._s(i.order_sn))])]),t._v(" "),e("div",{staticClass:"font-12 c-gray flex padding-5-t"},[e("div",[t._v("下单时间：")]),t._v(" "),e("div",[t._v(t._s(i.addtime_cn))])])])])],1)}),t._v(" "),t.records.finished?e("div",{staticClass:"loaded"},[e("div",{staticClass:"loaded-tips"},[t._v("没有更多了")])]):t._e()],2)],1)],1),t._v(" "),t.showPreLoading?e("iloading"):t._e()],1)},staticRenderFns:[]};var r=e("VU/8")(l,n,!1,function(t){e("OdGC")},null,null);i.default=r.exports},OdGC:function(t,i){}});
//# sourceMappingURL=31.0ebfe5cc5b8efd2ed800.js.map