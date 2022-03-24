<?php
    (String)$SQL_HOST = "localhost";
    (String)$SQL_USERNAME = "root"; //This is okay to publish to git because its just a local server
    (String)$SQL_PASSWORD = "localuserpass"; //same with this

    $DEFAULT_DB = "LocalWeb";

    function Connect(): mysqli
    {
        global $SQL_HOST;
        global $SQL_USERNAME;
        global $SQL_PASSWORD;
        global $DEFAULT_DB;
        $mysql = new mysqli($SQL_HOST, $SQL_USERNAME, $SQL_PASSWORD,$DEFAULT_DB);
        if ($mysql -> connect_errno) {
            echo "Failed to connect to MySQL: " . $mysql -> connect_error;
            exit();
          }
        return $mysql;
    }

    function CheckSetup(): bool
    {
        $mysql = Connect();
        $res = true;
        try{
            $q = $mysql->query('SELECT settingValue FROM `settings` WHERE settingName="isSetup";');
            $row = $q->fetch_assoc();
            $res = boolval($row['settingValue']);
        }
        catch(mysqli_sql_exception)
        {
            echo 'Setting up page';
            $res = false;
        }
        if($res == false)
        {
            /*Create Tables*/
            $res = $mysql->query("CREATE TABLE IF NOT EXISTS settings(id int NOT NULL AUTO_INCREMENT, settingName varchar(255), settingValue varchar(4096), PRIMARY KEY (id));");
            $res = $mysql->query("CREATE TABLE IF NOT EXISTS users(id int NOT NULL AUTO_INCREMENT, username varchar(255), passhash varchar(4096),roleID int, PRIMARY KEY (id));");
            $res = $mysql->query("CREATE TABLE IF NOT EXISTS pages(id int NOT NULL AUTO_INCREMENT, pageName varchar(255), pageHTML TEXT(1000000),authorID int, PRIMARY KEY (id));");

            /*Create default objects*/
            $res = $mysql->query('INSERT INTO settings(settingName, settingValue) VALUES("isSetup","1");');
            if(!$res)
            {
                echo "Failed to create settings in database... Err: " . $mysql->error_get_last;
                exit();
            }
            global $ERR_PAGE_ID;
            $res = $mysql->query('INSERT INTO users(username, passhash) VALUES("admin","c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec");');
            $res = $mysql->query('INSERT INTO pages(pagename, pageHTML, authorID) VALUES("Oops - 404 Not found",
            "
            <h1 id=\"ErrHeader\" class=\"centered header\">This is properly not where you want to be...</h1>
            <p id=\"ErrText\" class=\"centered paragraph\"><a href=\"index.php\">Go back?</a></p>
            "
            ,"1")');
            $res = $mysql->query('SELECT id FROM pages WHERE pageName="Oops - 404 Not found";');
            while($row = $res->fetch_assoc())
            {
                $ERR_PAGE_ID = $row['id'];
            }
            $res = $mysql->query('INSERT INTO settings(settingName, settingValue) VALUES("errorPageID","' . $ERR_PAGE_ID .'");');
            $res = $mysql->query('INSERT INTO settings(settingName, settingValue) VALUES("frontPageID","2")');
            $res = $mysql->query('INSERT INTO pages(pageName, pageHTML, authorID) VALUES ("Front page ;\)",
            "
            <p class=\"centered paragraph\">Welcome my boi</p>
            "
            ,1);');
            echo '<h1>Page setup complete - Please Reload the page</h1>';
            exit();

        }
        $mysql->close();
        return true;
    }

    function CheckAndSetToken($username, $token)
    {
        //TODO
    }

    function GetSetting(String $SettingName): String
    {
        $res = SQL_ExecuteQuery('SELECT settingValue FROM settings WHERE settingName="' . $SettingName . '";');
        $row = $res->fetch_assoc();
        $res->close();
        return intval($row['settingValue']);
    }

    function SetSetting(String $settingName, String $settingVal): void
    {
        if(GetSetting($settingName) != "")
        {
            $res = SQL_ExecuteQuery('UPDATE settings SET settingValue="' . $settingVal . '" WHERE settingName="' . $settingName . '";');
            $res->close();
            return;
        }
        else
        {
            $res = SQL_ExecuteQuery('INSERT INTO settings(settingName, settingValue) VALUES("' . $settingName . '","' . $settingVal . '");');
            $res->close();
            return;
        }
    }
    function GetHashForUserByName(String $username): String
    {
        $res = SQL_ExecuteQuery('SELECT passhash FROM users WHERE username="' . $username . '";');
        $hash = $res->fetch_assoc()['passhash'];
        $res->close();
        return $hash;
    }
    function GetHashForUserByID(int $id): String
    {
        //TODO
        return "";
    }
    function GetUsernameByID(int $id): String
    {
        //TODO
        return "";
    }

    function GetErrorPageID(): int
    {
        $set = GetSetting("errorPageID");
        return intval($set);
    }

    function GetFrontPageID(): int
    {
        $set = GetSetting("frontPageID");
        return intval($set);
    }

    function GetPageName(int $id): String
    {
        $res = SQL_ExecuteQuery("SELECT pageName FROM pages WHERE id=" . $id . ";");
        $name = "";
        while($row = $res->fetch_assoc())
        {
            //hopefully only one with the given ID
            $name = $row['pageName'];
        }
        $res->close();
        if($name == "")
        {
            GetPageName(GetErrorPageID());
        }
        return $name;

    }

    function GetPageBody(int $id): String
    {
        $res = SQL_ExecuteQuery("SELECT id,pageHTML FROM pages WHERE id=" . $id . ";");
        $html_arr = $res->fetch_assoc();
        $html = "";
        if($html_arr != NULL)
        {
            $html = $html_arr['pageHTML'];
        }
        $res->close();
        if($html == "")
        {
            return GetPageBody(GetErrorPageID());
        }
        return $html;
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
?>