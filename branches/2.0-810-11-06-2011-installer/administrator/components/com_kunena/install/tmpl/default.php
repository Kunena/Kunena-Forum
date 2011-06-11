<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
$this->document->addStyleSheet ( JURI::base(true).'/components/com_kunena/install/media/install.css' );
if ($this->go == 'next') {
	$jversion = new JVersion ();
	if ($jversion->RELEASE == '1.5') {
		JHtml::_('behavior.mootools');
	} else {
		JHtml::_('behavior.framework', true);
	}
	$this->document = JFactory::getDocument();
	$this->document->addScriptDeclaration(" // <![CDATA[
window.addEvent('domready', function() {window.location='".JRoute::_('index.php?option=com_kunena&view=install&task=run&'.JUtility::getToken() .'=1', false)."';});
// ]]>");
}
?>

<div id="container" class="clearfix fixed fluid_">
		<div class="section"> 
			<div class="full">  
				<div class="panel wizard">
			<div class="kheader"><div class="wizard-title"> <h2><?php echo JText::_('COM_KUNENA_INSTALL_WIZARD_SETUP') ?></h2> <span><?php echo JText::_('COM_KUNENA_INSTALL_WIZARD_GUIDE') ?></span> </div><div class="progress-bar"></div> </div>  <div class="content"> <div class="wizard-content"> <ul> <li> <div class="wizard-text"><div id="stepbar"><h1><?php echo JText::_('COM_KUNENA_INSTALL_STEPS') ?></h1>
			<?php $this->showSteps(); ?>
 </div></div> <div class="step-content"> <?php
if (!empty($this->requirements->fail)):
	echo $this->loadTemplate('reqfail');
elseif (!$this->step):
	echo $this->loadTemplate('start');
else:
	echo $this->loadTemplate('install');
endif;
?>  
		<div class="wizard-nav clearfix">
            <?php if ($this->step) : ?><div class="far-right">
       <div class="button1-left"><div class="next"><a onclick="<?php echo $this->getActionURL(); ?>"><?php echo $this->getAction(); ?></a>               </div></div></span> </a></div> <?php endif; ?></div>
       						</div>
       					</div>
       				</div>
       			</div>
       		</div>
       	</div>
       </div>