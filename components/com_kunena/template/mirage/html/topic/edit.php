<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.keepalive');

$editor = KunenaBbcodeEditor::getInstance();
$editor->initialize();

include_once (KPATH_SITE.'/lib/kunena.bbcode.js.php');
include_once (KPATH_SITE.'/lib/kunena.special.js.php');

$this->displayTemplateFile('topic', 'edit', 'form');
