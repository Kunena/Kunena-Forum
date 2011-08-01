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

JHTML::_('behavior.tooltip');
?>
<?php /*?>
		<div class="kuserprofile">
			<?php if (!empty($this->editLink)) echo $this->editLink ?>
			<h2 class="kheader"><a href="#" rel="kmod-detailsbox"><?php echo JText::_('COM_KUNENA_USER_PROFILE').' '.$this->escape($this->name) ?></a></h2>
			<div class="kdetailsbox kmod-userbox" id="kmod-detailsbox">
				<?php $this->displaySummary(); ?>
				<div class="clrline"></div>
				<?php $this->displayTab(); ?>
				<div class="clr"></div>
			</div>
		</div>
<?php */?>

<div class="forumlist tk-profile-sommary">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon profilesommary">
						<dt>
							<span class="ktitle">
							<?php echo JText::_('COM_KUNENA_USER_PROFILE').' '.$this->escape($this->name) ?></span>
						</dt>
							<?php if (!empty($this->editlink)) echo '<dd style="float:right;margin-bottom: -10px; margin-top: 3px;"><span class="tk-editlink"><span>'.$this->editlink.'</span></span></dd>';?>
					</dl>
				</li>
			</ul>
			<div id="profilesommary_tbody">
			<ul class="topiclist forums">
				<li class="rowfull" style="padding-bottom: 50px;">
					<dl class="icon profile-sommary">
					<dt></dt>
					<?php if ($this->avatarlink) : ?>
					<dd class="tk-profile-avatar">
						<div id="kprofile-container">
							<div id="kprofile-leftcol">
								<div class="avatar-lg"><?php echo $this->avatarlink; ?></div>
							</div>
						</div>
					</dd>
					<?php endif; ?>
					<dd class="tk-profile-name" style="border:0;margin-bottom:40px;">
						<div>
							<h1 style="margin:10px 10px 20px 10px;"><?php if ($this->config->userlist_name): ?><?php echo $this->escape($this->profile->name);?><?php else :?><?php echo $this->escape($this->profile->username);?><?php endif;?></h1>
							<?php if (!empty($this->rank_image)): ?><span style="clear:left;margin:0 10px;font-style: italic;font-size: 17px;"><?php echo $this->rank_image; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php if (!empty($this->rank_title)): ?><?php echo $this->rank_title; ?><?php endif?></span><?php endif;?>
						</div>
					</dd>
					<dd class="tk-profile-info">
						<?php if (!empty($this->userpoints)): ?>
						<div class="tk-profile-info-points tk-profile-info-body">
							<span class="tk-info-header"><?php echo JText::_('COM_KUNENA_AUP_POINTS'); ?></span>
							<span class="tk-info-text"><?php echo intval($this->userpoints); ?></span>
						</div>
						<?php endif; ?>
						<div class="tk-profile-info-timezone tk-profile-info-body">
							<span class="tk-info-header"><?php echo JText::_('COM_KUNENA_MYPROFILE_TIMEZONE'); ?></span>
							<span class="tk-info-text">GMT <?php echo $this->localtime->toTimezone() ?></span>
						</div>
						<div class="tk-profile-info-localtime tk-profile-info-body">
							<span class="tk-info-header"><?php echo JText::_('COM_KUNENA_MYPROFILE_LOCAL_TIME'); ?></span>
							<span class="tk-info-text"><?php echo $this->localtime->toKunena('time') ?></span>
						</div>
						<?php if (!empty($this->posts)): ?>
						<div class="tk-profile-info-localtime tk-profile-info-body">
							<span class="tk-info-header"><?php echo JText::_('COM_KUNENA_MYPROFILE_POSTS'); ?></span>
							<span class="tk-info-text"><?php echo $this->posts; ?></span>
						</div>
						<?php endif; ?>
						<div class="tk-profile-info-localtime tk-profile-info-body">
							<span class="tk-info-header"><?php echo JText::_('COM_KUNENA_MYPROFILE_PROFILEVIEW'); ?></span>
							<span class="tk-info-text"><?php echo $this->profile->uhits; ?></span>
						</div>
					</dd>
					</dl>
					</li>
				</ul>
			</div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>

			<div>
			<?php $this->displayTab(); ?>
			</div>



<script type="text/javascript">
// <![CDATA[
window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new KunenaTabs(tabs); }); });
// ]]>
</script>