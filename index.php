<?php
require_once('require/classes/MySqlManager.php');

$sqlManager = new MySqlManager('db_name');

/* Example SP call */
//$consulta = "CALL test()";

/* Example call*/
$query = "SELECT * from users where user = ?";

/* Example data*/
$data = array(); 

$data[] = array(
    "type"=>"s", //tipo de valor
    "value"=>'example_user' //valor a reemplazar en el '?'
);

$response = $sqlManager->executeQuery($query,$data);

echo "<pre>";
print_r( $response);

?>