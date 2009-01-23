/*<![CDATA[*/
/**
* @version $Id: bojForumCore.js 1061 2008-10-05 20:37:53Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

jQuery.cookie = function(name, value, options)
{
    if (typeof value != 'undefined')
    { // name and value given, set cookie
        options = options || { };
        var expires = '';

        if (options.expires && ( typeof options.expires == 'number' || options.expires.toGMTString))
        {
            var date;

            if (typeof options.expires == 'number')
            {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            }
            else
            {
                date = options.expires;
            }

            expires = '; expires=' + date.toGMTString(); // use expires attribute, max-age is not supported by IE
        }

        var path = options.path ? '; path=' + options.path : '';
        var domain = options.domain ? '; domain=' + options.domain : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    }
    else
    { // only name given, get cookie
        var cookieValue = null;

        if (document.cookie && document.cookie != '')
        {
            var cookies = document.cookie.split(';');

            for (var i = 0; i < cookies.length; i++)
            {
                var cookie = jQuery.trim(cookies[i]);

                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '='))
                {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }

        return cookieValue;
    }
};

function JRshrinkHeaderMulti(mode, imgId, cid)
{
    if (mode == 1)
    {
        cMod = 0;
    }
    else
    {
        cMod = 1;
    }

    jQuery.cookie("upshrink_" + imgId, cMod);
    jQuery("#" + imgId).attr("src", window.jr_expandImg_url + (cMod ? "expand.gif" : "shrink.gif"));

    if (cMod)
    {
        jQuery("#" + cid).hide();
    }
    else
    {
        jQuery("#" + cid).show();
    }
}


function fbGetPreview(content, sitemid) {
    var templatePath = document.postform.templatePath.value;
    var content = encodeURIComponent(content);
    
    jQuery.ajax({url:"index2.php",
    data : { msgpreview : content, Itemid : sitemid , option: "com_fireboard" , func: "getpreview" , no_html: 1},
    type: "POST",
    beforeSend : function (req){
        jQuery('#previewContainer').show();
        jQuery('#previewMsg'). html("<img src='"+templatePath+"/images/preview_loading.gif' />");    
    },
    success : function (req){
        jQuery('#previewMsg'). html(req)
        return;
    }
    });
    return false;
}

jQuery(function()
{
    jQuery(".hideshow").click(function()
    {
        var imgId = jQuery(this).attr("id");
        var cId = imgId.split("__")[1];
        var cVal = jQuery.cookie("upshrink_" + imgId);
        JRshrinkHeaderMulti(cVal, imgId, cId);
    }).each(function()
    {
        var imgId = jQuery(this).attr("id");
        var cId = imgId.split("__")[1];

        if (jQuery.cookie("upshrink_" + imgId) == 1)
        {
            JRshrinkHeaderMulti(0, imgId, cId);
        }
    });

});

/*]]>*/