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
			
			if(1=0)
			{
				$check_user = 'SELECT * FROM "TOS"."TOKEN"; WHERE "STATUS"='."'"."OK"."'".' AND "TOKEN" = '."'".$userX."'";
				$result = pg_exec($dbconn, $check_user);
				$numrows = pg_numrows($result);
				if($numrows <= 0)
				{
					
					// $check_user = 'SELECT * FROM "TOS"."TOKEN"; WHERE "TOKEN" = '."'".$userX."'";
					// $result = pg_exec($dbconn, $check_user);
					// $numrows = pg_numrows($result);
					// if($numrows <= 0)
					// {
						// $messages = [
								// 'type' => 'text',			
								// 'text' => 'ไม่มีผู้ใช้นี้ และระบบได้เพิ่มให้แล้วกรุณาให้ admin อนุมัติ'
								// ];
								// $messagesX[0] = $messages;
								
								// $insert_newuser = 'INSERT INTO "TOS"."TOKEN"("TOKEN", "STATUS") VALUES ('."'".$userX."'".','."'"."'".')';
								// $result = pg_exec($dbconn, $insert_newuser);
					// }
					// else
					// {
						// $messages = [
								// 'type' => 'text',			
								// 'text' => 'ผู้ใช้ยังไม่ได้รับอณุญาติ'
								// ];
								// $messagesX[0] = $messages;
					// }								
				// }
				
				
			}
			else
			{
				$messages = [
				'type' => 'text',			
				'text' => 'สอบถามวันที่ 01/01 เวลา 02:50 โดย:'.$userX
				];
				$messagesX[0] = $messages;
			}
			
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

function _resultMSG($text, $dbconn, $event, $access_token)
{
	
	$replyToken = $event['replyToken'];
	$userId = $event['source']['userId'];
	$userX = $event['source']['userId'];
	$id = $event['message']['id'];
	
	
	
	$chk_access_loop = 'SELECT GA.*, GR."ID"
						FROM "GROUP_ANSWER" GA
						INNER JOIN "ACTORvsGROUP_ANSWER" GR ON GR."GROUP_ANSWER_ID" = GA."ID"
						INNER JOIN "ACTOR" AC ON GR."ACTOR_ID" = AC."ID"
						WHERE AC."USER_ID" = '."'".$userX."'".'
						AND GA."QUESTION_START_GROUP" = '."'".$text."'".'';
	$result_grp = pg_exec($dbconn, $chk_access_loop);
	$numrows_grp = pg_numrows($result_grp);
	
	if($numrows_grp > 0)
	{
		$delete_old_loop = 'DELETE FROM "ACTIVE_LOOP"
							WHERE "ID" IN(SELECT LP."ID"
							FROM "ACTIVE_LOOP" LP
							INNER JOIN "ACTORvsGROUP_ANSWER" GR ON LP."ACTORvsGROUP_ANSWER_ID" = GR."ID"
							INNER JOIN "ACTOR" AC ON GR."ACTOR_ID" = AC."ID"
							WHERE AC."USER_ID" = '."'".$userX."'".')';	
		$result = pg_exec($dbconn, $delete_old_loop);
		
		$id_ans_grp = pg_fetch_result($result_grp, 0, 4);
		
		
		
		$return = pg_fetch_result($result_grp, 0, 3);		
		$messages = [
		'type' => 'text',			
		'text' => 'FU R13='.$return
		];
		$messagesX[0] = $messages;
		$numrows = 1;
	}
	else
	{		
		$return = 'ไม่มีสิทธิ์ในการใช้งานระบบนี้ หรือไม่มีระบบนี้ให้ใช้แล้ว';
		
		$messages = [
		'type' => 'text',			
		'text' => 'FU R13='.$return
		];
		$messagesX[0] = $messages;
		$numrows = 1;
	}
		
	
	// $know = 'SELECT * FROM "KNOW" WHERE LOWER("FACTOR") like ';
	// $know = $know."LOWER('%".$text."%')";
	// $result = pg_exec($dbconn, $know );				
	// $numrows = pg_numrows($result);
	// $return = '';
	
	// $messagesX = array($numrows+1);				
	// $retMsg = 0;				
	// if($numrows > 0)
	// {
		// while ($row = pg_fetch_row($result)) 
		// {					
			// $return = 'JOB='.$row[1].' '.$row[2].'; ';
			// $messages = [
			// 'type' => 'text',			
			// 'text' => $return
			// ];

			// $messagesX[$retMsg] = $messages;
			// $retMsg++;
		// }
	// }
	// else
	// {
		// $return = 'ไม่มีผลลัพธ์ที่ต้องการ';
		
		// $messages = [
		// 'type' => 'text',			
		// 'text' => 'FU R13='.$return." ".$delete_old_loop
		// ];
		
		// $messagesX[0] = $messages;
		// $numrows = 1;
	// }
	
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
			'text' => 'FU R13='.$return." ไปยัง ".$cmd_to." ด้วยคำสั่ง ".$cmd_str
			];
			$messagesX[0] = $messages;
			$numrows = 1;
		}
		else
		{
			$return = 'ไม่มีข้อมูลฐานข้อมูล '.$cmd_to;
			$messages = [
			'type' => 'text',			
			'text' => 'FU R13='.$return
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


function _resultMSG_BK1($text, $dbconn, $event, $access_token)
{
	
	$know = 'SELECT * FROM "KNOW" WHERE LOWER("FACTOR") like ';
	$know = $know."LOWER('%".$text."%')";
	$result = pg_exec($dbconn, $know );				
	$numrows = pg_numrows($result);
	$return = '';
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
		'text' => 'FU R13='.$return
		];
		
		$messagesX[0] = $messages;
		$numrows = 1;
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


echo "OK2";