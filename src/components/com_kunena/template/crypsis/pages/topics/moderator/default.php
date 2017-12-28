<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Pages.Topics
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$content = $this->execute('Topic/List/Moderator')
	->setLayout('moderator');

$this->addBreadcrumb(
	$content->headerText,
	'index.php?option=com_kunena&view=topics&layout=moderator'
);

echo $content;
