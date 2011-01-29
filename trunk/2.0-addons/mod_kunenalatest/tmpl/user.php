<?php
/**
 * @version $Id: default.php 4211 2011-01-16 15:09:56Z xillibit $
 * KunenaLatest Module
 * @package Kunena latest
 *
 * @Copyright (C) 2010-2011 www.kunena.org. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ( '' );
?>
<div class="<?php echo $this->params->get ( 'moduleclass_sfx' )?> klatest <?php echo $this->params->get ( 'sh_moduleshowtype' )?>">
	<ul class="klatest-items">
		<?php if (empty ( $this->topics )) : ?>
			<li><?php echo JText::_('COM_KUNENA_VIEW_NO_POSTS') ?></li>
		<?php else : ?>
			<?php $this->displayRows (); ?>
		<?php endif; ?>
	</ul>
</div>