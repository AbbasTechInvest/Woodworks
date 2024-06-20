<?php
/* enqueue scripts and style from parent theme */
 
function strorefront_styles() {
wp_enqueue_style( 'child-style', get_stylesheet_uri(),
array( 'strore-front-style' ), wp_get_theme()->get('Version') );
}
add_action( 'wp_enqueue_styles', 'strorefront_styles');
?>