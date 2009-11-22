<?php
/**
 * @version		$Id: default.php 1024 2009-08-19 06:18:15Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
$app =& JFactory::getApplication();
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
?>
<div id="kunena">
<?php echo $this->loadCommonTemplate('header'); ?>
<?php echo $this->loadCommonTemplate('pathway'); ?>

<table class="forum_body">
	<thead>
		<tr>
			<th colspan="10">
				<h1><?php echo $this->escape($this->title); ?></h1>
				<div><?php echo $app->getCfg('sitename'); ?> has <?php echo $this->total; ?> registered users.</div>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th class="mcol col_id">&nbsp;</th>
			<th class="lcol col_emoticon">Status</th>
			<th class="lcol col_emoticon">Avatar</th>
      <th class="mcol col_topics">Name</th>
      <th class="mcol col_topics">Username</th>
      <th class="mcol col_topics">Posts</th>
      <th class="mcol col_topics">Karma</th>
      <th class="mcol col_topics">Join date</th>
      <th class="mcol col_topics">Last login</th>
      <th class="mcol col_topics">Hits</th>
		</tr>
			<?php $i ="1";
          foreach($this->userlist as $userlist){ ?>
         <tr><td class="fcol">
					<?php echo $i++; ?>
			</td>	<td>
      <img src ="<?php echo KURL_COMPONENT_MEDIA; ?>/images/icons/offlineicon.gif" alt="" />
		  </td>
      <td>
      <?php if($userlist->avatar == null) { echo "<img src = \"".KURL_COMPONENT_MEDIA."images/avatars/s_nophoto.jpg\" alt=\"\" />"; } else { echo "avatar user"; } ?>
		  </td><td>
					<?php echo JHtml::_('klink.user', 'atag', $userlist->userid, $userlist->name, $userlist->name); ?>
			</td><td>
					<?php echo JHtml::_('klink.user', 'atag', $userlist->userid, $userlist->username, $userlist->username); ?>
			</td><td>
				<?php echo $userlist->posts; ?>
			</td><td>
					<?php echo $userlist->karma; ?>
			</td><td>
					<?php echo $userlist->registerDate; ?>
			</td><td>
					<?php echo $userlist->lastvisitDate; ?>
			</td><td>
					<?php echo $userlist->uhits; ?>
			</td></tr>
            <?php }   ?>
	</tbody>
</table>

<!-- B: Category List Bottom -->
<div class="bottom_info_box">
	<?php echo $this->loadCommonTemplate('forumcat'); ?>
</div>
<!-- F: Category List Bottom -->

<?php
echo $this->loadCommonTemplate ( 'footer' );
?>
	</div>