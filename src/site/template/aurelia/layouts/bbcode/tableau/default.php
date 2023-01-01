<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

// [tableau height=1000]https://public.tableau.com/views/VGContest_InteractiveFiscalCalendar_ShinichiroMurakami/FYCalendar?:embed=y&:embed_code_version=3&:loadOrderID=0&:display_count=y&publish=yes&:origin=viz_share_link[/tableau]

// Display visual analytics from https://www.tableau.com/
?>

<div class='tableauPlaceholder' id='viz748596' style='position: relative'>
    <noscript>
        <a href='https://www.tableau.com/'>
            <img alt=' ' src='https://public.tableau.com/static/images/VG/<?php echo $this->vizualization; ?>/<?php echo $this->vizualizationTab; ?>/1_rss.png' style='border: none' />
        </a>
    </noscript>
    <object class='tableauViz'  style='display:none;'>
        <param name='host_url' value='ttps://public.tableau.com/' />
        <param name='embed_code_version' value='3' /> 
        <param name='site_root' value='' />
        <param name='name' value='<?php echo $this->vizualization; ?>/<?php echo $this->vizualizationTab; ?>' />
        <param name='tabs' value='yes' />
        <param name='toolbar' value='yes' />
        <param name='static_image' value='https://public.tableau.com/static/images/VG/<?php echo $this->vizualization; ?>/<?php echo $this->vizualizationTab; ?>/1_rss.png' /> 
        <param name='animate_transition' value='yes' />
        <param name='display_static_image' value='yes' />
        <param name='display_spinner' value='yes' />
        <param name='display_overlay' value='yes' />
        <param name='display_count' value='yes' />
        <param name='filter' value='publish=yes' />
    </object>
</div>
<script type='text/javascript'>
    var divElement = document.getElementById('viz748596');
    var vizElement = divElement.getElementsByTagName('object')[0];
    vizElement.style.width='100%';vizElement.style.height=(divElement.offsetWidth*0.75)+'px';
    var scriptElement = document.createElement('script');
    scriptElement.src = 'https://public.tableau.com/javascripts/api/viz_v1.js';
    vizElement.parentNode.insertBefore(scriptElement, vizElement);
</script>