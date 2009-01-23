$(document).ready(function() {

 // $.cluetip.setup({insertionType: 'insertBefore', insertionElement: 'div:first'});

//default theme
  $('a.basic').cluetip();
  $('a.custom-width').cluetip({width: '200px', showTitle: false});
  $('h4').cluetip({attribute: 'id', hoverClass: 'highlight'});
  $('#sticky').cluetip({'sticky': true,'closePosition': 'title'});
  $('#examples a:eq(4)').cluetip({
    hoverClass: 'highlight',
    sticky: true,
    closePosition: 'bottom',
    closeText: '<img src="components/com_fireboard/template/numinu/plugin/jtip/cross.png" alt="close" width="16" height="16" />',
    truncate: 60
  });
  $('a.load-local').cluetip({mouseOutClose: true,local:true,sticky: true,closePosition: 'title', width: '200px', closeText: '<img src="components/com_fireboard/template/numinu/plugin/jtip/cross.png" alt="close" />',dropShadow: true,cursor: 'pointer',positionBy: 'mouse'});
  $('#clickme').cluetip({activation: 'click', width: 650});

// jTip theme
  $('a.jt:eq(0)').cluetip({cluetipClass: 'jtip', arrows: true, dropShadow: false,
    sticky: true,
    mouseOutClose: true,
    closePosition: 'title',
    closeText: '<img src="components/com_fireboard/template/numinu/plugin/jtip/cross.png" alt="close" />'
  });
  $('a.jt:eq(1)').cluetip({cluetipClass: 'jtip', local:true, arrows: true, dropShadow: false, hoverIntent: false});
  $('span[@title]').css({borderBottom: '1px solid #900'}).cluetip({splitTitle: '|', arrows: true, dropShadow: false, cluetipClass: 'jtip'});

  $('a.jt:eq(2)').cluetip({cluetipClass: 'jtip', arrows: true, dropShadow: false, height: '150px', sticky: true});

// Rounded Corner theme
  $('ol.rounded a:eq(0)').cluetip({cluetipClass: 'rounded', dropShadow: false, positionBy: 'mouse'});
  $('ol.rounded a:eq(1)').cluetip({cluetipClass: 'rounded', dropShadow: false, positionBy: 'bottomTop'});
  $('ol.rounded a:eq(2)').cluetip({cluetipClass: 'rounded', dropShadow: false, sticky: true, ajaxCache: false});
  $('ol.rounded a:eq(3)').cluetip({cluetipClass: 'rounded', dropShadow: false});    
});


  



