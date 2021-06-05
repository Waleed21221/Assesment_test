<?php

if(!isset($_SERVER[ 'PHP_AUTH_USER' ])){
    header("WWW-Authenticate: Basic realm=\"Authentication\"");
    header("HTTP/1.0 401 Unauthorized");
    echo "Sorry, You are not Authorized";
    exit;
}

else{
    if(($_SERVER['PHP_AUTH_USER'] == 'waleed' && ($_SERVER['PHP_AUTH_PW'] == 'waleed123'))){

$Servername='localhost';
$Userrname='root';
$Password='';
$db_name='mydb2';
$Dst_Servername='localhost';
$Dst_Username='root';
$Dst_Password='';

if(isset($_GET['db'])){
$Dst_db_Name= $_GET['db'];
// print_r($Dst_db_Name);
// die();
}

$db1 = new mysqli ($Servername,$Userrname,$Password) or die($db1->error);
mysqli_select_db($db1,$db_name) or die($db1->error);
$result = mysqli_query($db1,"SHOW TABLES;") or die($db1->error);
$buf="set foreign_key_checks = 0;\n";
$constraints='';
while($row = mysqli_fetch_array($result))
{
    $result2 = mysqli_query($db1,"SHOW CREATE TABLE ".$row[0].";") or die($db1->error);
    $res = mysqli_fetch_array($result2);
    if(preg_match("/[ ]*CONSTRAINT[ ]+.*\n/",$res[1],$matches))
    {
        $res[1] = preg_replace("/,\n[ ]*CONSTRAINT[ ]+.*\n/","\n",$res[1]);
        $constraints.="ALTER TABLE ".$row[0]." ADD ".trim($matches[0]).";\n";
    }
    $buf.=$res[1].";\n";
}
$buf.=$constraints;
$buf.="set foreign_key_checks = 1";

//Copy DB Schema and made another one 
$db2 = new mysqli($Dst_Servername,$Dst_Username,$Dst_Password) or die($db2->error);
$sql = 'CREATE DATABASE '.$Dst_db_Name;
if(!mysqli_query($db2,$sql)) die($db2->error);
mysqli_select_db($db2,$Dst_db_Name) or die($db2->error);
$queries = explode(';',$buf);
foreach($queries as $query)
{
    if(!mysqli_query($db2,$query)) die($db2->error);
}
	echo 'Database created with name '.$Dst_db_Name.'. Make sure to check your Database';
    }

    else{
        header("WWW-Authenticate: Basic realm=\"Private Area\"");
        header("HTTP/1.0 401 Unauthorized");
        echo "Wrong User Name Or Password";
    }
}

?>
