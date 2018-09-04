<?php
$url = 'http://theawkwardyeti.com/?random';
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

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_ENCODING, "");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);

libxml_use_internal_errors(true);

require('phpQuery-onefile.php');
phpQuery::newDocument($response);

header('Content-Type: application/json');
foreach (pq('#comic > a > img[alt][title]') AS $comic) {
	$obj = [];

	$obj['source'] = 'awkwardyeti';
	$obj['link'] = $url;
	$obj['src'] = pq($comic)->attr('src');
	preg_match('!uploads/(\d{4}/\d{2}(/\d{2})?)!', $obj['src'], $matches);
	$obj['serial'] = $matches[1];
	$obj['title'] = pq($comic)->attr('alt');
	$obj['alt'] = pq($comic)->attr('title');

	echo json_encode($obj, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
	exit();
}
