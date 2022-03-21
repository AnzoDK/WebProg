<?php
    (String)$SQL_HOST = "localhost";
    (String)$SQL_USERNAME = "root"; //This is okay to publish to git because its just a local server
    (String)$SQL_PASSWORD = "localuserpass"; //same with this

    $DEFAULT_DB = "LocalWeb";

    function Connect(): mysqli
    {
        $mysql = new mysqli($SQL_HOST, $SQL_USERNAME, $SQL_PASSWORD,$DEFAULT_DB);
        return $mysql;
    }

    function CheckSetup(): bool
    {
        $mysql = Connect();
        $res = $mysql->query("CREATE TABLE IF NOT EXISTS users(id int, username varchar(255), passhash varchar(4096),roleID int);");
        $res = $mysql->query("CREATE TABLE IF NOT EXISTS pages(id int, pageName varchar(255), pageHTML varchar(1000000),authorID int);");
        $res->close();
        return true;
    }

    function GetPageName(int $id): String
    {
        $res = SQL_ExecuteQuery("SELECT pageName FROM pages WHERE id=" . $id);
        $res->close();
        while($row = $res->fetch_assoc())
        {
            //hopefully only one with the given ID
            return $row['pageName'];
        }

    }

    function SQL_ExecuteQuery(String $sql): mysqli_result
    {
        if(CheckSetup())
        {
            $mysql = Connect();
            $res = $mysql->query($sql);
            return $res;
        }
        else
        {
            die("Setup is not okay - and couldn't automatically be fixed");
        }
    }



        
    }
?>