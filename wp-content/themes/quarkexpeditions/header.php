<?php
/**
 * Site header.
 *
 * @package quark
 */

// Site header template.
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<?php do_action( 'quark_head_first' ); ?>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no maximum-scale=1">
	<meta name="theme-color" content="#000">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
