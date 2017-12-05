<?php
$access_token = 'mLSwGrC8Du7SCoky4y2lpyuGn9zTaf6pyOlc1sy8G69dcZ7b4Dq7b6IVfPz009P52SRjiJj0jUdRg/1W8VljdQQo/SrxkAPYCXxIDui+gXdj2vPKjLnUYItkw6PFY3oqjompHNgaL2EqLuFza3HT1wdB04t89/1O/w1cDnyilFU=';
//Connect DB
$dbconn = pg_connect("host=ec2-107-22-252-91.compute-1.amazonaws.com port=5432 dbname=d1t089mnl00iir user=feajajzganbfiq password=57ba34efa8018b168b1edbdd5849b55f67c2a8a1f48e644a1e1fc6e951d9517a");
//$result = pg_query($dbconn, "SELECT * FROM KNOW");

//var_dump(pg_fetch_all($result));
//connect to a database named "mary" on the host "sheep" with a username and password

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

$getResult = '';
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) 
	{
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') 
		{
			// Get text sent
			$text = $event['message']['text'];
			$numrows = 0;
			$messagesX = array(1);			
			// Get replyToken				
			$replyToken = $event['replyToken'];
			$userId = $event['source']['userId'];
			$userX = $event['source']['userId'];
			$id = $event['message']['id'];
		
			if(1==0)
			{
				
			}
			else
			{
				// $messages = [
				// 'type' => 'text',			
				// 'text' => 'สอบถามวันที่ 01/01 เวลา 02:50 โดย:'.$userX
				// ];
				// $messagesX[0] = $messages;
			}
			
			$messages = [
				'type' => 'text',			
				'text' => 'R1 สอบถามวันที่ 01/01 เวลา 02:50 โดย:'.$userX
				];
				$messagesX[0] = $messages;
			
			_sendOut($access_token, $replyToken, $messagesX);
				
		}	
	}
}

function _sendOut($access_token, $replyToken, $messagesX)
{
	$url = 'https://api.line.me/v2/bot/message/reply';
	$data = [
		'replyToken' => $replyToken,
		'messages' => $messagesX,
	];
	
	$post = json_encode($data);
	$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$result = curl_exec($ch);
	curl_close($ch);

	echo $result . "\r\n";
}


echo "OK2";