<?php
/**
* @version $Id: fb_statsbar.php 462 2007-12-10 00:05:53Z fxstein $
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

// Graph Generator for PHP
// http://szewo.com/php/graph

class phpGraph
{
    var $_values;
    var $_ShowLabels;
    var $_ShowCounts;
    var $_ShowCountsMode;

    var $_BarWidth;
    var $_GraphWidth;
    var $_BarImg;
    var $_BarImg2;
    var $_BarBorderWidth;
    var $_BarBorderColor;
    var $_RowSortMode;
    var $_TDClassHead;
    var $_TDClassLabel;
    var $_TDClassCount;
    var $_GraphTitle;

    var $_maxVal;

    function phpGraph()
    {
        $this->_values = array ();
        $this->_ShowLabels = true;
        $this->_BarWidth = 50;
        $this->_GraphWidth = 360;
        //$this->_BarImg = "";
        //$this->_BarImg2 = "";
        $this->_BarBorderWidth = 0;
        $this->_BarBorderColor = "red";
        $this->_ShowCountsMode = 2;
        $this->_RowSortMode = 1;
        $this->_TDClassHead = "grphh";
        $this->_TDClassLabel = "grph";
        $this->_TDClassCount = "grphc";
        $this->_GraphTitle = "Graph title>";
        $this->_maxVal = 100;
    }

    function SetBarBorderWidth($width)
    {
        $this->_BarBorderWidth = $width;
    }

    function SetBorderColor($color)
    {
        $this->_BarBorderColor = $color;
    }

    //  mode = 1 labels asc, 2 label desc
    function SetSortMode($mode)
    {
        switch ($mode)
        {
            case 1:
                asort ($this->_values);

                break;

            case 2:
                arsort ($this->_values);

                break;

            default: break;
        }
    }

    function AddValue($labelName, $theValue) {
        array_push($this->_values, array
        (
            "label" => $labelName,
            "value" => $theValue
        ));
    }

    function SetBarWidth($width)
    {
        $this->_BarWidth = $width;
    }

    function SetBarImg($img)
    {
        $this->_BarImg = $img;
    }

    function SetBarImg2($img)
    {
        $this->_BarImg2 = $img;
    }

    function SetShowLabels($lables)
    {
        $this->_ShowLabels = $labels;
    }

    function SetGraphWidth($width)
    {
        $this->_GraphWidth = $width;
    }

    function SetGraphTitle($title)
    {
        $this->_GraphTitle = $title;
    }

    //mode = percentage or counts
    function SetShowCountsMode($mode)
    {
        $this->_ShowCountsMode = $mode;
    }

    //mode = none(0) label(1) or count(2)
    function SetRowSortMode($sortmode)
    {
        $this->_RowSortMode = $sortmode;
    }

    function SetTDClassHead($class)
    {
        $this->_TDClassHead = $class;
    }

    function SetTDClassLabel($class)
    {
        $this->_TDClassLabel = $class;
    }

    function SetTDClassCount($class)
    {
        $this->_TDClassCount = $class;
    }

    function SetMaxVal($value)
    {
        $this->_maxVal = $value;
    }

    function BarGraphHoriz()
    {
        $maxval = $this->_maxVal;

        foreach ($this->_values as $value)
            $sumval += $value["value"];

        $this->SetSortMode($this->_RowSortMode);
        echo '<div class="viewcover">';

        foreach ($this->_values as $value)
        {
            if ($this->_ShowLabels)
            {
                if ($this->_ShowCountsMode > 0)
                    echo "";

                if ($this->_ShowCountsMode > 0)
                {
                    switch ($this->_ShowCountsMode)
                    {
                        case 1:
                            $count = round(100 * $value["value"] / $sumval) . "%";

                            break;

                        case 2:
                            $count = $value["value"];

                            break; /* Exit the switch and the while. */

                        default: break;
                    }

                    echo "<strong>" . $value["label"] . " " . $count . "</strong></div>";
                }

                echo '<div class="viewcover">';
                echo "<table  border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\"><tr>";
            }

            $height = $this->_BarWidth;
            $width = @ceil(($value["value"] / $maxval) * $this->_GraphWidth);
            $rest_width = ($this->_GraphWidth - $width);
            echo '<td width="' . $this->_GraphWidth . '">';
            echo '<img src="' . $this->_BarImg . '" height="' . $height . '" width="' . $width . '"';
            echo " alt=\"graph\" style=\"border: " . $this->_BarBorderWidth . "px solid " . $this->_BarBorderColor . "\"";
            echo " />";

            if ($rest_width > 0)
            {
                echo '<img src="' . $this->_BarImg2 . '" height="' . $height . '" width="' . ($rest_width - 4) . '"';
                echo " alt=\"graph\" style=\"border: " . $this->_BarBorderWidth . "px solid " . $this->_BarBorderColor . "\"";
                echo " />";
            }

            echo "</td></tr>";
        }

        echo "</table></div>";
    }
}
?>