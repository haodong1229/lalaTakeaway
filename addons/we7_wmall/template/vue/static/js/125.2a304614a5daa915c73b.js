webpackJsonp([125],{Q5q8:function(t,a,i){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var e={render:function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("div",{attrs:{id:"superRedpacket-grant"}},[i("div",{staticClass:"content",style:{background:t.activity.data.activity.backgroundColor}},[i("div",{staticClass:"banner"},[i("img",{attrs:{src:t.activity.data.activity.image,alt:""}})]),t._v(" "),i("div",{staticClass:"redpacket"},[1!=t.activity.status?i("div",{staticClass:"redpacket-item activity-empty"},[t._v("\n\t\t\t\t活动已经过期啦\n\t\t\t")]):0==t.redpackets.length?i("div",{staticClass:"redpacket-item activity-empty"},[t._v("\n\t\t\t\t红包抢光啦\n\t\t\t")]):t.redpackets.length>0?[t._l(t.redpackets,function(a){return i("div",{key:a.id,staticClass:"redpacket-item"},[i("div",{staticClass:"clearfix"},[t._m(0,!0),t._v(" "),i("div",{staticClass:"redpacket-info"},[i("span",{staticClass:"redpacket-title",style:{color:t.activity.data.activity.color}},[t._v(t._s(a.title))]),t._v(" "),i("span",{staticClass:"use-condition"},[t._v("满"+t._s(a.condition)+"可用")])]),t._v(" "),i("div",{staticClass:"price",style:{color:t.activity.data.activity.color}},[i("span",[t._v("￥")]),t._v("\n\t\t\t\t\t\t\t"+t._s(a.discount)+"\n\t\t\t\t\t\t")])]),t._v(" "),i("div",{staticClass:"redpacket-bottom"},[i("div",{staticClass:"use-days-limit"},[t._v("\n\t\t\t\t\t\t\t"+t._s(a.condition_cn)+" "+t._s(a.category_cn?a.category_cn:"")+" "+t._s(a.times_cn?a.times_cn:"")+"\n\t\t\t\t\t\t")]),t._v(" "),i("router-link",{staticClass:"to-use",attrs:{to:t.util.getUrl({path:"/pages/home/index"})}},[t._v("去使用")])],1)])}),t._v(" "),i("div",{staticClass:"get-success"},[i("span",[t._v("红包已放入账号:")]),t._v(" "),i("span",[t._v(t._s(t.member.nickname)+" "+t._s(t.member.mobile))])]),t._v(" "),i("router-link",{staticClass:"immediate-use",style:{background:t.activity.data.activity.button.backgroundColor,color:t.activity.data.activity.button.color},attrs:{to:t.util.getUrl({path:"/pages/home/index"})}},[t._v("立即使用")]),t._v(" "),i("router-link",{staticClass:"give-redpacket",style:{background:t.activity.data.activity.button.backgroundColor,color:t.activity.data.activity.button.color},attrs:{to:t.util.getUrl({path:"/pages/member/redPacket/index"})}},[t._v("查看我的红包")])]:t._e()],2),t._v(" "),i("div",{staticClass:"records-agreement"},[i("div",{staticClass:"records-title"},[t._v("\n\t\t\t\t看看大家手气\n\t\t\t")]),t._v(" "),i("div",{staticClass:"records-container"},[i("ul",t._l(t.rankings,function(a){return i("li",[i("div",{staticClass:"item-content"},[i("div",{staticClass:"item-media"},[a.avatar?i("img",{attrs:{src:a.avatar,alt:""}}):i("img",{attrs:{src:"static/img/head.png",alt:""}})]),t._v(" "),i("div",{staticClass:"item-inner"},[i("div",{staticClass:"item-title-row"},[i("div",{staticClass:"item-title"},[t._v(t._s(a.nickname))]),t._v(" "),i("div",{staticClass:"item-after"},[t._v(t._s(a.total_discount)+"元")])]),t._v(" "),i("div",{staticClass:"item-title-row"},[i("div",{staticClass:"item-subtitle"},[t._v(t._s(a.granttime_cn))]),t._v(" "),1==t.member.ranking?i("div",{staticClass:"item-after color"},[t._v("手气最佳")]):t._e()])])])])}),0)]),t._v(" "),i("div",{staticClass:"agreement-title"},[t._v("\n\t\t\t\t活动规则\n\t\t\t")]),t._v(" "),i("div",{staticClass:"agreement-inner",domProps:{innerHTML:t._s(t.activity.data.activity.agreement)}})])]),t._v(" "),i("transition",{attrs:{name:"loading"}},[t.showPreLoading?i("iloading"):t._e()],1)],1)},staticRenderFns:[function(){var t=this.$createElement,a=this._self._c||t;return a("div",{staticClass:"image"},[a("img",{attrs:{src:"static/img/superredpacket.png",alt:""}})])}]};var s=i("VU/8")({components:{},data:function(){return{showPreLoading:!0,is_get:0,get_status:0,rankings:{},activity:{data:{activity:{}}},member:{},redpackets:[]}},methods:{onLoad:function(){var t=this;this.util.request({url:"superRedpacket/share/grant",data:{order_id:this.order_id}}).then(function(a){t.showPreLoading=!1;var i=a.data.message;if(i.errno)return t.$toast(i.message),t.activity=i.message.activity,!1;i=i.message,t.activity=i.activity,t.get_status=i.get_status,1==t.get_status&&t.util.$toast("领取红包成功","",1e3),t.is_get=i.is_get,1==t.is_get&&t.util.$toast("您已领取过这个红包了","",1e3),t.redpackets=i.redpackets,t.rankings=i.rankings,t.member=i.member,t.util.setWXTitle(t.activity.name)})}},created:function(){this.query=this.$route.query,this.query&&(this.order_id=this.query.order_id)},mounted:function(){this.onLoad()}},e,!1,function(t){i("lMaJ")},null,null);a.default=s.exports},lMaJ:function(t,a){}});