<?php
/**
 * @version		$Id: view.html.php 3901 2010-11-15 14:14:02Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

/**
 * Search View
 */
class KunenaViewSearch extends KunenaView {
	function displayDefault($tpl = null) {
		$this->me = KunenaFactory::getUser();

		$searchdatelist	= array();
		$searchdatelist[] 	= JHTML::_('select.option',  'lastvisit', JText::_('COM_KUNENA_SEARCH_DATE_LASTVISIT') );
		$searchdatelist[] 	= JHTML::_('select.option',  '1', JText::_('COM_KUNENA_SEARCH_DATE_YESTERDAY') );
		$searchdatelist[] 	= JHTML::_('select.option',  '7', JText::_('COM_KUNENA_SEARCH_DATE_WEEK') );
		$searchdatelist[] 	= JHTML::_('select.option',  '14',  JText::_('COM_KUNENA_SEARCH_DATE_2WEEKS') );
		$searchdatelist[] 	= JHTML::_('select.option',  '30', JText::_('COM_KUNENA_SEARCH_DATE_MONTH') );
		$searchdatelist[] 	= JHTML::_('select.option',  '90', JText::_('COM_KUNENA_SEARCH_DATE_3MONTHS') );
		$searchdatelist[] 	= JHTML::_('select.option',  '180', JText::_('COM_KUNENA_SEARCH_DATE_6MONTHS') );
		$searchdatelist[] 	= JHTML::_('select.option',  '365', JText::_('COM_KUNENA_SEARCH_DATE_YEAR') );
		$searchdatelist[] 	= JHTML::_('select.option',  'all', JText::_('COM_KUNENA_SEARCH_DATE_ANY') );
		$this->searchdatelist   = JHTML::_('select.genericlist',  $searchdatelist, 'searchdate', 'class="ks"', 'value', 'text',isset($this->params['searchdate']) ? $this->params['searchdate'] : ''  );

		$beforeafterlist	= array();
		$beforeafterlist[] 	= JHTML::_('select.option',  'after', JText::_('COM_KUNENA_SEARCH_DATE_NEWER') );
		$beforeafterlist[] 	= JHTML::_('select.option',  'before', JText::_('COM_KUNENA_SEARCH_DATE_OLDER') );
		$this->beforeafterlist= JHTML::_('select.genericlist',  $beforeafterlist, 'beforeafter', 'class="ks"', 'value', 'text',isset($this->params['beforeafter']) ? $this->params['beforeafter'] : '' );

		$sortbylist	= array();
		$sortbylist[] 	= JHTML::_('select.option',  'title', JText::_('COM_KUNENA_SEARCH_SORTBY_TITLE') );
		//$sortbylist[] 	= JHTML::_('select.option',  'replycount', JText::_('COM_KUNENA_SEARCH_SORTBY_POSTS') );
		$sortbylist[] 	= JHTML::_('select.option',  'views', JText::_('COM_KUNENA_SEARCH_SORTBY_VIEWS') );
		//$sortbylist[] 	= JHTML::_('select.option',  'threadstart', JText::_('COM_KUNENA_SEARCH_SORTBY_START') );
		$sortbylist[] 	= JHTML::_('select.option',  'lastpost', JText::_('COM_KUNENA_SEARCH_SORTBY_POST') );
		//$sortbylist[] 	= JHTML::_('select.option',  'postusername', JText::_('COM_KUNENA_SEARCH_SORTBY_USER') );
		$sortbylist[] 	= JHTML::_('select.option',  'forum', JText::_('COM_KUNENA_SEARCH_SORTBY_FORUM') );
		$this->sortbylist= JHTML::_('select.genericlist',  $sortbylist, 'sortby', 'class="ks"', 'value', 'text',isset($this->params['sortby']) ? $this->params['sortby'] : '' );

		$limitlist	= array();
		$limitlist[] 	= JHTML::_('select.option',  '5', JText::_('COM_KUNENA_SEARCH_LIMIT5') );
		$limitlist[] 	= JHTML::_('select.option',  '10', JText::_('COM_KUNENA_SEARCH_LIMIT10') );
		$limitlist[] 	= JHTML::_('select.option',  '15', JText::_('COM_KUNENA_SEARCH_LIMIT15') );
		$limitlist[] 	= JHTML::_('select.option',  '20', JText::_('COM_KUNENA_SEARCH_LIMIT20') );
		$this->limitlist= JHTML::_('select.genericlist',  $limitlist, 'limit', 'class="ks"', 'value', 'text'/*,$this->limit*/ );

		//category select list
		$options = array ();
		$options [] = JHTML::_ ( 'select.option', '0', JText::_('COM_KUNENA_SEARCH_SEARCHIN_ALLCATS') );

		$cat_params = array ('sections'=>true);
		$selected = explode ( ',', isset($this->params ['catids']) ? $this->params ['catids'] : '' );
		$this->categorylist = JHTML::_('kunenaforum.categorylist', 'catids[]', 0, $options, $cat_params, 'class="inputbox" size="8" multiple="multiple"', 'value', 'text', $selected);


		parent::display ();
	}

	function displayResults($tpl = null) {
		$this->results = $this->get('Results');
		$this->get('Error');
		$app = JFactory::getApplication ();
		$this->searchword = $app->getUserState('com_kunena.searchword');
		$this->params = $app->getUserState('com_kunena.search');
		$this->total = $this->get('total');

		// FIXME: need to show error

		$searchlist = $this->get('searchstrings');
		foreach ( $this->results as $i => $result ) {
			// Clean up subject
			$ressubject = KunenaHtmlParser::parseText ($result->subject);
			// Strip smiles and bbcode out of search results; they look ugly
			$resmessage = KunenaHtmlParser::parseBBCode ($result->message);

			foreach ( $searchlist as $searchword ) {
				if (empty ( $searchword ))
					continue;
				$ressubject = preg_replace ( "/" . preg_quote ( $searchword, '/' ) . "/iu", '<span  class="searchword" >' . $searchword . '</span>', $ressubject );
				// FIXME: enable highlighting, but only after we can be sure that we do not break html
				//$resmessage = preg_replace ( "/" . preg_quote ( $searchword, '/' ) . "/iu", '<span  class="searchword" >' . $searchword . '</span>', $resmessage );
			}
			$this->results [$i]->htmlsubject = $ressubject;
			$this->results [$i]->htmlmessage = $resmessage;
		}


    $this->display($tpl);
	}

	function getPagination($function, $maxpages) {
	 /*$limit = $this->state->get ( 'list.limit' );
		$page = floor ( $this->state->get ( 'list.start' ) / $limit ) + 1;
		$totalpages = max(1, floor ( ($this->total-1) / $limit ) + 1);
    $q = $this->state->get('com_kunena.searchword');  */

		if ($page == 0)
			$page ++;
		$startpage = ($page - floor ( $maxpages / 2 ) < 1) ? 1 : $page - floor ( $maxpages / 2 );
		$endpage = $startpage + $maxpages;
		if ($endpage > $totalpages) {
			$startpage = ($totalpages - $maxpages) < 1 ? 1 : $totalpages - $maxpages;
			$endpage = $totalpages;
		}

		$output = '<ul class="kpagination">';
		$output .= '<li class="page">' . JText::_('COM_KUNENA_PAGE') . '</li>';

		if ($startpage > 1) {
			if ($endpage < $totalpages)
				$endpage --;
			$output .= '<li>' . CKunenaLink::GetSearchLink ( $function, $q, 0, $limit, 1, '', $rel = 'nofollow' ) . '</li>';

			if ($startpage > 2) {
				$output .= '<li class="more">...</li>';
			}
		}

		for($i = $startpage; $i <= $endpage && $i <= $totalpages; $i ++) {
			if ($page == $i) {
				$output .= '<li class="active">' . $i . '</li>';
			} else {
				$output .= '<li>' . CKunenaLink::GetSearchLink ( $function, $q, ($i - 1) * $limit, $limit, $i, '', $rel = 'nofollow' ) . '</li>';
			}
		}

		if ($endpage < $totalpages) {
			if ($endpage < $totalpages - 1) {
				$output .= '<li class="more">...</li>';
			}

			$output .= '<li>' . CKunenaLink::GetSearchLink ( $function, $q, ($totalpages - 1) * $limit, $limit, $totalpages, '', $rel = 'nofollow' ) . '</li>';
		}

		$output .= '</ul>';
		return $output;
	}

}