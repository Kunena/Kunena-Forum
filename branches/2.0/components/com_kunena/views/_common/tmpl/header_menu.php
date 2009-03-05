<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');
?>

				<div id="top-menu">
					<!-- Home Link -->
					<?php echo JHTML::_( 'Kunena.iconlink', $this->icons, 'home', JRoute::_('index.php?option=com_kunena'), JText::_('KUNENA_HOME') ); ?>

<?php
if (!$this->user->get('guest')) {
	if ($this->params->get('cb_profile')) {
		$link = 'index.php?option=com_comprofiler&Itemid='.$this->cbitemid.'&task=userDetails';
	} else {
		$link = 'index.php?option=com_kunena&func=myprofile&do=show';
	}
?>
					<!-- My Profile Link -->
					<?php echo JHTML::_( 'Kunena.iconlink', $this->icons, 'profile', JRoute::_($link), JText::_('KUNENA_GEN_MYPROFILE') );
}

/*
	if (in_array( $this->catid, $this->user->roles['moderate'])) {
		// TODO: Model this to a forum model - it's a bit of a hack
		$db = &JFactory::getDBO();

		$db->setQuery(
			'SELECT COUNT(*)' .
			' FROM `#__fb_messages`' .
			' WHERE `catid`='.(int) $this->catid.
			' AND `hold`=1'
		);
		if ($numPending = $database->loadResult()) {
?>
					<!-- Moderator Pending Messages Link -->
					<span style="color:red"><?php echo JHTML::_( 'Kunena.iconlink', $this->icons, 'pendingmessages', JRoute::_('index.php?option=com_kunena&func=review&action=list&catid='.$this->catid ), $numPending.' '.JText::_('KUNENA_SHOWCAT_PENDING') ); ?><span>
<?php
		}
	}
*/
?>
					<!-- Latest Posts Link -->
					<?php echo JHTML::_( 'Kunena.iconlink', $this->icons, 'showlatest', JRoute::_('index.php?option=com_kunena&func=latest' ), JText::_('KUNENA_GEN_LATEST_POSTS') );

if ($this->params->get('enableRulesPage')) {
	if ($this->params->get('help_infb')) {
		$link = JRoute::_('index.php?option=com_kunena&view=rules');
	} else {
		$link = $this->config['rules_link'];
	}
?>
					<!-- Rules Link -->
					<?php echo JHTML::_( 'Kunena.iconlink', $this->icons, 'rules', $link, JText::_('KUNENA_GEN_RULES') );
}

if ($this->params->get('enableHelpPage')) {
	if ($this->params->get('help_infb')) {
		$link = JRoute::_('index.php?option=com_kunena&view=faq');
	} else {
		$link = $this->params->get('help_link');
	}
?>
					<!-- Help Link -->
					<?php echo JHTML::_( 'Kunena.iconlink', $this->icons, 'help', $link, JText::_('KUNENA_GEN_HELP') );
}
?>

				</div>
