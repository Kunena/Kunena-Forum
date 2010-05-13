<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die();


// Help get past php timeouts if we made it that far
// Joomla 1.5 installer can be very slow and this helps avoid timeouts
@set_time_limit(300);
$kn_maxTime = @ini_get('max_execution_time');

$maxMem = trim(@ini_get('memory_limit'));
if ($maxMem) {
	$unit = strtolower($maxMem{strlen($maxMem) - 1});
	switch($unit) {
		case 'g':
			$maxMem	*=	1024;
		case 'm':
			$maxMem	*=	1024;
		case 'k':
			$maxMem	*=	1024;
	}
	if ($maxMem < 16000000) {
		@ini_set('memory_limit', '16M');
	}
	if ($maxMem < 32000000) {
		@ini_set('memory_limit', '32M');
	}
	if ($maxMem < 48000000) {
		@ini_set('memory_limit', '48M');
	}
}
ignore_user_abort(true);

// Kunena wide defines
require_once (JPATH_ROOT  .DS. 'components' .DS. 'com_kunena' .DS. 'lib' .DS. 'kunena.defines.php');
include_once (KUNENA_PATH .DS. "class.kunena.php");

$lang = JFactory::getLanguage();
$lang->load('com_kunena', KUNENA_PATH);
$lang->load('com_kunena', KUNENA_PATH_ADMIN);

include_once(KUNENA_PATH_ADMIN_LIB .DS. 'fx.upgrade.class.php');

function com_install()
{
	$kunena_db = JFactory::getDBO();

	// Determine MySQL version from phpinfo
	$kunena_db->setQuery("SELECT VERSION() as mysql_version");
	$mysqlversion = $kunena_db->loadResult();
	check_dberror("Unable to load MySQL version.");

	//before we do anything else we want to check for minimum system requirements
	if (version_compare(phpversion(), KUNENA_MIN_PHP, ">=") && version_compare($mysqlversion, KUNENA_MIN_MYSQL, ">"))
	{
		//install & upgrade class
		$fbupgrade = new fx_Upgrade("com_kunena", "kunena.install.upgrade.xml", "fb_", "install", false);

		// Start Installation/Upgrade
		$fbupgrade->doUpgrade();

		// THIS PROCEDURE IS UNTRANSLATED!
	?>

<style>
.fbscs {
	margin: 0;
	padding: 0;
	list-style: none;
}

.fbscslist {
	list-style: none;
	padding: 5px 10px;
	margin: 3px 0;
	border: 1px solid #66CC66;
	background: #D6FEB8;
	display: block;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #333;
}

.fbscslisterror {
	list-style: none;
	padding: 5px 10px;
	margin: 3px 0;
	border: 1px solid #FF9999;
	background: #FFCCCC;
	display: block;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #333;
}
</style>

<div style="border: 1px solid #ccc; background: #FBFBFB; padding: 10px; text-align: left; margin: 10px 0;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="20%" valign="top" style="padding: 10px;"><a
			href="index.php?option=com_kunena"><img
			src="components/com_kunena/images/kunena.logo.png" alt="Kunena"
			border="0"></a></td>

		<td width="80%" valign="top" style="padding: 10px;">
		<div style="clear: both; text-align: left; padding: 0 20px;">
		<ul class="fbscs">
		<?php

			//
			// We might want to make the file copy below part of the install as well
			//

			jimport('joomla.filesystem.folder');
		    $ret = JFolder::copy(JPATH_ROOT .DS. "components" .DS. "com_kunena" .DS. "kunena.files.distribution",
		    				JPATH_ROOT .DS. "images" .DS. "fbfiles", '', true);

			if ($ret !== true)
			{
			?>

			<li class="fbscslisterror">
			<div style="border: 1px solid #FF6666; background: #FFCC99; padding: 10px; text-align: left; margin: 10px 0;">
			<img src='images/publish_x.png' align='middle' />
			Creation/permission setting of the following directories failed: <br />
			<pre>
			<?php echo JPATH_ROOT;?>/images/fbfiles/
			<?php echo JPATH_ROOT;?>/images/fbfiles/avatars
			<?php echo JPATH_ROOT;?>/images/fbfiles/avatars/gallery (you have to put avatars inside if you want to use it)
			<?php echo JPATH_ROOT;?>/images/fbfiles/category_images
			<?php echo JPATH_ROOT;?>/images/fbfiles/files
			<?php echo JPATH_ROOT;?>/images/fbfiles/images
			</pre>
			a) You can copy the contents of _kunena.files.distribution under
			components/com_kunena to your Joomla root, under images/ folder.

			<br />
			b) If you already have the contents there, but Kunena installation
			was not able to make them writable, then please do it manually.
			</div>

			</li>

			<?php
			}
		?>
		</ul>
		</div>

		<div
			style="border: 1px solid #FFCC99; background: #FFFFCC; padding: 20px; margin: 20px; clear: both;">
		<strong>I N S T A L L : <font color="green">Successful</font> </strong>
		<br />
		<br />
		<strong>php version: <font color="green"><?php echo phpversion(); ?></font> (Required &gt;= <?php echo KUNENA_MIN_PHP; ?>)</strong>
		<br />
		<strong>mysql version: <font color="green"><?php echo $mysqlversion; ?></font> (Required &gt; <?php echo KUNENA_MIN_MYSQL; ?>)</strong>
		</div>

		<?php
	}
	else
	{
		// Minimum version requirements not satisfied
		?>
<div style="border: 1px solid #ccc; background: #FBFBFB; padding: 10px; text-align: left; margin: 10px 0;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="20%" valign="top" style="padding: 10px;"><a
			href="index.php?option=com_kunena"><img
			src="components/com_kunena/images/kunena.logo.png" alt="Kunena"
			border="0"></a></td>

		<td width="80%" valign="top" style="padding: 10px;">

		<div
			style="border: 1px solid #FFCC99; background: #FFFFCC; padding: 20px; margin: 20px; clear: both;">
		<strong>I N S T A L L : <font color="red">F A I L E D - Minimum Version Requirements not satisfied</font> </strong>
		<br />
		<br />
		<strong>php version: <font color="<?php echo version_compare(phpversion(), KUNENA_MIN_PHP, '>=')?'green':'red'; ?>"><?php echo phpversion(); ?></font> (Required &gt;= <?php echo KUNENA_MIN_PHP; ?>)</strong>
		<br />
		<strong>mysql version: <font color="<?php echo version_compare($mysqlversion, KUNENA_MIN_MYSQL, '>')?'green':'red'; ?>"><?php echo $mysqlversion; ?></font> (Required &gt; <?php echo KUNENA_MIN_MYSQL; ?>)</strong>
		</div>
</table>
</div>
<?php
	}

	// Rest of footer
?>
		<div style="border: 1px solid #99CCFF; background: #D9D9FF; padding: 20px; margin: 20px; clear: both;">
		<strong>Thank you for using Kunena!</strong> <br />
		Kunena Forum Component <em>for Joomla! </em> &copy; by <a
			href="http://www.Kunena.com" target="_blank">www.Kunena.com</a>.
		All rights reserved.</div>
		</td>
	</tr>
</table>
</div>
	<?php
}
