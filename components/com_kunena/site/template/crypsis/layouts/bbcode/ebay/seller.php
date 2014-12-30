<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.BBCode
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

// [ebay]sellerid[/ebay]

// Display ebay seller.
?>
<object width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>">
	<param name="movie" value="http://togo.ebay.com/togo/seller.swf?2008013100" />
	<param name="flashvars" value="base=http://togo.ebay.com/togo/&lang=<?php echo $this->language; ?>&seller=<?php echo $this->content; ?>&campid=<?php echo $this->affiliate; ?>" />
	<embed src="http://togo.ebay.com/togo/seller.swf?2008013100" type="application/x-shockwave-flash" width="355" height="355" flashvars="base=http://togo.ebay.com/togo/&lang=<?php echo $this->language; ?>&seller=<?php echo $this->content; ?>&campid=<?php echo $this->affiliate; ?>">
	</embed>
</object>
