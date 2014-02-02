<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>

<div class="kmsgimage">
	<a href="<?php echo $this->fileurl ?>" title="" class="fancybox-button" rel="fancybox-button">
		<img src="<?php echo $this->fileurl ?>"<?php echo $this->width ?> style="max-height:<?php echo $this->imageheight ?>px;" alt="" />
	</a>
</div>