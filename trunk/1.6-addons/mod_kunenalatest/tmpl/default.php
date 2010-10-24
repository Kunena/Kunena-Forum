<?php
/**
 * @version $Id$
 * KunenaLatest Module
 * @package Kunena latest
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

// no direct access
defined ( '_JEXEC' ) or die ( '' );
?>
<div class="<?php echo $this->params->get ( 'moduleclass_sfx' )?> klatest <?php echo $this->params->get ( 'sh_moduleshowtype' )?>">

<ul class="klatest-items">
<?php
if (is_array ( $this->klistpost ) && !$this->kunena_config->board_offline) {
	foreach ( $this->klistpost as $item ) {
?>

<li class="klatest-item">
	<ul class="klatest-itemdetails">
		<?php
		if ( $this->params->get ( 'sh_topiciconoravatar' ) == 1) { ?>
		<li class="klatest-avatar">
			<?php echo modKunenaLatestHelper::userAvatar( $item->userid, $this->params ); ?>
		</li>
		<?php } elseif( $this->params->get ( 'sh_topiciconoravatar' ) == 0) {  ?>
		<li class="klatest-topicicon">
		<?php echo $this->ktemplate->getTopicIcon($item); ?>
		</li>
		<?php } ?>
		<li class="klatest-subject">
		<?php
			if ($this->params->get ( 'sh_sticky' )) {
				if ($item->ordering) {
					echo '<img src="' . JURI::root () . 'modules/mod_kunenalatest/tmpl/sticky.png" alt="' . JText::_ ( 'MOD_KUNENALATEST_STICKY_TOPIC' ) . '" title="' . JText::_ ( 'MOD_KUNENALATEST_STICKY_TOPIC' ) . '" />';
				}
			}
			echo CKunenaLink::GetThreadLink ( 'view', $item->catid, $item->id, JString::substr ( htmlspecialchars ( $item->subject ), '0', $this->params->get ( 'titlelength' ) ), JString::substr ( htmlspecialchars ( KunenaParser::stripBBCode($item->message) ), '0', $this->params->get ( 'messagelength' ) ), 'follow' );
			if ($item->unread) {
				echo '<sup class="knewchar">(' . JText::_($this->params->get ( 'unreadindicator' )) . ")</sup>";
			}
			if ($this->params->get ( 'sh_favorite' )) {
				if ($item->favcount) {
					if ($item->myfavorite) {
						echo '<img class="favoritestar" src="'.$this->ktemplate->getImagePath('icons/favoritestar.png').'"  alt="' . JText::_ ( 'MOD_KUNENALATEST_FAVORITE' ) . '" title="' . JText::_ ( 'MOD_KUNENALATEST_FAVORITE' ) . '" />';
					} else {
						echo '<img class="favoritestar-grey" src="'.$this->ktemplate->getImagePath('icons/favoritestar-grey.png').'"  alt="' . JText::_ ( 'MOD_KUNENALATEST_FAVORITE' ) . '" title="' . JText::_ ( 'MOD_KUNENALATEST_FAVORITE' ) . '" />';
					}
				}
			}
			if ($this->params->get ( 'sh_locked' )) {
				if ($item->locked) {
					echo '<img src="'.$this->ktemplate->getImagePath('icons/lock_sm.png').'"  alt="' . JText::_ ( 'MOD_KUNENALATEST_LOCKED_TOPIC' ) . '" title="' . JText::_ ( 'MOD_KUNENALATEST_LOCKED_TOPIC' ) . '" />';
				}
			}
			?>
		</li>
		<?php if ($this->params->get ( 'sh_firstcontentcharacter' )) : ?>
			<li class="klatest-preview-content"><?php echo JString::substr(KunenaParser::stripBBCode($item->message), '0', $this->params->get ( 'lengthcontentcharacters' )); ?></li>
		<?php endif; ?>
		<?php if ($this->params->get ( 'sh_category' )) : ?>
			<li class="klatest-cat"><?php echo JText::_ ( 'MOD_KUNENALATEST_IN_CATEGORY' ).' '.CKunenaLink::GetCategoryLink ( 'showcat', $item->catid, $item->catname ); ?></li>
		<?php endif; ?>
		<?php if ($this->params->get ( 'sh_author' )) : ?>
			<li class="klatest-author"><?php echo JText::_ ( 'MOD_KUNENALATEST_LAST_POST_BY' ) .' '. CKunenaLink::GetProfileLink ( $item->userid, $item->name ); ?></li>
		<?php endif; ?>
		<?php if ($this->params->get ( 'sh_time' )) : ?>
			<li class="klatest-posttime"><?php $override = $this->params->get ( 'dateformat' ); echo CKunenaTimeformat::showDate($item->lasttime, $override ? $override : 'config_post_dateformat');?></li>
		<?php endif; ?>
	</ul>
</li>
<?php
	} //end foreach
?>
</ul>
<p class="klatest-more"><?php echo CKunenaLink::GetShowLatestLink ( JText::_ ( 'MOD_KUNENALATEST_MORE_LINK' ) , $this->latestdo ); ?></p>
<?php
} else {
	echo JText::_('MOD_KUNENALATEST_OFFLINE');
} ?>
</div>