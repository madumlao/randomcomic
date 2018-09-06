<?php
require_once('phpQuery-onefile.php');

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
