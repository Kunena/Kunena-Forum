<?php
/**
* @version $Id: advsearch.php 947 2008-08-11 01:56:01Z fxstein $
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

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

//Get main categories with child categories
$catid = intval(mosGetParam($_REQUEST, "catid", 0));

function JJ_categoryArray()
{
    global $database;
    // get a list of the menu items
    $query = "SELECT c.*, c.parent" . "\n FROM #__fb_categories c" . "\n WHERE published =1" . "\n ORDER BY name";
    $database->setQuery($query);
    $items = $database->loadObjectList();
    	check_dberror("Unable to load categories.");

    // establish the hierarchy of the menu
    $children = array ();

    // first pass - collect children
    foreach ($items as $v)
    {
        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array ();
        array_push($list, $v);
        $children[$pt] = $list;
    }

    // second pass - get an indent list of the items
    $array = fbTreeRecurse(0, '', array (), $children, 10, 0, 1);
    return $array;
}

function JJ_categoryParentList($catid, $action, $options = array ())
{
    global $database;
    $list = JJ_categoryArray();
    $this_treename = '';

    foreach ($list as $item)
    {
        if ($this_treename)
        {
            if ($item->id != $catid && strpos($item->treename, $this_treename) === false) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            }
        }
        else
        {
            if ($item->id != $catid) {
                $options[] = mosHTML::makeOption($item->id, $item->treename);
            }
            else {
                $this_treename = "$item->treename/";
            }
        }
    }

    $parent = mosHTML::selectList($options, 'catid', 'class="inputbox" size="13" multiple="multiple"', 'value', 'text', $catid);
    return $parent;
}

//category select list
$options = array ();
$options[] = mosHTML::makeOption('0', 'All Categories');
$lists['parent'] = JJ_categoryParentList($catid, "", $options);
?>

<form action = "index.php" method = "get" id = "searchform" name = "adminForm">
    <input type = "hidden" name = "option" value = "com_fireboard"/>

    <input type = "hidden" name = "func" value = "advsearchresult"/>

    <input type = "hidden" name = "Itemid" value = "<?php echo FB_FB_ITEMID;?>"/>

    <table class = "bof-forum-cat" border = "0" cellspacing = "0" cellpadding = "0">
        <thead>
            <tr>
                <th>
                    <div class = "cat-title">
                        <h3>Forumlarda Ara</h3>
                    </div>
                </th>
            </tr>
        </thead>

        <tbody id = "main_tbody">
            <tr class = "bof-sectiontableentry2">
                <td class = "td-1">
                    <table class = "panel" cellpadding = "0" cellspacing = "3" border = "0" width = "100%">
                        <tr>
                            <td align = "left" width = "50%">
                                <fieldset class = "fieldset" style = "margin:0px">
                                    <legend>
                                        Kelimelerde ara
                                    </legend>

                                    <table cellpadding = "0" cellspacing = "3" border = "0">
                                        <tr>
                                            <td colspan = "2">
                                                <div>
                                                    Keywords:
                                                </div>

                                                <div>
                                                    <input type = "text" class = "bof-inputbox" name = "searchword" size = "35" value = "" style = "width:250px"/>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <select name = "titleonly">
                                                    <option value = "0" selected = "selected">Bütün Mesajlarda ara</option>

                                                    <option value = "1">Mesaj baþlýklarýnda ara</option>
                                                </select>
                                            </td>

                                            <td align = "right">
                                                &nbsp;
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>

                            <td align = "left" valign = "top" width = "50%">
                                <fieldset class = "fieldset" style = "margin:0px">
                                    <legend>
                                        Kullanýcý adý ara
                                    </legend>

                                    <table cellpadding = "0" cellspacing = "3" border = "0">
                                        <tr>
                                            <td colspan = "2">
                                                <div>
                                                    Kullanýcý ismi:
                                                </div>

                                                <div id = "userfield">
                                                    <input type = "text" class = "bginput" name = "searchuser" size = "35" value = "" style = "width:250px"/>
                                                </div>

                                                <div id = "userfield_menu" class = "vbmenu_popup" style = "display:none">
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <select name = "starteronly">
                                                    <option value = "0" selected = "selected">Kullanýcýnýn Mesajlarýný ara</option>

                                                    <option value = "1">Kullanýcýnýn Konularýný ara</option>
                                                </select>
                                            </td>

                                            <td>
                                                <label for = "cb_exactname"><input type = "checkbox" name = "exactname" value = "1" id = "cb_exactname" checked = "checked"/>

                                                Tam isim</label>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <div align = "left">
        <table class = "bof-forum-cat advsearch" border = "0" cellspacing = "0" cellpadding = "0">
            <thead>
                <tr>
                    <th>
                        <div class = "cat-title">
                            <h3>Arama Ayarlarý</h3>
                        </div>

                        <img id = "BoxSwitch_announcements__announcements_tbody" class = "hideshow" src = "<?php echo JB_URLIMAGESPATH . 'shrink.gif' ; ?>" alt = ""/>
                    </th>
                </tr>
            </thead>

            <tbody id = "announcements_tbody">
                <tr class = "bof-sectiontableentry2">
                    <td class = "td-1">
                        <div class = "anndesc">
                            <table class = "panel" cellpadding = "0" cellspacing = "3" border = "0" width = "100%">
                                <tr valign = "top">
                                    <td width = "50%">
                                        <fieldset class = "fieldset">
                                            <legend>
                                                Cevap Sayýsýna Göre
                                            </legend>

                                            <div style = "padding:3px">
                                                <select name = "replyless" style = "width:150px">
                                                    <option value = "0" selected = "selected">Minimum</option>

                                                    <option value = "1">Maksimum</option>
                                                </select>

                                                <input type = "text" class = "bginput" style = "font-size:11px" name = "replylimit" size = "3" value = "0"/>

                                                Cevaplar
                                            </div>
                                        </fieldset>

                                        <fieldset class = "fieldset">
                                            <legend>
                                                Tarihe göre
                                            </legend>

                                            <div style = "padding:3px">
                                                <select name = "searchdate" style = "width:150px">
                                                    <option value = "0" selected = "selected">her Tarihe</option>

                                                    <option value = "lastvisit">son Ziyaretinizden</option>

                                                    <option value = "1">Dün</option>

                                                    <option value = "7">son Haftadan</option>

                                                    <option value = "14">2 Hafta önce</option>

                                                    <option value = "30">son Aydan itibaren</option>

                                                    <option value = "90">son 3 Aydan</option>

                                                    <option value = "180">son 6 Aydan</option>

                                                    <option value = "365">son Seneden</option>
                                                </select>

                                                <select name = "beforeafter">
                                                    <option value = "after" selected = "selected">Yeniler</option>

                                                    <option value = "before">Eskiler</option>
                                                </select>
                                            </div>
                                        </fieldset>

                                        <fieldset class = "fieldset">
                                            <legend>
                                                Sonuçlarý Sýrala
                                            </legend>

                                            <div style = "padding:3px">
                                                <select name = "sortby" style = "width:150px">
                                                    <option value = "title">Baþlýk</option>

                                                    <option value = "replycount">Cevap sayýsýna göre</option>

                                                    <option value = "views">Görüntüleme sayýsýna göre</option>

                                                    <option value = "threadstart">Konu Tarihine</option>

                                                    <option value = "lastpost" selected = "selected">son Mesaj</option>

                                                    <option value = "postusername">Kullanýcý ismi</option>

                                                    <option value = "forum">Forum</option>
                                                </select>

                                                <select name = "order">
                                                    <option value = "descending" selected = "selected">Azalan Sýralamasý</option>

                                                    <option value = "ascending">Yükselen Sýralamasý</option>
                                                </select>
                                            </div>
                                        </fieldset>
                                    </td>

                                    <td width = "50%">
                                        <fieldset class = "fieldset" style = "margin:0px">
                                            <legend>
                                                Aranacak Forumlar...
                                            </legend>

                                            <div style = "padding:3px">
                                                <div>
<?php echo $lists['parent']; ?>
                                                </div>

                                                <div>
                                                    <label for = "cb_childforums"><input type = "checkbox" name = "childforums" value = "1" id = "cb_childforums" checked = "checked"/>

                                                    Alt Forumlarý da aramaya dahil et</label>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style = "margin-top:6px">
        <input type = "submit" class = "button" value = "Send"/>

        <input type = "reset" class = "button" value = "Cancel" onclick = "window.location='index.php?option=com_fireboard&amp;Itemid=<?php echo $Itemid;?>';"/>
    </div>

    </td>
    </tr>
    </table>
</form>