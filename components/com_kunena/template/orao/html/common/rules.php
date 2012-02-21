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
<div id="tk-register-rules" class="register">
<div class="forumlist" style="padding-top: 3px !important;">
	<div class="catinner">
			<ul class="topiclist">
				<li class="header">
					<dl class="icon rules">
						<dt class="first">
							<span class="ktitle">
								<?php echo JText::_('COM_KUNENA_FORUM_RULES'); ?>
							</span>
						</dt>
					</dl>
				</li>
			</ul>
			<ul class="topiclist forums">
				<li class="rowfull">
					<dl class="icon rules">
					<dt></dt>
					<?php // FIXME :  Insert rules page and translation?>
						<dd class="first" style="padding-top: 10px !important;">
							<?php echo JText::_( 'Joomla! is a free open source framework and content publishing system designed for quickly creating highly interactive multi-language Web sites, online communities, media portals, blogs and eCommerce applications.
Joomla! LogoJoomla! provides an easy-to-use graphical user interface that simplifies the management and publishing of large volumes of content including HTML, documents, and rich media. Joomla! is used by organisations of all sizes for intranets and extranets and is supported by a community of tens of thousands of users.
							' ); ?>:
						</dd>
					</dl>
				</li>
			</ul>
			<ul id="rules_tbody" class="topiclist forums">
				<li class="rowfull">
					<dl class="">
						<dt class="body" style="padding:15px">
							<span id="tk-register-title" class="tk-register-agree tk-tips" title="<?php echo JText::_('COM_KUNENA_RULES_NO_THANKS') ?>"><a class="tk-registerlink"><?php echo JText::_('COM_KUNENA_TEMPLATE_AGREE'); ?></a></span>
							<span class="tk-register-cancel tk-tips" title="<?php echo JText::_('COM_KUNENA_RULES_CLICK_CONTINUE') ?>"><a class="tk-registerlink" href="javascript:register.sweepToggle('contract')"><?php echo JText::_('COM_KUNENA_TEMPLATE_DO_NOT_AGREE'); ?></a></span>
						</dt>
					</dl>
				</li>
			</ul>
	</div>
</div>
</div>