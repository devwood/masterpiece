<?php
$access_token = 'NENB7H4HyQxHVCl8OJ94uvbss5SOxzlTNYPk02k+BuzBjG3OczD2x7rDlXgfjR9VAr3FJqIdK8GoKzbsNAiDfQ6NWVPy+JCYNhjZ/5zyt2H+4RHcDvtHNE5JDS27CRHsAyS5El5uVBXYds2s76MeRAdB04t89/1O/w1cDnyilFU=';

$url = 'https://api.line.me/v1/oauth/verify';

$headers = array('Authorization: Bearer ' . $access_token);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result;