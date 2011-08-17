<?php
/**
* @version $Id: initialize.php 2751 2010-06-16 15:46:57Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

$document = JFactory::getDocument();
$template = KunenaFactory::getTemplate();
$this->params = $template->params;
$iconsurl = JURI::base() . "media/kunena/icons";

$styles = <<<EOF
#kunena div#ktab li.item-{$this->params->get('topmenu1Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu1Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu1Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu2Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu2Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu2Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu3Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu3Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu3Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu4Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu4Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu4Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu5Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu5Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu5Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu6Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu6Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu6Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu7Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu7Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu7Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu8Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu8Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu8Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu9Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu9Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu9Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu10Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu10Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu10Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu11Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu11Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu11Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu12Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu12Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu12Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu13Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu13Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu13Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu14Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu14Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu14Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu15Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu15Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu15Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu16Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu16Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu16Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu17Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu17Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu17Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu18Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu18Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu18Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu19Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu19Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu19Itemidimage')}");
}
#kunena div#ktab li.item-{$this->params->get('topmenu20Itemid')} a,#kunena div#ktab-flat li.item-{$this->params->get('topmenu20Itemid')} a {
	padding-left:20px !important; background-image: url("{$iconsurl}/{$this->params->get('topmenu20Itemidimage')}");
}
#kunena div#ktab li a,
#kunena div#ktab-flat li a {
	background-position: left center;
	background-repeat: no-repeat;
}
EOF;
$document->addStyleDeclaration($styles);