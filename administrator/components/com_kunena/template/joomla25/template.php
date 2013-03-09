<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaAdminTemplate25 {

	public function initialize() {
		// Include MooTools framework
		JHtml::_('behavior.framework', true);

		$this->compileLess("kunena.less","bootstrap-custom.css");
		$document = JFactory::getDocument();
		$document->addStyleSheet ( JUri::root(true).'/media/kunena/css/joomla25/bootstrap-custom.css' );
		$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/joomla25/layout.css' );
		$document->addStyleSheet ( JUri::base(true).'/components/com_kunena/media/css/joomla25/styles.css' );
		$document->addScript ( JUri::base(true).'/components/com_kunena/media/kunena/js/tabs.js' );
	}

	public function compileLess($inputFile, $outputFile) {
		if ( !class_exists( 'lessc' ) ) {
			require_once KPATH_ADMIN . '/libraries/external/lessc/lessc.php';
		}

		// Load the cache.
		$cacheFile = JPATH_CACHE."/kunena.bootstrap.{$inputFile}.cache";
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

}