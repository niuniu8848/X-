(window.webpackJsonp=window.webpackJsonp||[]).push([["chunk-eef37c32"],{1925:function(e,t,a){"use strict";a("e036")},e036:function(e,t,a){},ea87:function(e,t){e.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADoAAAAoCAMAAACYexmKAAACZ1BMVEVHcEz/+e86n/8rd7//+O//++//9+//+e//+u//+e+9tbP/7tH/znj/9OD/vkve19HsbQDufx7l6Ok0j+X/xFr/yWkhWo8ZRnA2k+v/5LMcS3g1kej/47OhqbX/6cIqcrcfVYf/2Zb/2ZX/z3fy8u5OnOb/uTw4mfX/1IaVuNfsvZb55dHglVo0kOfagTzx8ezi5OEvgc82kuvu6ODdi0v87+A5nv361rOqrbT+8OB7p9Hi29WAtuiz0ev/ymk3mPP/04e0sbRQhryYpbb/ymg6nftOkcg4mPPOWQDps4cxhtcyiNsoba8RLUgdUIAjXpckY58dT38sesMTMlB/c2vPgyRgibLBubfWzsn38Odcf6MxXIarrbTTzsk+f73bzcKrxNnPpYbzoVrz0bP98ODmqXj959Hjn2nsrHc5mvb3u4b1snj4xJX9xWn2mC39wmmAt+l0sOimy+tbpexPoO03mPRQmNTEpWPm1buMmYCixuc5m/jm7O6/1+v/3qXUrFSiklbNm3a8p3r0qmn82bPxiy38t0v5s1o0e76ZxOr/36X7vmkfVIdBlufxmUvMnznl1a/xx3T84sLtdg8ugM06f7rBoU77v2lroL+mm2RGnu7NoTz+xWmZmHFHga6/nkmQoZAsecEWPGA9hMRmk6qAmJAvg9Hkl1qbq7+vyN3mji3njy2ZmnK8o2ZqjbEVN1gYQ2ymm2U3l/JFh8UmaKc7YIVggo9DdqftvZXcy7w+iMpGh8XX4OY3le9CmOrk6OlTj8g3lvBGfacYQWhehKslZaNKh8Svk0gXPmSzq55Mi77MmHKYl25w2jlDAAAACnRSTlMA////kH+A74+A9jIfqgAAAmxJREFUSMdjYGBlZyQDsHMwMHDgkdfy8MQtycmAx07eVR6ruXHby4BbI48Itx63KA9OBTi1yohIMjLqMDLKqSmSplWOTwNE6YAIJVFu4rXyirrwMsK1MnLbiXATqdWBD6ayUQVCc4sqEqNVhk8SRKkUFapKS0hIFNvYgl2tVkdIKzefMsitKgXCEplmTCBgpl4C1qyM7mVUrYKCuWBP1qgKqzMhgL0N2NS21mY8WgXywbSqcCkTMrDvBAVet4Agbq2WluBorBXWZUIFvYyMXSKS7S34g0mJj7tKWBpNq/QEUETzcBOIHG67ejfhahSds2ZOBIUBQa2MjBXlldKuSDoXzJ8nxEicVn6upv6lm2CaZ8xeMoWZWK3WXFwr1m50kzjoru5eNm3u4mXezPpE2woEJ4+fOXVs15qGqQuZvYx8DEjR6sx8KFQgdBszEBjqSgmRolWWmTlEIASkk1l+gz9JWrmcmJm3gzX6BfsTHUyxYK2OR8TExBSkFvkGMnszk+RXLtl1W8XFpXoCmEkJpniQTosOZhgw1D1MnIO1TRKhwQQD8icCTmsT1mqamm4KdrBjHEyjX/BR5p16nvi1pphrmSSDUxMQxEQpAINJfL1vYJARMDXt0NOaPglfVs8AUcZgrc5h0cBgEtsC9OveAyCPLBeYjFtrkmAaPIgtEH4N2rcfVErMEewjogjnRwmm3XtA9QgfN3EVR2REuBNU42ZNfXgRS1SdA/RxgqaVleZKIXARq0FCdYWtHiFZqwOfHGmVJEplSYZWc24RZV6cVTO+Rki2QB4vngYBJx6tWQI5uCVZGBhYyGv8sDEAAC4wdUSsvtDRAAAAAElFTkSuQmCC"},f4ef:function(e,t,a){"use strict";a.r(t),a("14d9");var s=a("816d"),i=a("fca6");s={components:{TitleSmall:s.a,FundBuy:i.a},data(){return{language:"zh-CN",tableData:[],amount_sum:"",amountSumValue:"",today_profit:"",todayProfitValue:"",aready_profit:"",areadyProfitValue:"",order_sum:"",showProductId:"",typeName:"mine",outputCurrency:"",buyCurrency:"",textData:[{recommend:this.$t("message.user.yijiyonghu"),revenue:"20%",subscribe:"5%"},{recommend:this.$t("message.user.erjiyonghu"),revenue:"10%",subscribe:"3%"},{recommend:this.$t("message.user.sanjiyonghu"),revenue:"5%",subscribe:"1%"}],checked1:!1,checked2:!1,tableOrderData:[],mineOrderData:[],tableDataAll:[],money:0,checkdIndex:0}},mounted(){"en"==localStorage.getItem("localLan")?this.language="en":"cht"==localStorage.getItem("localLan")?this.language="CN":"zh-CN"==localStorage.getItem("localLan")?this.language="zh-CN":this.language="other",this.getlist(),this.getIncome();var e=localStorage.getItem("spToken","");e&&""!=e&&(this.getMineOrderList(),this.getUsdt());let t=this,a=document.getElementById("lockMiner_main_search");a.oninput=function(){t.search(a.value)}},methods:{goAccounts(){this.$router.push({path:"/wallet/financialAccounts",query:{type:1}})},goHistory(){this.$router.push({path:"/order/financialHistory",query:{type:"miner"}})},getlist(){this.$fetch("api/miner!list.action").then(e=>{this.tableDataAll=e.data,this.tableData=[].concat(this.tableDataAll)})},getIncome(){this.$fetch("api/minerOrder!listSum.action").then(e=>{this.amount_sum=e.data.amount_sum,this.amountSumValue=e.data.amountSumValue,this.today_profit=e.data.today_profit,this.todayProfitValue=e.data.todayProfitValue,this.aready_profit=e.data.aready_profit,this.areadyProfitValue=e.data.areadyProfitValue,this.order_sum=e.data.order_sum,this.outputCurrency=e.data.outputCurrency,this.buyCurrency=e.data.buyCurrency||"usdt"})},getOrderList(){this.$fetch("api/financeOrder!list.action",{state:1,page_no:1}).then(e=>{})},getMineOrderList(){this.$fetch("api/minerOrder!list.action",{state:1,page_no:1}).then(e=>{this.mineOrderData=e.data})},getUsdt(){this.$fetch("api/wallet!getAll.action").then(e=>{this.money=e.data.usdt}).catch(e=>{})},buyBtn(e){""==this.$store.state.token?this.$router.push("/login"):(this.$refs.panelShow.show(),this.showProductId=e)},goRouter(e){"/loginOut"!=e?this.$router.push(e):Axios.loginOut().then(e=>{"0"==e.code&&(localStorage.removeItem("spToken"),localStorage.removeItem("vuex"),localStorage.removeItem("username"),this.$router.push("/login"),window.location.reload())})},Checkd(){if(this.checked1||this.checked2){if(this.checked1&&this.checked2){var e=[],t=[].concat(this.tableDataAll);for(let s=0;s<t.length;s++){var a=t[s];for(let e=0;e<this.mineOrderData.length;e++)this.mineOrderData[e];a.investment_min<=this.money&&"1"==a.on_sale&&e.push(JSON.parse(JSON.stringify(a)))}var s=[];for(let t=0;t<e.length;t++){var i=e[t];i.investment_min<=this.money&&"1"==i.on_sale&&s.push(JSON.parse(JSON.stringify(i)))}this.tableData=s}else if(this.checked1){var n=[],r=[].concat(this.tableDataAll);for(let e=0;e<r.length;e++){var o=r[e];o.investment_min<=this.money&&"1"==o.on_sale&&n.push(JSON.parse(JSON.stringify(o)))}this.tableData=n}else if(this.checked2){var l=[],c=[].concat(this.tableDataAll);for(let e=0;e<c.length;e++){var u=c[e];u.investment_min<=this.money&&"1"==u.on_sale&&l.push(JSON.parse(JSON.stringify(u)))}this.tableData=l}}else this.tableData=[].concat(this.tableDataAll)},search(e){var t=[],a=[].concat(this.tableDataAll);for(let i=0;i<a.length;i++){var s=a[i];(s.name_en&&0<=s.name_en.indexOf(e)||s.name_cn&&0<=s.name_cn.indexOf(e)||s.name&&0<=s.name.indexOf(e))&&t.push(JSON.parse(JSON.stringify(s)))}this.tableData=t}},computed:{getChecked1(){return this.checked1},getChecked2(){return this.checked2}},watch:{getChecked1:function(e,t){this.checkdIndex++,this.Checkd(),this.checkdIndex=0},getChecked2:function(e,t){this.checkdIndex++,this.Checkd(),this.checkdIndex=0}}},a("1925"),i=a("2877"),i=Object(i.a)(s,(function(){var e=this,t=e._self._c;return t("div",[t("title-small"),t("div",{staticClass:"miner-background"},[t("div",{staticStyle:{width:"1200px",margin:"0 auto"}},[t("div",{staticStyle:{width:"1200px",height:"auto","padding-top":"30px"}},[t("div",{staticClass:"wealth-title"},[e._v(e._s(e.$t("message.home.kuangchi_1")))]),t("div",{staticClass:"wealth-content"},[e._v(e._s(e.$t("message.home.kuangchi_2")))])]),t("div",{staticClass:"wealth-list",staticStyle:{width:"800px",height:"140px"}},[t("div",[t("p",[e._v(e._s(e.$t("message.user.dingdan")))]),t("p",[e._v(e._s((+e.amount_sum).toFixed(1))+" "+e._s(e.outputCurrency?e.outputCurrency.toUpperCase():"USDT"))]),t("p",[e._v("≈ $ "+e._s((+e.amountSumValue).toFixed(1)))])]),t("div",[t("p",[e._v(e._s(e.$t("message.user.rishouyi")))]),t("p",[e._v(e._s((+e.today_profit).toFixed(1))+" "+e._s(e.outputCurrency?e.outputCurrency.toUpperCase():"USDT"))]),t("p",[e._v("≈ $ "+e._s((+e.todayProfitValue).toFixed(1)))])]),t("div",[t("p",[e._v(e._s(e.$t("message.user.leijishouyi")))]),t("p",[e._v(e._s((+e.aready_profit).toFixed(1))+" "+e._s(e.outputCurrency?e.outputCurrency.toUpperCase():"USDT"))]),t("p",[e._v("≈ $ "+e._s((+e.areadyProfitValue).toFixed(1)))])]),t("div",[t("p",[e._v(e._s(e.$t("message.user.tuoguanzhongdingdan")))]),t("p",[e._v(e._s(e.order_sum))])])]),t("div",{staticStyle:{width:"1200px",height:"100px"}},[t("div",{staticStyle:{width:"800px",height:"100px",margin:"auto",display:"flex",position:"relative"}},[t("div",{staticClass:"theme-color wealth-zhanghu text-decoration-underline",on:{click:e.goAccounts}},[e._v(e._s(e.$t("message.user.zhanghu")))]),t("div",{staticClass:"theme-color wealth-licai text-decoration-underline",on:{click:e.goHistory}},[e._v(e._s(e.$t("message.user.lishi")))])])]),t("div",{staticStyle:{width:"1200px",height:"160px",position:"relative",display:"flex","padding-top":"130px"}},[t("a",{staticClass:"theme-color wealth-btn-licai-grey mouse-cursor",on:{click:function(t){return e.goRouter("/fundMa")}}},[e._v(e._s(e.$t("message.user.jijinlicai_1")))]),t("a",{staticClass:"theme-color wealth-btn-kuangchi",staticStyle:{position:"relative"},on:{click:function(e){}}},[e._v(" "+e._s(e.$t("message.user.kuangchisuokuang_1"))+" "),t("div",{staticClass:"wealth-btn-rect-kuangchi"})])])])]),t("div",{staticClass:"content-view-box item-box-wealth",staticStyle:{"background-color":"white"}},[t("div",{staticStyle:{position:"absolute","margin-left":"0px",height:"44px",width:"500px"}},[t("input",{directives:[{name:"model",rawName:"v-model",value:e.checked1,expression:"checked1"}],staticClass:"css-input",attrs:{type:"checkbox",id:"checkbox1",scoped:""},domProps:{checked:Array.isArray(e.checked1)?-1<e._i(e.checked1,null):e.checked1},on:{change:function(t){var a,s=e.checked1,i=(t=t.target,!!t.checked);Array.isArray(s)?(a=e._i(s,null),t.checked?a<0&&(e.checked1=s.concat([null])):-1<a&&(e.checked1=s.slice(0,a).concat(s.slice(a+1)))):e.checked1=i}}}),t("label",{staticStyle:{"background-color":"white","margin-left":"5px"},attrs:{for:"checkbox"}},[e._v(e._s(e.$t("message.home.OnlyDisplayAndPurchase")))]),t("input",{directives:[{name:"model",rawName:"v-model",value:e.checked2,expression:"checked2"}],staticClass:"css-input",staticStyle:{"margin-left":"30px"},attrs:{type:"checkbox",id:"checkbox2",scoped:""},domProps:{checked:Array.isArray(e.checked2)?-1<e._i(e.checked2,null):e.checked2},on:{change:function(t){var a,s=e.checked2,i=(t=t.target,!!t.checked);Array.isArray(s)?(a=e._i(s,null),t.checked?a<0&&(e.checked2=s.concat([null])):-1<a&&(e.checked2=s.slice(0,a).concat(s.slice(a+1)))):e.checked2=i}}}),t("label",{staticStyle:{"background-color":"white","margin-left":"5px"},attrs:{for:"checkbox"}},[e._v(e._s(e.$t("message.home.MatchMyAssets")))])]),t("div",{staticClass:"css-search-1",staticStyle:{position:"absolute","margin-left":"800px","margin-top":"-22px"}},[t("div",{staticClass:"css-search-2"},[t("div",{staticClass:"css-search-3"},[t("div",{staticClass:"bn-input-prefix css-search-4"},[t("svg",{staticClass:"css-search-5",attrs:{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",fill:"none"}},[t("path",{attrs:{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M11 6a5 5 0 110 10 5 5 0 010-10zm0-3a8 8 0 017.021 11.838l3.07 3.07-1.59 1.591-1.591 1.591-3.07-3.07A8 8 0 1111 3z",fill:"currentColor"}})])]),t("input",{staticClass:"css-search-6",attrs:{"data-bn-type":"input",id:"lockMiner_main_search",placeholder:e.$t("message.hangqing.sousuo"),value:""}})])])])]),t("div",{staticClass:"content-view-box padding-top-bottom30"},[t("el-table",{staticStyle:{width:"100%"},attrs:{data:e.tableData}},[t("el-table-column",{attrs:{prop:"date",label:e.$t("message.user.xiangmumingcheng")},scopedSlots:e._u([{key:"default",fn:function(s){return[t("div",{staticClass:"flex-row-center"},[t("img",{attrs:{src:a("ea87"),alt:"picture",width:"58px",height:"40px"}}),t("div",{staticClass:"font-size16 margin-left10"},["zh-CN"==e.language?t("div",[e._v(" "+e._s(s.row.name)+" ")]):e._e(),"CN"==e.language?t("div",[e._v(" "+e._s(s.row.name_cn)+" ")]):e._e(),"en"==e.language?t("div",[e._v(" "+e._s(s.row.name_en)+" ")]):e._e(),"Korean"==e.language?t("div",[e._v(" "+e._s(s.row.name_kn)+" ")]):e._e(),"Japanese"==e.language?t("div",[e._v(" "+e._s(s.row.name_jn)+" ")]):e._e(),"other"==e.language?t("div",[e._v(" "+e._s(s.row.name_en)+" ")]):e._e()])])]}}])}),t("el-table-column",{attrs:{prop:"daily_rate",label:e.$t("message.user.rishouyi")},scopedSlots:e._u([{key:"default",fn:function(a){return[t("div",{staticClass:"green font-bold font-size16"},[e._v(e._s(a.row.daily_rate)+" %")])]}}])}),t("el-table-column",{attrs:{prop:"cycle",label:e.$t("message.user.zhouqi")},scopedSlots:e._u([{key:"default",fn:function(a){return[t("div",{staticClass:"font-size16 font-bold"},[e._v(e._s(0!=a.row.cycle?a.row.cycle:e.$t("message.user.wuxianqi")))])]}}])}),t("el-table-column",{attrs:{prop:"address",label:e.$t("message.user.danbixiane")},scopedSlots:e._u([{key:"default",fn:function(a){return[t("div",{staticClass:"font-size16 font-bold"},[e._v(e._s(a.row.investment_min)+" - "+e._s(a.row.investment_max)+" ")])]}}])}),t("el-table-column",{attrs:{label:e.$t("message.user.caozuo")},scopedSlots:e._u([{key:"default",fn:function(a){return[t("button",{staticClass:"wealth-buy-button",attrs:{type:"primary"},on:{click:function(t){return e.buyBtn(a.row.id)}}},[e._v(e._s(e.$t("message.user.mairu")))])]}}])})],1)],1),t("div",{staticClass:"content-view-box"},[t("div",{staticClass:"font-size26 recharge-question-text"},[e._v(e._s(e.$t("message.user.xiangguanwenti")))]),t("el-collapse",[t("el-collapse-item",{attrs:{title:"1."+e.$t("message.user.chang5"),name:"1"}},[t("div",[e._v(" "+e._s(e.$t("message.user.chang6"))+" ")])]),t("el-collapse-item",{attrs:{title:"2."+e.$t("message.user.chang9"),name:"2"}},[t("el-table",{attrs:{data:e.tableData,border:""}},[t("el-table-column",{attrs:{prop:"name",label:e.$t("message.user.kuangjimingcheng")},scopedSlots:e._u([{key:"default",fn:function(a){return[t("div",{staticClass:"font-size16"},["zh-CN"==e.language?t("div",[e._v(" "+e._s(a.row.name)+" ")]):e._e(),"CN"==e.language?t("div",[e._v(" "+e._s(a.row.name_cn)+" ")]):e._e(),"en"==e.language?t("div",[e._v(" "+e._s(a.row.name_en)+" ")]):e._e(),"other"==e.language?t("div",[e._v(" "+e._s(a.row.name_en)+" ")]):e._e()])]}}])}),t("el-table-column",{attrs:{prop:"investment_min",label:e.$t("message.user.kuangjijine")},scopedSlots:e._u([{key:"default",fn:function(a){return[t("div",{staticClass:"font-size16 font-bold"},[e._v(e._s(a.row.investment_min)+" - "+e._s(a.row.investment_max)+" ")])]}}])}),t("el-table-column",{attrs:{prop:"cycle",label:e.$t("message.user.zhouqi")},scopedSlots:e._u([{key:"default",fn:function(a){return[t("div",{staticClass:"font-size16 font-bold"},[e._v(e._s(0!=a.row.cycle?a.row.cycle:e.$t("message.user.wuxianqi")))])]}}])}),t("el-table-column",{attrs:{prop:"daily_rate",label:e.$t("message.user.rishouyi")},scopedSlots:e._u([{key:"default",fn:function(a){return[t("div",{staticClass:"font-bold font-size16"},[e._v(e._s(a.row.daily_rate)+" %")])]}}])})],1)],1),t("el-collapse-item",{attrs:{title:"3."+e.$t("message.user.chang7"),name:"3"}},[t("div",{staticClass:"margin-bottom20"},[e._v(" "+e._s(e.$t("message.user.chang8"))+" ")]),t("el-table",{attrs:{data:e.textData,border:""}},[t("el-table-column",{attrs:{prop:"recommend",label:e.$t("message.user.tuijianyonghu")}}),t("el-table-column",{attrs:{prop:"revenue",label:e.$t("message.user.chang9")}}),t("el-table-column",{attrs:{prop:"subscribe",label:e.$t("message.user.chang10")}})],1)],1)],1)],1),e.buyCurrency?t("fund-buy",{ref:"panelShow",attrs:{iproduceId:e.showProductId,type:e.typeName,buyCurrency:e.buyCurrency}}):e._e(),t("footer-view")],1)}),[],!1,null,"d53586ce",null);t.default=i.exports}}]);