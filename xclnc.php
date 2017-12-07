<?php
$access_token = 'OrV9Mpp37NNZ4Mbi05Cn59nEGNlUrCMieKnqCW8+Tw9HS2OMT5mcErUTFfKmlcVkDWI4ioax5+j6huC/d9Nqk9Ptutv9RYODpgMU+Xn3kwnSCWM9KnJBbKB3sZafoelT4PJq+HT68JufP8RVG9pCegdB04t89/1O/w1cDnyilFU=';
$dbconn = pg_connect("host=ec2-107-22-252-91.compute-1.amazonaws.com port=5432 dbname=d1t089mnl00iir user=feajajzganbfiq password=57ba34efa8018b168b1edbdd5849b55f67c2a8a1f48e644a1e1fc6e951d9517a");
$content = file_get_contents('php://input');
$events = json_decode($content, true);

$getResult = '';
if (!is_null($events['events'])) {
	$okreturn = 0;
	foreach ($events['events'] as $event) 
	{
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') 
		{
			
			$return = '';
				
			$replyToken = $event['replyToken'];
			$userId = $event['source']['userId'];
			$userX = $event['source']['userId'];
			$id = $event['message']['id'];

			$text = $event['message']['text'];
			$numrows = 0;
			$messagesX = array(1);
			
			
			if (strpos(strtoupper($text), 'XQUERY'))//Case พิเศษสำหรับ XQuery XCLNC
			{
				$getResult = "";
				$getResult = _resultXQUERY($text, $dbconn, $event, $access_token);
			}
			elseif(strpos(strtoupper($text), 'ALL POS'))
			{
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
				'text' => 'FU R17='. $returnonline
				];
				$messagesX[0] = $messages;
				
				_sendOut($access_token, $replyToken, $messagesX);
			}
			else
			{
				$messages = [
				'type' => 'text',			
				'text' => 'FU R17='
				];
				$messagesX[0] = $messages;
				
				_sendOut($access_token, $replyToken, $messagesX);
			}
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

function _resultXQUERY($text, $dbconn, $event, $access_token)
{
	$getResult = '';
	
	$replyToken = $event['replyToken'];
	$userId = $event['source']['userId'];
	$userX = $event['source']['userId'];
	$id = $event['message']['id'];
	
	$whereQ = 'xquery';
	$chk_access_loop = 'SELECT GA.*, GR."ID"
						FROM "GROUP_ANSWER" GA
						INNER JOIN "ACTORvsGROUP_ANSWER" GR ON GR."GROUP_ANSWER_ID" = GA."ID"
						INNER JOIN "ACTOR" AC ON GR."ACTOR_ID" = AC."ID"
						WHERE AC."USER_ID" = '."'".$userX."'".'
						AND GA."QUESTION_START_GROUP" = '."'".$whereQ."'".'';
	$result_grp = pg_exec($dbconn, $chk_access_loop);
    $numrows_grp = pg_numrows($result_grp);
	$messagesX = array(1);
	
	if($numrows_grp > 0)
	{
		$cmd_sp = explode("XQUERY", strtoupper($text));
		$cmd_to = $cmd_sp [0];
		$cmd_str = $cmd_sp [1];
		$cmd_to = trim($cmd_to);
		
		
		$check_user = 'SELECT * FROM public."QUERY_TOKEN" WHERE "TOKEN" = '."'".$cmd_to."'";
		$result_touser = pg_exec($dbconn, $check_user);
		$numrows_touser = pg_numrows($result_touser);
		
		if($numrows_touser > 0)
		{	
			$ins_cmd = 'INSERT INTO public."QUERY_CMD"("FORM_TOKEN", "TO_TOKEN_CLIENT_ID", "CMD_REQUEST") VALUES ('."'".$access_token."'".', '."'".$cmd_to."'".', '."'".$cmd_str."'".');';
			$result_ins_cmd = pg_exec($dbconn, $ins_cmd);
	
			$return = pg_fetch_result($result_grp, 0, 3);
			$messages = [
			'type' => 'text',			
			'text' => 'FU R17='.$return." ไปยัง ".$cmd_to." ด้วยคำสั่ง ".$cmd_str
			];
			$messagesX[0] = $messages;
			$numrows = 1;
		}
		else
		{
			$return = 'ไม่มีข้อมูลฐานข้อมูล '.$cmd_to;
			$messages = [
			'type' => 'text',			
			'text' => 'FU R17='.$return
			];
			$messagesX[0] = $messages;
			$numrows = 1;
		}
	}
	
	if(1==1)
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
	$getResult = "OK ".$access_token;
	
	
	return $getResult;
}



echo "OK";