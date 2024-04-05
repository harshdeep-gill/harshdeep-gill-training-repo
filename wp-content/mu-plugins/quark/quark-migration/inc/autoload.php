<?php
/**
 * Autoloader.
 *
 * @package quark-migration
 */

namespace Quark\Migration;

/**
 * Autoloader for plugin.
 *
 * @param string $class_name Class name.
 *
 * @return void
 */
function autoload( string $class_name = '' ): void {
	// Check if class have current name in it or not.
	if ( ! str_starts_with( $class_name, __NAMESPACE__ ) ) {
		return;
	}

	// Find file name based on class name.
	$class_path = array_map(
		function ( $item ) {
			return str_replace( '_', '-', strtolower( implode( '-', preg_split( '/(?<=[a-z]) (?=[A-Z]) | (?<=[A-Z]) (?=[A-Z][a-z])/x', $item ) ) ) ); // @phpstan-ignore-line
		},
		explode( '\\', str_replace( __NAMESPACE__ . '\\', '', $class_name ) )
	);

	// Find file path based on class name.
	$file_name = 'class-' . array_pop( $class_path ) . '.php';
	$path      = __DIR__ . DIRECTORY_SEPARATOR . ( ! empty( $class_path ) ? implode( DIRECTORY_SEPARATOR, $class_path ) . DIRECTORY_SEPARATOR : '' ) . $file_name;

	// Check if file exists or not. If file exists then include it.
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}

// phpcs:disable PSR1.Files.SideEffects
spl_autoload_register( __NAMESPACE__ . '\\autoload' );
// phpcs:enable
