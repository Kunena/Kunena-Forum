<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template
 * @subpackage Users
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$db = JFactory::getDBO();
$document = JFactory::getDocument();
$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.css' );
if (JFactory::getLanguage()->isRTL()) $document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/admin.rtl.css' );
$document->addScriptDeclaration(' var current_count = '.JString::strlen($this->user->signature).'
var max_count = '.(int) $this->config->maxsig.'

function textCounter(field, target) {
	if (field.value.length > max_count) {
		field.value = field.value.substring(0, max_count);
	} else {
		current_count = max_count - field.value.length;
		target.value = current_count;
	}
}');

$paneOptions = array(
		'onActive' => 'function(title, description){
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
}',
		'onBackground' => 'function(title, description){
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
}',
		'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
		'useCookie' => true, // this must not be a string. Don't use quotes.
);
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/template/joomla25/common/menu.php'; ?></div>
	<div class="kadmin-right">
	<div class="kadmin-functitle icon-profiles"><?php echo JText::_('COM_KUNENA_PROFFOR'); ?>: <?php echo $this->escape($this->user->name) .' ('. $this->escape($this->user->username) .')'; ?></div>
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="view" value="users" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="1" />
		<input type="hidden" name="uid" value="<?php echo $this->user->userid; ?>" />
		<?php echo JHtml::_( 'form.token' ); ?>

		<?php
			echo JHtml::_('tabs.start', 'pane', $paneOptions);
			echo JHtml::_('tabs.panel', JText::_('COM_KUNENA_A_BASIC_SETTINGS'), 'panel_basic');
		?>
		<fieldset>
		<legend><?php echo JText::_('COM_KUNENA_A_BASIC_SETTINGS') ?></legend>
		<table class="kadmin-adminform">
			<tr>
				<th colspan="3" class="title"><?php echo JText::_('COM_KUNENA_GENPROF'); ?></th>
			</tr>
			<tr>
				<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_PREFOR'); ?></td>
				<td align="left" valign="top" class="contentpane"><?php echo $this->selectOrder; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_RANKS'); ?></td>
				<td align="left" valign="top" class="contentpane"><?php echo $this->selectRank; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="150" valign="top" class="contentpane"><?php echo JText::_('COM_KUNENA_GEN_SIGNATURE'); ?>:
				</td>
				<td align="left" valign="top" class="contentpane">
	<textarea class="inputbox" name="signature" cols="50" rows="6"
	onkeyup="textCounter(this, this.form.current_count);"><?php echo $this->escape( $this->user->signature ); ?></textarea>
	<br /><br />
	<div><?php echo JText::sprintf('COM_KUNENA_SIGNATURE_LENGTH_COUNTER', intval($this->config->maxsig),
			'<input readonly="readonly" type="text" name="current_count" value="'.(intval($this->config->maxsig)-JString::strlen($this->user->signature)).'" size="3" />');?>
	</div>
	<br />
	<div> <input type="checkbox" value="1" name="deleteSig" /> <em><?php echo JText::_('COM_KUNENA_DELSIG'); ?></em></div>

	</td>
	</tr>
	</table>
</fieldset>

<?php echo JHtml::_('tabs.panel', JText::_('COM_KUNENA_A_AVATARS'), 'panel_avatars'); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_AVATARS') ?></legend>
				<table class="kadmin-adminform">
				<tr>
				<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_UAVATAR'); ?></th>
			</tr>
			<tr>
				<td class="contentpane">
				<?php echo $this->avatar;
				if ($this->editavatar) { ?>
					<p><input type="checkbox" value="1"
					name="deleteAvatar" /> <em><?php echo JText::_('COM_KUNENA_DELAV'); ?></em></p></td>
				<?php } else {
					echo "<td>&nbsp;</td>";
					echo '<input type="hidden" value="" name="avatar" />';
				}
				?>

				<td><?php if ($this->editavatar) {
					 } else {
					echo "<td>&nbsp;</td>";
				}
				?></td>
			</tr>
		</table>

	</fieldset>

<?php echo JHtml::_('tabs.panel', JText::_('COM_KUNENA_MOD_NEW'), 'panel_mods'); ?>

		<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_MOD_NEW') ?></legend>
				<table class="kadmin-adminform">
					<tr>
						<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_MODCHANGE'); ?></th>
					</tr>
					<tr>
						<td width="150" class="contentpane"><?php echo JText::_('COM_KUNENA_ISMOD'); ?></td>
						<td><?php echo JText::_('COM_KUNENA_MODCATS'); ?></td>
					</tr>
					<tr>
						<td width="150" class="contentpane"><?php
						echo $this->selectMod;
						?>
						</td>
						<td><?php echo $this->modCats; ?></td>
					</tr>
				</table>
			</fieldset>

			<?php echo JHtml::_('tabs.panel', JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS'), 'panel_catsubscriptions'); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS') ?></legend>
				<table class="kadmin-adminform">
					<tr>
						<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_SUBFOR') . ' ' . $this->escape($this->user->username); ?></th>
					</tr>
					<?php
					$enum = 1; //reset value
					$k = 0; //value for alternating rows

					if (!empty($this->subscatslist)) {
						foreach($this->subscatslist as $subscats) { //get all category details for each subscription
							$db->setQuery ( "select cat.name AS catname, cat.id, msg.subject, msg.id, msg.catid, msg.name AS username from #__kunena_categories AS cat INNER JOIN #__kunena_messages AS msg ON cat.id=msg.catid where cat.id='$subscats->category_id' GROUP BY cat.id" );
							$catdetail = $db->loadObjectList ();
							if (KunenaError::checkDatabaseError()) break;

							foreach ( $catdetail as $cat ) {
								$k = 1 - $k;
								echo "<tr class=\"row$k\">";
								echo "  <td width=\"30\">$enum</td>";
								echo " <td><strong>" . $this->escape ( $cat->catname ) ."</strong>" ." &nbsp;". JText::_('COM_KUNENA_LAST_MESSAGE'). "<em>".$this->escape ( $cat->subject )."</em>" ." &nbsp;". JText::_('COM_KUNENA_BY') ." &nbsp;". "<em>".$this->escape ( $cat->username )."</em></td>";
								echo "</tr>";
								$enum ++;
							}
						}
					} else {
						echo "<tr><td class=\"message\">" . JText::_('COM_KUNENA_NOCATSUBS') . "</td></tr>";
					}
					?>
				</table>
			</fieldset>

			<?php echo JHtml::_('tabs.panel', JText::_('COM_KUNENA_TOPIC_SUBSCRIPTIONS'), 'panel_subscriptions'); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_TOPIC_SUBSCRIPTIONS') ?></legend>
				<table class="kadmin-adminform">
					<tr>
						<th colspan="2" class="title"><?php echo JText::_('COM_KUNENA_SUBFOR') . ' ' . $this->escape($this->user->username); ?></th>
					</tr>
					<?php
						$enum = 1; //reset value
						$k = 0; //value for alternating rows


					if ($this->sub) {
						foreach ( $this->sub as $subs ) { //get all message details for each subscription
							$db->setQuery ( "select * from #__kunena_messages where id='$subs->thread'" );
							$subdet = $db->loadObjectList ();
							if (KunenaError::checkDatabaseError()) break;

							foreach ( $subdet as $sub ) {
								$k = 1 - $k;
								echo "<tr class=\"row$k\">";
								echo "  <td width=\"30\">$enum</td>";
								echo " <td><strong>" . $this->escape ( $sub->subject ) ."</strong>" ." &nbsp;". JText::_('COM_KUNENA_BY' ) ." &nbsp;". "<em>".$this->escape ( $sub->name )."</em></td>";
								echo "</tr>";
								$enum ++;
							}
						}
					} else {
						echo "<tr><td class=\"message\">" . JText::_('COM_KUNENA_NOSUBS') . "</td></tr>";
					}
					?>
				</table>
			</fieldset>

			<?php echo JHtml::_('tabs.panel', JText::_('COM_KUNENA_TRASH_IP'), 'panel_ips'); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_TRASH_IP') ?></legend>
				<table class="kadmin-adminform">
					<tr>
						<th colspan="3" class="title"><?php
						echo JText::sprintf('COM_KUNENA_IPFOR', $this->escape($this->user->username));
						?>
						</th>
					</tr>
					<?php
					$i = 0;
					$k = 0; //value for alternating rows

					$userids='';
					foreach ($this->ipslist as $ip => $list) {
						$k = 1 - $k;
						$i++;
						$userlist = array();
						$mescnt = 0;
						foreach ($list as $curuser) {
							if ($curuser->userid == $this->user->userid) {
								$mescnt += intval($curuser->mescnt);
								continue;
							}
							$userlist[] = $this->escape($curuser->username).' ('.$this->escape($curuser->mescnt).')';
						}
						$userlist = implode(', ', $userlist);
						echo "<tr class=\"row$k\">";
						echo "  <td width=\"30\">".$i."</td>";
						echo "  <td width=\"60\"><strong>".$this->escape($ip)."</strong></td>";
						echo "  <td>(".JText::sprintf('COM_KUNENA_IP_OCCURENCES', $mescnt).(!empty($userlist)?" ".JText::sprintf('COM_KUNENA_USERIDUSED', $this->escape($userlist)):'').")</td>";
						//echo "  <td>&nbsp;</td>";
						echo "</tr>";
					}
					?>
				</table>
			</fieldset>

			<?php echo JHtml::_('tabs.end'); ?>
	</form>
	</div>
	<div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>
