<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage User
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

if ($this->me->exists()) {
	$this->document->addScriptDeclaration( "// <![CDATA[
document.addEvent('domready', function() {
	// Attach auto completer to the following ids:
	new Autocompleter.Request.JSON('kusersearch', '".KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list&format=raw')."', { 'postVar': 'search' });
});
// ]]>");
}
?>
<div class="search-user">
		<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list') ?>" name="usrlform" method="post">
			<input type="hidden" name="view" value="user" />
			<?php echo JHTML::_( 'form.token' ); ?>

			<input id="kusersearch" type="text" name="search" class="inputbox"
				value="<?php echo $this->escape($this->state->get('list.search', JText::_('COM_KUNENA_USRL_SEARCH'))); ?>" onblur="if(this.value=='') this.value='<?php echo $this->escape(JText::_('COM_KUNENA_USRL_SEARCH')); ?>';" onfocus="if(this.value=='<?php echo $this->escape(JText::_('COM_KUNENA_USRL_SEARCH')); ?>') this.value='';" />
			<input type="image" src="<?php echo $this->ktemplate->getImagePath('usl_search_icon.png') ?>" alt="<?php echo JText::_('COM_KUNENA_USRL_SEARCH'); ?>" style="border: 0px;" />
		</form>
</div>

<div class="box-module">
	<div class="box-wrapper box-color box-border box-border_radius box-shadow">   
		<div class="userlist block">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<a href="#" class="ksection-headericon"><?php echo $this->getImage('icon-whosonline-sm.png') ?></a>
					<h2 class="header"><a href="#" rel="ksection-detailsbox"><?php echo JText::_('COM_KUNENA_USRL_USERLIST') ?></a></h2>
				</div>
			</div>
			<div class="dettailsbox-wrapper box-full">
				<div class="kuserlist-items">
					<?php foreach ($this->users as $user) { $this->displayUserRow($user); } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>