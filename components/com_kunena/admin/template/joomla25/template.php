<?php
/**
 * Kunena Component
 * @package Kunena.Template.Joomla25
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaAdminTemplate25 {

	public function initialize() {

		JHtml::addIncludePath(JUri::root(true).'/libraries/html/html');

		// Add JavaScript Frameworks
		JHtml::_('moobootstrap.framework');

		$this->compileLess("kunena.less","bootstrap-custom.css");
		$document = JFactory::getDocument();
		$document->addStyleSheet ( JUri::root(true).'/media/kunena/css/joomla25/bootstrap-custom.css' );
		$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/joomla25/layout.css' );
		$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/joomla25/styles.css' );

		$document->addScript ( JUri::root(true).'/media/kunena/js/bootstrap/moobootstrap.js' );
		//$document->addScript ( JUri::root(true).'/media/kunena/js/tabs.js' );
	}

	public function compileLess($inputFile, $outputFile) {
		if ( !class_exists( 'lessc' ) ) {
			require_once KPATH_FRAMEWORK . '/external/lessc/lessc.php';
		}

		// Load the cache.
		$cacheDir = JPATH_CACHE.'/kunena';
		if (!is_dir($cacheDir)) JFolder::create($cacheDir);
		$cacheFile = "{$cacheDir}/kunena.bootstrap.{$inputFile}.cache";
		if ( file_exists( $cacheFile ) ) {
			$cache = unserialize( file_get_contents( $cacheFile ) );
		} else {
			$cache = KPATH_MEDIA.'/less/bootstrap/'.$inputFile;
		}
		$outputFile = KPATH_MEDIA.'/css/joomla25/'.$outputFile;

		$less = new lessc;
		//$less->setVariables($this->style_variables);
		$newCache = $less->cachedCompile( $cache );
		if ( !is_array( $cache ) || $newCache['updated'] > $cache['updated'] || !is_file($outputFile) ) {
			$cache = serialize( $newCache );
			JFile::write( $cacheFile, $cache );
			JFile::write( $outputFile, $newCache['compiled'] );
		}
	}

	public function getTemplatePaths($path = '', $fullpath = false) {
		if ($path) $path = JPath::clean("/$path");
		$array = array();
		$array[] = ($fullpath ? KPATH_ADMIN : KPATH_COMPONENT_RELATIVE).'/template/joomla25'.$path;

		return $array;
	}

	/**
	 * Renders an item in the pagination block
	 *
	 * @param   JPaginationObject  $item  The current pagination object
	 *
	 * @return  string  HTML markup for active item
	 *
	 * @since   3.0
	 */
	public function paginationItem(JPaginationObject $item)
	{
		// Special cases for "Start", "Prev", "Next", "End".
		switch ($item->text) {
			case JText::_('JLIB_HTML_START') :
				$display = JText::_('JLIB_HTML_START') ;
				break;
			case JText::_('JPREV') :
				$display = JText::_('JPREV');
				break;
			case JText::_('JNEXT') :
				$display = JText::_('JNEXT');
				break;
			case JText::_('JLIB_HTML_END') :
				$display = JText::_('JLIB_HTML_END');
				break;
			default:
				$display = $item->text;
		}
		$display = htmlspecialchars($display, ENT_COMPAT, 'UTF-8');

		// Check if the item can be clicked.
		if (!is_null($item->base)) {
			$limit = 'limitstart.value=' . (int) $item->base;

			return '<li><a href="#" title="' . $item->text . '" onclick="document.adminForm.' . $item->prefix . $limit . '; Joomla.submitform();return false;">' . $display . '</a></li>';
		}

		// Check if the item is the active (or current) page.
		if (!empty($item->active)) {
			return '<li class="active"><a>' . $display . '</a></li>';
		}

		// Doesn't match any other condition, render disabled item.
		return '<li class="disabled"><a>' . $display . '</a></li>';
	}
}
