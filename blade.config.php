<?php
/**
 * Blade Config.
 *
 * @package travelopia-blade
 */

$blade_config = [
	'paths_to_views'         => [
		__DIR__ . '/wp-content/themes/quarkexpeditions/src/front-end',
		__DIR__ . '/wp-content/themes/quarkexpeditions/src/front-end/components',
	],
	'path_to_compiled_views' => __DIR__ . '/wp-content/themes/quarkexpeditions/dist/blade',
	'never_expire_cache'     => isset( $_ENV['PANTHEON_ENVIRONMENT'] ),
];
