webpackJsonp([23],{"8ORi":function(t,s){},KJVA:function(t,s,i){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var e=i("Cz8s"),a=i("deIj"),r={data:function(){return{records:{page:1,psize:15,loading:!1,finished:!1,empty:!1,data:[]},showPreLoading:!0,isRefresh:!1,filter:{items:{status:"-1"}}}},components:{publicHeader:e.a},methods:{onLoad:function(t){Object(a.b)({vue:this,force:t,url:"plateform/advertise/trade/list"})},onPullDownRefresh:function(){this.onLoad(!0)},onToggleStatus:function(t){t=parseInt(t),this.filter.items.status!=t&&(this.filter.items.status=t)}},mounted:function(){this.onLoad()},watch:{filter:{handler:function(t,s){this.onLoad(!0)},deep:!0}}},l={render:function(){var t=this,s=t.$createElement,i=t._self._c||s;return i("div",{attrs:{id:"advertise-order"}},[i("public-header",{attrs:{title:"购买记录"}}),t._v(" "),i("div",{staticClass:"content"},[i("div",{staticClass:"wrap-search wrap-search-input"},[i("div",{staticClass:"tab-group flex-lr border-1px-b"},[i("div",{staticClass:"tab-item",class:{active:-1==t.filter.items.status},on:{click:function(s){t.onToggleStatus(-1)}}},[t._v("全部")]),t._v(" "),i("div",{staticClass:"tab-item",class:{active:0==t.filter.items.status},on:{click:function(s){t.onToggleStatus(0)}}},[t._v("未开始")]),t._v(" "),i("div",{staticClass:"tab-item",class:{active:1==t.filter.items.status},on:{click:function(s){t.onToggleStatus(1)}}},[t._v("进行中")]),t._v(" "),i("div",{staticClass:"tab-item",class:{active:2==t.filter.items.status},on:{click:function(s){t.onToggleStatus(2)}}},[t._v("已下架")])]),t._v(" "),i("van-search",{attrs:{placeholder:"请输入商户名称"},model:{value:t.filter.items.keyword,callback:function(s){t.$set(t.filter.items,"keyword",s)},expression:"filter.items.keyword"}})],1),t._v(" "),i("van-pull-refresh",{on:{refresh:function(s){t.onPullDownRefresh()}},model:{value:t.isRefresh,callback:function(s){t.isRefresh=s},expression:"isRefresh"}},[t.records.empty?i("div",{staticClass:"no-data"},[i("img",{attrs:{src:"static/img/order_no_con.png",alt:""}}),t._v(" "),i("p",[t._v("没有符合条件的数据!")])]):i("van-list",{staticClass:"order-list",attrs:{finished:t.records.finished,offset:100,"immediate-check":!1},on:{load:t.onLoad},model:{value:t.records.loading,callback:function(s){t.$set(t.records,"loading",s)},expression:"records.loading"}},[t._l(t.records.data,function(s,e){return i("div",{key:s.id,staticClass:"order-item"},[i("div",{staticClass:"order-title flex-lr"},[i("div",{staticClass:"title flex"},[i("div",{staticClass:"title-img"},[i("img",{attrs:{src:s.logo,alt:""}})]),t._v(" "),i("div",{staticClass:"title-name"},[t._v(t._s(s.store_title))])]),t._v(" "),0==s.status?i("div",{staticClass:"itag itag-danger"},[t._v("未开始")]):t._e(),t._v(" "),1==s.status?i("div",{staticClass:"itag itag-primary"},[t._v("进行中")]):t._e(),t._v(" "),2==s.status?i("div",{staticClass:"itag itag-disabled"},[t._v("已结束")]):t._e()]),t._v(" "),i("div",{staticClass:"order-detail border-1px-t"},[i("div",{staticClass:"order-info flex"},[i("div",[t._v("广告类型：")]),t._v(" "),i("div",[t._v(t._s(s.title))])]),t._v(" "),i("div",{staticClass:"order-info flex"},[i("div",[t._v("支付费用：")]),t._v(" "),i("div",{staticClass:"c-danger"},[t._v("￥"+t._s(s.final_fee))])]),t._v(" "),i("div",{staticClass:"order-info flex"},[i("div",[t._v("购买时间：")]),t._v(" "),i("div",[t._v(t._s(s.days)+"天")])]),t._v(" "),i("div",{staticClass:"order-info flex"},[i("div",[t._v("有效期：")]),t._v(" "),i("div",[t._v(t._s(s.addtime_cn)+" ~ "+t._s(s.endtime_cn))])])])])}),t._v(" "),t.records.finished?i("div",{staticClass:"loaded"},[i("div",{staticClass:"loaded-tips"},[t._v("没有更多了")])]):t._e()],2)],1)],1),t._v(" "),t.showPreLoading?i("iloading"):t._e()],1)},staticRenderFns:[]};var o=i("VU/8")(r,l,!1,function(t){i("8ORi")},null,null);s.default=o.exports}});