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

// Include the JXtended HTML helpers.
JHTML::addIncludePath(JPATH_ROOT.'/plugins/system/jxtended/html/html');

// Load the tooltip and form vaildation behaviors.
JHTML::_('behavior.tooltip');
JHTML::script('dashboard.js', 'administrator/components/com_kunena/media/js/');

// Load the gallery default stylesheet.
JHTML::stylesheet('default.css', 'administrator/components/com_kunena/media/css/');

// Build the toolbar.
$this->buildDefaultToolBar();
?>
<form name="adminForm" action="<?php echo JRoute::_('index.php?option=com_kunena'); ?>" method="post">
	<input type="hidden" name="task" value="" />
</form>

<div id="extension-data">
	<h1>
		<img src="components/com_kunena/media/images/logo-300-fb.png" alt="JXtended Kunena" />
	</h1>

	<h2>
		<?php echo JText::_('FB Verison History');?>
	</h2>
	<div>
		<?php if (!$this->get('init_acl')) : ?>
		<p class="upgrade">
			<a href="index.php?option=com_kunena&amp;task=setup.initacl">
				<?php echo JText::_('FB Init Access Control');?></a>
		</p>
		<?php endif; ?>
		<dl>
<?php foreach ($this->versions as $version) : ?>
			<dt><strong><?php echo $version->version;?></strong></dt>
			<dd>
				<?php echo JHTML::date($version->installed_date); ?>
				<br /><?php echo nl2br($version->log); ?>
			</dd>
<?php endforeach; ?>
		</dl>
<?php if (!empty($this->upgrades)) : ?>
		<p class="upgrade"><a href="index.php?option=com_kunena&amp;task=setup.upgrade">
			<?php echo JText::_('FB Upgrade Database');?></a></p>
<?php endif; ?>
	</div>
</div>
