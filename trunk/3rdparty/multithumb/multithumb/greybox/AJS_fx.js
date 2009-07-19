AJS.fx={_shades:{0:"ffffff",1:"ffffee",2:"ffffdd",3:"ffffcc",4:"ffffbb",5:"ffffaa",6:"ffff99"},highlight:function(_1,_2){
var _3=new AJS.fx.Base();
_3.elm=AJS.$(_1);
_3.setOptions(_2);
_3.options.duration=600;
AJS.update(_3,{increase:function(){
if(this.now==7){
_1.style.backgroundColor="transparent";
}else{
_1.style.backgroundColor="#"+AJS.fx._shades[Math.floor(this.now)];
}
}});
return _3.custom(6,0);
},fadeIn:function(_4,_5){
_5=_5||{};
if(!_5.from){
_5.from=0;
AJS.setOpacity(_4,0);
}
if(!_5.to){
_5.to=1;
}
var s=new AJS.fx.Style(_4,"opacity",_5);
return s.custom(_5.from,_5.to);
},fadeOut:function(_7,_8){
_8=_8||{};
if(!_8.from){
_8.from=1;
}
if(!_8.to){
_8.to=0;
}
_8.duration=300;
var s=new AJS.fx.Style(_7,"opacity",_8);
return s.custom(_8.from,_8.to);
},setWidth:function(_a,_b){
var s=new AJS.fx.Style(_a,"width",_b);
return s.custom(_b.from,_b.to);
},setHeight:function(_d,_e){
var s=new AJS.fx.Style(_d,"height",_e);
return s.custom(_e.from,_e.to);
}};
AJS.fx.Base=new AJS.Class({init:function(){
AJS.bindMethods(this);
},setOptions:function(_10){
this.options=AJS.update({onStart:function(){
},onComplete:function(){
},transition:AJS.fx.Transitions.sineInOut,duration:500,wait:true,fps:50},_10||{});
},step:function(){
var _11=new Date().getTime();
if(_11<this.time+this.options.duration){
this.cTime=_11-this.time;
this.setNow();
}else{
setTimeout(AJS.$b(this.options.onComplete,this,[this.elm]),10);
this.clearTimer();
this.now=this.to;
}
this.increase();
},setNow:function(){
this.now=this.compute(this.from,this.to);
},compute:function(_12,to){
var _14=to-_12;
return this.options.transition(this.cTime,_12,_14,this.options.duration);
},clearTimer:function(){
clearInterval(this.timer);
this.timer=null;
return this;
},_start:function(_15,to){
if(!this.options.wait){
this.clearTimer();
}
if(this.timer){
return;
}
setTimeout(AJS.$p(this.options.onStart,this.elm),10);
this.from=_15;
this.to=to;
this.time=new Date().getTime();
this.timer=setInterval(this.step,Math.round(1000/this.options.fps));
return this;
},custom:function(_17,to){
return this._start(_17,to);
},set:function(to){
this.now=to;
this.increase();
return this;
},setStyle:function(elm,_1b,val){
if(this.property=="opacity"){
AJS.setOpacity(elm,val);
}else{
AJS.setStyle(elm,_1b,val);
}
}});
AJS.fx.Style=AJS.fx.Base.extend({init:function(elm,_1e,_1f){
this.parent();
this.elm=elm;
this.setOptions(_1f);
this.property=_1e;
},increase:function(){
this.setStyle(this.elm,this.property,this.now);
}});
AJS.fx.Styles=AJS.fx.Base.extend({init:function(elm,_21){
this.parent();
this.elm=AJS.$(elm);
this.setOptions(_21);
this.now={};
},setNow:function(){
for(p in this.from){
this.now[p]=this.compute(this.from[p],this.to[p]);
}
},custom:function(obj){
if(this.timer&&this.options.wait){
return;
}
var _23={};
var to={};
for(p in obj){
_23[p]=obj[p][0];
to[p]=obj[p][1];
}
return this._start(_23,to);
},increase:function(){
for(var p in this.now){
this.setStyle(this.elm,p,this.now[p]);
}
}});
AJS.fx.Transitions={linear:function(t,b,c,d){
return c*t/d+b;
},sineInOut:function(t,b,c,d){
return -c/2*(Math.cos(Math.PI*t/d)-1)+b;
}};
script_loaded=true;


script_loaded=true;