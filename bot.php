<?php
$access_token = 'NENB7H4HyQxHVCl8OJ94uvbss5SOxzlTNYPk02k+BuzBjG3OczD2x7rDlXgfjR9VAr3FJqIdK8GoKzbsNAiDfQ6NWVPy+JCYNhjZ/5zyt2H+4RHcDvtHNE5JDS27CRHsAyS5El5uVBXYds2s76MeRAdB04t89/1O/w1cDnyilFU=';
//Connect DB
$dbconn = pg_connect("host=ec2-107-22-252-91.compute-1.amazonaws.com port=5432 dbname=d1t089mnl00iir user=feajajzganbfiq password=57ba34efa8018b168b1edbdd5849b55f67c2a8a1f48e644a1e1fc6e951d9517a");
//$result = pg_query($dbconn, "SELECT * FROM KNOW");
$result = pg_exec($dbconn, "SELECT 1 as one
union
select 2 ");
$numrows = pg_numrows($result);
//var_dump(pg_fetch_all($result));
//connect to a database named "mary" on the host "sheep" with a username and password

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
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
			// Get replyToken
			$replyToken = $event['replyToken'];


			$userId = $event['source']['userId'];
			$id = $event['message']['id'];



			// Build message to reply back
			$messages = [
				'type' => 'text',
				'text' => 'Test16 '.$text.' Reply='.$replyToken.' user='.$userId.' id='.$id.'rows = '.$numrows
			];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
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
		
		if ($event['type'] == 'message' && $event['message']['type'] == 'sticker') 		
		{
		// Get text sent
			//$text = $event['message']['text'];
			// Get replyToken
			$replyToken = $event['replyToken'];


			$userId = $event['source']['userId'];
			$id = $event['message']['id'];



			// Build message to reply back
			$messages = [
				'type' => 'sticker',
				'packageId' => '1',
				'stickerId' => '3'
			];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
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
	}
}
echo "OK";