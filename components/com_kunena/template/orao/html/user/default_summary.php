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
					<div id="kprofile-rightcoltop">
						<div class="kprofile-rightcol2">
							<div class="kiconrow">
								<?php echo $this->profile->socialButton('twitter', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('facebook', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('myspace', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('linkedin', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('skype', $this->showUnusedSocial) ?>
							</div>
							<div class="kiconrow">
								<?php echo $this->profile->socialButton('delicious', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('friendfeed', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('digg', $this->showUnusedSocial) ?>
							</div>
							<div class="clr"></div>
							<div class="kiconrow">
								<?php echo $this->profile->socialButton('yim', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('aim', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('gtalk', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('icq', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('msn', $this->showUnusedSocial) ?>
							</div>
							<div class="kiconrow">
								<?php echo $this->profile->socialButton('blogspot', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('flickr', $this->showUnusedSocial) ?>
								<?php echo $this->profile->socialButton('bebo', $this->showUnusedSocial) ?>
							</div>
						</div>
						<div class="kprofile-rightcol1">
							<ul>
								<li><span class="kicon-profile kicon-profile-location"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCATION') ?>:</strong> <?php echo $this->locationlink ?></li>
								<li><span class="kicon-profile kicon-profile-gender-unknown"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_GENDER') ?>:</strong> <?php echo $this->gender ?></li>
								<li><span class="kicon-profile kicon-profile-birthdate"></span><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_BIRTHDATE') ?>:</strong> <?php //echo KunenaDate::getInstance($this->profile->birthdate)->toKunena('date', 'ago', 'utc') ?></li>
							</ul>
						</div>
					</div>
					<div id="kprofile-leftcolbot">
						<div class="kprofile-leftcol2">
							<ul>
								<?php if ($this->config->showemail && (!$this->profile->hideEmail || $this->me->isModerator())) : ?><li><span class="kicon-profile kicon-profile-email"></span><?php echo JHTML::_('email.cloak', $this->user->email) ?></li><?php endif; ?>
								<?php if (!empty($this->profile->websiteurl)):?><li><span class="kicon-profile kicon-profile-website"></span><a href="http://<?php echo $this->escape($this->profile->websiteurl) ?>" target="_blank"><?php echo KunenaHtmlParser::parseText($this->profile->websitename) ?></a></li><?php endif ?>
								<li>&nbsp;</li>
							</ul>
						</div>
					</div>