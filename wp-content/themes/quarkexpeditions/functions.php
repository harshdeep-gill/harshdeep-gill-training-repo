<?php
/**
 * Functions and definitions.
 *
 * @package quark
 */

namespace Quark\Theme;

// Includes.
require_once __DIR__ . '/inc/core.php';
require_once __DIR__ . '/inc/editor.php';
require_once __DIR__ . '/inc/blocks.php';
require_once __DIR__ . '/inc/partials.php';
require_once __DIR__ . '/inc/template-tags.php';
require_once __DIR__ . '/inc/helpers.php';

// Setup.
Core\setup();
Blocks\setup();
Editor\setup();
Partials\setup();
