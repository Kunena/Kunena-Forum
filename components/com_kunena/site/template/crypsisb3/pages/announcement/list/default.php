<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Pages.Announcement
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
$this->addBreadcrumb(
	JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'),
	KunenaRoute::normalize("index.php?option=com_kunena&view=announcement&layout=list")
);

echo $this->subRequest('Announcement/List');
?>

<div class="clearfix"></div>

