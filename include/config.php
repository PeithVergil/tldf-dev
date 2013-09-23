<?php

ini_set('memory_limit', -1);

/**
 * Parse configuration xml-file
 */

$data = implode('', file(dirname(__FILE__).'/config.xml'));

$xml_parser = xml_parser_create();
xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1);
xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
xml_parse_into_struct($xml_parser, $data, $vals, $index);
xml_parser_free($xml_parser);

$config = array();
foreach ($vals as $i => $node) {
	if ($node['type'] == 'complete') {
		$config[$node['tag']] = isset($node['value']) ? $node['value'] : '';
	}
}

/**
 * Parse constants xml-file
 */

$data = implode('', file(dirname(__FILE__).'/constants.xml'));

$xml_parser = xml_parser_create();
xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 1);
xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
xml_parse_into_struct($xml_parser, $data, $vals, $index);
xml_parser_free($xml_parser);

foreach ($vals as $i => $node) {
	if ($node['type'] == 'complete') {
		if (!defined($node['tag'])) {
			define($node['tag'], $config['table_prefix'].$node['value']);
		}
	}
}

unset($data, $vals, $index, $node);

?>