<?php
/**
 * Partials functions.
 *
 * @package quark
 */

namespace Quark\Theme\Partials;

/**
 * Setup.
 *
 * @return void
 */
function setup(): void {
	// Fire hooks to bootstrap theme partials.
	add_action( 'init', __NAMESPACE__ . '\\register_partials' );
}

/**
 * Register all partials.
 *
 * @return void
 */
function register_partials(): void {
	// List of partials to register.
	$partials = [
		'BookDeparturesExpeditions' => 'book-departures-expeditions.php',
		'BookDeparturesShips'       => 'book-departures-ships.php',
	];

	// Register partials.
	foreach ( $partials as $namespace => $file_name ) {
		// Include and bootstrap partials.
		require_once __DIR__ . '/partials/' . $file_name;
		call_user_func( __NAMESPACE__ . '\\' . $namespace . '\\bootstrap' );
	}
}
