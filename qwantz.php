<?php
require_once("include.php");

if (!isset($_REQUEST['slug'])) {
	// get a random comic

	// get the list of comics
	$url = 'http://www.qwantz.com/index.php';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($ch);
	curl_close($ch);
	libxml_use_internal_errors(true);

	phpQuery::newDocument($response);

	// get the 'prev' link of the document - indicates how many comics have been released so far
	$prev = pq('a[href][rel="prev"]')->attr('href');
	preg_match('/[0-9]+$/', $prev, $matches);
	$maxcomic=$matches[0]+1;

	$slug = rand(1, $maxcomic);
	$serial = $slug;
	$url .= "?comic=$slug";
} else {
	$slug = $_REQUEST['slug'];
	$serial = $slug;
	$url = "http://www.qwantz.com/index.php?comic=$slug";
}

// get the comic
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);
curl_close($ch);

phpQuery::newDocument($response);
$title = pq('title')->html();
preg_match('!([^- ][^-]*) - ([^-]*)$!', $title, $matches);
$title = $matches[1];
$img = pq('img.comic[src][title]');

# return JSON encoding
header('Content-Type: application/json');
$obj = [];
$obj['source'] = 'qwantz';
$obj['source_title'] = 'Dinosaur Comics';
$obj['link'] = $url;
$obj['slug'] = $slug;
$obj['serial'] = $serial;
$obj['src'] = 'http://qwantz.com/' . pq($img)->attr('src');
$obj['title'] = $title;
$obj['alt'] = pq($img)->attr('title');

echo json_encode($obj, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
exit();
