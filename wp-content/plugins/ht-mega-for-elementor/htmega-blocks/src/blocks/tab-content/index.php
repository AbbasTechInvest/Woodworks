<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}
$activeClass = $settings['id'] === $settings['activeTab'] ? 'htmega-tab-pane-active': '';
$activeDisplay = $settings['id'] === $settings['activeTab'] ? 'block': 'none';
echo "<div class='htmega-tab-pane {$activeClass}' data-tab-id='{$settings['id']}' style='display: {$activeDisplay}'>";
echo $content;
echo "</div>";