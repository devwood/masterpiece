<?php
// $jobid=$_GET["job"];
// $dbconn = pg_connect("host=ec2-107-22-252-91.compute-1.amazonaws.com port=5432 dbname=d1t089mnl00iir user=feajajzganbfiq password=57ba34efa8018b168b1edbdd5849b55f67c2a8a1f48e644a1e1fc6e951d9517a");

// // $servername = "163.44.198.39";
// // $username = "cp572795";
// // $password = "1022@Mine";
// // $dbx = "cp572795_LINEQUERY_TOS";
// // $conn = new mysqli($servername, $username, $password, $dbx);
// // mysqli_set_charset($conn,"utf8");

// // $check_job = "select * from JOB_DETAIL where SALE_ORDER = '".$jobid."'";
// // $result = $conn->query($check_job);	

// // $rows = array();
// // while($r = mysqli_fetch_assoc($result)) {
    // // $rows[] = $r;
// // }

// // $myJSON = json_encode($rows);




// $check_module = 'SELECT "TOKEN", "NAME" FROM "QUERY_TOKEN"';
// $result = pg_exec($dbconn, $check_module);

// $rows = array();
// while($r = pg_fetch_row($result)) {
    // $rows[] = $r;
// }


// $myJSON = json_encode($rows);

//echo $myJSON;
// echo '55';

echo '{
   "timex": "02:48:37 PM",
   "milliseconds_since_epoch": 1519224517177,
   "date": "02-21-2018"
}';
?>