<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->document->addStyleSheet ( JURI::base(true).'/components/com_kunena/install/media/install.css' );
if (version_compare(JVERSION, '1.6','>')) {
	// Joomla 1.6+
	JHtml::_('behavior.framework', true);
} else {
	// Joomla 1.5
	JHtml::_('behavior.mootools');
}
$this->document = JFactory::getDocument();
$this->document->addScriptDeclaration(" // <![CDATA[
window.addEvent('domready', function() {window.location='".JRoute::_($this->getUrl(), false)."';});
// ]]>");

JToolBarHelper::title('<span>Kunena '.'@kunenaversion@'.'</span> '. JText::_( 'COM_KUNENA_INSTALLER' ), 'kunena.png' );
$this->app->enqueueMessage(JText::_('COM_KUNENA_INSTALL_DO_NOT_INTERRUPT'), 'notice');
?>
<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="u">
				<div class="u">
					<div class="u"></div>
				</div>
			</div>
			<div class="n">
				<span class="step"><?php echo sprintf('Preparing installation') ?></span>

			</div>
			<div class="c">
				<div class="c">
					<div class="c"></div>
				</div>
			</div>
		</div>

		<div id="installer">
			<div class="u">
				<div class="u">
					<div class="u"></div>
				</div>
			</div>
			<div class="n">
				<?php echo sprintf('Please wait...') ?>
			</div>
			<div class="c">
				<div class="c">
					<div class="c"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="clr"></div>
