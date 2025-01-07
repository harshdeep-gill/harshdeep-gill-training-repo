<?php
/**
 * Namespace functions.
 *
 * @package quark-core
 */

namespace Quark\Core;

use JB\Cloudinary\Core as Cloudinary_Core;
use JB\Cloudinary\Frontend as Cloudinary_Frontend;

use WP_Post;
use WP_Screen;
use WP_Term;
use WP_Query;
use WP_User;

use function Travelopia\Core\cached_nav_menu;

use const Quark\Localization\AUD_CURRENCY;
use const Quark\Localization\CAD_CURRENCY;
use const Quark\Localization\DEFAULT_CURRENCY;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Localization\GBP_CURRENCY;
use const Quark\Localization\USD_CURRENCY;

const REST_API_NAMESPACE = 'quark-core/v1';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Other hooks.
	add_action( 'admin_menu', __NAMESPACE__ . '\\setup_settings' );
	add_action( 'init', __NAMESPACE__ . '\\nav_menus' );
	add_filter( 'quark_front_end_data', __NAMESPACE__ . '\\core_front_end_data' );
	add_filter( 'wp_mail_from', __NAMESPACE__ . '\\default_email_from_address' );
	add_filter( 'wp_mail_from_name', __NAMESPACE__ . '\\default_email_from_name' );
	add_action( 'admin_menu', __NAMESPACE__ . '\\reusable_blocks_menu_item' );

	// Allow Auto Cloudinary in REST API calls.
	add_action( 'rest_api_init', __NAMESPACE__ . '\\init_auto_cloudinary' );
	add_filter( 'cloudinary_allow_rest_api_call', '__return_true' );

	// Rest API.
	add_action( 'rest_api_init', __NAMESPACE__ . '\\register_endpoints' );
	add_filter( 'travelopia_security_public_rest_api_routes', __NAMESPACE__ . '\\security_public_rest_api_routes' );

	// Filter for Attachments.
	add_filter( 'add_attachment', __NAMESPACE__ . '\\update_svg_content', 10, 4 );
	add_filter( 'upload_mimes', __NAMESPACE__ . '\\allow_mime_types' );

	// Get front-end markup for manipulation.
	add_filter( 'wp_body_open', __NAMESPACE__ . '\\start_output_buffering' );
	add_filter( 'wp_footer', __NAMESPACE__ . '\\end_output_buffering' );

	// Set Excerpt length - Set higher priority to override other plugins.
	add_filter( 'excerpt_length', __NAMESPACE__ . '\\increase_excerpt_length', 999 );

	// Limit post revision.
	add_filter( 'wp_revisions_to_keep', __NAMESPACE__ . '\\limit_revisions_for_posts', 10, 2 );

	// Custom fields.
	if ( is_admin() ) {
		require_once __DIR__ . '/../custom-fields/options-social.php';
		require_once __DIR__ . '/../custom-fields/attachments.php';
		require_once __DIR__ . '/../custom-fields/pages-setup.php';

		// Custom styles for ACF fields.
		add_action( 'acf/input/admin_head', __NAMESPACE__ . '\\acf_styles_for_read_only_fields' );
	}
}

/**
 * Site settings.
 *
 * @return void
 */
function setup_settings(): void {
	// Check if ACF plugin is enabled.
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	// Social.
	acf_add_options_sub_page(
		[
			'page_title'  => 'Social',
			'menu_title'  => 'Social',
			'parent_slug' => 'site-settings',
			'capability'  => 'manage_options',
		]
	);

	// Pages Setup.
	acf_add_options_sub_page(
		[
			'page_title'  => 'Pages Setup',
			'menu_title'  => 'Pages Setup',
			'parent_slug' => 'site-settings',
		]
	);
}

/**
 * Register navigation menus.
 *
 * @return void
 */
function nav_menus(): void {
	// Register all nav menus.
	register_nav_menus(
		[
			'main' => 'Main Menu',
		]
	);
}

/**
 * Initialize the Auto Cloudinary plugin.
 *
 * @return void
 */
function init_auto_cloudinary(): void {
	// Check if Auto Cloudinary plugin is enabled.
	if ( class_exists( 'JB\Cloudinary\Core' ) && class_exists( 'JB\Cloudinary\Frontend' ) ) {
		// Get core instance.
		$core = Cloudinary_Core::get_instance();

		// Check if we got the instance.
		if ( $core instanceof Cloudinary_Core ) {
			$core->setup();
		}

		// Get front-end instance.
		$front_end = Cloudinary_Frontend::get_instance();

		// Check if we got the instance.
		if ( $front_end instanceof Cloudinary_Frontend ) {
			$front_end->setup();
		}
	}
}

/**
 * Get core front-end data.
 *
 * @param mixed[] $data Template data.
 *
 * @return mixed[]
 */
function core_front_end_data( array $data = [] ): array {
	// Build core data.
	$header_options = [
		'logo_url' => home_url(),
		'nav_menu' => cached_nav_menu(
			[
				'theme_location' => 'main',
				'container'      => '',
				'menu_class'     => '',
				'menu_id'        => '',
			]
		),
	];

	// Get socials links from options.
	$social_options = [
		'facebook'  => get_option( 'options_facebook_url', '' ),
		'twitter'   => get_option( 'options_twitter_url', '' ),
		'instagram' => get_option( 'options_instagram_url', '' ),
		'pinterest' => get_option( 'options_pinterest_url', '' ),
		'youtube'   => get_option( 'options_youtube_url', '' ),
	];

	// Return updated data.
	return array_merge(
		$data,
		[
			'current_url'     => get_permalink(),
			'site_url'        => home_url(),
			'site_name'       => get_bloginfo( 'name' ),
			'header'          => $header_options,
			'social_links'    => $social_options,
			'search_page_url' => strval( get_permalink( absint( get_option( 'options_expedition_search_page' ) ) ) ),
		]
	);
}

/**
 * Default "from" email address.
 *
 * @return string
 */
function default_email_from_address(): string {
	// Update default "from" email address.
	return 'noreply@quarkexpeditions.com';
}

/**
 * Register REST API endpoints.
 *
 * @return void
 */
function register_endpoints(): void {
	// Include rest API file.
	require_once __DIR__ . '/../rest-api/class-partial.php';

	// List the REST APIs needed.
	$endpoints = [
		new RestApi\Partial(),
	];

	// Register the rest routes.
	foreach ( $endpoints as $endpoint ) {
		$endpoint->register_routes();
	}
}

/**
 * Register public REST API routes.
 *
 * @param string[] $routes Public routes.
 *
 * @return string[]
 */
function security_public_rest_api_routes( array $routes = [] ): array {
	// Add routes.
	$routes[] = sprintf( '/%s/partial/get', REST_API_NAMESPACE );

	// Return routes.
	return $routes;
}

/**
 * Default "from" name.
 *
 * @return string
 */
function default_email_from_name(): string {
	// Update default "from" name.
	return 'Quark Expeditions';
}

/**
 * Add an admin menu item for Reusable Blocks.
 *
 * @return void
 */
function reusable_blocks_menu_item(): void {
	// Add a menu page for Reusable Blocks.
	add_menu_page(
		'Reusable Blocks',
		'Reusable Blocks',
		'edit_posts',
		'edit.php?post_type=wp_block',
		'', // @phpstan-ignore-line
		'dashicons-controls-repeat',
		22
	);
}

/**
 * Get front-end data.
 *
 * All front-end data goes through this function.
 *
 * @param bool $force Force uncached version.
 *
 * @return mixed[]
 */
function get_front_end_data( bool $force = false ): array {
	// Set cached version.
	static $template_data = [];

	// Check if template data is already set and not forced.
	if ( false === $force && ! empty( $template_data ) ) {
		return $template_data;
	}

	// Get front-end data.
	$template_data = (array) apply_filters( 'quark_front_end_data', [] );

	// Return updated front-end data.
	return $template_data;
}

/**
 * Update attachment meta data for SVG.
 *
 * @param int $post_id Attachment ID.
 *
 * @return void
 */
function update_svg_content( int $post_id = 0 ): void {
	// Get Attachment.
	$post = get_post( $post_id );

	// If post is not instance of WP_Post then bail out.
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	// If attachment is not SVG then bail out.
	if ( 'attachment' !== $post->post_type || 'image/svg+xml' !== $post->post_mime_type ) {
		return;
	}

	// Get attached file.
	$attached_file = get_attached_file( $post_id );

	// If Attached file is empty then bail out.
	if ( empty( $attached_file ) ) {
		return;
	}

	// Get SVG content.
	$content = file_get_contents( $attached_file );  // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	// If SVG content is empty then bail out.
	if ( empty( $content ) ) {
		return;
	}

	// Update SVG content.
	update_post_meta( $post_id, 'svg_content', wp_kses_post( $content ) );
}

/**
 * Allow mime type.
 *
 * @param array{}|array<string, string> $mime_types Mime types.
 *
 * @return array{} Mime types.
 */
function allow_mime_types( array $mime_types = [] ): array {
	// Adding svg extension.
	$mime_types['svg'] = 'image/svg+xml';

	// Return mime types.
	return $mime_types;
}

/**
 * Get a visitor's geo country.
 *
 * @note This uses Pantheon's AGCDN header which sets
 *       a two-letter ISO string for the visitor's country.
 *
 * @return string
 */
function get_visitor_geo_country(): string {
	// init geolocation.
	static $geolocation = null;

	// Return if already set.
	if ( null !== $geolocation ) {
		return $geolocation;
	}

	// Get headers.
	$headers = getallheaders();

	// Get geolocation.
	if ( ! empty( $headers['x-geocountry'] ) ) {
		$geolocation = strtoupper( sanitize_text_field( $headers['x-geocountry'] ) );
	} elseif ( ! empty( $headers['X-Geo-Country'] ) ) {
		$geolocation = strtoupper( sanitize_text_field( $headers['X-Geo-Country'] ) );
	} elseif ( ! empty( $headers['X-Geo-Country-Code'] ) ) {
		$geolocation = strtoupper( sanitize_text_field( $headers['X-Geo-Country-Code'] ) );
	} else {
		$geolocation = '';
	}

	// Return geolocation.
	return $geolocation;
}

/**
 * Check if we are currently in an automated test.
 *
 * @return bool
 */
function doing_automated_test(): bool {
	// Get automated test User Agent.
	if ( defined( 'QUARK_AUTOMATED_TEST_USER_AGENT' ) && ! empty( QUARK_AUTOMATED_TEST_USER_AGENT ) ) {
		$test_user_agent = QUARK_AUTOMATED_TEST_USER_AGENT;
	} else {
		$test_user_agent = '';
	}

	// Check if we have this option, and if it's the same as the current User Agent.
	if ( ! empty( $test_user_agent ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) && $test_user_agent === $_SERVER['HTTP_USER_AGENT'] ) {
		return true;
	}

	// Nope, user agent does not match, not an automated test.
	return false;
}

/**
 * Format price.
 *
 * @param float  $price Price.
 * @param string $currency Currency.
 *
 * @return string Formatted price.
 */
function format_price( float $price = 0, string $currency = DEFAULT_CURRENCY ): string {
	// Set default separators.
	$string_format = '%1$s%2$s %3$s';

	// Set Currency symbol.
	$currency_symbols = [
		AUD_CURRENCY => '$',
		CAD_CURRENCY => '$',
		USD_CURRENCY => '$',
		EUR_CURRENCY => '€',
		GBP_CURRENCY => '£',
	];

	// Validate currency.
	$currency = array_key_exists( strtoupper( $currency ), $currency_symbols ) ? strtoupper( $currency ) : DEFAULT_CURRENCY;

	// Current symbol.
	$currency_symbol     = $currency_symbols[ strtoupper( $currency ) ];
	$decimal_separator   = '.';
	$thousands_separator = ',';

	// Decimal separator.
	$decimals = fmod( $price, absint( $price ) ) ? 2 : 0;

	// Return formatted price.
	return sprintf(
		$string_format,
		$currency_symbol,
		number_format( $price, $decimals, $decimal_separator, $thousands_separator ),
		$currency
	);
}

/**
 * Organize terms by hierarchy.
 *
 * @param array<int> $terms Terms.
 * @param string     $taxonomy Taxonomy.
 *
 * @return array{}|array{
 *     array{
 *         parent_term: WP_Term,
 *         child_terms: array<WP_Term>
 *    }
 * }
 */
function order_terms_by_hierarchy( array $terms = [], string $taxonomy = '' ): array {
	// Organise terms.
	$organised_terms = [];

	// Check for terms.
	if ( empty( $terms ) || empty( $taxonomy ) ) {
		return $organised_terms;
	}

	// Loop through terms and organise them.
	foreach ( $terms as $term ) {
		// Get the term.
		$term = get_term( $term, $taxonomy );

		// Check for term.
		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		// Organise terms by parent.
		if ( empty( $term->parent ) ) {
			$organised_terms[ $term->term_id ]['parent_term'] = $term;
		} else {
			$organised_terms[ $term->parent ]['child_terms'][] = $term;

			// Check parent term added.
			if ( empty( $organised_terms[ $term->parent ]['parent_term'] ) ) {
				$parent_term = get_term( $term->parent, $taxonomy );

				// Check for term.
				if ( ! $parent_term instanceof WP_Term ) {
					continue;
				}

				// Add parent term.
				$organised_terms[ $parent_term->term_id ]['parent_term'] = $parent_term;
			}
		}
	}

	// Return organised terms.
	return $organised_terms;
}

/**
 * Get pure text from HTML.
 * Useful for title or content of post.
 *
 * @param string $html HTML content.
 *
 * @return string
 */
function get_raw_text_from_html( string $html = '' ): string {
	// Check if HTML is empty.
	if ( empty( $html ) ) {
		return '';
	}

	// Get pure text.
	$text = wp_strip_all_tags( $html, true );

	// Remove non-breaking space.
	$text = str_replace( '&nbsp;', ' ', $text );

	// Decode HTML entities.
	$text = html_entity_decode( $text );

	// Convert encoding to UTF-8.
	$text = mb_convert_encoding( $text, 'UTF-8', 'UTF-8' );

	// Return pure text.
	return $text;
}

/**
 * Check if the test is in progress.
 *
 * @return bool
 */
function doing_tests(): bool {
	// Check if doing tests.
	return defined( 'WP_TESTS' ) && true === WP_TESTS;
}

/**
 * Get pagination links based on arguments.
 *
 * @param array<mixed|string> $args Pagination args.
 *
 * @return string
 */
function get_pagination_links( array $args = [] ): string {
	// Build args.
	$args = wp_parse_args(
		$args,
		[
			'query'   => false,
			'noindex' => false,
		]
	);

	// Check for query.
	if ( empty( $args['query'] ) || ! $args['query'] instanceof WP_Query ) {
		global $wp_query;
		$args['query'] = $wp_query;
	}

	// Get current page number.
	$current = max( 1, $args['query']->get( 'paged' ) );
	$total   = $args['query']->max_num_pages;

	// Get pagination links.
	$pagination_links = paginate_links(
		[
			'current'   => $current,
			'total'     => $total,
			'prev_text' => __( 'Previous', 'qrk' ),
			'next_text' => __( 'Next ', 'qrk' ),
			'type'      => 'array',
		]
	);

	// Prepare pagination links.
	if ( is_array( $pagination_links ) ) {
		$previous = '';
		$next     = '';

		// Shift previous link.
		if ( $current && 1 < $current ) {
			$previous = array_shift( $pagination_links );
		}

		// Pop next link.
		if ( $current < $total ) {
			$next = array_pop( $pagination_links );
		}

		// Get First and Last page.
		$first_page = strval( array_shift( $pagination_links ) );
		$last_page  = strval( array_pop( $pagination_links ) );

		// Check for dots.
		$has_dots_after_first_page = false;
		$has_dots_before_last_page = false;

		// Check for dots.
		if ( ! empty( $pagination_links[0] ) ) {
			$has_dots_after_first_page = str_contains( $pagination_links[0], 'dots' );
		}

		// Check for dots.
		if ( ! empty( $pagination_links[ count( $pagination_links ) - 1 ] ) ) {
			$has_dots_before_last_page = str_contains( $pagination_links[ count( $pagination_links ) - 1 ], 'dots' );
		}

		// Prepare pagination links.
		$pagination_links = sprintf(
			"%s\n%s\n%s\n%s\n%s",
			$previous,
			! $has_dots_after_first_page ? $first_page : '',
			implode( "\n", $pagination_links ),
			! $has_dots_before_last_page ? $last_page : '',
			$next
		);
	}

	// Bail out if pagination links are empty.
	if ( empty( $pagination_links ) ) {
		return '';
	}

	// Check for noindex.
	if ( true === $args['noindex'] ) {
		$pagination_links = str_replace( ' href=', ' rel="noindex, nofollow" href=', $pagination_links );
	}

	// Remove trailing slash from main page.
	$current_post = get_queried_object();

	// Check if current post is instance of WP_Post.
	if ( $current_post instanceof WP_Post ) {
		$post_slug        = $current_post->post_name;
		$pagination_links = str_replace( $post_slug . '/"', $post_slug . '"', $pagination_links );
	} elseif ( $current_post instanceof WP_Term ) {
		$pagination_links = str_replace( $current_post->slug . '/"', $current_post->slug . '"', $pagination_links );
	} elseif ( $current_post instanceof WP_User ) {
		$pagination_links = str_replace( $current_post->data->user_login . '/"', $current_post->data->user_login . '"', $pagination_links );
	}

	// All done, return build pagination links.
	return $pagination_links;
}

/**
 * Get first pagination link.
 *
 * @param array<mixed|string> $args Pagination args.
 *
 * @return string
 */
function get_first_pagination_link( array $args = [] ): string {
	// Build args.
	$args = wp_parse_args(
		$args,
		[
			'noindex' => false,
		]
	);

	// Get first page link.
	$first_page_link = get_pagenum_link();

	// Check if we have first page link.
	if ( empty( $first_page_link ) ) {
		return '';
	}

	// Check for noindex.
	if ( true === $args['noindex'] ) {
		$first_page_link = str_replace( ' href=', ' rel="noindex, nofollow" href=', $first_page_link );
	}

	// Remove trailing slash from main page.
	$current_post = get_queried_object();

	// Check if current post is instance of WP_Post.
	if ( $current_post instanceof WP_Post ) {
		$post_slug       = $current_post->post_name;
		$first_page_link = str_replace( $post_slug . '/"', $post_slug . '"', $first_page_link );
	} elseif ( $current_post instanceof WP_Term ) {
		$first_page_link = str_replace( $current_post->slug . '/"', $current_post->slug . '"', $first_page_link );
	} elseif ( $current_post instanceof WP_User ) {
		$first_page_link = str_replace( $current_post->data->user_login . '/"', $current_post->data->user_login . '"', $first_page_link );
	}

	// Return first page link.
	return $first_page_link;
}

/**
 * Get last pagination link.
 *
 * @param array<mixed|string> $args Pagination args.
 *
 * @return string
 */
function get_last_pagination_link( array $args = [] ): string {
	// Build args.
	$args = wp_parse_args(
		$args,
		[
			'total'   => 1,
			'noindex' => false,
		]
	);

	// Get last page link.
	$last_page_link = get_pagenum_link( $args['total'] );

	// Check if we have last page link.
	if ( empty( $last_page_link ) ) {
		return '';
	}

	// Check for noindex.
	if ( true === $args['noindex'] ) {
		$last_page_link = str_replace( ' href=', ' rel="noindex, nofollow" href=', $last_page_link );
	}

	// Remove trailing slash from main page.
	$current_post = get_queried_object();

	// Check if current post is instance of WP_Post.
	if ( $current_post instanceof WP_Post ) {
		$post_slug      = $current_post->post_name;
		$last_page_link = str_replace( $post_slug . '/"', $post_slug . '"', $last_page_link );
	} elseif ( $current_post instanceof WP_Term ) {
		$last_page_link = str_replace( $current_post->slug . '/"', $current_post->slug . '"', $last_page_link );
	} elseif ( $current_post instanceof WP_User ) {
		$last_page_link = str_replace( $current_post->data->user_login . '/"', $current_post->data->user_login . '"', $last_page_link );
	}

	// Return last page link.
	return $last_page_link;
}

/**
 * Check if we are in the block editor.
 * We don't have any functionality to identify if we are in the block editor inside the block render callback.
 *
 * Warning: This function is not 100% reliable, it's just a workaround.
 * And the function should be used strictly inside render callback.
 *
 * Reference:
 * https://wordpress.stackexchange.com/questions/398378/gutenberg-how-to-hide-server-side-render-output-in-the-editor-but-keep-it-in-fr
 *
 * @return bool
 */
function is_block_editor(): bool {
	// Check if we are in the block editor.
	if ( wp_is_serving_rest_request() ) {
		return true;
	}

	// Not in the block editor.
	return false;
}

/**
 * Start output buffering.
 *
 * @return void
 */
function start_output_buffering(): void {
	// Start output buffering.
	ob_start();
}

/**
 * End output buffering.
 *
 * @return void
 */
function end_output_buffering(): void {
	// Get the buffered content.
	$content = ob_get_clean();

	// Apply filters before rendered.
	$content = strval( apply_filters( 'quark_front_end_markup', $content ) );

	// Render the markup.
	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Increase excerpt length.
 *
 * @return int
 */
function increase_excerpt_length(): int {
	// Return excerpt length.
	return 255;
}

/**
 * Limit revisions for posts.
 *
 * @param int          $num Number of revisions to save for the posts.
 * @param WP_Post|null $post Post object.
 *
 * @return int Number of revisions to save for the posts.
 */
function limit_revisions_for_posts( int $num = 0, WP_Post $post = null ): int {
	// Check if post is not null and post type supports revisions.
	if (
		$post instanceof WP_Post
		&& post_type_supports( $post->post_type, 'revisions' )
	) {
		// Limit to 5 revisions for posts.
		$num = 5;
	}

	// Return number of revisions.
	return $num;
}

/**
 * Add custom styles for read-only ACF fields.
 *
 * @return void
 */
function acf_styles_for_read_only_fields(): void {
	// Get the current screen information.
	$screen = get_current_screen();

	// Check if we're on a post edit screen and ACF is active.
	if ( $screen instanceof WP_Screen && 'post' === $screen->base ) {
		?>
		<style>
			/* Read-only styling for ACF Fields with class .quark-readonly-field */
			.acf-field.quark-readonly-field {
				position: relative;
				cursor: not-allowed;
			}

			.acf-field.quark-readonly-field .acf-input {
				pointer-events: none;
			}

			.quark-readonly-field .acf-fields,
			.quark-readonly-field .select2 .select2-selection {
				background-color: #f0f0f1;
			}
		</style>
		<?php
	}
}
