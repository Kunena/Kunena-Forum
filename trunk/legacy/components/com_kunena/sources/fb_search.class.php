<?php
/**
* @version $Id: fb_search.class.php 946 2008-08-11 01:55:08Z fxstein $
* Fireboard Component
* @package Fireboard
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
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

// Need purify function for search result display
if (file_exists(JB_ABSTMPLTPATH."/smile.class.php")) {
  include_once(JB_ABSTMPLTPATH."/smile.class.php");
}
else {
  include(JB_ABSPATH . '/template/default/smile.class.php');
}


class jbSearch
{
    /** search results **/
    var $arr_FB_results;
    /** search strings **/
    var $arr_FB_searchstrings;
    /** error number **/
    var $int_FB_errornr;
    /** error msg **/
    var $str_FB_errormsg;
    /** limit **/
    var $limit;
    /** limitstart **/
    var $limitstart;
    /**
     * Search constructor
     * @param object database
     * @param string search
     * @param int uid (userid)
     */
    function jbSearch(&$database, $search, $uid, $limitstart = 0, $limit = 3)
    {
        $this->limitstart = $limitstart;
        $this->limit = $limit;
        /* handle search string */
        $search = htmlspecialchars($search, ENT_COMPAT, 'UTF-8');
        $arr_searchwords = split(' ', $search);
        /* return error if empty search string */
        if (count($arr_searchwords) == 0)
        {
            $this->int_FB_errornr = 2;
            $this->str_FB_errormsg = _NOKEYWORD;
            $this->arr_FB_results = array ();
            return array ();
        }

        for ($x = 0; $x < count($arr_searchwords); $x++)
        {
            $searchword = $arr_searchwords[$x];
            $searchword = $database->getEscaped(trim(strtolower($searchword)));
            $matches = array ();
            $not = '';
            $operator = ' OR ';

            if (strstr($searchword, '-') == $searchword)
            {
                $not = 'NOT';
                $operator = 'AND';
                $searchword = substr($searchword, 1);
            }

            if (preg_match('/^author:(\w+)/', $searchword, $matches)) {
                $querystrings[] = 'm.name ' . $not . ' LIKE \'%' . $matches[1] . '%\'';
            }
            else if (preg_match('/^after:(\d{8})/', $searchword, $matches)) {
                $querystrings[] = 'm.time > UNIX_TIMESTAMP(' . $matches[1] . ')';
            }
            else if (preg_match('/^before:(\d{8})/', $searchword, $matches)) {
                $querystrings[] = 'm.time < UNIX_TIMESTAMP(' . $matches[1] . ')';
            }
            else if (preg_match('/^groupby:(\w{4,6})/', $searchword, $matches)) {
                $groupby[] = $matches[1];
            }
            else if (preg_match('/^orderby:(\w{4,6})/', $searchword, $matches)) {
                $orderby[] = $matches[1];
            }
            else {
                $querystrings[] = '(t.message ' . $not . ' LIKE \'%' . $searchword . '%\' ' . $operator . ' m.subject ' . $not . ' LIKE \'%' . $searchword . '%\')';
            }
        }

        $this->arr_FB_searchstrings = $arr_searchwords;
        /* get allowed forums */
        $allowed_forums = '';

        if ($uid > 0)
        {
            $database->setQuery('SELECT allowed FROM #__fb_sessions WHERE userid=' . $uid);
            $allowed_forums = $database->loadResult();
        }
        /* non registered users can only search in public forums */
        if (empty($allowed_forums))
        {
            $database->setQuery("SELECT id FROM #__fb_categories WHERE pub_access=0 AND published=1");
            if ($arr_pubcats = $database->loadResultArray())
                $allowed_forums = implode(',', $arr_pubcats);
        }
        /* if there are no forums to search in, set error and return */
        if (empty($allowed_forums))
        {
            $this->int_FB_errornr = 1;
            $this->str_FB_errormsg = _FB_SEARCH_NOFORUM;
            return 0;
        }
        /* build query */
        $querystrings[] = 'm.catid IN (' . $allowed_forums . ')';
        $querystrings[] = 'm.moved!=1';
        $querystrings[] = 'm.hold=0';
        $where = implode(' AND ', $querystrings);

        if (count($orderby) > 0)
            $orderby = implode(',', $orderby);
        else
            $orderby = 'm.ordering DESC, m.time DESC,m.hits DESC';

        if (count($groupby) > 0)
            $groupby = ' GROUP BY ' . implode(',', $groupby);
        else
            $groupby = '';

        $sql = 'SELECT m.id,m.subject,m.catid,m.thread,m.name,m.time,t.message FROM #__fb_messages_text as t JOIN #__fb_messages as m ON m.id=t.mesid WHERE ' . $where . $groupby . ' ORDER BY ' . $orderby . ' LIMIT ' . $limitstart . ',' . $limit;
        /* get total */
        $database->setQuery('SELECT count(m.id) FROM #__fb_messages as m JOIN #__fb_messages_text as t ON m.id=t.mesid WHERE ' . $where . $groupby . ' LIMIT 300');
        $this->total = $database->loadResult();
                check_dberror("Unable to count messages.");

        /* get results */
        $database->setQuery($sql);
        $rows = $database->loadObjectList();
                check_dberror("Unable to load messages.");

        $this->str_FB_errormsg = $sql . '<br />' . $database->getErrorMsg();

        if (count($rows) > 0)
            $this->arr_FB_results = $rows;
        else
            $this->arr_FB_results = array ();

        return $this->arr_FB_results;
    }
    /** get searchstrings (array) **/
    function get_searchstrings() {
        return $this->arr_FB_searchstrings;
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
        return $this->arr_FB_results;
    }
    /**
     * Display results
     * @param string actionstring
     */
    function show()
    {
        $searchword = implode(' ', $this->get_searchstrings());
        $results = $this->get_results();
        $totalRows = $this->total;
        $actionstring = $this->str_FB_actionstring;

        if ($this->get_limit() < $totalRows)
        {
            require_once(JB_JABSPATH . '/includes/pageNavigation.php');
            $pageNav = new mosPageNav($totalRows, $this->get_limitstart(), $this->get_limit());
        }

        if (defined('JB_DEBUG'))
            echo '<p style="background-color:#FFFFCC;border:1px solid red;">' . $this->str_FB_errormsg . '</p>';
?>

<?php
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
                            <span class="fb_title"><?php echo _FORUM_SEARCHTITLE; ?></span>
                            <b><?php printf(_FORUM_SEARCH, $searchword); ?></b>
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

                if ($totalRows == 0 && $this->int_FB_errornr) {
                    echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '" ><td colspan="3"  style="text-align:center;font-weight:bold">Error ' . $this->int_FB_errornr . ': ' . $this->str_FB_errormsg . '</td></tr>';
                }

								// Cleanup incoming searchword; international chars can cause garbage at the end
								// real problem might lie with search box form and how we post and receive the data
								// However, this works for now
								$searchword = trim($searchword);

                // JJ Add different color
                foreach ($results as $result)
                {
                    $k = 1 - $k;
                    $ressubject = $result->subject;
                    // Clean up subject
                    $ressubject = smile::purify($ressubject);
                    $ressubject = preg_replace("/".preg_quote($searchword, '/')."/i", '<span  class="searchword" >' . $searchword . '</span>', $ressubject);
                    $resmessage = $result->message;
                    // Strip smiles and bbcode out of search results; they look ugly
                    $resmessage = smile::purify($resmessage);
                    $resmessage = preg_replace("/".preg_quote($searchword, '/')."/i", '{{' . $searchword . '}}', $resmessage);
                    $searchResultList = str_replace("{{", '<span class="fb_search-results">', substr(htmlspecialchars($resmessage), 0, 300));
                    $searchResultList = str_replace("}}", '</span>', $searchResultList);
                    echo '<tr class="' . $boardclass . '' . $tabclass[$k] . '">';
                    echo '<td  class = "td-1" ><a href="'
                             . sefRelToAbs(JB_LIVEURLREL . '&amp;func=view&amp;id=' . $result->id . '&amp;catid=' . $result->catid . '#' . $result->id) . '" >' . $ressubject . '</a><br />' . $searchResultList . '<br /><br /></td>';
                    echo '<td class = "td-2" >' . $result->name . '</td>';
                    echo '<td class = "td-3" >' . date(_DATETIME, $result->time) . '</td></tr>';
                    echo "\n";
                }
                ?>

                <?php
                if ($totalRows > $this->limit)
                {
                ?>

                    <tr  class = "fb_sth" >
                        <th colspan = "3" style = "text-align:center" class = "th-1 <?php echo $boardclass; ?>sectiontableheader">
                            <?php
                            // TODO: fxstein - Need to perform SEO cleanup
                            echo $pageNav->writePagesLinks(JB_LIVEURL . '&amp;func=search&amp;searchword=' . $searchword);
                            ?>
                        </th>
                    </tr>

                <?php
                }
                ?>

                <tr  class = "fb_sth" >
                   <th colspan = "3" style = "text-align:center" class = "th-1 <?php echo $boardclass; ?>sectiontableheader">
                        <?php
                        printf(_FORUM_SEARCHRESULTS, count($results), $totalRows);
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
}?>

