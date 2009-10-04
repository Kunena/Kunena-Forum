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
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
?>
<div id="kunena">
<?php echo $this->loadCommonTemplate('header'); ?>
<?php echo $this->loadCommonTemplate('pathway'); ?>

<table class="forum_body">
	<thead>
		<tr>
			<th>
				<h1><?php echo $this->escape($this->title); ?></h1>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="fcol">This information is available only for registered users.</td>
		</tr>
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