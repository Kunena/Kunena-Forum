<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

$attachment = $this->attachment;

if ($attachment->isImage())
{
	echo $this->render('image');
}
elseif ($attachment->isAudio())
{
	echo $this->render('audio');
}
elseif ($attachment->isVideo())
{
	echo $this->render('video');
}
elseif ($attachment->isPdf())
{
	echo $this->render('pdf');
}
else
{
	echo $this->render('file');
}
