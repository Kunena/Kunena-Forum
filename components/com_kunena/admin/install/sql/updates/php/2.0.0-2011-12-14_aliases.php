<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Kunena 2.0.0: Create category aliases (all that K1.7 accepts)
function kunena_200_2011_12_14_aliases($parent) {
	$config = KunenaFactory::getConfig ();

	// Create views
	foreach (KunenaRoute::$views as $view=>$dummy) {
		kCreateAlias('view', $view, $view, 1);
	}
	// Create layouts
	foreach (KunenaRoute::$layouts as $layout=>$dummy) {
		kCreateAlias('layout', "category.{$layout}", "category/{$layout}", 1);
		kCreateAlias('layout', "category.{$layout}", $layout, 0);
	}
	// Create legacy functions
	foreach (KunenaRouteLegacy::$functions as $func=>$dummy) {
		kCreateAlias('legacy', $func, $func, 1);
	}
	$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');
	$aliasLit = $aliasUtf = array();
	// Create SEF: id
	foreach ($categories as $category) {
		kCreateCategoryAlias($category, $category->id);
		// Create SEF names
		$aliasUtf[$category->id] = kStringURLSafe ( $category->name );
		$aliasLit[$category->id] = JFilterOutput::stringURLSafe ( $category->name );
	}
	// Sort aliases by category id (oldest ID accepts also sefcat format..
	ksort($categories);

	// Create SEF: id-name and id-Name (UTF8)
	foreach ($categories as $id=>$category) {
		$created = false;
		if ($config->get('sefutf8')) {
			$name = $aliasUtf[$category->id];
			if (!empty($name)) $created = kCreateCategoryAlias($category, "{$id}-{$name}", 1);
		}
		$name = $aliasLit[$category->id];
		if (!empty($name)) kCreateCategoryAlias($category, "{$id}-{$name}", !$created);
	}
	// Create SEF: name and Name (UTF8)
	if ($config->get('sefcats')) {
		foreach ($categories as $category) {
			$created = false;
			if ($config->get('sefutf8')) {
				$name = $aliasUtf[$category->id];
				$keys = array_keys($aliasUtf, $name);
				if (!empty($name)) $created = kCreateCategoryAlias($category, $name, count($keys) == 1);
			}
			$name = $aliasLit[$category->id];
			$keys = array_keys($aliasLit, $name);
			if (!empty($name)) kCreateCategoryAlias($category, $name, !$created && count($keys) == 1);
		}
	}

	return array ('action' => '', 'name' => JText::_ ( 'COM_KUNENA_INSTALL_200_ALIASES' ), 'success' => true );
}

function kCreateAlias($type, $item, $alias, $state=0) {
	$state = (int) $state;
	$db = JFactory::getDbo();
	$query = "INSERT IGNORE INTO #__kunena_aliases (alias, type, item, state) VALUES ({$db->Quote($alias)},{$db->Quote($type)},{$db->Quote($item)},{$db->Quote($state)})";
	$db->setQuery ($query);
	$success = $db->query () && $db->getAffectedRows ();
	if ($success && $state) {
		// There can be only one primary alias
		$query = "UPDATE #__kunena_aliases SET state=0 WHERE type={$db->Quote($type)} AND item={$db->Quote($item)} AND alias!={$db->Quote($alias)} AND state=1";
		$db->setQuery ($query);
		$db->query ();
	}
	return $success;
}

function kCreateCategoryAlias($category, $alias, $state=0) {
	$state = (int) $state;
	$db = JFactory::getDbo();
	$query = "INSERT IGNORE INTO #__kunena_aliases (alias, type, item) VALUES ({$db->Quote($alias)},'catid',{$db->Quote($category->id)})";
	$db->setQuery ($query);
	$success = $db->query () && $db->getAffectedRows ();
	if ($success && $state) {
		// Update primary alias into category table
		$query = "UPDATE #__kunena_categories SET alias={$db->Quote($alias)} WHERE id={$db->Quote($category->id)}";
		$db->setQuery ($query);
		$db->query ();
	}
	return $success;
}

function kStringURLSafe($str) {
	return JString::trim ( preg_replace ( array ('/(\s|\xE3\x80\x80)+/u', '/[\$\&\+\,\/\:\;\=\?\@\'\"\<\>\#\%\{\}\|\\\^\~\[\]\`\.\(\)\*\!]/u' ), array ('-', '' ), $str ) );
}
