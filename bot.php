<?php

// example: https://github.com/onlinetuts/line-bot-api/blob/master/php/example/chapter-01.php

include ('line-bot-api/php/line-bot.php');

$channelSecret = 'b59f920f0f1dff37e63849cbe8576b4b';
$access_token  = '2LJ4kkW8bdgLMycAtZvQFlrKePaDBnJQly9AzsxCLkaATYE8g3Aj0jv1FTZT2WavAr3FJqIdK8GoKzbsNAiDfQ6NWVPy+JCYNhjZ/5zyt2HBToJYXRONzbuqG6pxnqNsehHESqfcP2qea6vaiHbclwdB04t89/1O/w1cDnyilFU=';

$bot = new BOT_API($channelSecret, $access_token);
	
if (!empty($bot->isEvents)) {
		
	$bot->replyMessageNew($bot->replyToken, json_encode($bot->message));

	if ($bot->isSuccess()) {
		echo 'Succeeded!';
		exit();
	}

	// Failed
	echo $bot->response->getHTTPStatus . ' ' . $bot->response->getRawBody(); 
	exit();

}