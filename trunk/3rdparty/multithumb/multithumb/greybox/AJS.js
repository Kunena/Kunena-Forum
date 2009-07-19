AJS={BASE_URL:"",drag_obj:null,drag_elm:null,_drop_zones:[],_cur_pos:null,join:function(_1,_2){
try{
return _2.join(_1);
}
catch(e){
var r=_2[0]||"";
AJS.map(_2,function(_4){
r+=_1+_4;
},1);
return r+"";
}
},getScrollTop:function(){
var t;
if(document.documentElement&&document.documentElement.scrollTop){
t=document.documentElement.scrollTop;
}else{
if(document.body){
t=document.body.scrollTop;
}
}
return t;
},addClass:function(){
var _6=AJS.forceArray(arguments);
var _7=_6.pop();
var _8=function(o){
if(!new RegExp("(^|\\s)"+_7+"(\\s|$)").test(o.className)){
o.className+=(o.className?" ":"")+_7;
}
};
AJS.map(_6,function(_a){
_8(_a);
});
},setStyle:function(){
var _b=AJS.forceArray(arguments);
var _c=_b.pop();
var _d=_b.pop();
AJS.map(_b,function(_e){
_e.style[_d]=AJS.getCssDim(_c);
});
},_getRealScope:function(fn,_10,_11,_12){
var _13=window;
_10=AJS.$A(_10);
if(fn._cscope){
_13=fn._cscope;
}
return function(){
var _14=[];
var i=0;
if(_11){
i=1;
}
AJS.map(arguments,function(arg){
_14.push(arg);
},i);
_14=_14.concat(_10);
if(_12){
_14=_14.reverse();
}
return fn.apply(_13,_14);
};
},preloadImages:function(){
AJS.AEV(window,"load",AJS.$p(function(_17){
AJS.map(_17,function(src){
var pic=new Image();
pic.src=src;
});
},arguments));
},_createDomShortcuts:function(){
var _1a=["ul","li","td","tr","th","tbody","table","input","span","b","a","div","img","button","h1","h2","h3","br","textarea","form","p","select","option","iframe","script","center","dl","dt","dd","small","pre"];
var _1b=function(elm){
var _1d="return AJS.createDOM.apply(null, ['"+elm+"', arguments]);";
var _1e="function() { "+_1d+"    }";
eval("AJS."+elm.toUpperCase()+"="+_1e);
};
AJS.map(_1a,_1b);
AJS.TN=function(_1f){
return document.createTextNode(_1f);
};
},documentInsert:function(elm){
if(typeof (elm)=="string"){
elm=AJS.HTML2DOM(elm);
}
document.write("<span id=\"dummy_holder\"></span>");
AJS.swapDOM(AJS.$("dummy_holder"),elm);
},getWindowSize:function(doc){
doc=doc||document;
var _22,_23;
if(self.innerHeight){
_22=self.innerWidth;
_23=self.innerHeight;
}else{
if(doc.documentElement&&doc.documentElement.clientHeight){
_22=doc.documentElement.clientWidth;
_23=doc.documentElement.clientHeight;
}else{
if(doc.body){
_22=doc.body.clientWidth;
_23=doc.body.clientHeight;
}
}
}
return {"w":_22,"h":_23};
},flattenList:function(_24){
var r=[];
var _26=function(r,l){
AJS.map(l,function(o){
if(o==null){
}else{
if(AJS.isArray(o)){
_26(r,o);
}else{
r.push(o);
}
}
});
};
_26(r,_24);
return r;
},setEventKey:function(e){
e.key=e.keyCode?e.keyCode:e.charCode;
if(window.event){
e.ctrl=window.event.ctrlKey;
e.shift=window.event.shiftKey;
}else{
e.ctrl=e.ctrlKey;
e.shift=e.shiftKey;
}
switch(e.key){
case 63232:
e.key=38;
break;
case 63233:
e.key=40;
break;
case 63235:
e.key=39;
break;
case 63234:
e.key=37;
break;
}
},removeElement:function(){
var _2b=AJS.forceArray(arguments);
AJS.map(_2b,function(elm){
AJS.swapDOM(elm,null);
});
},_unloadListeners:function(){
if(AJS.listeners){
AJS.map(AJS.listeners,function(elm,_2e,fn){
AJS.REV(elm,_2e,fn);
});
}
AJS.listeners=[];
},partial:function(fn){
var _31=AJS.forceArray(arguments);
return AJS.$b(fn,null,_31.slice(1,_31.length).reverse(),false,true);
},getIndex:function(elm,_33,_34){
for(var i=0;i<_33.length;i++){
if(_34&&_34(_33[i])||elm==_33[i]){
return i;
}
}
return -1;
},isDefined:function(o){
return (o!="undefined"&&o!=null);
},isArray:function(obj){
return obj instanceof Array;
},setLeft:function(){
var _38=AJS.forceArray(arguments);
_38.splice(_38.length-1,0,"left");
AJS.setStyle.apply(null,_38);
},appendChildNodes:function(elm){
if(arguments.length>=2){
AJS.map(arguments,function(n){
if(AJS.isString(n)){
n=AJS.TN(n);
}
if(AJS.isDefined(n)){
elm.appendChild(n);
}
},1);
}
return elm;
},isOpera:function(){
return (navigator.userAgent.toLowerCase().indexOf("opera")!=-1);
},isString:function(obj){
return (typeof obj=="string");
},hideElement:function(elm){
var _3d=AJS.forceArray(arguments);
AJS.map(_3d,function(elm){
elm.style.display="none";
});
},setOpacity:function(elm,p){
elm.style.opacity=p;
elm.style.filter="alpha(opacity="+p*100+")";
},setHeight:function(){
var _41=AJS.forceArray(arguments);
_41.splice(_41.length-1,0,"height");
AJS.setStyle.apply(null,_41);
},setWidth:function(){
var _42=AJS.forceArray(arguments);
_42.splice(_42.length-1,0,"width");
AJS.setStyle.apply(null,_42);
},createArray:function(v){
if(AJS.isArray(v)&&!AJS.isString(v)){
return v;
}else{
if(!v){
return [];
}else{
return [v];
}
}
},isDict:function(o){
var _45=String(o);
return _45.indexOf(" Object")!=-1;
},isMozilla:function(){
return (navigator.userAgent.toLowerCase().indexOf("gecko")!=-1&&navigator.productSub>=20030210);
},_listenOnce:function(elm,_47,fn){
var _49=function(){
AJS.removeEventListener(elm,_47,_49);
fn(arguments);
};
return _49;
},addEventListener:function(elm,_4b,fn,_4d,_4e){
if(!_4e){
_4e=false;
}
var _4f=AJS.$A(elm);
AJS.map(_4f,function(_50){
if(_4d){
fn=AJS._listenOnce(_50,_4b,fn);
}
if(AJS.isIn(_4b,["submit","load","scroll","resize"])){
var old=elm["on"+_4b];
elm["on"+_4b]=function(){
if(old){
fn(arguments);
return old(arguments);
}else{
return fn(arguments);
}
};
return;
}
if(AJS.isIn(_4b,["keypress","keydown","keyup","click"])){
var _52=fn;
fn=function(e){
AJS.setEventKey(e);
return _52.apply(null,arguments);
};
}
if(_50.attachEvent){
_50.attachEvent("on"+_4b,fn);
}else{
if(_50.addEventListener){
_50.addEventListener(_4b,fn,_4e);
}
}
AJS.listeners=AJS.$A(AJS.listeners);
AJS.listeners.push([_50,_4b,fn]);
});
},createDOM:function(_54,_55){
var i=0,_57;
elm=document.createElement(_54);
if(AJS.isDict(_55[i])){
for(k in _55[0]){
_57=_55[0][k];
if(k=="style"){
elm.style.cssText=_57;
}else{
if(k=="class"||k=="className"){
elm.className=_57;
}else{
elm.setAttribute(k,_57);
}
}
}
i++;
}
if(_55[0]==null){
i=1;
}
AJS.map(_55,function(n){
if(n){
if(AJS.isString(n)||AJS.isNumber(n)){
n=AJS.TN(n);
}
elm.appendChild(n);
}
},i);
return elm;
},setTop:function(){
var _59=AJS.forceArray(arguments);
_59.splice(_59.length-1,0,"top");
AJS.setStyle.apply(null,_59);
},getElementsByTagAndClassName:function(_5a,_5b,_5c){
var _5d=[];
if(!AJS.isDefined(_5c)){
_5c=document;
}
if(!AJS.isDefined(_5a)){
_5a="*";
}
var els=_5c.getElementsByTagName(_5a);
var _5f=els.length;
var _60=new RegExp("(^|\\s)"+_5b+"(\\s|$)");
for(i=0,j=0;i<_5f;i++){
if(_60.test(els[i].className)||_5b==null){
_5d[j]=els[i];
j++;
}
}
return _5d;
},removeClass:function(){
var _61=AJS.forceArray(arguments);
var cls=_61.pop();
var _63=function(o){
o.className=o.className.replace(new RegExp("\\s?"+cls,"g"),"");
};
AJS.map(_61,function(elm){
_63(elm);
});
},bindMethods:function(_66){
for(var k in _66){
var _68=_66[k];
if(typeof (_68)=="function"){
_66[k]=AJS.$b(_68,_66);
}
}
},log:function(o){
if(AJS.isMozilla()){
console.log(o);
}else{
var div=AJS.DIV({"style":"color: green"});
AJS.ACN(AJS.getBody(),AJS.setHTML(div,""+o));
}
},isNumber:function(obj){
return (typeof obj=="number");
},map:function(_6c,fn,_6e,_6f){
var i=0,l=_6c.length;
if(_6e){
i=_6e;
}
if(_6f){
l=_6f;
}
for(i;i<l;i++){
fn.apply(null,[_6c[i],i]);
}
},removeEventListener:function(elm,_73,fn,_75){
if(!_75){
_75=false;
}
if(elm.removeEventListener){
elm.removeEventListener(_73,fn,_75);
if(AJS.isOpera()){
elm.removeEventListener(_73,fn,!_75);
}
}else{
if(elm.detachEvent){
elm.detachEvent("on"+_73,fn);
}
}
},getCssDim:function(dim){
if(AJS.isString(dim)){
return dim;
}else{
return dim+"px";
}
},setHTML:function(elm,_78){
elm.innerHTML=_78;
return elm;
},bind:function(fn,_7a,_7b,_7c,_7d){
fn._cscope=_7a;
return AJS._getRealScope(fn,_7b,_7c,_7d);
},forceArray:function(_7e){
var r=[];
AJS.map(_7e,function(elm){
r.push(elm);
});
return r;
},update:function(l1,l2){
for(var i in l2){
l1[i]=l2[i];
}
return l1;
},getBody:function(){
return AJS.$bytc("body")[0];
},HTML2DOM:function(_84,_85){
var d=AJS.DIV();
d.innerHTML=_84;
if(_85){
return d.childNodes[0];
}else{
return d;
}
},getElement:function(id){
if(AJS.isString(id)||AJS.isNumber(id)){
return document.getElementById(id);
}else{
return id;
}
},showElement:function(){
var _88=AJS.forceArray(arguments);
AJS.map(_88,function(elm){
elm.style.display="";
});
},swapDOM:function(_8a,src){
_8a=AJS.getElement(_8a);
var _8c=_8a.parentNode;
if(src){
src=AJS.getElement(src);
_8c.replaceChild(src,_8a);
}else{
_8c.removeChild(_8a);
}
return src;
},isIn:function(elm,_8e){
var i=AJS.getIndex(elm,_8e);
if(i!=-1){
return true;
}else{
return false;
}
}};
AJS.$=AJS.getElement;
AJS.$$=AJS.getElements;
AJS.$f=AJS.getFormElement;
AJS.$p=AJS.partial;
AJS.$b=AJS.bind;
AJS.$A=AJS.createArray;
AJS.DI=AJS.documentInsert;
AJS.ACN=AJS.appendChildNodes;
AJS.RCN=AJS.replaceChildNodes;
AJS.AEV=AJS.addEventListener;
AJS.REV=AJS.removeEventListener;
AJS.$bytc=AJS.getElementsByTagAndClassName;
AJS.addEventListener(window,"unload",AJS._unloadListeners);
AJS._createDomShortcuts();
AJS.Class=function(_90){
var fn=function(){
if(arguments[0]!="no_init"){
return this.init.apply(this,arguments);
}
};
fn.prototype=_90;
AJS.update(fn,AJS.Class.prototype);
return fn;
};
AJS.Class.prototype={extend:function(_92){
var _93=new this("no_init");
for(k in _92){
var _94=_93[k];
var cur=_92[k];
if(_94&&_94!=cur&&typeof cur=="function"){
cur=this._parentize(cur,_94);
}
_93[k]=cur;
}
return new AJS.Class(_93);
},implement:function(_96){
AJS.update(this.prototype,_96);
},_parentize:function(cur,_98){
return function(){
this.parent=_98;
return cur.apply(this,arguments);
};
}};
AJS.$=AJS.getElement;
AJS.$$=AJS.getElements;
AJS.$f=AJS.getFormElement;
AJS.$b=AJS.bind;
AJS.$p=AJS.partial;
AJS.$FA=AJS.forceArray;
AJS.$A=AJS.createArray;
AJS.DI=AJS.documentInsert;
AJS.ACN=AJS.appendChildNodes;
AJS.RCN=AJS.replaceChildNodes;
AJS.AEV=AJS.addEventListener;
AJS.REV=AJS.removeEventListener;
AJS.$bytc=AJS.getElementsByTagAndClassName;
AJSDeferred=function(req){
this.callbacks=[];
this.errbacks=[];
this.req=req;
};
AJSDeferred.prototype={excCallbackSeq:function(req,_9b){
var _9c=req.responseText;
while(_9b.length>0){
var fn=_9b.pop();
var _9e=fn(_9c,req);
if(_9e){
_9c=_9e;
}
}
},callback:function(){
this.excCallbackSeq(this.req,this.callbacks);
},errback:function(){
if(this.errbacks.length==0){
alert("Error encountered:\n"+this.req.responseText);
}
this.excCallbackSeq(this.req,this.errbacks);
},addErrback:function(fn){
this.errbacks.unshift(fn);
},addCallback:function(fn){
this.callbacks.unshift(fn);
},addCallbacks:function(fn1,fn2){
this.addCallback(fn1);
this.addErrback(fn2);
},sendReq:function(_a3){
if(AJS.isObject(_a3)){
this.req.send(AJS.queryArguments(_a3));
}else{
if(AJS.isDefined(_a3)){
this.req.send(_a3);
}else{
this.req.send("");
}
}
}};
script_loaded=true;


script_loaded=true;