var GB_CURRENT=null;
GB_hide=function(){
GB_CURRENT.hide();
};
GreyBox=new AJS.Class({init:function(_1){
this.use_fx=AJS.fx;
this.type="page";
this.overlay_click_close=false;
this.salt=0;
this.root_dir=GB_ROOT_DIR;
this.callback_fns=[];
this.reload_on_close=false;
this.src_loader=this.root_dir+"loader_frame.html";
var _2=window.location.hostname.indexOf("www");
var _3=this.src_loader.indexOf("www");
if(_2!=-1&&_3==-1){
this.src_loader=this.src_loader.replace("://","://www.");
}
if(_2==-1&&_3!=-1){
this.src_loader=this.src_loader.replace("://www.","://");
}
this.show_loading=true;
AJS.update(this,_1);
},addCallback:function(fn){
if(fn){
this.callback_fns.push(fn);
}
},show:function(_5){
GB_CURRENT=this;
this.url=_5;
var _6=[AJS.$bytc("object"),AJS.$bytc("select")];
AJS.map(AJS.flattenList(_6),function(_7){
_7.style.visibility="hidden";
});
this.createElements();
return false;
},hide:function(){
var _8=this.callback_fns;
if(_8!=[]){
AJS.map(_8,function(fn){
fn();
});
}
this.onHide();
if(this.use_fx){
var _a=this.overlay;
AJS.fx.fadeOut(this.overlay,{onComplete:function(){
AJS.removeElement(_a);
_a=null;
},duration:300});
AJS.removeElement(this.g_window);
}else{
AJS.removeElement(this.g_window,this.overlay);
}
this.removeFrame();
AJS.REV(window,"scroll",_GB_setOverlayDimension);
AJS.REV(window,"resize",_GB_update);
var _b=[AJS.$bytc("object"),AJS.$bytc("select")];
AJS.map(AJS.flattenList(_b),function(_c){
_c.style.visibility="visible";
});
GB_CURRENT=null;
if(this.reload_on_close){
window.location.reload();
}
},update:function(){
this.setOverlayDimension();
this.setFrameSize();
this.setWindowPosition();
},createElements:function(){
this.initOverlay();
this.g_window=AJS.DIV({"id":"GB_window"});
AJS.hideElement(this.g_window);
AJS.getBody().insertBefore(this.g_window,this.overlay.nextSibling);
this.initFrame();
this.initHook();
this.update();
var me=this;
if(this.use_fx){
AJS.fx.fadeIn(this.overlay,{duration:300,to:0.7,onComplete:function(){
me.onShow();
AJS.showElement(me.g_window);
me.startLoading();
}});
}else{
AJS.setOpacity(this.overlay,0.7);
AJS.showElement(this.g_window);
this.onShow();
this.startLoading();
}
AJS.AEV(window,"scroll",_GB_setOverlayDimension);
AJS.AEV(window,"resize",_GB_update);
},removeFrame:function(){
try{
AJS.removeElement(this.iframe);
}
catch(e){
}
this.iframe=null;
},startLoading:function(){
this.iframe.src=this.src_loader+"?s="+this.salt++;
AJS.showElement(this.iframe);
},setOverlayDimension:function(){
var _e=AJS.getWindowSize();
if(AJS.isMozilla()||AJS.isOpera()){
AJS.setWidth(this.overlay,"100%");
}else{
AJS.setWidth(this.overlay,_e.w);
}
var _f=Math.max(AJS.getScrollTop()+_e.h,AJS.getScrollTop()+this.height);
if(_f<AJS.getScrollTop()){
AJS.setHeight(this.overlay,_f);
}else{
AJS.setHeight(this.overlay,AJS.getScrollTop()+_e.h);
}
},initOverlay:function(){
this.overlay=AJS.DIV({"id":"GB_overlay"});
if(this.overlay_click_close){
AJS.AEV(this.overlay,"click",GB_hide);
}
AJS.setOpacity(this.overlay,0);
AJS.getBody().insertBefore(this.overlay,AJS.getBody().firstChild);
},initFrame:function(){
if(!this.iframe){
var d={"name":"GB_frame","class":"GB_frame","frameBorder":0};
this.iframe=AJS.IFRAME(d);
this.middle_cnt=AJS.DIV({"class":"content"},this.iframe);
this.top_cnt=AJS.DIV();
this.bottom_cnt=AJS.DIV();
AJS.ACN(this.g_window,this.top_cnt,this.middle_cnt,this.bottom_cnt);
}
},onHide:function(){
},onShow:function(){
},setFrameSize:function(){
},setWindowPosition:function(){
},initHook:function(){
}});
_GB_update=function(){
if(GB_CURRENT){
GB_CURRENT.update();
}
};
_GB_setOverlayDimension=function(){
if(GB_CURRENT){
GB_CURRENT.setOverlayDimension();
}
};
AJS.preloadImages(GB_ROOT_DIR+"indicator.gif");
script_loaded=true;
var GB_SETS={};
function decoGreyboxLinks(){
var as=AJS.$bytc("a");
AJS.map(as,function(a){
if(a.getAttribute("href")&&a.getAttribute("rel")){
var rel=a.getAttribute("rel");
if(rel.indexOf("gb_")==0){
var _14=rel.match(/\w+/)[0];
var _15=rel.match(/\[(.*)\]/)[1];
var _16=0;
var _17={"caption":a.title||"","url":a.href};
if(_14=="gb_pageset"||_14=="gb_imageset"){
if(!GB_SETS[_15]){
GB_SETS[_15]=[];
}
GB_SETS[_15].push(_17);
_16=GB_SETS[_15].length;
}
if(_14=="gb_pageset"){
a.onclick=function(){
GB_showFullScreenSet(GB_SETS[_15],_16);
return false;
};
}
if(_14=="gb_imageset"){
a.onclick=function(){
GB_showImageSet(GB_SETS[_15],_16);
return false;
};
}
if(_14=="gb_image"){
a.onclick=function(){
GB_showImage(_17.caption,_17.url);
return false;
};
}
if(_14=="gb_page"){
a.onclick=function(){
var sp=_15.split(/, ?/);
GB_show(_17.caption,_17.url,parseInt(sp[1]),parseInt(sp[0]));
return false;
};
}
if(_14=="gb_page_fs"){
a.onclick=function(){
GB_showFullScreen(_17.caption,_17.url);
return false;
};
}
if(_14=="gb_page_center"){
a.onclick=function(){
var sp=_15.split(/, ?/);
GB_showCenter(_17.caption,_17.url,parseInt(sp[1]),parseInt(sp[0]));
return false;
};
}
}
}
});
}
AJS.AEV(window,"load",decoGreyboxLinks);
GB_showImage=function(_1a,url,_1c){
var _1d={width:300,height:300,type:"image",fullscreen:false,center_win:true,caption:_1a,callback_fn:_1c};
var win=new GB_Gallery(_1d);
return win.show(url);
};
GB_showPage=function(_1f,url,_21){
var _22={type:"page",caption:_1f,callback_fn:_21,fullscreen:true,center_win:false};
var win=new GB_Gallery(_22);
return win.show(url);
};
GB_Gallery=GreyBox.extend({init:function(_24){
this.parent({});
this.img_close=this.root_dir+"g_close.gif";
AJS.update(this,_24);
this.addCallback(this.callback_fn);
},initHook:function(){
AJS.addClass(this.g_window,"GB_Gallery");
var _25=AJS.DIV({"class":"inner"});
this.header=AJS.DIV({"class":"GB_header"},_25);
AJS.setOpacity(this.header,0);
AJS.getBody().insertBefore(this.header,this.overlay.nextSibling);
var _26=AJS.TD({"id":"GB_caption","class":"caption","width":"40%"},this.caption);
var _27=AJS.TD({"id":"GB_middle","class":"middle","width":"20%"});
var _28=AJS.IMG({"src":this.img_close});
AJS.AEV(_28,"click",GB_hide);
var _29=AJS.TD({"class":"close","width":"40%"},_28);
var _2a=AJS.TBODY(AJS.TR(_26,_27,_29));
var _2b=AJS.TABLE({"cellspacing":"0","cellpadding":0,"border":0},_2a);
AJS.ACN(_25,_2b);
if(this.fullscreen){
AJS.AEV(window,"scroll",AJS.$b(this.setWindowPosition,this));
}else{
AJS.AEV(window,"scroll",AJS.$b(this._setHeaderPos,this));
}
},setFrameSize:function(){
var _2c=this.overlay.offsetWidth;
var _2d=AJS.getWindowSize();
if(this.fullscreen){
this.width=_2c-40;
this.height=_2d.h-80;
}
AJS.setWidth(this.iframe,this.width);
AJS.setHeight(this.iframe,this.height);
AJS.setWidth(this.header,_2c);
},_setHeaderPos:function(){
AJS.setTop(this.header,AJS.getScrollTop()+10);
},setWindowPosition:function(){
var _2e=this.overlay.offsetWidth;
var _2f=AJS.getWindowSize();
AJS.setLeft(this.g_window,((_2e-50-this.width)/2));
var _30=AJS.getScrollTop()+55;
if(!this.center_win){
AJS.setTop(this.g_window,_30);
}else{
var fl=((_2f.h-this.height)/2)+20+AJS.getScrollTop();
if(fl<0){
fl=0;
}
if(_30>fl){
fl=_30;
}
AJS.setTop(this.g_window,fl);
}
this._setHeaderPos();
},onHide:function(){
AJS.removeElement(this.header);
AJS.removeClass(this.g_window,"GB_Gallery");
},onShow:function(){
if(this.use_fx){
AJS.fx.fadeIn(this.header,{to:1});
}else{
AJS.setOpacity(this.header,1);
}
}});
AJS.preloadImages(GB_ROOT_DIR+"g_close.gif");
GB_showFullScreenSet=function(set,_33,_34){
var _35={type:"page",fullscreen:true,center_win:false};
var _36=new GB_Sets(_35,set);
_36.addCallback(_34);
_36.showSet(_33-1);
return false;
};
GB_showImageSet=function(set,_38,_39){
var _3a={type:"image",fullscreen:false,center_win:true,width:300,height:300};
var _3b=new GB_Sets(_3a,set);
_3b.addCallback(_39);
_3b.showSet(_38-1);
return false;
};
GB_Sets=GB_Gallery.extend({init:function(_3c,set){
this.parent(_3c);
if(!this.img_next){
this.img_next=this.root_dir+"next.gif";
}
if(!this.img_prev){
this.img_prev=this.root_dir+"prev.gif";
}
this.current_set=set;
},showSet:function(_3e){
this.current_index=_3e;
var _3f=this.current_set[this.current_index];
this.show(_3f.url);
this._setCaption(_3f.caption);
this.btn_prev=AJS.IMG({"class":"left",src:this.img_prev});
this.btn_next=AJS.IMG({"class":"right",src:this.img_next});
AJS.AEV(this.btn_prev,"click",AJS.$b(this.switchPrev,this));
AJS.AEV(this.btn_next,"click",AJS.$b(this.switchNext,this));
GB_STATUS=AJS.SPAN({"class":"GB_navStatus"});
AJS.ACN(AJS.$("GB_middle"),this.btn_prev,GB_STATUS,this.btn_next);
this.updateStatus();
},updateStatus:function(){
AJS.setHTML(GB_STATUS,(this.current_index+1)+" / "+this.current_set.length);
if(this.current_index==0){
AJS.addClass(this.btn_prev,"disabled");
}else{
AJS.removeClass(this.btn_prev,"disabled");
}
if(this.current_index==this.current_set.length-1){
AJS.addClass(this.btn_next,"disabled");
}else{
AJS.removeClass(this.btn_next,"disabled");
}
},_setCaption:function(_40){
AJS.setHTML(AJS.$("GB_caption"),_40);
},updateFrame:function(){
var _41=this.current_set[this.current_index];
this._setCaption(_41.caption);
this.url=_41.url;
this.startLoading();
},switchPrev:function(){
if(this.current_index!=0){
this.current_index--;
this.updateFrame();
this.updateStatus();
}
},switchNext:function(){
if(this.current_index!=this.current_set.length-1){
this.current_index++;
this.updateFrame();
this.updateStatus();
}
}});
AJS.AEV(window,"load",function(){
AJS.preloadImages(GB_ROOT_DIR+"next.gif",GB_ROOT_DIR+"prev.gif");
});
GB_show=function(_42,url,_44,_45,_46){
var _47={caption:_42,height:_44||500,width:_45||500,fullscreen:false,callback_fn:_46};
var win=new GB_Window(_47);
return win.show(url);
};
GB_showCenter=function(_49,url,_4b,_4c,_4d){
var _4e={caption:_49,center_win:true,height:_4b||500,width:_4c||500,fullscreen:false,callback_fn:_4d};
var win=new GB_Window(_4e);
return win.show(url);
};
GB_showFullScreen=function(_50,url,_52){
var _53={caption:_50,fullscreen:true,callback_fn:_52};
var win=new GB_Window(_53);
return win.show(url);
};
GB_Window=GreyBox.extend({init:function(_55){
this.parent({});
this.img_header=this.root_dir+"header_bg.gif";
this.img_close=this.root_dir+"w_close.gif";
this.show_close_img=true;
AJS.update(this,_55);
this.addCallback(this.callback_fn);
},initHook:function(){
AJS.addClass(this.g_window,"GB_Window");
this.header=AJS.TABLE({"class":"header"});
this.header.style.backgroundImage="url("+this.img_header+")";
var _56=AJS.TD({"class":"caption"},this.caption);
var _57=AJS.TD({"class":"close"});
if(this.show_close_img){
var _58=AJS.IMG({"src":this.img_close});
var _59=AJS.SPAN("Close");
var btn=AJS.DIV(_58,_59);
AJS.AEV([_58,_59],"mouseover",function(){
AJS.addClass(_59,"on");
});
AJS.AEV([_58,_59],"mouseout",function(){
AJS.removeClass(_59,"on");
});
AJS.AEV([_58,_59],"mousedown",function(){
AJS.addClass(_59,"click");
});
AJS.AEV([_58,_59],"mouseup",function(){
AJS.removeClass(_59,"click");
});
AJS.AEV([_58,_59],"click",GB_hide);
AJS.ACN(_57,btn);
}
tbody_header=AJS.TBODY();
AJS.ACN(tbody_header,AJS.TR(_56,_57));
AJS.ACN(this.header,tbody_header);
AJS.ACN(this.top_cnt,this.header);
if(this.fullscreen){
AJS.AEV(window,"scroll",AJS.$b(this.setWindowPosition,this));
}
},setFrameSize:function(){
if(this.fullscreen){
var _5b=AJS.getWindowSize();
overlay_h=_5b.h;
this.width=Math.round(this.overlay.offsetWidth-(this.overlay.offsetWidth/100)*10);
this.height=Math.round(overlay_h-(overlay_h/100)*10);
}
AJS.setWidth(this.header,this.width+6);
AJS.setWidth(this.iframe,this.width);
AJS.setHeight(this.iframe,this.height);
},setWindowPosition:function(){
var _5c=AJS.getWindowSize();
AJS.setLeft(this.g_window,((_5c.w-this.width)/2)-13);
if(!this.center_win){
AJS.setTop(this.g_window,AJS.getScrollTop());
}else{
var fl=((_5c.h-this.height)/2)-20+AJS.getScrollTop();
if(fl<0){
fl=0;
}
AJS.setTop(this.g_window,fl);
}
}});
AJS.preloadImages(GB_ROOT_DIR+"w_close.gif",GB_ROOT_DIR+"header_bg.gif");


script_loaded=true;