<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */
defined('_JEXEC') or die;

?>
<table
	style="border: 1px solid #FFCC99; background: #FFFFCC; padding: 5px; margin: 0 0 20px 20px; clear: both;">
	<tr>
		<th style="text-align: center;"><?php echo $this->txt_action; ?></th>
	</tr>
	<tr>
		<td style="text-align: center;"><input type="button"
			onclick="window.location='<?php echo $this->link; ?>'"
			value="<?php echo $this->txt_install; ?>"
			style="padding: 10px; text-align: center; font-weight: bold; background: #aadd44; border: solid 1px #669900; cursor: pointer;" />
		</td>
	</tr>
</table>
