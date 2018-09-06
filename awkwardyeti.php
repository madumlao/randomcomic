<?php
require_once("include.php");

if (!isset($_REQUEST['slug'])) {
	// get a random comic
	
	$url = 'http://theawkwardyeti.com/?random';

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($ch);

	$headers = parse_headers($response);

	$url = $headers['Location'];
	preg_match('!/([^/]*)(/*)?$!', $url, $matches);
	$slug = $matches[1];
} else {
	$slug = $_REQUEST['slug'];
	$url = "http://theawkwardyeti.com/$slug";
}


$ch = curl_init($url);
curl_setopt($ch, CURLOPT_ENCODING, "");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);

libxml_use_internal_errors(true);

phpQuery::newDocument($response);

header('Content-Type: application/json');
foreach (pq('#comic > a > img[alt][title]') AS $comic) {
	$obj = [];

	$obj['source'] = 'awkwardyeti';
	$obj['link'] = $url;
	$obj['slug'] = $slug;
	$obj['src'] = pq($comic)->attr('src');
	preg_match('!uploads/(\d{4}/\d{2}(/\d{2})?)!', $obj['src'], $matches);
	$obj['serial'] = $matches[1];
	$obj['title'] = pq($comic)->attr('alt');
	$obj['alt'] = pq($comic)->attr('title');

	echo json_encode($obj, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
	exit();
}
