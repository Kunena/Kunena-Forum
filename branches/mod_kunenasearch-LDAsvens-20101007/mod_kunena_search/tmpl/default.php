<?php
/**
 * @version $Id$
 * KunenaSearch Module
 * 
 * @package	Kunena Search
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */

defined('_JEXEC') or die('Restricted access');
JHtml::stylesheet('kunena_search.css', 'modules/mod_kunena_search/');	
?>

<form action="<?php echo CKunenaLink::GetSearchURL('advsearch'); ?>" method="post" id="ksearchform" name="adminForm">
	<div class="ksearch<?php echo $moduleclass_sfx ?>">
		<fieldset class="kfieldset-search">
			<legend class="klegend-search"><?php echo JText::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD'); ?></legend>
			<label class="ksearchlabel" for="kkeywords"><?php echo JText::_('COM_KUNENA_SEARCH_KEYWORDS'); ?>:</label>
			<input id="kkeywords" type="text" class="ks input" name="q" size="<?php echo $ksearch_width ?>" 
			  value="<?php echo $ksearch_txt ?>" 
			  onblur="if(this.value=='') this.value='<?php echo $ksearch_txt ?>';" 
			  onfocus="if(this.value=='<?php echo $ksearch_txt ?>') this.value='';"  />
			<input id="kkeywordfilter" type="hidden" name="titleonly" value="0" />
				<?php
					if ($ksearch_button==1){
						if ($ksearch_button_pos=='bottom') echo '<br />';
					echo "<input type=\"submit\" value=\"{$ksearch_button_txt}\" class=\"kbutton{$moduleclass_sfx}\" />";
					};
				?>
		</fieldset>
	</div>
</form>