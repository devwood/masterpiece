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
		
			$loop = '001';
		
			if(1==1)
			{
				$s = 1;
				
				$check_user = 'SELECT * FROM "TOS"."TOKEN" WHERE "STATUS"='."'"."ACTIVE"."'".' AND "TOKEN" = '."'".$userX."'";
				$result = pg_exec($dbconn, $check_user);				
				$loop = $check_user;
				
				$numrows = pg_numrows($result);
				if($numrows <= 0)
				{
					
					$check_user = 'SELECT * FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'";
					$result = pg_exec($dbconn, $check_user);
					$numrows = pg_numrows($result);
					
					if($numrows <= 0)
					{
						$insert_newuser = 'INSERT INTO "TOS"."TOKEN"("TOKEN", "STATUS") VALUES ('."'".$userX."'".','."'"."'".')';
						$result = pg_exec($dbconn, $insert_newuser);						
						
						
						$del_loop = 'DELETE FROM "TOS"."CMD_LOOP" WHERE "TOKEN_ID" = (SELECT "ID" FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'".')';
						$result = pg_exec($dbconn, $del_loop);
						
						$insert_loop = 'INSERT INTO "TOS"."CMD_LOOP"("CMD", "TOKEN_ID")VALUES ('."'ADDUSER'".', (SELECT "ID" FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'".'));';
						$result = pg_exec($dbconn, $insert_loop);
						
						
						$messages = [
								'type' => 'text',			
								'text' => 'R1 ไม่มีผู้ใช้นี้ และระบบได้เพิ่มให้แล้วกรุณาให้ admin อนุมัติ '//.$insert_newuser
								];
								$messagesX[0] = $messages;
					}
					else//เริ่มทำงาน
					{
						$get_loop = 'SELECT "ID", "CMD", "TOKEN_ID" FROM "TOS"."CMD_LOOP" WHERE "TOKEN_ID" = (SELECT "ID" FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'".')';
						$result = pg_exec($dbconn, $get_loop);
						$numrows = pg_numrows($result);
						if($numrows > 0)
						{
							
							$return_cmd = pg_fetch_result($result, $numrows-1, 1);
							
							if($return_cmd == "ADDUSER")
							{
								$messages = [
								'type' => 'text',			
								'text' => 'R1 กรุณาใส่ชื่อของคุณ'//.json_encode($event)
								];
								$messagesX[0] = $messages;
								
								$insert_loop = 'INSERT INTO "TOS"."CMD_LOOP"("CMD", "TOKEN_ID")VALUES ('."'REQNAME'".', (SELECT "ID" FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'".'));';
								$result = pg_exec($dbconn, $insert_loop);
							}
							elseif($return_cmd == "REQNAME")
							{
								$updname_user = 'UPDATE "TOS"."TOKEN" SET "NAME" = '."'".$text."'".' WHERE "TOKEN" = '."'".$userX."'";
								$result = pg_exec($dbconn, $updname_user);
								
								$messages = [
								'type' => 'text',			
								'text' => 'R1 รอ Admin อนุมัติสักครู่' //.json_encode($event)
								];
								$messagesX[0] = $messages;
								
								
							}
							else
							{
								$messages = [
								'type' => 'text',			
								'text' => 'R1 อยู่นอกลูป='.$return_cmd.' CMD='.$get_loop
								];
								$messagesX[0] = $messages;
							}
						}
						else
						{
							$messages = [
							'type' => 'text',			
							'text' => 'R1 ผู้ใช้ยังไม่ได้รับอณุญาติ หรือมีความผิดปกติ กรุณาติดต่อผู้ดูแลระบบ พร้อมแจ้ง Code='.$userX//.json_encode($event)
							];
							$messagesX[0] = $messages;
						}
					}								
				}
				else
				{
					$chk_opencmd = 'SELECT Q.*
									FROM "TOS"."QUESTION_TYPE" Q
									INNER JOIN "TOS"."QUESTION_TYPEvsTOKEN" QvT ON QvT."QUESTION_TYPE_ID" = Q."ID"
									INNER JOIN "TOS"."TOKEN" TK ON TK."ID" = QvT."TOKEN_ID"
									WHERE TK."TOKEN" = '."'".$userX."'".'
									AND Q."QUESTION_DESC" = '."'".$text."'";
					
					$result = pg_exec($dbconn, $chk_opencmd);
					$numrows = pg_numrows($result);
					if($numrows > 0)
					{	
						$del_loop = 'DELETE FROM "TOS"."CMD_LOOP" WHERE "TOKEN_ID" = (SELECT "ID" FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'".')';
						$result = pg_exec($dbconn, $del_loop);
						
						$insert_new_loop = ' INSERT INTO "TOS"."CMD_LOOP"("CMD", "TOKEN_ID", "CMD_SQL", "DESC","RESULT_TYPE")
											   SELECT "CMD", (SELECT "ID" FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'".'), "CMD_SQL", "DESC","RESULT_TYPE"
											   FROM "TOS"."CMD_LOOP_TEMPLETE"
											   WHERE "QUESTION_TYPE_ID" = (SELECT "ID" FROM "TOS"."QUESTION_TYPE" WHERE "QUESTION_DESC" = '."'PR'".')';
						$result = pg_exec($dbconn, $insert_new_loop);
						
						$select_new_loop = 'SELECT "ID", "CMD", "TOKEN_ID", "CMD_SQL", "DESC", "RESULT_STATUS", "RESULT_TYPE"
											FROM "TOS"."CMD_LOOP"
											WHERE "TOKEN_ID" = (SELECT "ID" FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'".')
											AND coalesce("RESULT_STATUS",'."''".') = '."''".'
											ORDER BY "ID"';
						$result = pg_exec($dbconn, $select_new_loop);
						
						$messages = [
						'type' => 'text',			
						'text' => 'R1 พร้อมเริ่ม Loop ใหม่ '.pg_fetch_result($result, 0, 4).' ins_cmd='.$chk_opencmd
						];
						$messagesX[0] = $messages;
					}
					else
					{
						$select_loop = 'SELECT "ID", "CMD", "TOKEN_ID", "CMD_SQL", "DESC", "RESULT_STATUS", "RESULT_TYPE", "PARAMETER_TEXT"
											FROM "TOS"."CMD_LOOP"
											WHERE "TOKEN_ID" = (SELECT "ID" FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'".')
											AND coalesce("RESULT_STATUS",'."''".') = '."''".'
											ORDER BY "ID"';
						$result = pg_exec($dbconn, $select_loop);
						
						$parameter_loop = '';
						$parameter_loop = pg_fetch_result($result, 0, 7);
						$id_loop = pg_fetch_result($result, 0, 0);
						
						if($parameter_loop != '')
						{	
							$messages = [
							'type' => 'text',			
							'text' => 'R1 กำลังค้นข้อมูลกรุณารอสักครู่  หรือหากนานเกินไปกรุณาแจ้งทางผู้ดูแลระบบ'
							];
							$messagesX[0] = $messages;
						}
						else
						{
							$update_loop = 'UPDATE "TOS"."CMD_LOOP"
											SET "PARAMETER_TEXT"='."'".$text."'".'
											WHERE "ID" = '.$id_loop.';';
							$result = pg_exec($dbconn, $update_loop);
							
							$messages = [
							'type' => 'text',			
							'text' => 'R1 กำลังค้นข้อมูลกรุณารอสักครู่ '
							];
							$messagesX[0] = $messages;
						}
						
					}
					
					
					// $del_loop = 'DELETE FROM "TOS"."CMD_LOOP" WHERE "CMD" IN('."'ADDUSER'".','."'REQNAME'".') AND "TOKEN_ID" = (SELECT "ID" FROM "TOS"."TOKEN" WHERE "TOKEN" = '."'".$userX."'".')';
					// $result = pg_exec($dbconn, $del_loop);
					
					// $return_user = pg_fetch_result($result, 0, 3);
					// $messages = [
					// 'type' => 'text',			
					// 'text' => 'R1 สวัสดี '.$return_user.' ที่คุณสอบถามไม่อยู่ใน Scope การใช้งานของคุณ'
					// ];
					// $messagesX[0] = $messages;
				}
			}
			else
			{
				$s = 0;
				// $messages = [
				// 'type' => 'text',			
				// 'text' => 'สอบถามวันที่ 01/01 เวลา 02:50 โดย:'.$userX
				// ];
				// $messagesX[0] = $messages;
				
				// SELECT Q.*
				// FROM "TOS"."QUESTION_TYPE" Q
				// INNER JOIN "TOS"."QUESTION_TYPEvsTOKEN" QvT ON QvT."QUESTION_TYPE_ID" = Q."ID"
				// INNER JOIN "TOS"."TOKEN" TK ON TK."ID" = QvT."TOKEN_ID"
				// WHERE TK."TOKEN" = 'Ufcac064b5bbb5ff75a34dc573b82ee1f'
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


echo "OK2";