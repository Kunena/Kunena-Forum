<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2011 Kunena All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined( '_JEXEC' ) or die();

// TODO: DEPRECATED

// File system paths
define('KUNENA_COMPONENT_RELPATH', 'components' .DS. KUNENA_COMPONENT_NAME);

define('KUNENA_PATH', JPATH_ROOT .DS. KUNENA_COMPONENT_RELPATH);
define('KUNENA_PATH_LIB', KUNENA_PATH .DS. 'lib');
define('KUNENA_PATH_TEMPLATE', KUNENA_PATH .DS. 'template');

// Kunena uploaded files directory
define('KUNENA_RELPATH_UPLOADED', 'media/kunena/attachments');
define('KUNENA_PATH_UPLOADED', JPATH_ROOT .DS. KUNENA_RELPATH_UPLOADED);

// Kunena uploaded avatars directory
define('KUNENA_RELPATH_AVATAR_UPLOADED', '/media/kunena/avatars');
define('KUNENA_PATH_AVATAR_UPLOADED', JPATH_ROOT . KUNENA_RELPATH_AVATAR_UPLOADED);

// Kunena live url
define('KUNENA_LIVEURL', JURI::root() . 'index.php?option=com_kunena');
define('KUNENA_LIVEURLREL', 'index.php?option=com_kunena');

// Kunena sources absolute path
define('KUNENA_DIRECTURL', JURI::root() . 'components/com_kunena/');

// Kunena uploaded files directory
define('KUNENA_LIVEUPLOADEDPATH', JURI::root() . 'media/kunena');

$kunena_config = KunenaFactory::getConfig ();

// now continue with other paths

$fb_user_template = JRequest::getString('fb_user_template', '', 'COOKIE');
$fb_user_img_template = JRequest::getString('fb_user_img_template', '', 'COOKIE');
// don't allow directory travelling
$fb_user_template = strtr($fb_user_template, '\\/', '');
$fb_user_img_template = strtr($fb_user_img_template, '\\/', '');

if (JString::strlen($fb_user_template) > 0 && file_exists(KUNENA_PATH_TEMPLATE .DS. $fb_user_template .DS. 'template.xml'))
{
    $fb_cur_template = $fb_user_template;
    }
else if (file_exists(KUNENA_PATH_TEMPLATE .DS. $kunena_config->template .DS. 'css'))
{
    $fb_cur_template = $kunena_config->template;
    }
else
{
    $fb_cur_template = 'default';
    }

if (JString::strlen($fb_user_img_template) > 0 && file_exists(KUNENA_PATH_TEMPLATE .DS. $fb_user_img_template .DS. 'images'))
{
    $fb_cur_img_template = $fb_user_img_template;
    }
else if (file_exists(KUNENA_PATH_TEMPLATE .DS. $kunena_config->template .DS. 'images'))
{
    $fb_cur_img_template = $kunena_config->template;
    }
else
{
    $fb_cur_img_template = 'default';
    }

define('KUNENA_ABSTMPLTPATH', KUNENA_PATH_TEMPLATE .DS. $fb_cur_template);
define('KUNENA_ABSCATIMAGESPATH', JPATH_ROOT.DS.'media'.DS.'kunena'.DS.$kunena_config->catimagepath); // Kunena category images absolute path
define('KUNENA_TMPLTMAINIMGURL', KUNENA_DIRECTURL . "template/{$fb_cur_img_template}/");
