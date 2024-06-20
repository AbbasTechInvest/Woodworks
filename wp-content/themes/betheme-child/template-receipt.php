<?php 
/**
 * The template is for handling Hesabe payment response.
 * Template Name: receipt
 */

session_start();
if(isset($_SESSION['content'])): ?>
    <?php get_header(); ?>
    <?php echo $_SESSION['content']; ?>
    <?php get_footer(); ?>
    <?php unset($_SESSION['content']); ?>
    <?php session_destroy(); ?>
<?php endif ?>