webpackJsonp([36],{"WQI/":function(t,e){},oQKV:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=s("deIj"),i={data:function(){return{showPreLoading:!0,meals:[],member:{},select:{index:0,meal:{}},agreementShow:!1,agreement:"",submitting:!1}},methods:{onLoad:function(t){var e=this;Object(a.a)({vue:e,url:"svip/index/meal",data:{},success:function(t){e.meals=t.meals,e.member=t.member,e.select.meal=e.meals[0],e.agreement=t.agreement}})},onSelectMeal:function(t){this.select.index=t,this.select.meal=this.meals[t]},onTogglePopup:function(){this.agreementShow=!this.agreementShow},onSubmit:function(){var t=this;if(t.submitting)return!1;t.submitting=!0,Object(a.c)({vue:t,url:"svip/index/buy",data:{id:t.select.meal.id},success:function(e){t.util.$toast("请支付",t.util.getUrl({path:"pages/public/pay",query:{order_id:e.id,order_type:"svip"}}),1e3)},fail:function(e){t.submitting=!1,t.util.$toast(e.message,"",1e3)}})}},mounted:function(){this.onLoad()}},n={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{attrs:{id:"svip-purchase"}},[s("div",{staticClass:"content"},[s("header",{staticClass:"header"},[s("div",{staticClass:"user"},[s("img",{staticClass:"avatar",attrs:{src:t.member.avatar,alt:""}}),t._v(" "),s("div",[s("div",{staticClass:"info"},[s("span",[t._v(t._s(t.member.nickname))]),t._v(" "),s("span",[t._v("（"+t._s(t.member.mobile)+"）")])]),t._v(" "),s("div",{staticClass:"svip"},[1==t.member.svip_status?s("span",[t._v(t._s(t.member.svip_endtime_cn)+"到期，购买后有效期将顺延")]):s("span",[t._v("您还不是超级会员")])])])])]),t._v(" "),s("div",{staticClass:"wrapper-item"},[s("header",{staticClass:"header-title"},[s("h3",{staticClass:"title"},[t._v("\n\t\t\t\t\t选择优惠套餐\n\t\t\t\t\t"),t._e()])]),t._v(" "),t.meals.length>0?s("section",{staticClass:"item-list"},t._l(t.meals,function(e,a){return s("div",{key:a,staticClass:"container",class:{active:t.select.index==a}},[s("div",{staticClass:"div-all",on:{click:function(e){t.onSelectMeal(a)}}},[s("header",{staticClass:"header-body"},[s("h4",[t._v(t._s(e.title))]),t._v(" "),s("div",{staticClass:"price"},[s("div",{staticClass:"nums"},[s("span",{staticClass:"yuan"},[s("span",[t._v("￥")]),t._v(t._s(e.price)+"\n\t\t\t\t\t\t\t\t\t")]),t._v(" "),s("del",[t._v(t._s(e.oldprice))])])])]),t._v(" "),e.description?s("span",{staticClass:"tag"},[t._v(t._s(e.description))]):t._e(),t._v(" "),t._e()])])})):s("section",{staticClass:"description padding-15-b"},[s("span",[t._v("暂时没有可购买会员套餐")])]),t._v(" "),s("van-cell-group",[s("van-cell",{attrs:{title:"会员抵扣红包","is-link":"",value:"无抵扣红包"}})],1)],1),t._v(" "),t._e(),t._v(" "),s("section",{staticClass:"description"},[t._e(),t._v(" "),t._e(),t._v(" "),s("div",{on:{click:t.onTogglePopup}},[t._v("会员特权说明")])],1),t._v(" "),t.meals.length>0?s("div",{staticClass:"bottomBar"},[s("div",{staticClass:"allprice"},[s("div",{staticClass:"total"},[t._v("\n\t\t\t\t\t总价\n\t\t\t\t\t"),s("span",[t._v("￥"),s("span",[t._v(t._s(t.select.meal.price))])]),t._v(" "),s("del",[t._v(t._s(t.select.meal.oldprice))])])]),t._v(" "),1==t.member.svip_status?s("button",{attrs:{disabled:t.submitting},on:{click:t.onSubmit}},[t._v("立即续费")]):s("button",{attrs:{disabled:t.submitting},on:{click:t.onSubmit}},[t._v("立即开通")])]):t._e()],1),t._v(" "),s("van-popup",{staticClass:"agreement-popup",attrs:{position:"bottom"},model:{value:t.agreementShow,callback:function(e){t.agreementShow=e},expression:"agreementShow"}},[s("van-nav-bar",{staticClass:"border-0px",style:{background:"#ff2d4b",color:"#fff"},attrs:{title:"会员特权说明"},on:{"click-left":t.onTogglePopup}},[s("van-icon",{staticClass:"font-20",style:{color:"#fff"},attrs:{slot:"left",name:"left"},slot:"left"})],1),t._v(" "),s("div",{staticClass:"popup-content margin-10",domProps:{innerHTML:t._s(t.agreement)}})],1),t._v(" "),t.showPreLoading?s("iloading"):t._e()],1)},staticRenderFns:[]};var l=s("VU/8")(i,n,!1,function(t){s("WQI/")},null,null);e.default=l.exports}});
//# sourceMappingURL=36.28cca4cc633d7986107c.js.map