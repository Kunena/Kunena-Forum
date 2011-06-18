<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php echo $this->getModulePosition( 'kunena_bottom' ); ?>
<!-- Kunena Footer -->
<?php if (isset($this->rss)) : ?>
<div class="krss-block"><?php echo $this->rss; ?></div>
<?php endif; ?>
<div class="kcredits kms"><?php echo $this->credits; ?></div>
<?php if ( $this->config->time_to_create_page ) : ?>
<div class="kfooter">
	<span class="kfooter-time"><?php echo JText::sprintf('COM_KUNENA_VIEW_COMMON_FOOTER_TIME', $this->getTime()) ?></span>
</div>
<?php endif; ?>
<!-- /Kunena Footer -->