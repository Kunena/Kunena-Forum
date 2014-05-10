<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage User
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="kblock k-profile">
	<div class="kheader">
		<h2><span class="k-name"><?php echo JText::_('COM_KUNENA_USER_PROFILE'); ?> <?php echo $this->escape($this->name); ?></span>
		<?php if (!empty($this->editlink)) echo '<span class="kheadbtn kright">'.$this->editlink.'</span>';?></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<table class = "kblocktable" id ="kprofile">
				<tr>
					<td class = "kcol-first kcol-left">
						<div id="kprofile-leftcol">
							<?php $this->displaySummary(); ?>
						</div>
					</td>
					<td class="kcol-mid kcol-right">
						<div id="kprofile-rightcol">
							<?php $this->displayTab(); ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
