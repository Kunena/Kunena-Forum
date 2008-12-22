<?php
/**
 * @version		$Id: default.php 5 2008-11-22 07:05:46Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');
?>
<div id="<?php echo 'post-'.$this->post->id; ?>" class="">
	<h2><?php echo JHtml::date($this->post->created_time); ?></h2>
	<div class="postcontainer">
		<div class="membersummary">
			<dl>
				<dt><strong><a href="<?php echo JRoute::_('index.php?option=com_kunena&view=member&m_id='.$this->post->user_id); ?>"><?php echo $this->post->name; ?></a></strong></dt>
				<dd class="memberrank"><strong>{RANK}</strong></dd>
				<dd class="memberavatar"></dd>
			</dl>
		</div>
		<div class="postcontent">
			<h3><?php echo $this->post->subject; ?></h3>

			<div class="post">
				<?php echo $this->post->message; ?>

				<!-- <p class="edit"><em>Last edited by {NAME} ({DATE})</em></p> -->
			</div>
		</div>
	</div>
</div>
