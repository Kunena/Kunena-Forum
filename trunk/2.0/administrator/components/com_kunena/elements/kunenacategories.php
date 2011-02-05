<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once dirname(__FILE__) . '/kunenacategorylist.php';

/** @deprecated can be still used outside Kunena (menus etc) **/
class JElementKunenaCategories extends JElementKunenaCategorylist {
	var $_name = 'KunenaCategories';
}
