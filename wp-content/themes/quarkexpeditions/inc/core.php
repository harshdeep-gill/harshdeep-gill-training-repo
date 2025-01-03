<?php
/**
 * Core functions and definitions.
 *
 * @package quark
 */

namespace Quark\Theme\Core;

use function Quark\Core\is_china_website;

/**
 * Setup.
 *
 * @return void
 */
function setup(): void {
	// Hooks.
	add_action( 'after_setup_theme', __NAMESPACE__ . '\\theme_support' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\register_styles' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\register_scripts' );
	add_action( 'wordpress_blade_before_view', __NAMESPACE__ . '\\blade_view_before_load' );
	add_filter( 'wp_resource_hints', __NAMESPACE__ . '\\resource_hints', 10, 2 );
	add_filter( 'wp_kses_allowed_html', __NAMESPACE__ . '\\kses_custom_allowed_html', 10, 2 );
	add_filter( 'template_include', __NAMESPACE__ . '\\remove_default_title_tag' );
	add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );
}

/**
 * Set up theme defaults and registers support for various WordPress features.
 *
 * @return void
 */
function theme_support(): void {
	// Content-width.
	global $content_width;

	// Add default content width.
	if ( ! isset( $content_width ) ) {
		$content_width = 1280;
	}

	// Post thumbnails.
	add_theme_support( 'post-thumbnails' );

	// Misc. support.
	add_theme_support( 'title-tag' );
	add_theme_support(
		'html5',
		[
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		]
	);
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	remove_theme_support( 'core-block-patterns' );

	// Cleanup.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'rsd_link' );

	// Localization.
	load_theme_textdomain( 'qrk', get_stylesheet_directory() . '/locale' );
}

/**
 * Register and Enqueue Styles.
 *
 * @return void
 */
function register_styles(): void {
	// Dequeue WordPress styles.
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'global-styles' );

	// Get assets version.
	$assets_version = get_assets_version();

	// Enqueue styles.
	wp_register_style( 'tp-multi-select', get_template_directory_uri() . '/dist/vendor/tpmultiselectelement.css', [], $assets_version );
	wp_enqueue_style( 'qrk-global', get_template_directory_uri() . '/dist/global.css', [], $assets_version );
	wp_enqueue_style( 'nunito-sans', get_template_directory_uri() . '/src/assets/fonts/nunito-sans/nunito-sans.css', [], '1' );
	wp_enqueue_style( 'knockout-49-liteweight-regular', get_template_directory_uri() . '/src/assets/fonts/knockout-49-liteweight-regular/knockout-49-liteweight-regular.css', [], '1' );
	wp_enqueue_style( 'source-serif-4', get_template_directory_uri() . '/src/assets/fonts/source-serif-4/source-serif-4.css', [], '1' );
	wp_register_style( 'intl-tel-input-css', get_template_directory_uri() . '/dist/vendor/intltelinput.css', [], $assets_version );
	wp_register_style( 'tp-slider', get_template_directory_uri() . '/dist/vendor/tpsliderelement.css', [], $assets_version );
	wp_register_style( 'tp-lightbox', get_template_directory_uri() . '/dist/vendor/tplightboxelement.css', [], $assets_version );
	wp_register_style( 'tp-tabs', get_template_directory_uri() . '/dist/vendor/tptabselement.css', [], $assets_version );
	wp_register_style( 'tp-accordion', get_template_directory_uri() . '/dist/vendor/tpaccordionitemelement.css', [], $assets_version );
	wp_register_style( 'tp-toggle-attribute', get_template_directory_uri() . '/dist/vendor/tptoggleattributeelement.css', [], $assets_version );
	wp_register_style( 'tp-number-spinner', get_template_directory_uri() . '/dist/vendor/tpnumberspinnerelement.css', [], $assets_version );

	// Defer certain styles.
	add_filter(
		'travelopia_defer_styles',
		function ( $handles = [] ) {
			$handles[] = 'nunito-sans';
			$handles[] = 'source-serif-4';
			$handles[] = 'knockout-49-liteweight-regular';
			$handles[] = 'intl-tel-input-css';
			$handles[] = 'tp-slider';
			$handles[] = 'tp-tabs';
			$handles[] = 'tp-accordion';
			$handles[] = 'tp-lightbox';

			// Return handles.
			return $handles;
		}
	);
}

/**
 * Register and Enqueue Scripts.
 *
 * @return void
 */
function register_scripts(): void {
	// Register and enqueue scripts.
	$assets_version = get_assets_version();

	// Deregister jQuery for logged in users.
	if ( ! is_user_logged_in() ) {
		wp_deregister_script( 'jquery' );
	}

	// Deregister core scripts.
	wp_deregister_script( 'wp-embed' );

	// Enqueue scripts.
	wp_enqueue_script( 'global', get_template_directory_uri() . '/dist/global.js', [], $assets_version, true );
	wp_register_script( 'pristine-js', get_template_directory_uri() . '/dist/vendor/pristine.js', [], $assets_version, true );
	wp_register_script( 'tp-slider', get_template_directory_uri() . '/dist/vendor/tpsliderelement.js', [], $assets_version, true );
	wp_register_script( 'tp-tabs', get_template_directory_uri() . '/dist/vendor/tptabselement.js', [], $assets_version, true );
	wp_register_script( 'tp-accordion', get_template_directory_uri() . '/dist/vendor/tpaccordionitemelement.js', [], $assets_version, true );
	wp_register_script( 'tp-multi-select', get_template_directory_uri() . '/dist/vendor/tpmultiselectelement.js', [], $assets_version, true );
	wp_register_script( 'trustpilot', 'https://widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js', [], $assets_version, true );
	wp_register_script( 'wistia-embed', 'https://fast.wistia.com/assets/external/E-v1.js', [], $assets_version, true );
	wp_register_script( 'instagram-embed', 'https://www.instagram.com/embed.js', [], $assets_version, true );
	wp_register_script( 'tp-lightbox', get_template_directory_uri() . '/dist/vendor/tplightboxelement.js', [], $assets_version, true );
	wp_register_script( 'tp-toggle-attribute', get_template_directory_uri() . '/dist/vendor/tptoggleattributeelement.js', [], $assets_version, true );
	wp_register_script( 'tp-number-spinner', get_template_directory_uri() . '/dist/vendor/tpnumberspinnerelement.js', [], $assets_version, true );
	wp_register_script( 'querystring', get_template_directory_uri() . '/dist/vendor/querystring.js', [], $assets_version, true );
	wp_register_script( 'popover-polyfill', get_template_directory_uri() . '/dist/vendor/popoverpolyfill.js', [], $assets_version, true );

	/**
	 * This is a special case because this component needs to be loaded on all pages whether its initialize or not.
	 */
	quark_component_enqueue_assets( 'dynamic-phone-number' );

	// Pass variables to script.
	wp_localize_script(
		'global',
		'quark',
		[
			'recaptchaSiteKey' => quark_get_template_data( 'recaptcha_site_key', '' ),
			'fetchPartial'     => [
				'url'    => home_url( '/wp-json/quark-core/v1/partial/get' ),
				'method' => 'POST',
			],
			'isChinaSite'      => is_china_website(),
		]
	);

	// Pass dynamic phone number to script.
	wp_localize_script( 'global', 'dynamicPhoneNumber', (array) quark_get_template_data( 'dynamic_phone_number', [] ) );
}

/**
 * Action right before a Blade view is loaded.
 *
 * @param string $name Blade component name.
 *
 * @return void
 */
function blade_view_before_load( string $name = '' ): void {
	// Enqueue component assets.
	quark_component_enqueue_assets( $name );
}

/**
 * Custom resource hints.
 *
 * @param string[] $urls URLs to print for resource hints.
 * @param string   $relation_type The relation type the URLs are printed for.
 *
 * @return string[]
 */
function resource_hints( array $urls = [], string $relation_type = '' ): array {
	// Add custom resource hints.
	switch ( $relation_type ) {

		// DNS Prefetch.
		case 'dns-prefetch':
			$urls[] = 'https://res.cloudinary.com';
			$urls[] = 'https://www.googletagmanager.com';
			$urls[] = 'https://www.google-analytics.com';
			$urls[] = 'https://js-agent.newrelic.com';
			break;

	}

	// Return updated resource hints.
	return $urls;
}

/**
 * Custom allowed HTML for `wp_kses_post`
 *
 * @param mixed[] $tags Allowed HTML tags.
 * @param string  $context Context name.
 *
 * @return mixed[]
 */
function kses_custom_allowed_html( array $tags = [], string $context = 'post' ): array {
	// Do this only for the post context.
	if ( 'post' === $context ) {
		$tags = array_merge(
			$tags,
			[
				'quark-lp-header'                                  => [
					'class'  => true,
					'data-*' => true,
					'style'  => true,
				],
				'quark-form'                                       => [
					'class'         => true,
					'data-action'   => true,
					'style'         => true,
					'thank-you-url' => true,
				],
				'quark-inquiry-form-modal'                         => [
					'class' => true,
				],
				'quark-modal-open'                                 => [
					'class'    => true,
					'modal-id' => true,
				],
				'quark-fancy-video'                                => [
					'class' => true,
					'url'   => true,
				],
				'quark-departure-card'                             => [
					'class' => true,
				],
				'quark-expedition-card'                            => [
					'class' => true,
				],
				'quark-toast-message'                              => [
					'class'   => true,
					'visible' => true,
				],
				'quark-media-lightbox'                             => [
					'class' => true,
					'name'  => true,
				],
				'quark-lp-form-modal-cta'                          => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-video-icons-card'                           => [
					'class'    => true,
					'video_id' => true,
				],
				'quark-hero-overlay'                               => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-secondary-navigation'                       => [
					'class' => true,
				],
				'quark-form-two-step'                              => [
					'class' => true,
				],
				'quark-form-two-step-modal'                        => [
					'class' => true,
				],
				'quark-form-two-step-modal-cta'                    => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-table-of-contents'                          => [
					'class' => true,
				],
				'quark-form-two-step-compact-modal'                => [
					'class' => true,
				],
				'quark-form-two-step-compact-modal-cta'            => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-gated-brochure-modal-cta'                   => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-hero-card-slider'                           => [
					'class' => true,
				],
				'quark-thumbnail-cards-slider'                     => [
					'class' => true,
				],
				'quark-tabs'                                       => [
					'class' => true,
				],
				'quark-itinerary-tabs'                             => [
					'class' => true,
				],
				'quark-header-nav-menu-dropdown'                   => [
					'class' => true,
					'open'  => true,
				],
				'quark-drawer'                                     => [
					'id'                  => true,
					'class'               => true,
					'overlay-click-close' => true,
					'open'                => true,
					'data-*'              => true,
					'animation-direction' => true,
				],
				'quark-drawer-content'                             => [
					'class' => true,
				],
				'quark-drawer-close'                               => [
					'class' => true,
				],
				'quark-drawer-open'                                => [
					'class'     => true,
					'drawer-id' => true,
				],
				'quark-footer-accordion'                           => [
					'class'  => true,
					'active' => true,
				],
				'quark-tooltip'                                    => [
					'class' => true,
				],
				'quark-dates-rates-filter-chip'                    => [
					'class'        => true,
					'accordion_id' => true,
					'type'         => true,
				],
				'quark-dates-rates-table'                          => [
					'class' => true,
				],
				'quark-country-selector'                           => [
					'class' => true,
				],
				'quark-currency-switcher'                          => [
					'class' => true,
				],
				'quark-load-more'                                  => [
					'class'   => true,
					'loading' => true,
				],
				'quark-book-departures-expeditions'                => [
					'class'   => true,
					'loading' => true,
				],
				'quark-book-departures-expeditions-load-more'      => [
					'class'   => true,
					'loading' => true,
					'active'  => true,
				],
				'quark-book-departures-expeditions-results'        => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-featured-media-accordions'                  => [
					'class' => true,
				],
				'quark-wistia-embed'                               => [
					'class'    => true,
					'video-id' => true,
					'play'     => true,
				],
				'quark-dates-rates-results'                        => [
					'class'           => true,
					'data-*'          => true,
					'partial'         => true,
					'selector'        => true,
					'expedition-id'   => true,
					'page'            => true,
					'total-pages'     => true,
					'per-page'        => true,
					'result-count'    => true,
					'server-rendered' => true,
				],
				'quark-dates-rates-filters-controller'             => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-dates-rates-selected-filters'               => [
					'class' => true,
					'empty' => true,
				],
				'quark-dates-rates-selected-filter-pill'           => [
					'class'  => true,
					'filter' => true,
					'value'  => true,
					'data-*' => true,
				],
				'quark-dates-rates-filter-currency-dropdown'       => [
					'class' => true,
				],
				'quark-dates-rates-filter-currency-radios'         => [
					'class' => true,
				],
				'quark-dates-rates-filter-sticky-filter'           => [
					'class' => true,
				],
				'quark-dates-rates-filter-sticky-currency'         => [
					'class' => true,
				],
				'quark-dates-rates-filter-seasons'                 => [
					'class' => true,
				],
				'quark-dates-rates-filter-expeditions'             => [
					'class' => true,
				],
				'quark-dates-rates-filter-adventure-options'       => [
					'class' => true,
				],
				'quark-dates-rates-filter-departure-months'        => [
					'class' => true,
				],
				'quark-dates-rates-filter-durations'               => [
					'class' => true,
				],
				'quark-dates-rates-filter-ships'                   => [
					'class' => true,
				],
				'quark-dates-rates-filters-cta-view-results'       => [
					'class' => true,
				],
				'quark-dates-rates-filters-cta-clear-all'          => [
					'class' => true,
				],
				'quark-dates-rates-pagination-page-number'         => [
					'class'  => true,
					'number' => true,
				],
				'quark-dates-rates-pagination-prev-page'           => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-dates-rates-pagination-next-page'           => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-dates-rates-pagination-links-controller'    => [
					'class'        => true,
					'current-page' => true,
					'total-pages'  => true,
				],
				'quark-dates-rates-pagination-total-pages'         => [
					'class' => true,
				],
				'quark-dates-rates-pagination-items-per-page'      => [
					'class' => true,
				],
				'quark-dates-rates-results-count'                  => [
					'class' => true,
				],
				'quark-dates-rates'                                => [
					'class'   => true,
					'loading' => true,
				],
				'quark-dates-rates-filters-inputs-container'       => true,
				'quark-expedition-search'                          => [
					'class'   => true,
					'loading' => true,
				],
				'quark-expedition-search-load-more'                => [
					'class'          => true,
					'load-more-text' => true,
				],
				'quark-expedition-search-filters'                  => [
					'class' => true,
				],
				'quark-expedition-search-sidebar-filters-header'   => [
					'class' => true,
				],
				'quark-expedition-search-filter-destinations'      => [
					'class' => true,
				],
				'quark-expedition-search-filter-months'            => [
					'class' => true,
				],
				'quark-expedition-search-filter-itinerary-lengths' => [
					'class' => true,
				],
				'quark-expedition-search-filter-ships'             => [
					'class' => true,
				],
				'quark-expedition-search-filter-adventure-options' => [
					'class' => true,
				],
				'quark-expedition-search-filter-languages'         => [
					'class' => true,
				],
				'quark-expedition-search-filter-expeditions'       => [
					'class' => true,
				],
				'quark-expedition-search-filter-cabin-classes'     => [
					'class' => true,
				],
				'quark-expedition-search-filter-travelers'         => [
					'class' => true,
				],
				'quark-expedition-search-filters-cta-clear-all'    => [
					'class' => true,
				],
				'quark-expedition-search-filters-cta-view-results' => [
					'class' => true,
				],
				'quark-expedition-search-sticky-filters'           => [
					'class' => true,
				],
				'quark-expedition-search-selected-filters'         => [
					'class' => true,
				],
				'quark-expedition-search-selected-filter-pill'     => [
					'class' => true,
				],
				'quark-expedition-search-recent-searches'          => [
					'class'  => true,
					'data-*' => true,
				],
				'quark-expedition-search-sidebar-filters'          => [
					'class' => true,
				],
				'quark-expedition-search-results'                  => [
					'class'           => true,
					'parital'         => true,
					'selector'        => true,
					'remaining-count' => true,
					'results-count'   => true,
					'server-rendered' => true,
				],
				'quark-range-slider'                               => [
					'class'          => true,
					'prefix'         => true,
					'min'            => true,
					'max'            => true,
					'selected-value' => true,
					'suffix'         => true,
				],
				'quark-form-newsletter'                            => [
					'class' => true,
				],
				'tp-form'                                          => [
					'class'          => true,
					'prevent-submit' => true,
				],
				'tp-form-field'                                    => [
					'class'           => true,
					'required'        => true,
					'no-empty-spaces' => true,
					'email'           => true,
					'min-length'      => true,
					'max-length'      => true,
					'error'           => true,
					'data-*'          => true,
				],
				'tp-form-submit'                                   => [
					'class'           => true,
					'submitting-text' => true,
				],
				'tp-slider'                                        => [
					'class'           => true,
					'flexible-height' => true,
					'swipe'           => true,
					'infinite'        => true,
				],
				'tp-tabs'                                          => [
					'class'       => true,
					'current-tab' => true,
					'update-url'  => true,
				],
				'tp-tabs-nav'                                      => [],
				'tp-tabs-tab'                                      => [
					'id'    => true,
					'class' => true,
					'open'  => true,
				],
				'tp-tabs-nav-item'                                 => [
					'class'  => true,
					'active' => true,
				],
				'tp-slider-track'                                  => [
					'class' => true,
				],
				'tp-slider-slides'                                 => [
					'class' => true,
				],
				'tp-slider-slide'                                  => [
					'class' => true,
				],
				'tp-slider-nav'                                    => [
					'class' => true,
				],
				'tp-slider-arrow'                                  => [
					'class'     => true,
					'direction' => true,
				],
				'tp-slider-nav-item'                               => [
					'class'   => true,
					'current' => true,
				],
				'tp-modal'                                         => [
					'id'                  => true,
					'class'               => true,
					'overlay-click-close' => true,
					'open'                => true,
					'data-*'              => true,
				],
				'tp-modal-content'                                 => [
					'class' => true,
				],
				'tp-modal-close'                                   => [
					'class' => true,
				],
				'tp-accordion'                                     => [
					'class' => true,
				],
				'tp-accordion-item'                                => [
					'class'           => true,
					'id'              => true,
					'open-by-default' => true,
				],
				'tp-accordion-handle'                              => [
					'class' => true,
					'id'    => true,
				],
				'tp-accordion-content'                             => [
					'class' => true,
				],
				'tp-lightbox-trigger'                              => [
					'lightbox' => true,
					'group'    => true,
				],
				'tp-lightbox'                                      => [
					'id'    => true,
					'class' => true,
				],
				'tp-lightbox-close'                                => [
					'class' => true,
				],
				'tp-lightbox-content'                              => [
					'class' => true,
				],
				'tp-lightbox-previous'                             => [
					'class' => true,
				],
				'tp-lightbox-next'                                 => [
					'class' => true,
				],
				'tp-lightbox-count'                                => [
					'class' => true,
				],
				'tp-multi-select'                                  => [
					'name'            => true,
					'class'           => true,
					'id'              => true,
					'form'            => true,
					'multiple'        => true,
					'close-on-select' => true,
				],
				'tp-multi-select-field'                            => [
					'class' => true,
					'id'    => true,
				],
				'tp-multi-select-pills'                            => [
					'class' => true,
					'id'    => true,
				],
				'tp-multi-select-search'                           => [
					'class' => true,
					'id'    => true,
				],
				'tp-multi-select-placeholder'                      => [
					'class' => true,
					'id'    => true,
				],
				'tp-multi-select-status'                           => [
					'class'  => true,
					'id'     => true,
					'format' => true,
				],
				'tp-multi-select-options'                          => [
					'class' => true,
					'id'    => true,
				],
				'tp-multi-select-option'                           => [
					'class' => true,
					'id'    => true,
					'value' => true,
					'label' => true,
				],
				'tp-multi-select-select-all'                       => [
					'class'         => true,
					'id'            => true,
					'select-text'   => true,
					'unselect-text' => true,
				],
				'tp-number-spinner'                                => [
					'class' => true,
					'id'    => true,
					'min'   => true,
					'max'   => true,
					'step'  => true,
				],
				'tp-number-spinner-increment'                      => [
					'class' => true,
				],
				'tp-number-spinner-decrement'                      => [
					'class' => true,
				],
				'tp-number-spinner-input'                          => [
					'class' => true,
				],
				'iframe'                                           => [
					'class'           => true,
					'src'             => true,
					'height'          => true,
					'width'           => true,
					'allow'           => true,
					'allowfullscreen' => true,
					'title'           => true,
					'frameborder'     => true,
					'data-*'          => true,
				],
				'svg'                                              => [
					'id'              => true,
					'class'           => true,
					'aria-hidden'     => true,
					'aria-labelledby' => true,
					'role'            => true,
					'xmlns'           => true,
					'width'           => true,
					'height'          => true,
					'viewbox'         => true,
					'fill'            => true,
				],
				'g'                                                => [
					'fill'      => true,
					'fill-rule' => true,
					'transform' => true,
				],
				'title'                                            => [
					'title' => true,
				],
				'path'                                             => [
					'id'             => true,
					'd'              => true,
					'fill'           => true,
					'stroke'         => true,
					'stroke-linecap' => true,
					'stroke-width'   => true,
					'mask'           => true,
					'fill-rule'      => true,
					'transform'      => true,
					'opacity'        => true,
				],
				'mask'                                             => [
					'id'   => true,
					'fill' => true,
				],
				'polygon'                                          => [
					'id'     => true,
					'points' => true,
					'fill'   => true,
				],
				'circle'                                           => [
					'cx'             => true,
					'cy'             => true,
					'r'              => true,
					'stroke'         => true,
					'fill'           => true,
					'stroke-opacity' => true,
					'stroke-width'   => true,
				],
				'defs'                                             => true,
				'use'                                              => [
					'xlink:href' => true,
				],
				'lineargradient'                                   => [
					'id' => true,
					'x1' => true,
					'x2' => true,
					'y1' => true,
					'y2' => true,
				],
				'stop'                                             => [
					'offset'     => true,
					'stop-color' => true,
				],
				'form'                                             => [
					'id'         => true,
					'class'      => true,
					'method'     => true,
					'action'     => true,
					'novalidate' => true,
					'data-*'     => true,
				],
				'select'                                           => [
					'name'     => true,
					'class'    => true,
					'id'       => true,
					'form'     => true,
					'multiple' => true,
				],
				'option'                                           => [
					'value'    => true,
					'selected' => true,
				],
				'input'                                            => [
					'class'        => true,
					'type'         => true,
					'autocomplete' => true,
					'name'         => true,
					'placeholder'  => true,
					'required'     => true,
					'checked'      => true,
					'value'        => true,
					'id'           => true,
					'form'         => true,
					'data-*'       => true,
				],
				'textarea'                                         => [
					'id'          => true,
					'class'       => true,
					'placeholder' => true,
					'name'        => true,
					'rows'        => true,
				],
				'div'                                              => [
					'tabindex' => true,
					'id'       => true,
					'class'    => true,
					'data-*'   => true,
					'style'    => true,
				],
				'source'                                           => [
					'src'    => true,
					'type'   => true,
					'srcset' => true,
					'media'  => true,
				],
				'dialog'                                           => [
					'class' => true,
				],
				'template'                                         => [
					'class' => true,
				],
				'quark-dynamic-phone-number'                       => true,
			]
		);
	}

	// Return updated kses tags.
	return $tags;
}

/**
 * Get assets version.
 *
 * @return string
 */
function get_assets_version(): string {
	// Set cached version.
	static $version = null;

	// Look for cached version.
	if ( is_string( $version ) ) {
		return $version;
	}

	// Add fallback version.
	$version = '1';

	// Set assets file path.
	$assets_file_path = get_stylesheet_directory() . '/dist/site-assets.php';

	// Look for assets file.
	if ( ! file_exists( $assets_file_path ) ) {
		return $version;
	}

	// File exists, include it.
	$site_assets_data = include $assets_file_path;

	// Get version from assets file.
	if ( ! is_array( $site_assets_data ) || empty( $site_assets_data['version'] ) ) {
		return $version;
	}

	// Update version.
	$version = strval( $site_assets_data['version'] );

	// Return the version found in the assets file.
	return $version;
}

/**
 * Remove the default title tag.
 *
 * This should be ideally handled by Yoast, but it doesn't invoke this remove_action on correct hook, therefore this patch for now.
 *
 * @param string $template Template to be rendered.
 *
 * @return string
 */
function remove_default_title_tag( string $template = '' ): string {
	// Remove the default title tag added by WordPress when a block theme is activated.
	remove_action( 'wp_head', '_block_template_render_title_tag', 1 );

	// Return the template as-it-is.
	return $template;
}
