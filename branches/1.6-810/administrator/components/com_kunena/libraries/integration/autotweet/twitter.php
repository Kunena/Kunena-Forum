<?php
/**
 *  This class is a facade for the posting function of AutoTweet.
 *  It should be used as an API for external components only.
 *
 * @version	1.0
 * @author	Ulli Storck
 * @license	GPL 2.0
 *
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_autotweet' . DS . 'helpers' . DS . 'autotweetposthelper.php');

/**
* Instructions to enhance AutoTweet  for external component:
*
* There are two ways to use AutoTweet with your own component.
* 1. Write an extension plugin for AutoTweet.
* 2. Post (and log) a message with the API function  AutotweetAPI::postMessage()
*
* 1. Write an extension plugin for AutoTweet: Your plugin must derive form the base class  plgAutotweetBase
* (see /administrator/components/com_autotweet/helpers/autotweetbase.php)  and must implement the function
* getData to return the data for the post. Also it must trigger the function postStatusMessage to queue the message
* for posting. See my own AutoTweet extension plugins as example.
*
* 2. Post (and log) a message with the API function  AutotweetAPI::postMessage(): With this function you can send
* a message over AutoTweet without the need to write a plugin. So you can call this function directly from your component.
* This functions send the message directly (The queue is not used). 
*/


class AutotweetAPI
{
	// static class
	protected function __construct()
	{
		// nothing to do
	}

	
	/**
	* Function to post a message from an external component. This message is NOT queued.
	*
	* $article_id	id of article, event, forum post, ...
	* $data = array (
	*	'title'		=> 'Title of the article, event, forum post, ...',
	*	'text'		=> 'Text for the message.',
	*	'hashtags'	=> 'hashtags (They are inserted automatically by AutoTweet for channels supporting hashtags.',
	*	'url'		=> 'The complete routed URL of the article. (not shortened)'
	*	);
	* $source		source for post (plugin, component, ...)
	* $extension_autopublish_allowed	true, when autopublish is allowed for plugin, component, ...
	*
	*/
	public static function postMessage($article_id, $data, $source, $extension_autopublish_allowed = true)
	{
		$helper =& AutotweetPostHelper::getInstance();
		return $helper->sendMessage($article_id, $data, $source, $extension_autopublish_allowed);
	}
	
}
	
?>
