<?php
$access_token = 'NENB7H4HyQxHVCl8OJ94uvbss5SOxzlTNYPk02k+BuzBjG3OczD2x7rDlXgfjR9VAr3FJqIdK8GoKzbsNAiDfQ6NWVPy+JCYNhjZ/5zyt2H+4RHcDvtHNE5JDS27CRHsAyS5El5uVBXYds2s76MeRAdB04t89/1O/w1cDnyilFU=';
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
	$okreturn = 0;
	foreach ($events['events'] as $event) 
	{
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') 
		{
			// Get text sent
			$text = $event['message']['text'];
			$numrows = 0;
			$messagesX = array(1);
			
			
			 if (strpos($text, 'all pos') !== false)
			 {
				 $okreturn = 1;
				 
				$know = 'SELECT "TOKEN"||'."' IN='".'||cast(cast(EXTRACT(EPOCH FROM age(clock_timestamp(), "LAST_UPDATE_DATE"))/60 as bigint) as text)||'."'นาที'".' as LAST_ONLINE FROM public."QUERY_TOKEN" ORDER BY age(clock_timestamp(), "LAST_UPDATE_DATE")';
				//$know = $know."LOWER('%".$text."%')";
				$result = pg_exec($dbconn, $know );				
				$numrows = pg_numrows($result);
				
				$return = '';
				
				// Get replyToken
				$replyToken = $event['replyToken'];
				$userId = $event['source']['userId'];
				$userX = $event['source']['userId'];
				$id = $event['message']['id'];
				
				
				$returnonline = '';

				while ($row = pg_fetch_row($result)) 
				{					
					$returnonline = $returnonline.$row[0]."\r\n";					
				}
			
				$messages = [
				'type' => 'text',			
				'text' => $returnonline
				];
				$messagesX[0] = $messages;
			}
			elseif(strpos($text, 'online pos') !== false)
			{
				$okreturn = 1;
				
				$know = 'SELECT "TOKEN"||'."' IN='".'||cast(cast(EXTRACT(EPOCH FROM age(clock_timestamp(), "LAST_UPDATE_DATE"))/60 as bigint) as text)||'."'นาที'".' as LAST_ONLINE FROM public."QUERY_TOKEN" WHERE cast(EXTRACT(EPOCH FROM age(clock_timestamp(), "LAST_UPDATE_DATE"))/60 as bigint) < 5 ORDER BY age(clock_timestamp(), "LAST_UPDATE_DATE")';
				//$know = $know."LOWER('%".$text."%')";
				$result = pg_exec($dbconn, $know );				
				$numrows = pg_numrows($result);
				
				$return = '';
				
				// Get replyToken
				$replyToken = $event['replyToken'];
				$userId = $event['source']['userId'];
				$userX = $event['source']['userId'];
				$id = $event['message']['id'];
				
				if($numrows > 0)
				{
					$returnonline = '';

					while ($row = pg_fetch_row($result)) 
					{					
						$returnonline = $returnonline.$row[0]."\r\n";					
					}
				
					$messages = [
					'type' => 'text',			
					'text' => $returnonline
					];
					$messagesX[0] = $messages;
				}
				else
				{
					$messages = [
					'type' => 'text',			
					'text' => 'ไม่มีข้อมูล POS Online ใน 5 นาทีนี้'
					];
					$messagesX[0] = $messages;
				}
			}
			elseif(strpos(strtoupper($text), 'JOB') !== false)
			{	
				$okreturn = 1;
				$text = strtoupper($text);
				$text = str_replace("JOB","",$text);
				$know = 'SELECT * FROM "KNOW" WHERE LOWER("FACTOR") like ';
				$know = $know."LOWER('%".$text."%')";
				$result = pg_exec($dbconn, $know );				
				$numrows = pg_numrows($result);
				
				$return = '';
				
				// Get replyToken
				$replyToken = $event['replyToken'];
				$userId = $event['source']['userId'];
				$userX = $event['source']['userId'];
				$id = $event['message']['id'];



				$messagesX = array($numrows+1);				
				$retMsg = 0;
				
				if($numrows > 0)
				{
					while ($row = pg_fetch_row($result)) 
					{					
						$return = 'JOB='.$row[1].' '.$row[2].'; ';
						$messages = [
						'type' => 'text',			
						'text' => $return
						];

						$messagesX[$retMsg] = $messages;
						$retMsg++;
					}
				}
				else
				{
					$return = 'ไม่มีผลลัพธ์ที่ต้องการ';
					
					$messages = [
					'type' => 'text',			
					'text' => 'R36='.$return
					];
					
					$messagesX[0] = $messages;
					$numrows = 1;
				}
				// Build message to reply back
			}
			else
			{
				$getResult = "";
				$getResult = _resultMSG();
				
				$okreturn = 0;
				
				if($getResult == "OK")
				{					
					$return = '';								
					$replyToken = $event['replyToken'];
					$userId = $event['source']['userId'];
					$userX = $event['source']['userId'];
					$id = $event['message']['id'];


					{
						$return = 'ไม่มีผลลัพธ์ที่ต้องการ';
						
						$messages = [
						'type' => 'text',			
						'text' => 'R36='.$return.'  '.$getResult
						];
						
						$messagesX[0] = $messages;
						$numrows = 1;
					}
					
					if(1==1)
					{
						// Make a POST Request to Messaging API to reply to sender
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
				
				
				}
				
				//_resultMSG($text, $dbconn, $event);
			}
			
			
			
			
			
			
			
			if(1==0)
			{
				$messages = [
				'type' => 'text',			
				'text' => 'สอบถามวันที่ 01/01 เวลา 02:50 โดย:'.$userX
				];
				$messagesX[$numrows] = $messages;
			}
			
			
			if($okreturn ==1)
			{
				// Make a POST Request to Messaging API to reply to sender
				$url = 'https://api.line.me/v2/bot/message/reply';
				/*
				$data = [
					'replyToken' => $replyToken,
					'messages' => [$messages, $messages],
				];
				*/
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

//function _resultMSG($text, $dbconn, $event)
function _resultMSG()
{
	$getResult = "STEP 1";
	
	$know = 'SELECT * FROM "KNOW" WHERE LOWER("FACTOR") like ';
	$know = $know."LOWER('%".$text."%')";
	$result = pg_exec($dbconn, $know );				
	$numrows = pg_numrows($result);
	
	$return = '';
	
	// Get replyToken
	$replyToken = $event['replyToken'];
	$userId = $event['source']['userId'];
	$userX = $event['source']['userId'];
	$id = $event['message']['id'];
	
	
	$getResult = "STEP 2";



	$messagesX = array($numrows+1);				
	$retMsg = 0;				
	
	
	$getResult = "STEP 3";
	
	
	if($numrows > 0)
	{
		while ($row = pg_fetch_row($result)) 
		{					
			$return = 'JOB='.$row[1].' '.$row[2].'; ';
			$messages = [
			'type' => 'text',			
			'text' => $return
			];

			$messagesX[$retMsg] = $messages;
			$retMsg++;
		}
	}
	else
	{
		$return = 'ไม่มีผลลัพธ์ที่ต้องการ';
		
		$messages = [
		'type' => 'text',			
		'text' => 'FU R36='.$return
		];
		
		$messagesX[0] = $messages;
		$numrows = 1;
	}
	
	if(1==1)
	{
		// Make a POST Request to Messaging API to reply to sender
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
	
	$getResult = "OK";
	return $getResult;
}


echo "OK";