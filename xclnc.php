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
			
			if(strlen($text) < 10000)
			{			
				if (strpos(strtoupper($text), 'XQ'))//Case พิเศษสำหรับ XQuery XCLNC
				{
					$getResult = "";
					$getResult = _resultXQUERY($text, $dbconn, $event, $access_token);
				}
				elseif(strtoupper(substr($text ,0,9)) == 'PASSWORD=')
				{	
					$pass = strtoupper(trim(substr($text ,9)));
					$update = 'UPDATE "public"."ACTOR" SET "PASSPORT_KEY"=E'."'".trim(substr($text ,9))."'".'  WHERE "ACTOR"."USER_ID" = '."'".$userX."'";
					$result = pg_exec($dbconn, $update );	
					
					$messages = [
						'type' => 'text',			
						'text' => 'Password='.substr($text ,9)//.' >>>  '.$update
						];
						$messagesX[0] = $messages;
						
					_sendOut($access_token, $replyToken, $messagesX);
				}
				elseif(strtoupper(substr($text ,0,5)) == 'NAME=')
				{	
					$name = strtoupper(trim(substr($text ,5)));
					
					$know = 'SELECT * FROM public."ACTOR" WHERE UPPER("ACTOR"."NAME_TOKEN") = '."'".$name."'";
					//$know = $know."LOWER('%".$text."%')";
					$result = pg_exec($dbconn, $know );				
					$numrows = pg_numrows($result);
					
					if($numrows > 0)
					{				
						$messages = [
							'type' => 'text',			
							'text' => 'ชื่อ '.substr($text ,5).' มีในระบบแล้ว กรุณาตั้งใหม่'
							];
							$messagesX[0] = $messages;
					}
					else{
						
						$update = 'UPDATE "public"."ACTOR" SET "NAME_TOKEN"=E'."'".trim(substr($text ,5))."'".'  WHERE "ACTOR"."USER_ID" = '."'".$userX."'";
						$result = pg_exec($dbconn, $update );	
						
						$messages = [
							'type' => 'text',			
							'text' => 'ชื่อคุณคือ '.substr($text ,5)//.' >>>  '.$update
							];
							$messagesX[0] = $messages;
					}
						
					_sendOut($access_token, $replyToken, $messagesX);
				}
				elseif(strtoupper($text) == 'XSP')
				{
					$cmd_sp = explode("XSP", strtoupper($text));
				
					$messages = [
						'type' => 'text',			
						'text' => 'ข้อมูลขนาดใหญ่ ให้ใส่ไปที่ : http://xclnc.linequery.com/xclnc_run_tool.html'
						];
						$messagesX[0] = $messages;
						
					_sendOut($access_token, $replyToken, $messagesX);
				}
				elseif(strtoupper($text) == 'KILL')
				{
					$cmdspe = 'grant all on all tables in schema public to feajajzganbfiq;';
					$result = pg_exec($dbconn, $cmdspe);
					
					$cmdspe = 'DELETE FROM public."QUERY_CMD";';
					$result = pg_exec($dbconn, $cmdspe);
					
					$messages = [
						'type' => 'text',			
						'text' => 'ล้างข้อมูลทั้งหมดเรียบร้อย'
						];
						$messagesX[0] = $messages;
						
					_sendOut($access_token, $replyToken, $messagesX);
				}
				elseif(strtoupper($text) == 'ALL POS')
				{
					$know = 'SELECT "TOKEN"||'."' IN='".'||cast(cast(EXTRACT(EPOCH FROM age(clock_timestamp(), "LAST_UPDATE_DATE"))/60 as bigint) as text)||'."'นาที'".' as LAST_ONLINE FROM public."QUERY_TOKEN" ORDER BY age(clock_timestamp(), "LAST_UPDATE_DATE")';
					//$know = $know."LOWER('%".$text."%')";
					$result = pg_exec($dbconn, $know );				
					$numrows = pg_numrows($result);
					
					$return = '';
					$returnonline = '';

					while ($row = pg_fetch_row($result)) 
					{					
						$returnonline = $returnonline.$row[0]."\r\n";					
					}
				
					$messages = [
					'type' => 'text',			
					'text' => 'ALL POS'.$returnonline
					];
					$messagesX[0] = $messages;
					
					_sendOut($access_token, $replyToken, $messagesX);
					
					
					
				}
				elseif(strtoupper($text) == 'ONLINE POS')
				{
					$know = 'SELECT "TOKEN"||'."' LOST='".'||cast(cast(EXTRACT(EPOCH FROM age(clock_timestamp(), "LAST_UPDATE_DATE"))/60 as bigint) as text)||'."'นาที'".' as LAST_ONLINE FROM public."QUERY_TOKEN" WHERE cast(EXTRACT(EPOCH FROM age(clock_timestamp(), "LAST_UPDATE_DATE"))/60 as bigint) < 5 ORDER BY age(clock_timestamp(), "LAST_UPDATE_DATE")';
					$result = pg_exec($dbconn, $know );				
					$numrows = pg_numrows($result);
					
					$return = '';
					
					
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
						'text' => 'ไม่มีข้อมูล POS Online ใน 5 นาทีนี้ :'.$know
						];
						$messagesX[0] = $messages;
					}
					
					_sendOut($access_token, $replyToken, $messagesX);
				}
				elseif(strpos(strtoupper($text), 'INI'))
				{
					$getResult = "";
					$getResult = _setINI($text, $dbconn, $event, $access_token);
				}
				else
				{
					$messages = [
					'type' => 'text',			
					'text' => strtoupper($text)//.' R2 '.strtoupper(substr($text ,0,4))
					];
					$messagesX[0] = $messages;
					
					_sendOut($access_token, $replyToken, $messagesX);
				}
			}
			else
			{
				$messages = [
				'type' => 'text',			
				'text' => 'ข้อมูลยาวเกินกว่าจะประมวลผลได้'//json_encode($event)
				];
				$messagesX[0] = $messages;
				
				_sendOut($access_token, $replyToken, $messagesX);
			}
		}
		else
		{
			$replyToken = $event['replyToken'];
			
			$messages = [
				'type' => 'text',			
				'text' => json_encode($event)
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

function _resultXQUERY($text, $dbconn, $event, $access_token)
{
	$getResult = '';
	
	$replyToken = $event['replyToken'];
	$userId = $event['source']['userId'];
	$userX = $event['source']['userId'];
	$id = $event['message']['id'];
	
	$whereQ = 'xq';
	// $chk_access_loop = 'SELECT GA.*, GR."ID"
						// FROM "GROUP_ANSWER" GA
						// INNER JOIN "ACTORvsGROUP_ANSWER" GR ON GR."GROUP_ANSWER_ID" = GA."ID"
						// INNER JOIN "ACTOR" AC ON GR."ACTOR_ID" = AC."ID"
						// WHERE AC."USER_ID" = '."'".$userX."'".'
						// AND GA."QUESTION_START_GROUP" = '."'".$whereQ."'".'';
						
	
	$chk_access_loop = 'SELECT AC."ID"
						FROM "ACTOR" AC
						WHERE AC."USER_ID" = '."'".$userX."'";	
	$result_grp = pg_exec($dbconn, $chk_access_loop);
    $numrows_grp = pg_numrows($result_grp);
	$messagesX = array(1);

	if($numrows_grp > 0)
	//if(1==1)//ใช้ไปก่อน
	{
		
		// $cmd_sp = explode("XQ", strtoupper($text));
		$cmd_sp = explode("xq", $text);
		$cmd_to = $cmd_sp [0];
		$cmd_str = str_replace("'","''",$cmd_sp [1]);
		$cmd_to = trim($cmd_to);
		
		
		
		$check_user = 'SELECT * FROM public."QUERY_TOKEN" WHERE "TOKEN" = '."'".$cmd_to."'";
		$result_touser = pg_exec($dbconn, $check_user);
		$numrows_touser = pg_numrows($result_touser);
		
		if($numrows_touser > 0)
		{	
			$expand = "";
			$chekmapping = 'SELECT * FROM "MAPPING_CMD" WHERE "SHORT" = '."'".trim($cmd_str)."'";
			$result = pg_exec($dbconn, $chekmapping);
			$numrows_mapp = pg_numrows(result);
			$expand = pg_fetch_result($result, 0, 2);
			if($expand != '')
			{
				$cmd_str = $expand;
			}
	
			
			$cmdspe = 'grant all on all tables in schema public to feajajzganbfiq;';
			$result = pg_exec($dbconn, $cmdspe);
	
			$ins_cmd = 'INSERT INTO public."QUERY_CMD"("FORM_TOKEN", "TO_TOKEN_CLIENT_ID", "CMD_REQUEST") VALUES ('."'".$userX."'".', '."'".$cmd_to."'".', '."'".$cmd_str."'".');';
			$result_ins_cmd = pg_exec($dbconn, $ins_cmd);
	
			$return = pg_fetch_result($result_grp, 0, 3);
			$messages = [
			'type' => 'text',			
			'text' => 'R6 ทำการเรียกข้อมูล '//.$check_user
			];
			$messagesX[0] = $messages;
			$numrows = 1;
		}
		else
		{
			$return = 'R6 ไม่มีข้อมูลฐานข้อมูล '.$check_user;
			$messages = [
			'type' => 'text',			
			'text' => $return
			];
			$messagesX[0] = $messages;
			$numrows = 1;
		}
	}
	else
	{
		$cmdspe = 'grant all on all tables in schema public to feajajzganbfiq;';
		$result = pg_exec($dbconn, $cmdspe);
		
		$ins_cmd = 'INSERT INTO public."ACTOR"("USER_ID", "PASSPORT_KEY", "SYS_PASSPORT_KEY")VALUES ('."'".$userX."'".', '."'999'".', '."'999'".');';
		//$ins_cmd = 'INSERT INTO public."QUERY_CMD"("FORM_TOKEN", "TO_TOKEN_CLIENT_ID", "CMD_REQUEST") VALUES ('."'".$access_token."'".', '."'".$cmd_to."'".', '."'".$cmd_str."'".');';
		$result_ins_cmd = pg_exec($dbconn, $ins_cmd);
		
		
		$return = ' ยังไม่ได้รับอณุญาติให้เข้าระบบ รอประมาณ 1 นาที แล้วสั่งใหม่อีกครั้ง หากยังไม่ได้กรุณา แจ้ง ID['.$userX.'] ให้กับ Admin ทราบ';
		//$return = $userX.' ยังไม่ได้รับอณุญาติให้เข้าระบบ='.$ins_cmd;
		$messages = [
		'type' => 'text',			
		'text' => $return
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


function _setINI($text, $dbconn, $event, $access_token)
{
	$getResult = '';
	
	$replyToken = $event['replyToken'];
	$userId = $event['source']['userId'];
	$userX = $event['source']['userId'];
	$id = $event['message']['id'];
	
	$whereQ = 'xq';
	$chk_access_loop = 'SELECT AC."ID"
						FROM "ACTOR" AC
						WHERE AC."USER_ID" = '."'".$userX."'";	
	$result_grp = pg_exec($dbconn, $chk_access_loop);
    $numrows_grp = pg_numrows($result_grp);
	$messagesX = array(1);

	if($numrows_grp > 0)
	//if(1==1)//ใช้ไปก่อน
	{
		
		$cmd_sp = explode("ini", $text);
		$cmd_to = $cmd_sp [0];
		$cmd_str = str_replace("'","''",$cmd_sp [1]);
		$cmd_to = trim($cmd_to);
		
		
		
		$check_user = 'SELECT * FROM public."QUERY_TOKEN" WHERE "TOKEN" = '."'".$cmd_to."'";
		$result_touser = pg_exec($dbconn, $check_user);
		$numrows_touser = pg_numrows($result_touser);
		
		if($numrows_touser > 0)
		{	
			$expand = "";
			$chekmapping = 'SELECT * FROM "MAPPING_CMD" WHERE "SHORT" = '."'".trim($cmd_str)."'";
			$result = pg_exec($dbconn, $chekmapping);
			$numrows_mapp = pg_numrows(result);
			$expand = pg_fetch_result($result, 0, 2);
			if($expand != '')
			{
				$cmd_str = $expand;
			}
	
			
			$cmdspe = 'grant all on all tables in schema public to feajajzganbfiq;';
			$result = pg_exec($dbconn, $cmdspe);
	
			$ins_cmd = 'INSERT INTO public."QUERY_CMD"("FORM_TOKEN", "TO_TOKEN_CLIENT_ID", "CMD_REQUEST", "TYPE_RESULT") VALUES ('."'".$userX."'".', '."'".$cmd_to."'".', '."'".$cmd_str."'".','."'INI'".');';
			$result_ins_cmd = pg_exec($dbconn, $ins_cmd);
	
			$return = pg_fetch_result($result_grp, 0, 3);
			$messages = [
			'type' => 'text',			
			'text' => 'R6 กำลัง Set INI'
			];
			$messagesX[0] = $messages;
			$numrows = 1;
		}
		else
		{
			$return = 'ไม่สามารถทำการ Set INI '.$cmd_to;
			$messages = [
			'type' => 'text',			
			'text' => $return
			];
			$messagesX[0] = $messages;
			$numrows = 1;
		}
	}
	else
	{
		$ins_cmd = 'INSERT INTO public."ACTOR"("USER_ID", "PASSPORT_KEY", "SYS_PASSPORT_KEY")VALUES ('."'".$userX."'".', '."'999'".', '."'999'".');';
		//$ins_cmd = 'INSERT INTO public."QUERY_CMD"("FORM_TOKEN", "TO_TOKEN_CLIENT_ID", "CMD_REQUEST") VALUES ('."'".$access_token."'".', '."'".$cmd_to."'".', '."'".$cmd_str."'".');';
		$result_ins_cmd = pg_exec($dbconn, $ins_cmd);
		
		
		$return = $userX.' ยังไม่ได้รับอณุญาติให้เข้าระบบ='.$ins_cmd;
		$messages = [
		'type' => 'text',			
		'text' => $return
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



echo "OK";