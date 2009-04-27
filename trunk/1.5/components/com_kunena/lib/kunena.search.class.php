<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

DEFINE('KUNENA_URL_LIST_SEPARATOR', ' ');

class CKunenaSearch
{
    /** search results **/
    var $arr_kunena_results = array();
    /** function **/
    var $func;
    /** search words **/
    var $searchword;
    /** search strings **/
    var $arr_kunena_searchstrings;
    /** search username **/
    var $str_kunena_username;
    /** error number **/
    var $int_kunena_errornr;
    /** error msg **/
    var $str_kunena_errormsg;
    /** params **/
    var $params = array();
    /** limitstart **/
    var $limitstart;
    /** limit **/
    var $limit;
    /** defaults **/
    var $defaults = array(
	'titleonly' => 0,
	'searchuser' => '',
	'starteronly' => 0,
	'replyless' => 0,
	'replylimit' => 0,
	'searchdate' => '365',
	'beforeafter' => 'after',
	'sortby' => 'lastpost',
	'order' => 'dec',
	'catids' => '0');
    /**
     * Search constructor
     * @param limitstart First shown item
     * @param limit Limit
     */
    function CKunenaSearch()
    {
        global $kunena_db, $kunena_my;

        $fbConfig =& CKunenaConfig::getInstance();

        // TODO: started_by
        // TODO: active_in

	// Default values for checkboxes depends on function
	$this->func = JRequest::getVar('func');
	if($this->func == 'search') {
		$this->defaults['exactname'] = $this->defaults['childforums'] = 1;
	} else {
		$this->defaults['exactname'] = $this->defaults['childforums'] = 0;
	}

	$q = JRequest::getVar('q', ''); // Search words
	// Backwards compability for old templates
	if (empty($q) && isset($_REQUEST['searchword'])) {
		$q = JRequest::getVar('searchword', '');
	}
	$this->params['titleonly'] = intval(JRequest::getVar('titleonly', $this->defaults['titleonly']));
	$this->params['searchuser'] = JRequest::getVar('searchuser', $this->defaults['searchuser']);
	$this->params['starteronly'] = intval(JRequest::getVar('starteronly', $this->defaults['starteronly']));
	$this->params['exactname'] = intval(JRequest::getVar('exactname', $this->defaults['exactname']));
	$this->params['replyless'] = intval(JRequest::getVar('replyless', $this->defaults['replyless']));
	$this->params['replylimit'] = intval(JRequest::getVar('replylimit', $this->defaults['replylimit']));
	$this->params['searchdate'] = JRequest::getVar('searchdate', $this->defaults['searchdate']);
	$this->params['beforeafter'] = JRequest::getVar('beforeafter', $this->defaults['beforeafter']);
	$this->params['sortby'] = JRequest::getVar('sortby', $this->defaults['sortby']);
	$this->params['order'] = JRequest::getVar('order', $this->defaults['order']);
	$this->params['childforums'] = intval(JRequest::getVar('childforums', $this->defaults['childforums']));
	$this->params['catids'] = strtr(JRequest::getVar('catids', '0'), KUNENA_URL_LIST_SEPARATOR, ',');
	$limitstart = $this->limitstart = intval(JRequest::getVar('limitstart', 0));
	$limit = $this->limit = intval(JRequest::getVar('limit', $fbConfig->messages_per_page_search));
	extract($this->params);

	if ($limit<1 || $limit>40) $limit = $this->limit = $fbConfig->messages_per_page_search;

	if (isset($_POST['q']) || isset($_POST['searchword'])) {
		$this->params['catids'] = implode(',', JRequest::getVar('catid', array(0)));
		$url = CKunenaLink::GetSearchURL($fbConfig, $this->func, $q, $limitstart, $limit, $this->getUrlParams());
        	header("HTTP/1.1 303 See Other");
        	header("Location: " . htmlspecialchars_decode($url));
        	$mainframe->close();
	}

	if ($q == _GEN_SEARCH_BOX) $q = '';
	$this->searchword = $q;
	$q = $kunena_db->getEscaped($q);
        $arr_searchwords = split(' ', $q);
	$do_search = FALSE;
	$this->arr_kunena_searchstrings = array();
	foreach ($arr_searchwords as $q)
	{
		$q = trim($q);
		if (strlen($q)>2) $do_search = TRUE;
		$this->arr_kunena_searchstrings[] = $q;
	}
	if (strlen($searchuser)>0) $do_search = TRUE;
        $arr_searchwords = $this->arr_kunena_searchstrings;
	$this->str_kunena_username = $searchuser;

        if ($do_search != TRUE)
        {
            $this->int_kunena_errornr = 1;
            $this->str_kunena_errormsg = _KUNENA_SEARCH_ERR_SHORTKEYWORD;
            return;
        }

	$search_forums = $this->get_search_forums($catids, $childforums);
        /* if there are no forums to search in, set error and return */
        if (empty($search_forums))
        {
            $this->int_kunena_errornr = 2;
            $this->str_kunena_errormsg = _KUNENA_SEARCH_NOFORUM;
            return;
        }

        for ($x = 0; $x < count($arr_searchwords); $x++)
        {
            $searchword = $arr_searchwords[$x];
            $searchword = $kunena_db->getEscaped(trim(strtolower($searchword)));
            if (empty($searchword)) continue;
            $matches = array ();
            $not = '';
            $operator = ' OR ';

            if (strstr($searchword, '-') == $searchword)
            {
                $not = 'NOT';
                $operator = 'AND';
                $searchword = substr($searchword, 1);
            }

            if($titleonly=='0')
            {
                $querystrings[] = '(t.message ' . $not . ' LIKE \'%' . $searchword . '%\' ' . $operator . ' m.subject ' . $not . ' LIKE \'%' . $searchword . '%\')';
            } else {
                $querystrings[] = '(m.subject ' . $not . ' LIKE \'%' . $searchword . '%\')';
            }
        }

	//User searching
        if(strlen($searchuser)>0)
        {
            if($exactname=='1') {
                $querystrings[] = 'm.name LIKE \'' . $searchuser . '\'';
            } else {
                $querystrings[] = 'm.name LIKE \'%' . $searchuser . '%\'';
            }
        }

	$time = 0;
	switch($searchdate) {
		case 'lastvisit':
			$kunena_db->setQuery('SELECT lasttime FROM #__fb_sessions WHERE userid = '. $kunena_my->id);
			$time = $kunena_db->loadResult();
			break;
		case 'all':
			break;
		case '1':
		case '7':
		case '14':
		case '30':
		case '90':
		case '180':
		case '365':
			$time = time() - 86400*intval($searchdate); //24*3600
                        break;
		default:
			$time = time() - 86400*365;
			$searchdate = '365';
	}

	if ($time) {
		if($beforeafter == 'after') {
			$querystrings[] = 'm.time > \''.$time.'\'';
		} else {
			$querystrings[] = 'm.time <= \''.$time.'\'';
		}
	}

        /* build query */
        $querystrings[] = 'm.moved=0';
        $querystrings[] = 'm.hold=0';
        $querystrings[] = "m.catid IN ($search_forums)";
        $where = implode(' AND ', $querystrings);

        if($order =='dec') $order1 = 'DESC';
        else $order1 = 'ASC';
        switch ($sortby) {
        case 'title':
		$orderby = 'm.subject '. $order1. 'm.time '.$order1;
		break;
        case 'views':
		$orderby = 'm.hits '. $order1 .', m.time '.$order1;
        break;
/*
        case 'threadstart':
		$orderby = 'm.time '.$order1.', m.ordering '.$order1.', m.hits '.$order1;
        break;
*/
        case 'forum':
		$orderby = 'm.catid '.$order1.', m.time '.$order1.', m.ordering '.$order1;
		break;
/*
        case 'replycount':
        case 'postusername':
*/
        case 'lastpost':
        default:
		$orderby = 'm.time '.$order1.', m.ordering '.$order1.', m.catid '.$order1;
        }

        if (count($groupby) > 0)
            $groupby = ' GROUP BY ' . implode(',', $groupby);
        else
            $groupby = '';

        /* get total */
        $kunena_db->setQuery('SELECT count(m.id) FROM #__fb_messages as m JOIN #__fb_messages_text as t ON m.id=t.mesid WHERE ' . $where . $groupby);
        $this->total = $kunena_db->loadResult();
        check_dberror("Unable to count messages.");

        /* if there are no forums to search in, set error and return */
        if ($this->total == 0)
        {
            $this->int_kunena_errornr = 3;
            $this->str_kunena_errormsg = _KUNENA_SEARCH_ERR_NOPOSTS;
            return;
        }
	if ($this->total < $this->limitstart) $this->limitstart = $limitstart = (int)($this->total / $this->limit);

        /* get results */
        $sql = 'SELECT m.id,m.subject,m.catid,m.thread,m.name,m.time,t.message FROM #__fb_messages_text as t JOIN #__fb_messages as m ON m.id=t.mesid WHERE ' . $where . $groupby . ' ORDER BY ' . $orderby . ' LIMIT ' . $limitstart . ',' . $limit;
        $kunena_db->setQuery($sql);
        $rows = $kunena_db->loadObjectList();
        check_dberror("Unable to load messages.");

        $this->str_kunena_errormsg = $sql . '<br />' . $kunena_db->getErrorMsg();

        if (count($rows) > 0)
            $this->arr_kunena_results = $rows;
        else
            $this->arr_kunena_results = array ();

        return;
    }
    /** get searchstrings (array) **/
    function get_searchstrings() {
        return $this->arr_kunena_searchstrings;
    }
    function get_searchusername() {
        return $this->str_kunena_username;
    }
    /** get limit (int) **/
    function get_limit() {
        return $this->limit;
    }
    /** get start (int) **/
    function get_limitstart() {
        return $this->limitstart;
    }
    /** get results (array) **/
    function get_results() {
        return $this->arr_kunena_results;
    }
    function getUrlParams() {
	$url_params = '';
	foreach ($this->params as $param => $value) {
		if ($param == 'catids') $value = strtr($value, ',', KUNENA_URL_LIST_SEPARATOR);
		if ($value != $this->defaults[$param]) $url_params .= "&amp;$param=".urlencode($value);
	}
	return $url_params;
    }
    function get_search_forums(&$catids, $childforums = 1) {
        global $kunena_db, $kunena_my, $fbSession;

        /* get allowed forums */
        $allowed_forums = array();
        $allowed_string = '';
        if ($fbSession->allowed != 'na')
        {
            $allowed_string = "AND id IN ({$fbSession->allowed})";
	}

        $kunena_db->setQuery("SELECT id, parent FROM #__fb_categories WHERE pub_access='0' AND published='1' $allowed_string");
        $allowed_forums = $kunena_db->loadAssocList('id');
        check_dberror("Unable to get public categories.");

	foreach ($allowed_forums as $forum)
	{
		// Children list: parent => array(child1, child2, ...)
		$allow_list[$forum['parent']][] = $forum['id'];
	}

	$catids = split(',', $catids);
	$result = array();
	if (count($catids) > 0 && !in_array(0, $catids)) {
		// Algorithm:
		// Start with selected categories and pop them from the catlist one by one
		// Every popped item in the catlist will be added into result list
		// For every category: push all its children into the catlist
		while ($cur = array_pop($catids))
		{
			$result[$cur] = $cur;
			if (array_key_exists($cur, $allow_list))
				foreach ($allow_list[$cur] as $forum) 
					if (!in_array($forum, $catids))
						array_push($catids, $forum);
		}
		$search_forums = implode(",", $result);
	} else {
		$search_forums = implode(",", array_keys($allowed_forums));
	}
	return $search_forums;
    }
    /**
     * Display results
     * @param string actionstring
     */
    function show()
    {
	$fbConfig =& CKunenaConfig::getInstance();

	extract($this->params);
        $q = implode(" ", $this->get_searchstrings());
        $searchuser = $this->get_searchusername();
	$limitstart = $this->get_limitstart();
	$limit = $this->get_limit();

	$selected = ' selected="selected"';
	$checked = ' checked="checked"';
	$fb_advsearch_hide = 1;
	if ($this->int_kunena_errornr) {
	        $q = $this->searchword;
		$fb_advsearch_hide = 0;
	}
        if (file_exists(KUNENA_ABSTMPLTPATH . '/plugin/advancedsearch/advsearch.php')) {
        	include (KUNENA_ABSTMPLTPATH . '/plugin/advancedsearch/advsearch.php');
        }
        else {
                include (KUNENA_PATH_TEMPLATE_DEFAULT .DS. 'plugin/advancedsearch/advsearch.php');
        }

        $results = $this->get_results();
        $totalRows = (int)($this->total);
        $actionstring = $this->str_kunena_actionstring;

	$pagination = KunenaSearchPagination($this->func, $q, $this->getUrlParams(), floor($limitstart/$limit)+1, $limit, floor($totalRows/$limit)+1, 7);

        if (defined('KUNENA_DEBUG'))
            echo '<p style="background-color:#FFFFCC;border:1px solid red;">' . $this->str_kunena_errormsg . '</p>';
?>

<?php

	if (empty($q) && empty($searchuser)) {
		return;
	}

        $boardclass = 'fb_';
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
        <table  class = "fb_blocktable" id ="fb_forumsearch"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th colspan = "3">
                        <div class = "fb_title_cover">
                            <span class="fb_title fbl"><?php echo _KUNENA_SEARCH_RESULTS; ?></span>
                            <b><?php printf(_FORUM_SEARCH, $q); ?></b>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr class = "fb_sth">
                    <th class = "th-1 <?php echo $boardclass; ?>sectiontableheader">
<?php echo _GEN_SUBJECT; ?>
                    </th>

                    <th class = "th-2 <?php echo $boardclass; ?>sectiontableheader">
<?php echo _GEN_AUTHOR; ?>
                    </th>

                    <th class = "th-3 <?php echo $boardclass; ?>sectiontableheader">
<?php echo _GEN_DATE; ?>
                    </th>
                </tr>

                <?php
                $tabclass = array
                (
                    "sectiontableentry1",
                    "sectiontableentry2"
                );

                $k = 0;

                if ($totalRows == 0 && $this->int_kunena_errornr) {
                    echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '" ><td colspan="3"  style="text-align:center;font-weight:bold">' . $this->str_kunena_errormsg . '</td></tr>';
                }

				// Cleanup incoming searchword; international chars can cause garbage at the end
				// real problem might lie with search box form and how we post and receive the data
				// However, this works for now
				$q = trim($q);

                // JJ Add different color
                $searchlist = $this->get_searchstrings();
                foreach ($results as $result)
                {
                    $k = 1 - $k;
                    $ressubject = $result->subject;
                    // Clean up subject
                    $ressubject = stripslashes(smile::purify($ressubject));
                    $resmessage = stripslashes($result->message);
                    // Strip smiles and bbcode out of search results; they look ugly
                    $resmessage = CKunenaTools::prepareContent($resmessage);
                    $resmessage = smile::purify($resmessage);
                    $resmessage = mb_substr(html_entity_decode_utf8($resmessage), 0, 300);
                    $utf8 = (mb_detect_encoding($ressubject . $resmessage . 'a', 'UTF-8,ISO-8859-1') == 'UTF-8') ? "u" : "";
                    foreach ($searchlist as $searchword)
                    {
                        if (empty($searchword)) continue;
                        $ressubject = preg_replace("/".preg_quote($searchword, '/')."/i".$utf8, '<span  class="searchword" >' . $searchword . '</span>', $ressubject);
                        $resmessage = preg_replace("/".preg_quote($searchword, '/')."/i".$utf8, '<span  class="searchword" >' . $searchword . '</span>', $resmessage);
                    }
                    echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '">';
                    echo '<td  class = "td-1" ><a href="'
                             . JRoute::_(KUNENA_LIVEURL . '&amp;func=view&amp;id=' . $result->id . '&amp;catid=' . $result->catid) . '#' . $result->id . '" >' . $ressubject . '</a><br />' . $resmessage . '<br /><br /></td>';
                    echo '<td class = "td-2" >' . html_entity_decode_utf8(stripslashes($result->name)) . '</td>';
                    echo '<td class = "td-3" >' . date(_DATETIME, $result->time) . '</td></tr>';
                    echo "\n";
                }
                ?>

                <?php
                if ($totalRows > $limit)
                {
                ?>

                    <tr  class = "fb_sth" >
                        <th colspan = "3" style = "text-align:center" class = "th-1 <?php echo $boardclass; ?>sectiontableheader">
                            <?php
                            echo $pagination;
                            ?>
                        </th>
                    </tr>

                <?php
                }
                ?>

                <tr  class = "fb_sth" >
                   <th colspan = "3" style = "text-align:center" class = "th-1 <?php echo $boardclass; ?>sectiontableheader">
                        <?php
			$resStart = $limitstart+1;
			$resStop = $limitstart+count($results);
			if ($resStart<$resStop) $resStartStop = (string)($resStart).' - '.(string)($resStop);
			else $resStartStop = '0';
                        printf(_FORUM_SEARCHRESULTS, $resStartStop, $totalRows);
                        ?>
                    </th>
                </tr>
            </tbody>
        </table>
</div>
</div>
</div>
</div>
</div>
<?php
    }
}

function KunenaSearchPagination($function, $q, $urlparams, $page, $limit, $totalpages, $maxpages) {
    $fbConfig =& CKunenaConfig::getInstance();
    if ($page==0) $page++;
    $startpage = ($page - floor($maxpages/2) < 1) ? 1 : $page - floor($maxpages/2);
    $endpage = $startpage + $maxpages;
    if ($endpage > $totalpages) {
	$startpage = ($totalpages-$maxpages) < 1 ? 1 : $totalpages-$maxpages;
	$endpage = $totalpages;
    }

    $output = '<div class="fb_pagination">'._PAGE;
    if ($startpage > 1)
    {
	if ($endpage < $totalpages) $endpage--;
	$output .= CKunenaLink::GetSearchLink($fbConfig, $function, $q, 0, $limit, 1, $urlparams, $rel='nofollow');

	if ($startpage > 2)
        {
	    $output .= "...";
	}
    }

    for ($i = $startpage; $i <= $endpage && $i <= $totalpages; $i++)
    {
        if ($page == $i) {
            $output .= "<strong>$i</strong>";
        }
        else {
	    $output .= CKunenaLink::GetSearchLink($fbConfig, $function, $q, ($i-1)*$limit, $limit, $i, $urlparams, $rel='nofollow');
        }
    }

    if ($endpage < $totalpages)
    {
	if ($endpage < $totalpages-1)
        {
	    $output .= "...";
	}

	$output .= CKunenaLink::GetSearchLink($fbConfig, $function, $q, ($totalpages-1)*$limit, $limit, $totalpages, $urlparams, $rel='nofollow');
    }

    $output .= '</div>';
    return $output;
}

?>
