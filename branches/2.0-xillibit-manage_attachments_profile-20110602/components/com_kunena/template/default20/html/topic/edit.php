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

$this->setTitle ( $this->title );

JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.keepalive');

kimport ('kunena.bbcode.editor');
$editor = KunenaBBCodeEditor::getInstance();
$editor->initialize();

include_once (KPATH_SITE.'/lib/kunena.bbcode.js.php');
include_once (KPATH_SITE.'/lib/kunena.special.js.php');
?>
	<div id="kunena">
	<?php
		$this->displayMenu ();
		$this->displayLoginBox ();
		$this->displayBreadcrumb ();
		include 'edit_form.php';
		$this->displayFooter ();
	?>
	</div>