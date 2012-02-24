<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();
?>

<div class="liveupdate">
	<div class="liveupdate-ftp">
		<p><?php echo JText::_('LIVEUPDATE_FTP_REQUIRED')?></p>
		<form name="adminForm" action="index.php" method="get">
			<input name="option" value="<?php echo JRequest::getCmd('option','')?>" type="hidden" />
			<input name="view" value="<?php echo JRequest::getCmd('view','liveupdate')?>" type="hidden" />
			<input name="task" value="download" type="hidden" />
			<fieldset>
				<legend><?php echo JText::_('LIVEUPDATE_FTP') ?></legend>
				
				<table class="adminform">
					<tbody>
						<tr>
							<td width="120">
								<label for="username"><?php echo JText::_('LIVEUPDATE_FTPUSERNAME'); ?></label>
							</td>
							<td>
								<input type="text" id="username" name="username" class="input_box" size="70" value="" />
							</td>
						</tr>
						<tr>
							<td width="120">
								<label for="password"><?php echo JText::_('LIVEUPDATE_FTPPASSWORD'); ?></label>
							</td>
							<td>
								<input type="password" id="password" name="password" class="input_box" size="70" value="" />
							</td>
						</tr>
					</tbody>				
				</table>
				<input type="submit" value="<?php echo JText::_('LIVEUPDATE_DOWNLOAD_AND_INSTALL'); ?>" />				
			</fieldset>
		</form>
	</div>

	<p class="liveupdate-poweredby">
		Powered by <a href="https://www.akeebabackup.com/software/akeeba-live-update.html">Akeeba Live Update</a>
	</p>

</div>
