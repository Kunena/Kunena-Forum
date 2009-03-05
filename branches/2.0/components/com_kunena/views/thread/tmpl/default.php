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

// Load the default stylesheet.
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
?>

<h1><?php echo $this->thread->subject; ?></h1>

<p>
	<a href="<?php echo JRoute::_('index.php?option=com_kunena&task=post.add&cat_id='.$this->thread->category_id.'&thread_id='.$this->thread->id); ?>" >
		Post a reply</a>
</p>

<div class="forum-thread">
<?php
if (!empty($this->posts)) {
	foreach ($this->posts as $post)
	{
		$this->post = $post;
		echo $this->loadTemplate('post');
	}
}
?>
</div>
