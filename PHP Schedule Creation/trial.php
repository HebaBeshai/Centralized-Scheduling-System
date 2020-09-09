<?php


session_start();

$host = '10.200.78.247';
$dbname  = 'wp';
$user = 'class';
$password = 'software331';
//phpinfo();
//*********Connect to the database**************//
$connection = new mysqli($host, $user, $password, $dbname)
or die ("Error: could not connect to host ".$connection->errno);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Selecting Classes</title>
    <style>
        table  {border: 1px solid powderblue;}
        th, td {padding: 15px;}
        th     {color: red; font: 20px arial, sans-serif;}
        h1     {color: blue;}
        select {padding: 5px;}
    </style>
</head>

<body>
<center>
    <?php
    // determines if this is the first page or the refresh page
    if(!(isset($_POST['num']) && $_POST['num'] != "")){

        // produce some HTML code
        echo "<h1>Please Enter Course</h1>";
        echo "    <form method = 'post' >";
        echo "    Department Abbreviation: <input type=\"text\" name=\"dept\"><br>";
        echo "	  Course Number: <input type=\"text\" name=\"num\"><br>";
        echo "<input type=\"submit\">";
        echo "</form>";

    } else { // must be the refresh page

        $dept=$_POST["dept"];
        $num=$_POST["num"];

        echo "<h1>$dept $num</h1>";

        $query = "SELECT sections.Department, sections.Number, sections.Name, sections.Days, sections.Times, sections.Professor 
                FROM sections WHERE sections.Department LIKE '$dept' AND sections.Number LIKE '$num%'";
        $stmt  = $connection->prepare($query);
        $stmt->execute();
        $meta   = $stmt->result_metadata();  // need metadata to create HTML table

        // Start HTML table and header row.
        echo "<table border=1> <tr>";

        // Make first row of HTML table using column names
        echo "<th></th>";
        while( $field = $meta->fetch_field() ){

            // Get field name and echo within header tags.
            echo "<th>". $field->name . "</th>";
            // create an array of field names for later use
            $params[] = &$row[$field->name];
        }

        // apply the bind_result method to each field name in $params
        call_user_func_array(array($stmt, 'bind_result'), $params);

        // Fetch results row by row and create table entries
        echo "<form>";
        while ( $stmt->fetch() ){
            // create table row tags
            echo "<tr>";
            echo "<td><input type = 'radio' name='action'></td><br>";
            foreach($row as $key => $val){
                echo "<td>".$val."</td>";
            }
            echo "</input>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</form>";

        // Close $stmt
        $stmt->close();

        //SUBMIT BUTTON - INSERT BLAKES SCRIPT HERE
        echo "<form action='script.php'>";
        echo "<button type = 'submit'>Add to schedule</button>";
        echo "</form>";


        // add a Back button
        $_POST['page2'] = 'false';  // reset variable so Back button works right
        echo "<br><br>";
        echo "<form method = 'post'>";
        echo "   <button type = 'submit' >Go Back</button>";
        echo "</body>";
    }
    ?>
</center>
</BODY>
</HTML>