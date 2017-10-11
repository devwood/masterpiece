<?php
$access_token = 'NENB7H4HyQxHVCl8OJ94uvbss5SOxzlTNYPk02k+BuzBjG3OczD2x7rDlXgfjR9VAr3FJqIdK8GoKzbsNAiDfQ6NWVPy+JCYNhjZ/5zyt2H+4RHcDvtHNE5JDS27CRHsAyS5El5uVBXYds2s76MeRAdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data

			// Build message to reply back
			$messages = [
				'type' => 'sticker',
				'packageId' => '1',
				'stickerId' => '1'
			];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/push';
			$data = [
				'to' => 'U1533e4d7bd11d63a9f8a9329a5fa54cb',
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