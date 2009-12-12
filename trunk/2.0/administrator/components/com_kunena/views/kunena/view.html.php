<?php
/**
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

/**
 * Main backend view for Kunena
 */
class KunenaViewKunena extends JView
{
	/**
	 * The default method that will display the output of this view which is called by
	 * Joomla
	 *
	 * @param	string template	Template file name
	 **/
	function display( $tpl = null )
	{
		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');
		jimport('joomla.html.pane');
		$pane	=& JPane::getInstance('sliders');

		$this->assignRef( 'pane'		, $pane );
		parent::display( $tpl );
	}

	/**
	 * Private method to set the toolbar for this view
	 *
	 * @access private
	 *
	 * @return null
	 **/
	function setToolBar()
	{
		// Set the titlebar text
		JToolBarHelper::title( JText::_( 'Kunena' ), 'kunena' );
	}

	function addIcon( $image , $url , $text , $newWindow = false )
	{
		$lang		=& JFactory::getLanguage();

		$newWindow	= ( $newWindow ) ? ' target="_blank"' : '';
?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $url; ?>"<?php echo $newWindow; ?>>
					<?php echo JHTML::_('image', 'administrator/components/com_kunena/media/icons/' . $image , $text, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
<?php
	}
}