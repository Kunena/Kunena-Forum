<?php
/**
 * Kunena Component
 * @package Kunena.Administrator.Template.Joomla30
 * @subpackage Layouts.Pagination
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
$dataTarget = (isset($this->dataTarget)) ? " data-target=\"{$this->dataTarget}\"" : '';
$dataForm = (isset($this->dataForm)) ? " data-form=\"{$this->dataForm}\"" : '';
$class = (isset($this->class)) ? " {$this->class}" : 'btn-success';
?>
<a href="<?php echo JRoute::_($this->uri); ?>" data-toggle="ajaxmodal"<?php echo $dataTarget.$dataForm; ?>
   class="btn btn-small <?php echo $class; ?>">
	<i class="icon-apply" title="<?php echo $this->title; ?>"></i>
	<?php echo $this->title; ?>
</a>
