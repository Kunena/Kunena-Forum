<html>
    <head>
        <title>MamboV45104dev :: Administration [Mambo Open Source]</title>

        <style type = "text/css">
            body
            {
                background: #FFFFFF;
                margin: 10px 45px 15px;
                font-size: 11px;
                color: #000000;
                font-weight: 600;
                font-family: Helvetica;
            }
            /* standard list style table */
            table.adminlist
            {
                background-color: #ffffff;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
            }

            table.adminlist th
            {
                background-color: #999999;
                border-bottom: 1px dashed #CCCCCC;
                color: #FFFFFF;
                font-color: #000000;
                word-wrap: none;
                text-align: left;
                height: 25px;
            }

            table.adminlist tr.row0
            {
                background-color: #f5f5f5;
            }

            table.adminlist tr.row1
            {
                background-color: #ffffff;
            }

            table.adminlist td
            {
                border-bottom: 1px solid #e5e5e5;
            }

            table.adminlist tr.row0:hover
            {
                background-color: #f1f1f1;
            }

            table.adminlist tr.row1:hover
            {
                background-color: #f1f1f1;
            }
        </style>
    </head>

    <body>
        <?php
        define("_VALID_MOS", 1);

        include_once ("../../../components/com_fireboard/sources/fb_debug.php");
        require_once ("../../../configuration.php");
        require_once ("../../../includes/mambo.php");

        if ($mosConfig_db != "") {
            $database = new database($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix);
        }
        else if ($DBNAME != "") {
            $database = new database($DBHOST, $DBUSER, $DBPASS, $DBNAME, $DBPREF);
        }

        $query = "SELECT * FROM #__fb_categories WHERE parent > 0";
        $database->setQuery($query);

        $rows = $database->loadObjectList();
        	check_dberror("Unable to load categories.");
        ?>

        <table cellpadding = "4" cellspacing = "0" class = "adminlist">
            <tr>
                <td colspan = "3">
                    You can copy & paste the {mos_fb_discuss:##} for the category you want to use!
                </td>
            </tr>

            <tr>
                <th>
                    <b>fb catid

                    <b/>
                </th>

                <th>
                    <b>fb mosbot code

                    <b/>
                </th>

                <th>
                    <b>fb Categories

                    <b/>
                </th>
            </tr>

            <?php
            $k = 0;

            foreach ($rows as $row)
            {
            ?>

                <tr class = "<?php echo "row$k"; ?>">
                    <td><?php echo $row->id; ?>
                    </td>

                    <td>
                        {mos_fb_discuss:<?php echo $row->id; ?>}
                    </td>

                    <td><?php echo $row->name; ?>
                    </td>

                    <?php
                    $k = 1 - $k;
                    ?>
                </tr>

            <?php
            }
            ?>

            <tr>
                <td align = "left" class = "menubackgr" colspan = "3">
                    <script language = "Javascript1.2">
                        <!--
                        function printpage()
                        {
                            window.print();
                        }
                        //-->
                    </script>

                    <input type = "Button" name = "Print" value = "Print" onClick = "printpage();"><input type = "Button" name = "Close" value = "Close" onClick = "self.close();">
                </td>
            </tr>
        </table>
    </body>
</html>