<?php
/**
 * Blade Config.
 *
 * @package wordpress-blade
 */

// Blade config.
define(
	'WORDPRESS_BLADE',
	[
		'paths_to_views'         => [
			__DIR__ . '/wp-content/themes/quarkexpeditions/src/front-end',
			__DIR__ . '/wp-content/themes/quarkexpeditions/src/front-end/components',
		],
		'path_to_compiled_views' => __DIR__ . '/wp-content/themes/quarkexpeditions/dist/blade',
		'never_expire_cache'     => isset( $_ENV['PANTHEON_ENVIRONMENT'] ),
		'base_path'              => __DIR__,
	]
);
