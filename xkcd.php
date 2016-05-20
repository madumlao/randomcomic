<?php
$url = 'http://c.xkcd.com/random/comic/';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);

libxml_use_internal_errors(true);

require('phpQuery-onefile.php');
phpQuery::newDocument($response);

header('Content-Type: application/json');
foreach (pq('img[alt][title]') AS $comic) {
	$obj = [];

	$obj['src'] = pq($comic)->attr('src');
	$obj['title'] = pq($comic)->attr('title');
	$obj['alt'] = pq($comic)->attr('alt');

	echo json_encode($obj, JSON_UNESCAPED_SLASHES);
	exit();
}
