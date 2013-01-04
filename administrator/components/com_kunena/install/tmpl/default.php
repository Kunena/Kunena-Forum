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

$this->document->addStyleSheet ( JUri::base(true).'/components/com_kunena/install/media/install.css' );
if ($this->go == 'next') {
	JHtml::_('behavior.framework', true);

	$this->document = JFactory::getDocument();

	$locationUrl = json_encode(JRoute::_("index.php?option=com_kunena&view=install&task=run&n={$this->cnt}&".JSession::getFormToken() .'=1', false));
	$this->document->addScriptDeclaration("// <![CDATA[
window.addEvent('domready', function() {window.location={$locationUrl};});
// ]]>");
}
?>
<div id="stepbar">
	<div class="u">
		<div class="u">
			<div class="u"></div>
		</div>
	</div>
	<div class="n">
			<h1><?php echo JText::_('COM_KUNENA_INSTALL_STEPS') ?></h1>
			<?php $this->showSteps(); ?>
	</div>
	<div class="c">
		<div class="c">
			<div class="c"></div>
		</div>
	</div>
</div>

<div id="right">
	<div id="rightpad">
		<div id="step">
			<div class="u">
				<div class="u">
					<div class="u"></div>
				</div>
			</div>
			<div class="n">
<?php if ($this->step) :?>
				<div class="far-right">

							<div class="button1-left"><div class="next"><a onclick="<?php echo $this->getActionURL(); ?>"><?php echo $this->getAction(); ?></a></div></div>

				</div>
<?php endif; ?>
				<span class="step"><?php echo $this->steps[$this->step]['menu']; ?></span>

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
<?php
if (!$this->step):
	echo $this->loadTemplate('start');
else:
	echo $this->loadTemplate('install');
endif;
?>
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
