<?php
    require_once("sqlConnect.php");
    $pageID = null;
    if(isset($_GET['pageID']))
    {
        $pageID = $_GET['pageID'];
    }
    else
    {
        $pageID = 0;
    }
    
    
    $pageName = GetPageName($pageID);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Spaghetti - Webdev with a death wish
        </title>
    </head>
    <body>
        <?php
            echo '<h1 id="pageHeader" class="centered header">' . $pageName .'</h1>';
        ?>
    </body>
    <footer>

    </footer>
</html>