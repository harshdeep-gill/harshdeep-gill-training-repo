<?php
/**
 * Blade Config.
 *
 * @package travelopia-blade
 */

$blade_config = [
	'paths_to_views'         => [
		__DIR__ . '/wp-content/themes/quark/src/front-end',
		__DIR__ . '/wp-content/themes/quark/src/front-end/components',
		__DIR__ . '/wp-content/themes/quark/src/front-end/layouts',
	],
	'path_to_compiled_views' => __DIR__ . '/wp-content/themes/quark/dist/blade',
	'never_expire_cache'     => isset( $_ENV['PANTHEON_ENVIRONMENT'] ),
];
