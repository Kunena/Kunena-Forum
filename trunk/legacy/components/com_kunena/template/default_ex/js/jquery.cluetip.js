/*
 * jQuery clueTip plugin
 * Version 0.9.0  (09/04/2007)
 * @requires jQuery v1.1.1
 * @requires Dimensions plugin 
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
(function($) { 
/*
 * @name clueTip
 * @type jQuery
 * @cat Plugins/tooltip
 * @return jQuery
 * @author Karl Swedberg
 *
 * @credit Inspired by Cody Lindley's jTip (http://www.codylindley.com)
 * @credit Thanks to Shelane Enos for the feature ideas 
 * @credit Thanks to Glen Lipka, Jörn Zaefferer, and Dan G. Switzer for their expert advice
 * @credit Thanks to Torben Schreiter for identifying bugs and offering solutions
 * @credit Thanks to Jonathan Chaffer, as always, for help with the hard parts. :-)
 */

 /**
 * 
 * Displays a highly customizable tooltip when the user hovers (default) or clicks (optional) the matched element. 
 * By default, the clueTip plugin loads a page indicated by the "rel" attribute via ajax and displays its contents.
 * If a "title" attribute is specified, its value is used as the clueTip's heading.
 * The attribute to be used for both the body and the heading of the clueTip is user-configurable. 
 * Optionally, the clueTip's body can display content from an element on the same page.
 * * Just indicate the element's id (e.g. "#some-id") in the rel attribute.
 * Optionally, the clueTip's body can display content from the title attribute, when a delimiter is indicated. 
 * * The string before the first instance of the delimiter is set as the clueTip's heading.
 * * All subsequent strings are wrapped in separate DIVs and placed in the clueTip's body.
 * The clueTip plugin allows for many, many more options. Pleasee see the examples and the option descriptions below...
 * 
 * 
 * @example $('#tip).cluetip();
 * @desc This is the most basic clueTip. It displays a 275px-wide clueTip on mouseover of the element with an ID of "tip." On mouseout of the element, the clueTip is hidden.
 *
 *
 * @example $('a.clue').cluetip({
 *  hoverClass: 'highlight',
 *  sticky: true,
 *  closePosition: 'bottom',
 *  closeText: '<img src="cross.png" alt="close" />',
 *  truncate: 60,
 *  ajaxSettings: {
 *    type: 'POST'
 *  }
 * });
 * @desc Displays a clueTip on mouseover of all <a> elements with class="clue". The hovered element gets a class of "highlight" added to it (so that it can be styled appropriately. This is esp. useful for non-anchor elements.). The clueTip is "sticky," which means that it will not be hidden until the user either clicks on its "close" text/graphic or displays another clueTip. The "close" text/graphic is set to diplay at the bottom of the clueTip (default is top) and display an image rather than the default "Close" text. Moreover, the body of the clueTip is truncated to the first 60 characters, which are followed by an ellipsis (...). Finally, the clueTip retrieves the content using POST rather than the $.ajax method's default "GET."
 * 
 *
 *
 * @param Object defaults (optional) Customize your clueTips
 * @option Number width: default is 275. The width of the clueTip
 * @option Number|String height: default is 'auto'. The height of the clueTip. Setting a specific height also sets  <div id="cluetip-outer"> to overflow:auto
 * @option Boolean local: default is false. Whether to use content from the same page (using ID) for clueTip body
 * @option Boolean hideLocal: default is true. If local option is set to true, determine whether local content to be shown in clueTip should be hidden at its original location. 
 * @option String attribute default is 'rel'. The attribute to be used for the URL of the ajaxed content
 * @option Boolean showtitle: default is true. Shows the title bar of the clueTip, whether a title attribute has been set or not. Change this to false to hide the title bar.
 * @option String cluetipClass: default is 'default'; this adds a class to the outermost clueTip div with a class name in the form of 'cluetip-' + clueTipClass. It also adds "clue-left-default" or "clue-right-default" to the same div, depending on whether the clueTip is to the left or to the right of the link element. This allows you to create your own clueTip theme in a separate CSS file or use one of the three pre-packaged themes: default, jtip, or rounded.
 * @option String titleAttribute: default is 'title'. The attribute to be used for the clueTip's heading, if the attribute exists for the hovered element.
 * @option String splitTitle: default is '' (empty string). A character used to split the title attribute into the clueTip title and divs within the clueTip body; if used, the clueTip will be populated only by the title attribute, 
 * @option String hoverClass: default is empty string. designate one to apply to the hovered element
 * @option String closePosition: default is 'top'. Set to 'bottom' to put the closeText at the bottom of the clueTip body
 * @option String closeText: default is 'Close'. This determines the text to be clicked to close a clueTip when sticky is set to true.
 * @option Number truncate: default is 0. Set to some number greater than 0 to truncate the text in the body of the clueTip. This also removes all HTML/images from the clueTip body.
 * @option Boolean waitImage: default is true. Set to false to avoid having the plugin try to show/hide the image.
 * @option Boolean arrows: Default is false. Sets background-position-y to line up an arrow background image with the hovered element.
 * @option Boolean dropShadow: default is true; set it to false if you do not want the drop-shadow effect on the clueTip
 * @option Integer dropShadowSteps: default is 6; change this number to adjust the size of the drop shadow
 * @option Boolean sticky: default is false. Set to true to keep the clueTip visible until the user either closes it manually by clicking on the CloseText or display another clueTip.
 * @option Integer cluezIndex: default is 97; sets the z-index style property of the clueTip.
 * @option String positionBy: default is 'auto'. Available options: 'auto', 'mouse', or 'bottomTop'. Change to 'mouse' if you want to override positioning by element and position the clueTip based on where the mouse is instead. Change to 'bottomTop' if you want positioning to begin below the mouse when there is room or above if not -- rather than right or left of the elemnent and flush with element's top.
 * @option Object fx: default is: {open: 'show', openSpeed: '', close: 'hide', closeSpeed: ''}. Change these to apply one of jQuery's effects when opening or closing the clueTip
 * @option String activation: default is 'hover'. Set to 'toggle' to force the user to click the element in order to activate the clueTip.
 * @option Object hoverIntent: default is {sensitivity: 3, interval: 50, timeout: 0}. If jquery.hoverintent.js plugin is included in <head>, hoverIntent() will be used with these settings instead of hover(). Set to false if for some reason you have the hoverintent plugin included but don't want to use it. For info on hoverIntent options, see http://cherne.net/brian/resources/jquery.hoverIntent.html
 * @option Function onShow: default is function (ct, c){} ; allows you to pass in your own function once the clueTip has shown.
 * @option Boolean ajaxCache: Default is true; caches the results of the ajax request to avoid unnecessary hits to the server. When set to false, the script will make an ajax request every time the clueTip is shown, which allows for dynamic content to be loaded.
 * @option Object ajaxProcess: Default is function(data) { data = $(data).not('style, meta, link, script, title); return data; } . When getting clueTip content via ajax, allows processing of it before it's displayed. The default value strips out elements typically found in the <head> that might interfere with current page.
 * @option Object ajaxSettings: allows you to pass in standard $.ajax() parameters, not including error, complete, success, and url. Default is { dataType: 'html'}
 *
 */
  var $cluetip, $cluetipInner, $cluetipOuter, $cluetipTitle, $dropShadow, imgCount;
  var msie6 = $.browser.msie && ($.browser.version && $.browser.version < 7 || (/5\.5|6.0/).test(navigator.userAgent));

  $.fn.cluetip = function(options) {
    
    // set up default options
    var defaults = {
      width: 275,
      height: 'auto',
      local: false,
      hideLocal: true,
      attribute: 'rel',
      titleAttribute: 'title',
      splitTitle: '',
      showTitle: true,
      cluetipClass: 'default',
      hoverClass: '',
      waitImage: true,
      cursor: 'help',
      arrows: false, 
      dropShadow: true,
      dropShadowSteps: 3,
      sticky: false,
      mouseOutClose: false,
      activation: 'hover',
      closePosition: 'top',
      closeText: 'Close',
      truncate: 0,
      cluezIndex: 97,
      positionBy: 'auto', 
      fx: {
        open: 'show',
        openSpeed: '',
        close: 'hide',
        closeSpeed: ''
      },
      hoverIntent: {
        sensitivity: 3,
  			interval: 50,
  			timeout: 0
      },
      onShow: function (ct, c){},
      ajaxCache: true,  
      ajaxProcess: function(data) {
        data = $(data).not('style, meta, link, script, title');
        return data;
      },
      ajaxSettings: {
        dataType: 'html'
      }
    };
    
    if (options && options.ajaxSettings) {
      $.extend(defaults.ajaxSettings, options.ajaxSettings);
      delete options.ajaxSettings;
    }
    if (options && options.fx) {
      $.extend(defaults.fx, options.fx);
      delete options.fx;
    }
    if (options && options.hoverIntent) {
      $.extend(defaults.hoverIntent, options.hoverIntent);
      delete options.hoverIntent;
    }
    $.extend(defaults, options);
    
    return this.each(function() {
      // start out with no contents (for ajax activation)
      var cluetipContents = false;
      var cluezIndex = parseInt(defaults.cluezIndex, 10)-1;
      var isActive = false;
      
      // create the cluetip divs
      if (!$cluetip) {
        $cluetipInner = $('<div id="cluetip-inner"></div>');
        $cluetipTitle = $('<h3 id="cluetip-title"></h3>');        
        $cluetipOuter = $('<div id="cluetip-outer"></div>').append($cluetipInner).prepend($cluetipTitle);
        $cluetip = $('<div></div>').attr({'id': 'cluetip'}).css({zIndex: defaults.cluezIndex})
        .append($cluetipOuter)[insertionType](insertionElement).hide();
        $('<div id="cluetip-waitimage"></div>').css({position: 'absolute', zIndex: cluezIndex-1})
        .insertBefore('#cluetip').hide();
        $cluetip.css({position: 'absolute', zIndex: cluezIndex});
        $cluetipOuter.css({position: 'relative', zIndex: cluezIndex+1});
      }
      var dropShadowSteps = (defaults.dropShadow) ? +defaults.dropShadowSteps : 0;
      if (!$dropShadow) {
        $dropShadow = $([]);
        for (var i=0; i < dropShadowSteps; i++) {
          $dropShadow = $dropShadow.add($('<div></div>').css({zIndex: cluezIndex-i-1, opacity:.1, top: 1+i, left: 1+i}));
        };
        $dropShadow.css({position: 'absolute', backgroundColor: '#000'})
        .prependTo($cluetip);
      }
      var $this = $(this);      
      var tipAttribute = $this.attr(defaults.attribute), ctClass = defaults.cluetipClass;
      if (!tipAttribute && !defaults.splitTitle) return true;
      // if hideLocal is set to true, on DOM ready hide the local content that will be displayed in the clueTip
      if (defaults.local && defaults.hideLocal) { $(tipAttribute).hide(); }
      // vertical measurement variables
      var tipHeight, wHeight;
      var defHeight = isNaN(parseInt(defaults.height, 10)) ? 'auto' : (/\D/g).test(defaults.height) ? defaults.height : defaults.height + 'px';
      var sTop, linkTop, posY, tipY, mouseY;
      // horizontal measurement variables
      var tipWidth = parseInt(defaults.width, 10) + parseInt($cluetip.css('paddingLeft')) + parseInt($cluetip.css('paddingRight')) + dropShadowSteps;
      if( isNaN(tipWidth) ) tipWidth = 275;
      var linkWidth = this.offsetWidth;
      var linkLeft, posX, tipX, mouseX, winWidth;
            
      // parse the title
      var tipParts;
      var tipTitle = (defaults.attribute != 'title') ? $this.attr(defaults.titleAttribute) : '';
      if (defaults.splitTitle) {
        tipParts = tipTitle.split(defaults.splitTitle);
        tipTitle = tipParts.shift();
      }
      var localContent;

/***************************************      
* ACTIVATION
****************************************/
    
//activate clueTip
    var activate = function(event) {
      isActive = true;
      $cluetip.removeClass().css({width: defaults.width});
      if (tipAttribute == $this.attr('href')) {
        $this.css('cursor', defaults.cursor);
      }
      $this.attr('title','');
      if (defaults.hoverClass) {
        $this.addClass(defaults.hoverClass);
      }
      linkTop = posY = $this.offset().top;
      linkLeft = $this.offset().left;
      mouseX = event.pageX;
      mouseY = event.pageY;
      if ($this[0].tagName.toLowerCase() != 'area') {
        sTop = $(document).scrollTop();
        winWidth = $(window).width();
      }
// position clueTip horizontally
      posX = (linkWidth > linkLeft && linkLeft > tipWidth)
        || linkLeft + linkWidth + tipWidth > winWidth 
        ? linkLeft - tipWidth - 15 
        : linkWidth + linkLeft + 15;
      if ($this[0].tagName.toLowerCase() == 'area' || defaults.positionBy == 'mouse' || linkWidth + tipWidth > winWidth) { // position by mouse
        if (mouseX + 20 + tipWidth > winWidth) {  
          posX = (mouseX - tipWidth - 20) >= 0 ? mouseX - tipWidth - 20 :  mouseX - (tipWidth/2);
        } else {
          posX = mouseX + 20;
        }
        var pY = posX < 0 ? event.pageY + 20 : event.pageY;
      }
      posX < linkLeft ? $cluetip.addClass('clue-left-' + ctClass).removeClass('clue-right-' + ctClass)
      : $cluetip.addClass('clue-right-' + ctClass).removeClass('clue-left-' + ctClass);                
      $cluetip.css({left: (posX > 0 && defaults.positionBy != 'bottomTop') ? posX : (mouseX + (tipWidth/2) > winWidth) ? winWidth/2 - tipWidth/2 : Math.max(mouseX - (tipWidth/2),0)});
      wHeight = $(window).height();

/***************************************
* load the title attribute only (or user-selected attribute). 
* clueTip title is the string before the first delimiter
* subsequent delimiters place clueTip body text on separate lines
***************************************/
      if (tipParts) {
        for (var i=0; i < tipParts.length; i++){
          if (i == 0) {
            $cluetipInner.html(tipParts[i]);
          } else { 
            $cluetipInner.append('<div class="split-body">' + tipParts[i] + '</div>');
          }            
        };
        cluetipShow(pY);
      }
/***************************************
* load external file via ajax          
***************************************/
      else if (!defaults.local && tipAttribute.indexOf('#') != 0) {
        if (cluetipContents && defaults.ajaxCache) {
          $cluetipInner.html(cluetipContents);
          cluetipShow(pY);
        }
        else {
          var ajaxSettings = defaults.ajaxSettings;
          ajaxSettings.url = tipAttribute;
          ajaxSettings.beforeSend = function() {
            $cluetipOuter.children().empty();
            if (defaults.waitImage) {
              $('#cluetip-waitimage')
              .css({top: mouseY-10, left: parseInt(posX+(tipWidth/2),10)})
              .show();
            }
          };
         ajaxSettings.error = function() {
            if (isActive) {
              $cluetipInner.html('<i>sorry, the contents could not be loaded</i>');
            }
          };
          ajaxSettings.success = function(data) {
            cluetipContents = defaults.ajaxProcess(data);
            if (isActive) {
              $cluetipInner.html(cluetipContents);
            }
          };
          ajaxSettings.complete = function() {
          	imgCount = $('#cluetip-inner img').length;
        		if (imgCount) {
        		  $('#cluetip-inner img').load( function(){
          			imgCount--;
          			if (imgCount<1) {
          				$('#cluetip-waitimage').hide();
          			  if (isActive) cluetipShow(pY);
          			}
        		  }); 
        		} else {
      				$('#cluetip-waitimage').hide();
        		  cluetipShow(pY);    
        		} 
          };
          $.ajax(ajaxSettings);
        }

/***************************************
* load an element from the same page
***************************************/
      } else if (defaults.local && tipAttribute.indexOf('#') == 0){
        var localContent = $(tipAttribute).html();
        $cluetipInner.html(localContent);
        cluetipShow(pY);
      }
    };

// get dimensions and options for cluetip and prepare it to be shown
    var cluetipShow = function(bpY) {
      $cluetip.addClass('cluetip-' + ctClass);
      
      if (defaults.truncate) { 
        var $truncloaded = $cluetipInner.text().slice(0,defaults.truncate) + '...';
        $cluetipInner.html($truncloaded);
      }
      function doNothing() {}; //empty function
      tipTitle ? $cluetipTitle.show().html(tipTitle) : (defaults.showTitle) ? $cluetipTitle.show().html('&nbsp;') : $cluetipTitle.hide();
      if (defaults.sticky) {
        var $closeLink = $('<div id="cluetip-close"><a href="#">' + defaults.closeText + '</a></div>');
        (defaults.closePosition == 'bottom') ? $closeLink.appendTo($cluetipInner) : (defaults.closePosition == 'title') ? $closeLink.prependTo($cluetipTitle) : $closeLink.prependTo($cluetipInner);
        $closeLink.click(function() {
          cluetipClose();
          return false;
        });
        if (defaults.mouseOutClose) {
          $cluetip.hover(function() {doNothing(); }, 
          function() {$closeLink.trigger('click'); });
        } else {
          $cluetip.unbind('mouseout');
        }
      }
// now that content is loaded, finish the positioning 
      $cluetipOuter.css({overflow: defHeight == 'auto' ? 'visible' : 'auto', height: defHeight});
      tipHeight = defHeight == 'auto' ? $cluetip.outerHeight() : parseInt(defHeight,10);   
      tipY = posY;
      if ( (posX < mouseX && Math.max(posX, 0) + tipWidth > mouseX) || defaults.positionBy == 'bottomTop') {
        tipY = posY + tipHeight > sTop + wHeight && mouseY - sTop > tipHeight + 10 ? mouseY - tipHeight - 16 : mouseY + 20;
      } else if ( posY + tipHeight > sTop + wHeight ) {
        tipY = (tipHeight >= wHeight) ? sTop : sTop + wHeight - tipHeight - 10;
      } else if ($this.css('display') == 'block' || $this[0].tagName.toLowerCase() == 'area' || defaults.positionBy == "mouse") {
        tipY = bpY - 10;
      } else {
        tipY = posY - defaults.dropShadowSteps;
      } 
      $cluetip.css({top: tipY + 'px'});
      if (defaults.arrows) { // set up background positioning to align with element
        var bgPos = '0 0';
        var bgY = (posY - tipY - defaults.dropShadowSteps);
        if ($cluetip.is('.clue-left-' + ctClass)) {
          bgPos = posX >=0 ? '100% ' + bgY + 'px' : '100% 0';
        } else if ($cluetip.is('.clue-right-' +ctClass)) {
          bgPos = (posX >=0 && bgY > 0) ? '0 ' + bgY + 'px' : '0 0';
        }        
      } else {
        bgPos = '0 100%';
      }
      $cluetip.css({backgroundPosition: bgPos});

// (first hide, then) ***SHOW THE CLUETIP***
      $dropShadow.hide();
      $cluetip.hide()[defaults.fx.open](defaults.fx.open != 'show' && defaults.fx.openSpeed);
      if (defaults.dropShadow) $dropShadow.css({height: tipHeight, width: defaults.width}).show();
      // trigger the optional onShow function
      defaults.onShow($cluetip, $cluetipInner);
    };

/***************************************
   =INACTIVATION
-------------------------------------- */
    var inactivate = function() {
      isActive = false;
      $('#cluetip-waitimage').hide();
      if (!defaults.sticky) {
        cluetipClose();
      };
      if (defaults.hoverClass) {
        $this.removeClass(defaults.hoverClass);
      }
    };
// close cluetip and reset title attribute if one exists
    var cluetipClose = function() {
      $cluetipOuter 
      .parent()[defaults.fx.close](defaults.fx.closeSpeed).removeClass().end()
      .children().empty();
      if (tipTitle) {
        $this.attr('title', tipTitle);
      }
    };

/***************************************
   =BIND EVENTS
-------------------------------------- */
  // activate by click
      if (defaults.activation == 'click'||defaults.activation == 'toggle') {
        $this.click(function(event) {
          if ($cluetip.is(':hidden')) {
            activate(event);
          } else {
            inactivate(event);
          }
          this.blur();
          return false;
        });
  // activate by hover
    // clicking is returned false if cluetip url is same as href url
      } else {
        $this.click(function() {
          if (tipAttribute == $this.attr('href')) {
            return false;
          }
        });
        if ($.fn.hoverIntent && defaults.hoverIntent) {
          $this.hoverIntent({
            sensitivity: defaults.hoverIntent.sensitivity,
            interval: defaults.hoverIntent.interval,  
            over: function(event) {activate(event);}, 
            timeout: defaults.hoverIntent.timeout,  
            out: function(event) {inactivate(event);}
          });           
        } else {
          $this.hover(function(event) {
            activate(event);
          }, function(event) {
            inactivate(event);
          });
        }
      }
    });
  };
  
/*
 * Global defaults for clueTips. Apply to all calls to the clueTip plugin.
 *
 * @example $.cluetip.setup({
 *   insertionType: 'prependTo',
 *   insertionElement: '#container'
 * });
 * 
 * @property
 * @name $.cluetip.setup
 * @type Map
 * @cat Plugins/tooltip
 * @option String insertionType: Default is 'appendTo'. Determines the method to be used for inserting the clueTip into the DOM. Permitted values are 'appendTo', 'prependTo', 'insertBefore', and 'insertAfter'
 * @option String insertionElement: Default is 'body'. Determines which element in the DOM the plugin will reference when inserting the clueTip.
 *
 */
   
  var insertionType = 'appendTo', insertionElement = 'body';
  $.cluetip = {};
  $.cluetip.setup = function(options) {
    if (options && options.insertionType && (options.insertionType).match(/appendTo|prependTo|insertBefore|insertAfter/)) {
      insertionType = options.insertionType;
    }
    if (options && options.insertionElement) {
      insertionElement = options.insertionElement;
    }
  };
})(jQuery);