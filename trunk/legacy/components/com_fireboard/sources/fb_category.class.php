<?php
/**
* @version $Id: fb_category.class.php 462 2007-12-10 00:05:53Z fxstein $
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
// ################################################################
// MOS Intruder Alerts
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
// ################################################################

/**
 * A fbCategory Object
 */
class jbCategory {
    var $name;
    var $parent;
    var $locked;
    var $moderated;
    var $published;
    var $review;
    var $desc;
    var $id;
    var $pubaccess;
    var $adminaccess;
    var $pubrecurse;
    var $adminrecurse;
    var $database;

    function jbCategory(&$database,$id) {
        $this->database=$database;
        $database->setQuery('SELECT id,pub_access,pub_recurse,admin_access,admin_recurse,parent,name,locked,moderated,published,description,review FROM #__fb_categories WHERE id='.$id);
        $database->loadObject($cat);
        $this->setName($cat->name);
        $this->setParent($cat->parent);
        $this->setLocked($cat->locked);
        $this->setModerated($cat->moderated);
        $this->setPublished($cat->published);
        $this->setDescription($cat->description);
        $this->setReview($cat->review);
        $this->setPubAccess($cat->pub_access);
        $this->setPubRecurse($cat->pub_recurse);
        $this->setAdminAccess($cat->admin_access);
        $this->setAdminRecurse($cat->admin_recurse);
        $this->setId($cat->id);
    }
    function setPubAccess($pubaccess) {
        $this->pubaccess=$pubaccess;
    }
    function getPubAccess() {
        return $this->pubaccess;
    }
    function setAdminAccess($adminaccess) {
        $this->adminaccess=$adminaccess;
    }
    function getAdminAccess() {
        return $this->adminaccess;
    }
    function setPubRecurse($pubrecurse) {
        $this->pubrecurse=$pubrecurse;
    }
    function getPubRecurse() {
        return $this->pubrecurse;
    }
    function setAdminRecurse($pubrecurse) {
        $this->adminrecurse=$pubrecurse;
    }
    function getAdminRecurse() {
        return $this->adminrecurse;
    }
    function setReview($bool)  {
        $this->review=$bool;
    }
    function getReview() {
        return $this->review;
    }
    function setParent($id) {
        $this->parent=$id;
    }
    function getParent() {
        return $this->parent;
    }
    function setId($id) {
        $this->id=$id;
    }
    function getId() {
        return $this->id;
    }
    function setName($name) {
        $this->name=$name;
    }
    function getName() {
        return $this->name;
    }
    function setLocked($lock) {
        $this->locked=$lock;
    }
    function getLocked() {
        return $this->locked;
    }
    function setModerated($moderated) {
        $this->moderated=$moderated;
    }
    function getModerated() {
        return $this->moderated;
    }
    function setPublished($published) {
        $this->published=$published;
    }
    function getPublished() {
        return $this->published;
    }
    function setDescription($desc) {
        $this->desc=$desc;
    }
    function getDescription() {
        return $this->desc;
    }
}