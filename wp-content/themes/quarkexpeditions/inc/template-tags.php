<?php
/**
 * Template tags.
 *
 * @package quark
 */

/**
 * Bootstrap the front-end.
 *
 * @return void
 */
function quark_front_end(): void {
	// Get front-end data.
	$template_data = Quark\Core\get_front_end_data();

	// Check if we found a valid layout.
	if ( ! empty( $template_data['layout'] ) ) {
		ob_start();
		quark_layout(
			$template_data['layout'],
			! empty( $template_data['data'] ) ? $template_data['data'] : []
		);
		$content = ob_get_clean();
		echo strval( apply_filters( 'quark_front_end', $content ) ); // phpcs:ignore
	} elseif ( 'local' === wp_get_environment_type() ) {
		wp_die( 'Could not find a layout for this page. Set one using `Quark\Core\get_front_end_data`.' );
	}
}

/**
 * Get template data based on key.
 *
 * @param string     $key      Data key.
 * @param mixed|null $fallback Fallback, if key isn't present.
 *
 * @return mixed
 */
function quark_get_template_data( string $key = '', mixed $fallback = null ): mixed {
	// Get data.
	$template_data = Quark\Core\get_front_end_data();

	// Check if the key we want exists.
	if ( empty( $template_data['data'] ) || ! isset( $template_data['data'][ $key ] ) ) {
		// It doesn't, return fallback instead.
		return $fallback;
	}

	// Return data.
	return $template_data['data'][ $key ];
}

/**
 * Get template's layout.
 *
 * @return string
 */
function quark_get_template_layout(): string {
	// Get template data.
	$template_data = Quark\Core\get_front_end_data();

	// Get layout from data.
	if ( ! empty( $template_data['layout'] ) ) {
		return strval( $template_data['layout'] );
	}

	// Layout not found.
	return '';
}

/**
 * Render a layout.
 *
 * @param string  $name        Layout name.
 * @param mixed[] $attributes  Attributes to pass to component.
 * @param bool    $echo_output Echo the layout.
 *
 * @return string
 */
function quark_layout( string $name = '', array $attributes = [], bool $echo_output = true ): string {
	// Build content.
	$content = quark_get_component( $name, $attributes );

	// Add content after header and footer.
	ob_start();
	get_header();
	echo $content; // phpcs:ignore -- Components are responsible for escaping.
	get_footer();

	// Get layout content.
	$content = ob_get_clean();

	// Output or return.
	if ( true === $echo_output ) {
		echo $content; // phpcs:ignore -- Components are responsible for escaping.

		// Content was echoed, so lets return an empty string.
		return '';
	} else {
		// Content not echoed, return it instead.
		return strval( $content );
	}
}

/**
 * Render a component.
 *
 * @param string  $name       Component name.
 * @param mixed[] $attributes Attributes to pass to component.
 *
 * @return string
 */
function quark_get_component( string $name = '', array $attributes = [] ): string {
	// Check if Travelopia Blade is loaded correctly.
	if ( ! function_exists( 'Travelopia\Blade\load_view' ) ) {
		return '';
	}

	// Return rendered Blade component.
	return strval( Travelopia\Blade\load_view( $name, $attributes, false ) );
}

/**
 * Enqueue a component's assets, if any.
 *
 * @param string $name Component name.
 *
 * @return void
 */
function quark_component_enqueue_assets( string $name = '' ): void {
	// Check if the component name begins with `components`.
	// This is because our components are within the `components` directory.
	if ( str_starts_with( $name, 'components.' ) ) {
		$name_parts = explode( '.', $name );

		// Just get the name without the prefix.
		if ( ! empty( $name_parts[1] ) ) {
			$name = $name_parts[1];
		}
	}

	// Build component path.
	$path       = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
	$has_style  = false;
	$has_script = false;

	// Get assets version.
	$assets_version = Quark\Theme\Core\get_assets_version();

	// Check if component has CSS.
	if ( file_exists( $path . 'style.css' ) ) {
		$has_style = true;
	}

	// Check if component has JS.
	if ( file_exists( $path . 'index.js' ) ) {
		$has_script = true;
	}

	// If it has either, lets enqueue it.
	if ( $has_style || $has_script ) {
		$handle = $name . '-component';

		// Enqueue the assets.
		add_action(
			'wp_enqueue_scripts',
			function () use ( $has_style, $has_script, $handle, $name, $assets_version ) {
				// Enqueue CSS, if it exists.
				if ( $has_style ) {
					wp_enqueue_style( $handle, get_stylesheet_directory_uri() . '/dist/components/' . $name . '/style.css', [], $assets_version );
				}

				// Enqueue JS, if it exists.
				if ( $has_script ) {
					wp_enqueue_script( $handle, get_stylesheet_directory_uri() . '/dist/components/' . $name . '/index.js', [], $assets_version, true );
				}
			},
			30
		);
	}
}

/**
 * Wrapper function for dynamic images.
 *
 * @param mixed[] $args       Image args.
 * @param bool    $echo_image Echo the image.
 *
 * @return string|null
 */
function quark_dynamic_image( array $args = [], bool $echo_image = true ): ?string {
	// Check if Travelopia dynamic images are enabled.
	if ( function_exists( 'Travelopia\Media\get_dynamic_image' ) ) {
		$image = Travelopia\Media\get_dynamic_image( $args );
	} else {
		$image = wp_get_attachment_image( ! empty( $args['id'] ) ? absint( $args['id'] ) : 0, 'full' );
	}

	// If we don't need to echo the image, just return it.
	if ( false === $echo_image ) {
		return $image;
	}

	// We need to echo it.
	echo wp_kses_post( $image );

	// Lets return null as a fallback.
	return null;
}

/**
 * Wrapper function for dynamic image URLs.
 *
 * @param mixed[] $args Image args.
 *
 * @return string
 */
function quark_dynamic_image_url( array $args = [] ): string {
	// Check if Travelopia dynamic images are enabled.
	if ( function_exists( 'Travelopia\Media\get_dynamic_image_url' ) ) {
		return Travelopia\Media\get_dynamic_image_url( absint( $args['id'] ), (array) $args['transform'] );
	} else {
		$url = wp_get_attachment_image_url( ! empty( $args['id'] ) ? absint( $args['id'] ) : 0, 'full' );

		// Check if we found a valid URL.
		if ( false === $url ) {
			$url = '';
		}

		// Return URL.
		return $url;
	}
}

/**
 * Wrapper function for template parts.
 *
 * @param string  $name        Template part name.
 * @param mixed[] $args        Template part args.
 * @param bool    $echo_output Echo template part.
 *
 * @return string
 */
function quark_get_template_part( string $name = '', array $args = [], bool $echo_output = true ): string {
	// Check if we need to echo the template part.
	if ( $echo_output ) {
		get_template_part( 'parts/' . $name, null, $args );

		// We've echoed it, so lets return a blank string.
		return '';
	} else {
		ob_start();
		get_template_part( 'parts/' . $name, null, $args );

		// We're not echoing it, so lets return it as a string.
		return strval( ob_get_clean() );
	}
}

/**
 * Get a cached version of `wp_nav_menu`.
 *
 * @param mixed[] $args WP Nav Menu args.
 *
 * @return void
 */
function quark_cached_wp_nav_menu( array $args = [] ): void {
	// Check if Travelopia cached navigation is enabled.
	if ( function_exists( 'Travelopia\Core\cached_nav_menu' ) ) {
		echo strval( \Travelopia\Core\cached_nav_menu( $args ) ); // phpcs:ignore
	} else {
		wp_nav_menu( $args );
	}
}

/**
 * Enqueue a style.
 *
 * @param string $handle Handle.
 *
 * @return void
 */
function quark_enqueue_style( string $handle = '' ): void {
	// Enqueue a style.
	add_action(
		'wp_enqueue_scripts',
		function () use ( $handle ) {
			wp_enqueue_style( $handle );
		},
		20
	);
}

/**
 * Enqueue a script.
 *
 * @param string $handle Handle.
 *
 * @return void
 */
function quark_enqueue_script( string $handle = '' ): void {
	// Enqueue a script.
	add_action(
		'wp_enqueue_scripts',
		function () use ( $handle ) {
			wp_enqueue_script( $handle );
		},
		20
	);
}

/**
 * An image from within the theme.
 *
 * @param string  $path  Relative path from the `assets` directory.
 * @param mixed[] $attrs Image attributes.
 *
 * @return void
 */
function quark_theme_image( string $path = '', array $attrs = [] ): void {
	// Build file path.
	$file = get_template_directory_uri() . '/src/assets/' . $path;

	// Check for attributes.
	if ( empty( $attrs['loading'] ) ) {
		$attrs['loading'] = 'lazy';
	}

	// Output the image.
	echo wp_kses_post(
		sprintf(
			'<img src="%s" %s>',
			$file,
			implode(
				' ',
				array_map(
					function ( $value, $key ) {
						return $key . '="' . $value . '"';
					},
					$attrs,
					array_keys( $attrs )
				)
			)
		)
	);
}
