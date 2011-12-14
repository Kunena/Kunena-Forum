<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.filter.output');
require_once KPATH_SITE . '/router.php';

// Kunena 2.0.0: Create category aliases (all that K1.7 accepts)
function kunena_upgrade_200_aliases($parent) {
	$legacyFunctions = array (
		'listcat'=>1,
		'showcat'=>1,
		'latest'=>1,
		'mylatest'=>1,
		'noreplies'=>1,
		'subscriptions'=>1,
		'favorites'=>1,
		'userposts'=>1,
		'unapproved'=>1,
		'deleted'=>1,
		'view'=>1,
		'profile'=>1,
		'myprofile'=>1,
		'userprofile'=>1,
		'fbprofile'=>1,
		'moderateuser'=>1,
		'userlist'=>1,
		'rss'=>1,
		'post'=>1,
		'report'=>1,
		'template'=>1,
		'announcement'=>1,
		'article'=>1,
		'who'=>1,
		'poll'=>1,
		'polls'=>1,
		'stats'=>1,
		'help'=>1,
		'review'=>1,
		'rules'=>1,
		'search'=>1,
		'advsearch'=>1,
		'markallcatsread'=>1,
		'markthisread'=>1,
		'subscribecat'=>1,
		'unsubscribecat'=>1,
		'karma'=>1,
		'bulkactions'=>1,
		'templatechooser'=>1,
		'json'=>1,
		'pdf'=>1,
		'entrypage'=>1,
		'thankyou'=>1,
		'fb_pdf'=>1,
	);
	$config = KunenaFactory::getConfig ();

	foreach (KunenaRouter::$views as $view=>$dummy) {
		createAlias('view', $view, $view, 1);
	}
	foreach (KunenaRouter::$layouts as $layout=>$dummy) {
		createAlias('layout', "category.{$layout}", "category/{$layout}", 1);
		createAlias('layout', "category.{$layout}", $layout, 0);
	}
	foreach ($legacyFunctions as $func=>$dummy) {
		createAlias('legacy', $func, $func, 1);
	}
	$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');
	$aliasLit = $aliasUtf = array();
	// Create SEF: id
	foreach ($categories as $category) {
		createCategoryAlias($category, $category->id);
		// Create SEF names
		$aliasUtf[$category->id] = stringURLSafe ( $category->name );
		$aliasLit[$category->id] = JFilterOutput::stringURLSafe ( $category->name );
	}
	// Sort aliases by category id (oldest ID accepts also sefcat format..
	ksort($categories);

	// Create SEF: id-name and id-Name (UTF8)
	foreach ($categories as $id=>$category) {
		$created = false;
		if ($config->sefutf8) {
			$name = $aliasUtf[$category->id];
			if (!empty($name)) $created = createCategoryAlias($category, "{$id}-{$name}", 1);
		}
		$name = $aliasLit[$category->id];
		if (!empty($name)) createCategoryAlias($category, "{$id}-{$name}", !$created);
	}
	// Create SEF: name and Name (UTF8)
	if ($config->sefcats) {
		foreach ($categories as $id=>$category) {
			$created = false;
			if ($config->sefutf8) {
				$name = $aliasUtf[$category->id];
				$keys = array_keys($aliasUtf, $name);
				if (!empty($name)) $created = createCategoryAlias($category, $name, count($keys) == 1);
			}
			$name = $aliasLit[$category->id];
			$keys = array_keys($aliasLit, $name);
			if (!empty($name)) createCategoryAlias($category, $name, !$created && count($keys) == 1);
		}
	}
	return array ('action' => '', 'name' => JText::_ ( 'COM_KUNENA_INSTALL_200_ALIASES' ), 'success' => true );
}

function createAlias($type, $item, $alias, $state=0) {
	$state = (int) $state;
	$db = JFactory::getDbo();
	$query = "INSERT INTO #__kunena_aliases (alias, type, item, state) VALUES ({$db->Quote($alias)},{$db->Quote($type)},{$db->Quote($item)},{$db->Quote($state)})";
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

function createCategoryAlias($category, $alias, $state=0) {
	$state = (int) $state;
	$db = JFactory::getDbo();
	$query = "INSERT INTO #__kunena_aliases (alias, type, item) VALUES ({$db->Quote($alias)},'catid',{$db->Quote($category->id)})";
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

function stringURLSafe($str) {
	return JString::trim ( preg_replace ( array ('/(\s|\xE3\x80\x80)+/u', '/[\$\&\+\,\/\:\;\=\?\@\'\"\<\>\#\%\{\}\|\\\^\~\[\]\`\.\(\)\*\!]/u' ), array ('-', '' ), $str ) );
}
