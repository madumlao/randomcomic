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
foreach (pq('article.type-comic') AS $comic) {
	$obj = [];

	$img = pq($comic)->find('img.attachment-post-thumbnail')[0];
	$title = pq($comic)->find('.entry-header .c-blog-title')[0];
	$url = preg_replace('!/$!', '', $title->attr('href'));
	$slug = preg_replace('!.*/!', '', $url);
	$title_text = trim(pq($title)->find('h3')->text());

	$obj['source'] = 'awkwardyeti';
	$obj['source_title'] = 'The Awkward Yeti';
	$obj['link'] = $url;
	$obj['slug'] = $slug;
	$obj['src'] = pq($img)->attr('src');
	preg_match('!uploads/(\d{4}/\d{2}(/\d{2})?)!', $obj['src'], $matches);
	$obj['serial'] = $matches[1];
	$obj['title'] = $title_text;
	$obj['lt'] = pq($img)->attr('title');

	echo json_encode($obj, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
	exit();
}
