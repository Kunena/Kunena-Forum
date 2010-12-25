<?php
/**
* @package		Kunena Search
* @copyright	(C) 2010 Kunena Project. All rights reserved.
* @license		GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
$doc->addStyleSheet( JURI::root().'modules/mod_kunenasearch/tmpl/css/kunenasearch.css' );
?>

<form action="<?php echo CKunenaLink::GetSearchURL('advsearch'); ?>" method="post" id="ksearch-form" name="adminForm">
	<div class="ksearch<?php echo $this->ksearch_moduleclass_sfx; ?>">
		<fieldset class="ksearch-fieldset">
			<legend class="ksearch-legend"><?php echo JText::_('MOD_KUNENASEARCH_SEARCHBY_KEYWORD'); ?></legend>
			<label class="ksearch-label" for="ksearch-keywords"><?php echo JText::_('MOD_KUNENASEARCH_SEARCH_KEYWORDS'); ?>:</label>
			<input id="ksearch-keywords" type="text" class="ks kinput" name="q" size="<?php echo $this->ksearch_width; ?>" value="<?php echo $this->ksearch_txt; ?>" onblur="if(this.value=='') this.value='<?php echo $this->ksearch_txt; ?>';" onfocus="if(this.value=='<?php echo $this->ksearch_txt; ?>') this.value='';"  />
			<input id="ksearch-keywordfilter" type="hidden" name="titleonly" value="0" />
				<?php
					if ($this->ksearch_button==1){
						if ($this->ksearch_button_pos=='bottom'){
						echo '<br />';
						}
					echo '<input type="submit" value="'.$this->ksearch_button_txt.'" class="kbutton'.$this->ksearch_moduleclass_sfx.'" />';
					};
				?>
		</fieldset>
	</div>
</form>