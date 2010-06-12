<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();
if (!isset($this->showUnusedSocial)) $this->showUnusedSocial = false;

$doc =& JFactory::getDocument();
$doc->addScript( JURI::root().'plugins/system/mootools12/tips.js' );
?>

<div class="kiconrow">
	<?php echo $this->profile->socialButton('twitter', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('facebook', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('myspace', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('linkedin', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('skype', $this->showUnusedSocial); ?>
</div>
<div class="kiconrow">
	<?php echo $this->profile->socialButton('delicious', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('friendfeed', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('digg', $this->showUnusedSocial); ?>
</div>
<div class="clr"></div>
<div class="kiconrow">

	<?php echo $this->profile->socialButton('yim', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('aim', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('gtalk', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('icq', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('msn', $this->showUnusedSocial); ?>
</div>
<div class="kiconrow">
	<?php echo $this->profile->socialButton('blogspot', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('flickr', $this->showUnusedSocial); ?>
	<?php echo $this->profile->socialButton('bebo', $this->showUnusedSocial); ?>
</div>