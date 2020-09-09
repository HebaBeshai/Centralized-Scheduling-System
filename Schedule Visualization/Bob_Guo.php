

<?php
session_start();

$host = '10.200.78.247';
$dbname = 'wp';
$user = 'class';
$password = 'software331';

// passwords, etc. are hidden from the WWW
//require_once('configBD.php');

//Connect to the database*****//
$connection = new mysqli($host, $user, $password, $dbname)
or die ("Error: could not connect to host ".$connection->errno);
function PrintTable(mysqli $connection, $dept, $num )
{
   $weekString="',Monday,Tuesday,Wednesday,Thursday,Friday";
   $weekString1="M,T,W,R,F";
   $weekArray=explode(",",$weekString);
   $weekArray1=explode(",",$weekString1);
   echo "<h1>$dept $num</h1>";
   echo "<table border='1'>";
   echo "<tr>";
   for($i=0;$i<6;$i++){
       echo "<th>{$weekArray[$i]}</th>";
   }
   echo"</tr>";
   for($i=8;$i<18;++$i){
       echo "<tr><td>$i:00</td>";
       $j=$i+1;
       $query="SELECT * FROM sections WHERE Department='$dept' AND Number='$num' AND `Start Time`<'$j:00' AND `End Time`>'$i:00'";
       $res=$connection->query($query);
       if($res->num_rows==0){
           //Error

       }
       $arr=$res->fetch_all( MYSQLI_ASSOC);
       $count = count($arr);

       for($col=0;$col<5;$col++){
           echo "<td>";
           for($k=0;$k<$count;$k++){
               if(strpos($arr[$k]['Days'],$weekArray1[$col])===false){}
               else{
                   echo "{$arr[$k]['Department']}, {$arr[$k]['Number']}<br>";
                   echo "{$arr[$k]['Name']}<br>";
                   echo "{$arr[$k]['Times']}<br>";
               }
           }
           echo "</td>";
       }
       echo "</tr>";

   }
   echo "</table>";
}

?>

<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <title>Exploring contactList Database</title>
   <style>
       table {
           border: 1px solid powderblue;
       }

       th, td {
           padding: 15px;
       }

       th {
           color: red;
           font: 20px arial, sans-serif;
       }

       h1 {
           color: blue;
       }

       select {
           padding: 5px;
       }
   </style>
</head>

<body>
<center>

   <?php
   // determine if this is the first page or the refresh page
   if(!(isset($_POST['crn']) && $_POST['crn'] != "")){
       //Query database*********//
       // produce some HTML code

       if ($_SESSION['page2']) {echo "Please Enter New Course";} else {echo "<h1>Please Enter Course</h1>";}
       ?>

       <form method = 'post' >
           <table>
               <tr><td> CRN::</td><td><input type='text' name='crn'></td></tr>
           </table>
           <input type='submit'>
       </form>

       <?php

   } else {
       // must be the refresh page
       $crn=$_POST["crn"];

       $query="SELECT Department, Number, Name FROM sections WHERE CRN='$crn'";
       $res=$connection->query($query);
       if($res->num_rows!=1){
           //Error

       }
       $r=$res->fetch_assoc();
       $dept=$r["Department"];
       $num=$r["Number"];

       if ($_SESSION['page2']){

           $crn_old=$_SESSION['CRN_OLD'];
           $dept_old=$_SESSION['Department_OLD'];
           $num_old=$_SESSION['Number_OLD'];
           $query="SELECT COUNT(*) FROM `sections` AS tb1, `sections` AS tb2 
           WHERE tb1.CRN=$crn AND tb2.`CRN`=$crn_old AND (tb1.`Start Time`<=tb2.`Start Time` AND tb1.`End Time`>=tb2.`Start Time`
           OR tb1.`Start Time`<=tb2.`End Time` AND tb1.`End Time`>=tb2.`End Time`
           OR tb2.`Start Time`<=tb1.`Start Time` AND tb2.`End Time`>=tb1.`Start Time`)";
           $res=$connection->query($query);
           if($res->num_rows!=0){
               echo"<h1>Time conflict</h1>";
               if($_SESSION['page2']){
                   PrintTable($connection,$dept_old, $num_old);
               }
           }
           session_destroy();
       }
       else{
           $_SESSION['CRN_OLD']=$crn;
           $_SESSION['Department_OLD']=$dept;
           $_SESSION['Number_OLD']=$num;

       }
       PrintTable($connection,$dept, $num);

       //$days=$_POST["days"];
       //$startTime=$_POST["startTime"];
       //$endTime=$_POST["endTime"];


       //Detect time conflict

       //first line WHERE check $startTime in [Start Time, End Time] and $endTime in [Start Time, End Time]
       //second line check Start Time in [$startTime, $endTime] and End Time in [$startTime, $endTime]



       $_SESSION['page2']=true;
       echo "<br><br>";
       echo "<form method = 'post'>";
       echo " <button type = 'submit' >Go Back</button> ";
       echo "</body>";
       //} else{
       //    echo "<h1>No time conflict</h1>";
       //}

       //    // Close $stmt

       //        $stmt->close();
   }
   ?>

</center>
</body>
</html>
