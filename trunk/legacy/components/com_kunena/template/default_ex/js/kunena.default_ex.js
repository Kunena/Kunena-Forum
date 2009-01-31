jQuery(document).ready(function() {

 // jQuery.cluetip.setup({insertionType: 'insertBefore', insertionElement: 'div:first'});

//default theme
  jQuery('a.basic').cluetip();
  jQuery('a.custom-width').cluetip({width: '200px', showTitle: false});
  jQuery('h4').cluetip({attribute: 'id', hoverClass: 'highlight'});
  jQuery('#sticky').cluetip({'sticky': true,'closePosition': 'title'});
  jQuery('#examples a:eq(4)').cluetip({
    hoverClass: 'highlight',
    sticky: true,
    closePosition: 'bottom',
    closeText: '<img src="components/com_kunena/template/deafult_ex/plugin/jtip/cross.png" alt="close" width="16" height="16" />',
    truncate: 60
  });
  jQuery('a.load-local').cluetip({mouseOutClose: true,local:true,sticky: true,closePosition: 'title', width: '200px', closeText: '<img src="components/com_kunena/template/deafult_ex/plugin/jtip/cross.png" alt="close" />',dropShadow: true,cursor: 'pointer',positionBy: 'mouse'});
  jQuery('#clickme').cluetip({activation: 'click', width: 650});

// jTip theme
  jQuery('a.jt:eq(0)').cluetip({cluetipClass: 'jtip', arrows: true, dropShadow: false,
    sticky: true,
    mouseOutClose: true,
    closePosition: 'title',
    closeText: '<img src="components/com_kunena/template/deafult_ex/plugin/jtip/cross.png" alt="close" />'
  });
  jQuery('a.jt:eq(1)').cluetip({cluetipClass: 'jtip', local:true, arrows: true, dropShadow: false, hoverIntent: false});
  jQuery('span[@title]').css({borderBottom: '1px solid #900'}).cluetip({splitTitle: '|', arrows: true, dropShadow: false, cluetipClass: 'jtip'});

  jQuery('a.jt:eq(2)').cluetip({cluetipClass: 'jtip', arrows: true, dropShadow: false, height: '150px', sticky: true});

// Rounded Corner theme
  jQuery('ol.rounded a:eq(0)').cluetip({cluetipClass: 'rounded', dropShadow: false, positionBy: 'mouse'});
  jQuery('ol.rounded a:eq(1)').cluetip({cluetipClass: 'rounded', dropShadow: false, positionBy: 'bottomTop'});
  jQuery('ol.rounded a:eq(2)').cluetip({cluetipClass: 'rounded', dropShadow: false, sticky: true, ajaxCache: false});
  jQuery('ol.rounded a:eq(3)').cluetip({cluetipClass: 'rounded', dropShadow: false});    
});


  



