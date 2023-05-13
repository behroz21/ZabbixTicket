<?php

//  Sample execute link :
//  http://127.0.0.1/AddTicketZabbix.php?triggerid=23215&eventid=75



$url = "http://192.168.190.129/api_jsonrpc.php";
$UserName = 'Admin';
$Pas = 'zabbix';
$AuthKey = '';
$TriggerID = $_GET["triggerid"];
$ObjectID = '';
$EventID = $_GET["eventid"];
$MSGAck =  date("h:m:s",time());
$headers = array(
    "Accept: application/json, text/javascript, */*; q=0.01",
    "Content-Type: application/json-rpc",
 );

// Connect to Zabbix API for get AuthKey
$data = '{"method":"user.login","params":{"user":"'.$UserName.'","password":"'.$Pas.'"},"jsonrpc":"2.0","id":1}';
$ResultReqCon = Fcurl($url,$headers,$data);
//echo 'Detail Connect:<br><br>'.$ResultReqCon.'<br><br><br>';
$arrayCon = json_decode($ResultReqCon, true);
//print_r ($array[result][0][triggerid]);
$AuthKey = $arrayCon['result'];
echo '<b>AuthKey = </b>'.$AuthKey.'<br><br>';

// Get Detail of Trigger by send request to API Zabbix
$data ='{"method":"trigger.get","params":{"triggerids":"'.$TriggerID.'","monitored":true,"only_true":true,"expandDescription":true,"expandExpression":true,"selectHosts":"extend","selectGroups":"extend","selectTags":"extend","limit":100},"jsonrpc":"2.0","auth":"'.$AuthKey.'","id":1}';
$ResultReq = Fcurl($url,$headers,$data);
echo '<b>Detail Triger:</b><br>'.$ResultReq.'<br><br>';
$array = json_decode($ResultReq, true);
//print_r ($array[result][0][triggerid]);
echo '<b>triggerid =</b> '.$array['result'][0]['triggerid'];
echo '<b><br>lastchange =</b> '.date('Y-m-d H:i:s', $array['result'][0]['lastchange']);
echo '<b><br>description =</b> '.$array['result'][0]['description'];
echo '<b><br>priority =</b> '.$array['result'][0]['priority'];
echo '<b><br>name =</b> '.$array['result'][0]['hosts'][0]['host'];


// Send Message Ack for set Ticket Number on fired trigger
$data ='{"jsonrpc":"2.0","method":"event.acknowledge","params":{"eventids": "'.$EventID.'","action": 6,"message": "'.$MSGAck.'"},"auth":"'.$AuthKey.'","id":"1"}';
$ResultReq = Fcurl($url,$headers,$data);
echo '<b><br><br>Respons for request Ack Triger:</b><br>'.$ResultReq;

// Function for send request on URl
function Fcurl($url,$headers,$data) 
{  
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 2); //timeout in seconds

    $ResultCurl = curl_exec($curl);
    curl_close($curl);
    return $ResultCurl;
} 


?>
