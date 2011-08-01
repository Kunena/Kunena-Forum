<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon userlist">
						<dt><?php echo JText::_('COM_KUNENA_USRL_USERLIST') ?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>

			<div class="kuserlist-items">
				<?php foreach ($this->users as $user) { $this->displayUserRow($user); } ?>
			</div>
			<div class="clr"></div>

		<span class="corners-bottom"><span></span></span>
	</div>
</div>