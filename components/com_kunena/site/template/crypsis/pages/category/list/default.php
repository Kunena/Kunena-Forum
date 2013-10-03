<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Pages.Search
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$content = $this->execute('Category/Index');

$this->addBreadcrumb(JText::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT'),
	'index.php?option=com_kunena&view=category&layout=list');

echo $content;
echo $this->subRequest('Statistics/Whoisonline');
echo $this->subRequest('Page/Statistics');
