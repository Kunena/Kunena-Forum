<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

jimport('joomla.application.component.view');

/**
 * The HTML Kunena base view
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaView extends JView
{
	/**
	 * Object Constructor
	 *
	 * @param	array	$config	View configuration array
	 * @return	void
	 * @since	1.0
	 */
	function __construct($config=array())
	{
		parent::__construct($config);

		// add the Kunena HTML helpers to the JHTML path array
		JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @access	public
	 * @param string $tpl The name of the template source file ...
	 * automatically searches the template paths and compiles as needed.
	 * @return string The output of the the template script.
	 */
	function loadCommonTemplate($file = null)
	{
		global $mainframe, $option;

		// clear prior output
		$this->_output = null;

		// clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);

		// load the template script
		$filetofind = '_common/tmpl/'.$file.'.php';
		$this->_template = false;

		// make sure the template exists
		$theme = $mainframe->getTemplate();
		if (file_exists(JPATH_BASE.'/templates/'.$theme.'/html/'.$option.'/'.$filetofind)) {
			$this->_template = JPATH_BASE.'/templates/'.$theme.'/html/'.$option.'/'.$filetofind;
		}
		elseif (file_exists(JPATH_COMPONENT.'/views/'.$filetofind)) {
			$this->_template = JPATH_COMPONENT.'/views/'.$filetofind;
		}

		if ($this->_template != false)
		{
			// unset so as not to introduce into template scope
			unset($file);

			// never allow a 'this' property
			if (isset($this->this)) {
				unset($this->this);
			}

			// start capturing output into a buffer
			ob_start();

			// include the requested template filename in the local scope
			include $this->_template;

			// done with the requested template; get the buffer and clear it
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else {
			return JError::raiseError(500, 'Layout "'.$file.'" not found');
		}
	}
}