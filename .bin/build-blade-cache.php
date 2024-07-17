<?php
/**
 * Build Blade cache.
 *
 * @package quark
 */

// Bootstrap Blade.
require_once __DIR__ . '/../blade.config.php';
require_once __DIR__ . '/../wp-content/mu-plugins/travelopia/travelopia-blade/vendor/autoload.php';
require_once __DIR__ . '/../wp-content/mu-plugins/travelopia/travelopia-blade/inc/namespace.php';

Travelopia\Blade\bootstrap();

// Initialize blade.
$blade_config                  = $blade_config ?? [];
$blade                         = new Travelopia\Blade\Blade();
$blade->paths_to_views         = $blade_config['paths_to_views'] ?? [];
$blade->path_to_compiled_views = $blade_config['path_to_compiled_views'] ?? '';
$blade->initialize();

// Build Blade cache.
echo "Building Blade cache...\n";
$blade->build_cache();
echo "\033[32m✓ Blade cache built!\n";
exit( 0 );
