<?php
$url = 'http://www.qwantz.com/index.php';

// get the list of comics
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);
curl_close($ch);
libxml_use_internal_errors(true);

require('phpQuery-onefile.php');
phpQuery::newDocument($response);

header('Content-Type: application/json');

// get the 'prev' link of the document - indicates how many comics have been released so far
$prev = pq('a[href][rel="prev"]')->attr('href');
preg_match('/[0-9]+$/', $prev, $matches);
$maxcomic=$matches[0]+1;
$serial = rand(1, $maxcomic);
$url .= "?comic=$serial";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);
curl_close($ch);

phpQuery::newDocument($response);
$title = pq('title')->html();
$img = pq('img.comic[src][title]');

# return JSON encoding
$obj = [];
$obj['source'] = 'qwantz';
$obj['link'] = $url;
$obj['serial'] = $serial;
$obj['src'] = pq($img)->attr('src');
$obj['title'] = $title;
$obj['alt'] = pq($img)->attr('title');

echo json_encode($obj, JSON_UNESCAPED_SLASHES);
exit();
