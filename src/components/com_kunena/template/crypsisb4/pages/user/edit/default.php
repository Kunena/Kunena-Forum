<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Pages.User
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$content = $this->execute('User/Edit');

$avatartab = $this->input->getInt('avatartab');

$document = Factory::getApplication()->getDocument();

$document->addScriptOptions('com_kunena.avatartab', json_encode($avatartab));

$this->addBreadcrumb(
	Text::_('COM_KUNENA_EDIT'),
	'index.php?option=com_kunena&view=user&layout=edit'
);

echo $content;
