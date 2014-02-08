<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.BBCode
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

// [ebay]112233445566[/ebay]

// Display ebay item.
?>
<object width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>">
	<param name="movie" value="http://togo.ebay.com/togo/togo.swf" />
	<param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=<?php echo $this->language; ?>&mode=normal&itemid=<?php echo $this->content; ?>&campid=<?php echo $this->affiliate; ?>" />
	<embed src="http://togo.ebay.com/togo/togo.swf" type="application/x-shockwave-flash" width="355" height="300" flashvars="base=http://togo.ebay.com/togo/&lang=<?php echo $this->language; ?>&mode=normal&itemid=<?php echo $this->content; ?>&campid=<?php echo $this->affiliate; ?>">
	</embed>
</object>
