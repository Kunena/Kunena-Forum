<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Pages.Topics
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$content = $this->execute('Topic/List/Recent')
	->setLayout('recent');

$this->addBreadcrumb(
	$content->headerText,
	'index.php?option=com_kunena&view=topics&layout=default'
);

echo $content;
echo $this->subRequest('Widget/WhoIsOnline');
echo $this->subRequest('Widget/Statistics');
