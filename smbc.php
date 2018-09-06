<?php
require_once("include.php");

if (!isset($_REQUEST['slug'])) {
	// get a random comic

	$url = 'http://www.smbc-comics.com/rand.php';
	// get the comic source
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($ch);
	curl_close($ch);

	// response returns the title of a random comic
	$slug = json_decode($response);
	$url = 'http://www.smbc-comics.com/comic/' . $slug;
} else {
	$slug = $_REQUEST['slug'];
	$url = 'http://www.smbc-comics.com/comic/' . $slug;
}

// get the comic
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);

libxml_use_internal_errors(true);

phpQuery::newDocument($response);

header('Content-Type: application/json');

$obj = [];
$obj['source'] = 'smbc';
$obj['link'] = $url;
$title = pq('title')->html();
preg_match('!Cereal - (.*)$!', $title, $matches);
$obj['title'] = $matches[1];
$obj['slug'] = $slug;
$obj['src'] = pq('#cc-comic')->attr('src');
preg_match('!([^-/.]*)\.([^-/.]*)$!', $obj['src'], $matches);
$obj['serial'] = $matches[1];
$obj['alt'] = pq('#cc-comic')->attr('title');
echo json_encode($obj, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
exit();
