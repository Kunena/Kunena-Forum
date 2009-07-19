<!--
Notice: I feel so dirty doing this, but its the only way to make it cross browser.
-->
<html>
<head>
  <script>
    var GB = parent.GB_CURRENT;
    document.write('<script type="text/javascript" src="AJS.js"><\/script>');
    if(GB.use_fx) {
        document.write('<script type="text/javascript" src="AJS_fx.js"><\/script>');
    }
  </script>
  <style>
    body {
      padding: 0;
      margin: 0;
      overflow: hidden;
    }

    #GB_frame {
      visibility: hidden;
      width: 100%;
      height: 100%;
    }

    #loading {
      padding-top: 50px;
      position: absolute;
      width: 100%;
      top: 0;
      text-align: center;
      vertical-align: middle;
    }
  </style>
</head>
<body>

<div id="loading">
  <img src="indicator.gif">
</div>

<script>
var loading = AJS.$('loading');
var gb_type = GB.type;
var gb_url = GB.url;

//Start loading in the iframe
if(gb_type == "page") {
  document.write('<iframe id="GB_frame" src="' + gb_url + '" frameborder="0"></iframe>');
}
else {
  var img_holder = new Image();
  img_holder.src = gb_url;
  document.write('<img id="GB_frame" src="' + gb_url + '">');
}
var frame = AJS.$('GB_frame');
</script>

</body>
<script>
function setupOuterGB() {
    frame.style.visibility = 'visible';
    GB.setFrameSize();
    GB.setWindowPosition();
}

function loaded() {
    AJS.removeElement(loading);

    GB.overlay.innerHTML += "&nbsp;"; //Safari bugfix
    
    if(gb_type == "image") {
        if(img_holder.width != 0 && img_holder.height != 0) {
            var width = img_holder.width;
            var height = img_holder.height;

            GB.width = width;
            GB.height = height;

            setupOuterGB();

            if(GB.use_fx) {
                AJS.setOpacity(frame, 0);
                AJS.fx.fadeIn(frame);
            }
        }
    }
    else {
        GB.width = frame.offsetWidth;
        GB.height = frame.offsetHeight;
        setupOuterGB();
    }
}

if(GB.show_loading) {
    AJS.AEV(window, 'load', function(e) {
        loaded();
    });
}
else {
    loaded();
}
</script>
</html>
