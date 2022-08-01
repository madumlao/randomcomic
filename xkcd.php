<?php
require_once("include.php");

if (!isset($_REQUEST['slug'])) {
	// get a random comic

	$url = 'https://c.xkcd.com/random/comic/';
	// get the comic source
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($ch);

	$headers = parse_headers($response);
	
	$url = $headers['location'];
	preg_match('!(\d*)/$!', $url, $matches);
	$slug = $matches[1];
} else {
	$slug = $_REQUEST['slug'];
	$url = "https://xkcd.com/$slug";
}

// get the comic
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);

libxml_use_internal_errors(true);

phpQuery::newDocument($response);

header('Content-Type: application/json');
foreach (pq('img[alt][title]') AS $comic) {
	$obj = [];

	$obj['source'] = 'xkcd';
	$obj['source_title'] = 'XKCD';
	$obj['link'] = $url;
	$obj['slug'] = $slug;
	$obj['serial'] = $slug;
	$obj['src'] = pq($comic)->attr('src');
	$obj['title'] = pq($comic)->attr('alt');
	$obj['alt'] = pq($comic)->attr('title');

	echo json_encode($obj, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
	exit();
}
