webpackJsonp([172],{"7co2":function(t,e){},cV5v:function(t,e,o){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var r=o("Gu7T"),n=o.n(r),s=o("Dd8w"),a=o.n(s),i=o("NYxO"),l={name:"orderNote",components:{PublicHeader:o("Cz8s").a},data:function(){return{note:"",sid:Number,order_note:[]}},methods:a()({},Object(i.b)(["setOrderExtra"]),{onLoad:function(){var t=this;this.$route.query.sid?(this.sid=this.$route.query.sid,this.util.request({url:"wmall/store/table/note",data:{sid:this.sid}}).then(function(e){var o=e.data.message;o.errno?t.$toast(o.message):t.order_note=[].concat(n()(t.order_note),n()(o.message.store.order_note))})):this.$toast("参数错误")},onChooseLabel:function(t){this.note=this.note+" "+t},onSubmit:function(){this.setOrderExtra({key:"note",val:this.note}),this.$router.replace(this.util.getUrl({path:"/tangshi/pages/table/create",query:{sid:this.$route.query.sid,table_id:this.orderExtra.table_id}}))}}),computed:a()({},Object(i.c)(["orderExtra"])),mounted:function(){this.note=this.orderExtra.note||"",this.onLoad()}},d={render:function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{attrs:{id:"order-note"}},[o("public-header",{attrs:{title:"添加备注",bgcolor:"#f5f5f5",textcolor:"#000"}}),t._v(" "),o("div",{staticClass:"content"},[o("van-cell-group",{staticClass:"text"},[o("van-field",{attrs:{type:"textarea",placeholder:"请输入备注，最多50字哦"},model:{value:t.note,callback:function(e){t.note=e},expression:"note"}}),t._v(" "),t.order_note&&t.order_note.length>0?o("div",{staticClass:"label-box"},t._l(t.order_note,function(e){return o("div",{staticClass:"label",on:{click:function(o){t.onChooseLabel(e)}}},[t._v(t._s(e))])})):t._e()],1),t._v(" "),o("div",{staticClass:"save-btn"},[o("van-button",{attrs:{type:"danger",size:"large"},on:{click:t.onSubmit}},[t._v("保存")])],1)],1)],1)},staticRenderFns:[]};var c=o("VU/8")(l,d,!1,function(t){o("7co2")},null,null);e.default=c.exports}});