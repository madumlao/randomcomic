<?php
$url = 'http://c.xkcd.com/random/comic/';
// get the comic source
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);

$headers = parse_headers($response);
function parse_headers($response) {
	$headers = [];
	$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

	foreach (explode("\r\n", $header_text) as $i => $line) {
		if ($i === 0) {
			$headers['http_code'] = $line;
		} else {
			list($key, $value) = explode(': ', $line);
			$headers[$key] = $value;
		}
	}

	return $headers;
}

// follow the comic redirect
$url = $headers['Location'];
preg_match('!(\d*)/$!', $url, $matches);
$serial = $matches[1];
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

	$obj['source'] = 'xkcd';
	$obj['link'] = $url;
	$obj['serial'] = $serial;
	$obj['src'] = pq($comic)->attr('src');
	$obj['title'] = pq($comic)->attr('title');
	$obj['alt'] = pq($comic)->attr('alt');

	echo json_encode($obj, JSON_UNESCAPED_SLASHES);
	exit();
}
