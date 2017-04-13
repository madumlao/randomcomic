<?php
$url = 'http://www.smbc-comics.com/random.php';
// get the comic source
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);

libxml_use_internal_errors(true);

require('phpQuery-onefile.php');
phpQuery::newDocument($response);

header('Content-Type: application/json');

$obj = [];
$obj['source'] = 'smbc';
$obj['link'] = pq('#permalinktext')->attr('value');
$obj['title'] = pq('title')->html();
preg_match('!Cereal - (.*)$!', $obj['title'], $matches);
$obj['serial'] = $matches[1];
$obj['src'] = pq('#cc-comic')->attr('src');
$obj['alt'] = pq('#cc-comic')->attr('title');
echo json_encode($obj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
exit();
