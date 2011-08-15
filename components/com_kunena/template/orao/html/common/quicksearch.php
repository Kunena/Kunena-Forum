<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
		$q = JRequest::getVar ( 'q', '' ); // Search words
		$searchuser = JRequest::getVar ( 'searchuser', '' ); // Search user
		// Backwards compability for old templates
		if (empty ( $q ) && isset ( $_REQUEST ['searchword'] )) {
			$q = JRequest::getVar ( 'searchword', '' );
		}
		if (empty ( $searchuser ) && isset ( $_REQUEST ['searchword'] )) {
			$searchuser = JRequest::getVar ( 'searchword', '' );
		}
?>
<div id="mb_search" style="display:none;">
	<div class="tk-mb-header-search" style="display:none; margin-bottom:10px;">
		<span class="tk-mb-first"><?php echo JText::_('COM_KUNENA_TEMPLATE_SEARCH_IN_FORUM'); ?></span>
	</div>
	<form action="<?php echo CKunenaLink::GetSearchURL('advsearch'); ?>" method="post" id="searchform" name="adminForm">
		<input id="keywords" class="tk-searchbox" type="text" name="q" value="<?php echo $this->escape($q); ?>" />
		<?php /*?><input id="kusername" class="tk-searchbox" type="text" name="searchuser" value="<?php echo $this->escape($searchuser); ?>" /><?php */?>
		<input class="tk-search-button" type="submit" value="<?php echo JText::_('COM_KUNENA_SEARCH_SEND'); ?>" />
	</form>
	<div>
		<a class="tk-mb-advsearchlink" href="<?php JURI::base() ?>index.php?option=com_kunena&view=search"><?php echo JText::_('COM_KUNENA_SEARCH_ADVSEARCH'); ?></a>
	</div>
</div>