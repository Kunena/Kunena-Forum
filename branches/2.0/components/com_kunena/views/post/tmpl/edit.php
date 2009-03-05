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

// Include the component HTML helpers.
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');

// Load the default stylesheet.
JHTML::stylesheet('default.css', 'components/com_kunena/media/css/');

// Get the form fields.
$fields	= $this->form->getFields();
?>

<form action="<?php echo JRoute::_('index.php?option=com_kunena');?>" method="post" name="adminForm" id="post-form">

<ol class="fields">
<?php if (!$this->state->get('user.id')) : ?>
	<li>
		<?php echo $fields['name']->label,'<br />',$fields['name']->field; ?>
	</li>
	<li>
		<?php echo $fields['email']->label,'<br />',$fields['email']->field; ?>
	</li>
<?php endif; ?>

	<li>
		<?php echo $fields['icon']->label,'<br />',$fields['icon']->field; ?>
	</li>
	<li>
		<?php echo $fields['subject']->label,'<br />',$fields['subject']->field; ?>
	</li>
	<li>
		<?php echo $fields['message']->label,'<br />',$fields['message']->field; ?>
	</li>
</ol>

	<input type="hidden" name="task" value="post.save" />
	<input type="submit" />
	<?php echo JHTML::_('form.token'); ?>
</form>
