<?php
/**
 * @version		$Id: $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>
<div class="profilebox">
	<div>
		<?php echo JHtml::_('klink.user', 'atag', $this->profile->userid, $this->escape($this->profile->name), $this->escape($this->profile->name)); ?>
	</div>
	<div>
		<?php echo JHtml::_('klink.user', 'atag', $this->profile->userid, '<img src="http://kunena15/images/fbfiles/avatars/nophoto.jpg" alt="" />', $this->escape($this->profile->name)); ?>
	</div>
	<div>Admin</div>

	<div>
		<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/ranks/rankadmin.gif" alt="" />
	</div>
	<div>Posts: <?php echo $this->profile->posts; ?></div>
	<div>
		<table>
			<tr>
				<td width="64">
					<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/graph/col9m.png" height="4" width="0" alt="graph" />
					<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/emoticons/graph.gif" height="4" width="60" alt="graph" />
				</td>
			</tr>
		</table>
	</div>
	<div>
		<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/offline.gif" alt="User Offline" />
		<?php echo JHtml::_('klink.user', 'atag', $this->profile->userid, '<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/profile.gif" alt="Click here to see the profile of this user" title="Click here to see the profile of this user" />', $this->escape($this->profile->name)); ?>
	</div>
</div>
