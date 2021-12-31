<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Template.Crypsisb4
 * @subpackage    Pages.User
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

$content = $this->execute('User/Attachments');

$this->addBreadcrumb($content->headerText, 'index.php?option=com_kunena&view=user&layout=attachments');

echo $content;
