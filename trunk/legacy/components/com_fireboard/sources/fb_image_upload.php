<?php
/**
* @version $Id: fb_image_upload.php 855 2008-07-16 15:35:10Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $fbConfig;
require_once(JB_ABSSOURCESPATH . 'fb_helpers.php');

function imageUploadError($msg)
{
    global $message;
    $GLOBALS['FB_rc'] = 0;
    $message = str_replace("[img]", "", $message);

    fbAlert("$msg\n" . _IMAGE_NOT_UPLOADED);
}

$GLOBALS['FB_rc'] = 1; //reset return code
$filename = split("\.", $_FILES['attachimage']['name']);
//some transaltions for readability
//numExtensions= people tend to upload malicious files using mutliple extensions like: virus.txt.vbs; we'll want to have the last extension to validate against..
$numExtensions = (count($filename)) - 1;
//Translate all invalid characters
$imageName = preg_replace("/[^0-9a-zA-Z_]/", "_", $filename[0]);
// get the final extension
$imageExt = $filename[$numExtensions];
// create the new filename
$newFileName = $imageName . '.' . $imageExt;
// Get the Filesize
$imageSize = $_FILES['attachimage']['size'];

//Enforce it is a new file
if (file_exists(FB_ABSUPLOADEDPATH. "/images/$newFileName")) {
    $newFileName = $imageName . '-' . md5(microtime()) . "." . $imageExt;
}

if ($GLOBALS['FB_rc'])
{
    //Filename + proper path
    $imageLocation = FB_ABSUPLOADEDPATH. "/images/$newFileName";

    // Check for empty filename
    if (empty($_FILES['attachimage']['name'])) {
        imageUploadError(_IMAGE_ERROR_EMPTY);
    }

    // Check for allowed file type (jpeg, gif, png)
    if (!($imgtype = FB_check_image_type($imageExt))) {
        imageUploadError(_IMAGE_ERROR_TYPE);
    }

    // Check filesize
    $maxImgSize = $fbConfig->imagesize * 1024;

    if ($imageSize > $maxImgSize) {
        imageUploadError(_IMAGE_ERROR_SIZE . " (" . $fbConfig->imagesize . "kb)");
    }

    list($width, $height) = @getimagesize($_FILES['attachimage']['tmp_name']);

    // Check image width
    if ($width > $fbConfig->imagewidth) {
        imageUploadError(_IMAGE_ERROR_WIDTH . " (" . $fbConfig->imagewidth . " pixels");
    }

    // Check image height
    if ($height > $fbConfig->imageheight) {
        imageUploadError(_IMAGE_ERROR_HEIGHT . " (" . $fbConfig->imageheight . " pixels");
    }
}

if ($GLOBALS['FB_rc'])
{
    // file is OK, move it to the proper location
    move_uploaded_file($_FILES['attachimage']['tmp_name'], $imageLocation);
    @chmod($imageLocation, 0777);
}

if ($GLOBALS['FB_rc'])
{
    // echo '<span class="contentheading">'._IMAGE_UPLOADED."...</span>";
    if ($width < '100') {
        $code = '[img]' . FB_LIVEUPLOADEDPATH. '/images/' . $newFileName . '[/img]';
    }
    else {
        $code = '[img size=' . $width . ']' . FB_LIVEUPLOADEDPATH. '/images/' . $newFileName . '[/img]';
    }

    if (preg_match("/\[img\]/si", $message)) {
        $message = str_replace("[img]", $code, $message);
    }
    else {
        $message = $message . ' ' . $code;
    }
}
?>