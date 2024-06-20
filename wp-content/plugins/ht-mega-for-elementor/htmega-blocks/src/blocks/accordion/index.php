<?php
	// Exit if accessed directly.
	if ( !defined('ABSPATH') ) { exit; }

	$classNames = [
		"htmega-block-{$settings['blockUniqId']}",
		"htmega-accordion",
		"htmega-accordion-{$settings['style']}",
		"htmega-accordion-indicator-{$settings['iconAlignment']}"
	];
	$classes = implode(' ', $classNames);
	echo "<div class='{$classes}'>{$content}</div>";
?>