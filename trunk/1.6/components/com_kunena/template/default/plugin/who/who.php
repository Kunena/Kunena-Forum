<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();
// FIXME: get rid of this page
if ($this->config->showwhoisonline) {
	$users=$this->getUsersList();
?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="kwhois_tbody"></a></span>
		<h2><span><?php echo $this->app->getCfg('sitename'); ?> - <?php echo JText::_('COM_KUNENA_WHO_WHOIS_ONLINE'); ?></span></h2>
	</div>
	<div class="kcontainer" id="kwhois_tbody">
	<table>
		<tbody>
			<tr class = "ksth">
				<th class = "th-1 ksectiontableheader"><?php echo JText::_('COM_KUNENA_WHO_ONLINE_USER'); ?></th>
				<th class = "th-2 ksectiontableheader"><?php echo JText::_('COM_KUNENA_WHO_ONLINE_TIME'); ?></th>
				<th class = "th-3 ksectiontableheader"><?php echo JText::_('COM_KUNENA_WHO_ONLINE_FUNC'); ?></th>
			</tr>

			<?php
			$k = 0; //for alternating rows
			$tabclass = array ("row1","row2");

			foreach ($users as $user) :
				$k = 1 - $k;

				if ($user->userid == 0) {
					$user->username = JText::_('COM_KUNENA_GUEST');
				} else if ($user->showOnline < 1 && !CKunenaTools::isModerator($this->my->id)) {
					continue;
				}
			?>
			<tr class = "k<?php echo $this->escape($tabclass[$k]);?>">
				<td class = "td-1">
					<div style = "float: right; width: 14ex;"></div>
					<span>
						<?php
						if ($user->userid == 0) :
							echo JText::_('COM_KUNENA_GUEST');
						else :
							echo CKunenaLink::GetProfileLink(intval($user->userid));
						endif;
						?>
					</span>
					<?php
					if (CKunenaTools::isAdmin($this->my->id) && $this->config->hide_ip) :
						echo '('.$this->escape($user->userip).')';
					elseif (CKunenaTools::isModerator($this->my->id) && !$this->config->hide_ip) :
						echo '('.$this->escape($user->userip).')';
					endif;
					?>
					</td>
					<td class = "td-2" nowrap = "nowrap">
						<span title="<?php echo CKunenaTimeformat::showDate ( $user->time, 'config_post_dateformat_hover' ) ?>">
							<?php echo CKunenaTimeformat::showDate ( $user->time, 'config_post_dateformat' ) ?>
						</span>
					</td>

					<td class = "td-3">
						<strong><a href = "<?php echo $this->escape($user->link);?>" target = "_blank"><?php echo $this->escape($user->what); ?></a></strong>
					</td>
				</tr>
		<?php endforeach; ?>
	</table>
</div>
</div>
<?php } else { ?>
	<div style = "border:1px solid #FF6600; background: #FF9966; padding:20px; text-align:center;">
		<h1><?php echo JText::_('COM_KUNENA_WHO_IS_ONLINE_NOT_ACTIVE'); ?></h1>
	</div>
<?php } ?>