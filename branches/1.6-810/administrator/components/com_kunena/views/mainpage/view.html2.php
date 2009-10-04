<?php



// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class ViewMainpage
{	
    /**
     * ViewMainpage::mainpage()
     * 
     * @return null
     */
     
    function mainpage()
    {
    	//
    	$uri =& JURI::getInstance();
    	//
		require_once(JPATH_COMPONENT.DS.'models'.DS.'option.php');
		$option_model = new ModelOption();
		$modal_settings = (int) $option_model->getOption('modal_settings');
		$kunena_news = (int) $option_model->getOption('kunena_news');
    	//
		require_once(JPATH_COMPONENT.DS.'views'.DS.'navigation.view.php');
		require_once(JPATH_COMPONENT.DS.'views'.DS.'tmpl'.DS.'mainpage.html.php');
    }
    
 
    
    /**
     * ViewMainpage::loadKunenaRSS()
     * 
     * @return null
     */
     
    function loadKunenaRSS()
    {
    	//
    	require_once(JPATH_COMPONENT.DS.'models'.DS.'option.php');
    	$option_model = new ModelOption();
    	$modal_news = (int) $option_model->getOption('modal_news');
    	//
    	$uri =& JURI::getInstance();
    	//
		$rssNews = '';
		//
		if(ini_get('allow_url_fopen'))
		{
			$rssNews = file_get_contents('http://kunena.com/index.php?format=feed&type=rss');
			$xml = & JFactory::getXMLParser('Simple');
			$rssNews = str_replace(' xmlns:atom="http://www.w3.org/2005/Atom"', '',$rssNews);
			$rssNews = str_replace('<atom:link rel="self" type="application/atom+xml" href="http://www.kunena.com/index.php?format=feed&amp;type=atom" />', '', $rssNews);
			
			if($xml->loadString($rssNews))
			{
				$rssNews = '';
				
				for($i = 0; isset($xml->document->channel[0]->item[$i]) && $i < 3; $i++)
				{
					$rssNews .= '<li><h3><a href="'.$uri->root().'administrator/index.php?option=com_kunena&'.(($modal_news) ? 'tmpl=component&' : '').'c=news&task=view_news&num='.$i.'" '.(($modal_news) ? ' class="modal" rel="{handler: \'iframe\', size: {x: 800, y: 400}}"' : '').'>'.$xml->document->channel[0]->item[$i]->title[0]->data().'</a></h3><small>'.substr($xml->document->channel[0]->item[$i]->pubDate[0]->data(), 0 , -6).'</small><p>'.substr(strip_tags($xml->document->channel[0]->item[$i]->description[0]->data()), 0, 115).' <a href="'.$uri->root().'administrator/index.php?option=com_kunena&'.(($modal_news) ? 'tmpl=component&' : '').'c=news&task=view_news&num='.$i.'" '.(($modal_news) ? ' class="modal" rel="{handler: \'iframe\', size: {x: 800, y: 400}}"' : '').'>'.JText::_('READ_MORE').'...</a></p></li>';
				}	
			}
			else
			{
				$rssNews = '<li>'.JText::_('NEWS_PARSE_ERROR').'</li>';
			}
		}
		else
		{
			$rssNews = '<li>'.JText::_('NEWS_NO_CONFIGURATION').'</li>';
		}
		
		return $rssNews;
    }
}
