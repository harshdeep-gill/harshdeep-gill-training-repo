<?php
/**
 * Custom MultilingualPress Post Meta Module.
 *
 * @package quark-multilingual
 */

namespace Quark\Multilingual\MultilingualPress_Modules;

use WP_Post;
use WP_Error;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\Metabox;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxTab;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxField;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper;

use function Travelopia\Multilingual\get_languages;
use function Travelopia\Multilingual\get_post_translations;
use function Travelopia\Multilingual\get_term_translations;
use function Travelopia\Multilingual\get_translated_image;
use function Travelopia\Translation\translate_strings;

/**
 * Class Post_Meta.
 */
class Post_Meta implements ModuleServiceProvider {
	// phpcs:disable Travelopia.Whitespace.GroupedConst.AddEmptyLineBeforeConstGroup
	/**
	 * Module ID.
	 *
	 * @var string
	 */
	const MODULE_ID = 'qrk_post_meta';

	/**
	 * Metabox field ID.
	 *
	 * @var string
	 */
	const TAB = 'tab-post-meta';

	/**
	 * Metabox field key.
	 *
	 * @var string
	 */
	const FIELD_COPY_POST_META = 'remote-post-meta-copy';
	// phpcs:enable Travelopia.Whitespace.GroupedConst.AddEmptyLineBeforeConstGroup

	/**
	 * Registers the provided services on the given container.
	 *
	 * @param Container $container Container.
	 *
	 * @return void
	 */
	public function register( Container $container ): void { // phpcs:ignore Travelopia.Functions.FunctionArgsDefaultValue.Missing
		// Do nothing.
	}

	/**
	 * Registers the module at the module manager.
	 *
	 * @param ?ModuleManager $module_manager Module manager.
	 *
	 * @throws ModuleAlreadyRegistered If the module is already registered.
	 *
	 * @return bool
	 */
	public function registerModule( ModuleManager $module_manager = null ): bool {
		// Bail if module manager is not provided.
		if ( ! $module_manager instanceof ModuleManager ) {
			return false;
		}

		// Register the module.
		return $module_manager->register(
			new Module(
				self::MODULE_ID,
				[
					'description' => __(
						'Enable Post Meta synchronization support for MultilingualPress.',
						'qrk'
					),
					'name'        => __( 'Post Meta', 'qrk' ),
					'active'      => true,
				]
			)
		);
	}

	/**
	 * Performs various tasks on module activation.
	 *
	 * @param Container $container Container.
	 *
	 * @return void
	 */
	public function activateModule( Container $container ): void { // phpcs:ignore Travelopia.Functions.FunctionArgsDefaultValue.Missing
		// Register the module.
		$this->setup_metabox_fields();
		$this->enable_post_meta_sync_support();
	}

	/**
	 * Setup Metabox Fields.
	 *
	 * @return void
	 */
	private function setup_metabox_fields(): void {
		// Add the post meta field to the metabox.
		$post_meta_fields = new MetaboxTab(
			self::TAB,
			__( 'Post Meta', 'qrk' ),
			new MetaboxField(
				self::FIELD_COPY_POST_META,
				[ __CLASS__, 'render_post_meta_metabox' ],
				static function ( $value ) {
					return filter_var( $value, FILTER_VALIDATE_BOOLEAN ) ? '1' : '';
				}
			)
		);

		// Register the post meta field.
		add_filter(
			Metabox::HOOK_PREFIX . 'tabs',
			static function ( array $tabs ) use ( $post_meta_fields ) {
				return array_merge( $tabs, [ $post_meta_fields ] );
			},
			10,
			2
		);
	}

	/**
	 * Render the post meta metabox.
	 *
	 * @param MetaboxFieldsHelper $helper  Helper.
	 * @param RelationshipContext $context Context.
	 *
	 * @return void
	 */
	public static function render_post_meta_metabox( MetaboxFieldsHelper $helper, RelationshipContext $context ): void { // phpcs:ignore Travelopia.Functions.FunctionArgsDefaultValue.Missing
		// Get the post meta field id and name.
		$id   = $helper->fieldId( self::FIELD_COPY_POST_META );
		$name = $helper->fieldName( self::FIELD_COPY_POST_META );

		// HTML markup for the post meta field.
		?>
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Copy Post Meta', 'qrk' ); ?>
			</th>
			<td>
				<label for="<?php echo esc_attr( $id ); ?>">
					<input
						type="checkbox"
						name="<?php echo esc_attr( $name ); ?>"
						value="1"
						id="<?php echo esc_attr( $id ); ?>"
					/>
					<?php
					esc_html_e(
						'Overwrites the post meta on translated post with the post meta of source post.',
						'qrk'
					);
					?>
				</label>
			</td>
		</tr>
		<?php
	}

	/**
	 * Enable post meta sync support.
	 *
	 * @return void
	 */
	private function enable_post_meta_sync_support(): void {
		// Add filter to sync post meta keys.
		add_filter(
			PostRelationSaveHelper::FILTER_SYNC_KEYS,
			[ __CLASS__, 'sync_post_meta_keys' ],
			10,
			3
		);
	}

	/**
	 * Sync post meta keys.
	 *
	 * @param string[]            $meta_keys Meta keys.
	 * @param RelationshipContext $context   Context.
	 * @param Request             $request   Request.
	 *
	 * @return string[]
	 */
	public static function sync_post_meta_keys( array $meta_keys, RelationshipContext $context, Request $request ): array { // phpcs:ignore Travelopia.Functions.FunctionArgsDefaultValue.Missing
		// Get request object.
		$multilingualpress = $request->bodyValue(
			'multilingualpress',
			INPUT_POST,
			FILTER_DEFAULT,
			FILTER_FORCE_ARRAY
		);

		// Bail if multilingualpress is not an array.
		if ( ! is_array( $multilingualpress ) ) {
			return $meta_keys;
		}

		// Get the translation data.
		$translation    = $multilingualpress[ "site-{$context->remoteSiteId()}" ] ?? '';
		$relation       = $translation[ MetaboxFields::FIELD_RELATION ] ?? '';
		$copy_post_meta = $translation[ self::FIELD_COPY_POST_META ] ?? false;

		// Check if post meta should be copied.
		if (
			MetaboxFields::FIELD_RELATION_NEW === $relation
			|| $copy_post_meta
		) {
			// Get the post id of the post being translated.
			$post_id = $context->sourcePostId();

			/*
			 * Since we need all the meta keys of the particular post,
			 * we will get all the metadata of the post and later extract the meta keys.
			 */
			$post_meta = get_post_meta( $post_id );

			// Bail if post meta is not an array.
			if ( ! is_array( $post_meta ) ) {
				return $meta_keys;
			}

			// Get all the meta keys of the post.
			$meta_keys = array_merge( $meta_keys, array_keys( $post_meta ) );

			// Filter post meta values.
			add_filter(
				PostRelationSaveHelper::FILTER_METADATA,
				[ __CLASS__, 'filter_post_meta_values' ],
				10,
				2
			);
		}

		// Return meta keys.
		return $meta_keys;
	}

	/**
	 * Filter post meta values.
	 *
	 * @param array<string, mixed>     $post_meta Post meta.
	 * @param RelationshipContext|null $context   Context.
	 *
	 * @return array<string, mixed>
	 */
	public static function filter_post_meta_values( array $post_meta = [], ?RelationshipContext $context = null ): array { // phpcs:ignore Travelopia.Functions.FunctionArgsDefaultValue.Missing
		// Bail if context is not an instance of RelationshipContext.
		if ( ! $context instanceof RelationshipContext ) {
			return $post_meta;
		}

		// Get the source and destination site ids.
		$source_site_id      = $context->sourceSiteId();
		$destination_site_id = $context->remoteSiteId();

		// Store any attachment ids that are found in the meta.
		// We need to update these attachment ids for the translated post.
		$meta_keys = [
			'_thumbnail_id'         => 'attachment', // Featured image.
			'_yoast_wpseo_metadesc' => 'string', // Yoast SEO meta description.
		];

		/**
		 * Update meta keys with translated values by meta type.
		 *
		 * Example:
		 * $meta_keys = [
		 *   '_thumbnail_id' => 'attachment',
		 * ]
		 * will update the '_thumbnail_id' meta key with the translated attachment id.
		 *
		 * $meta_keys = [
		 *   'description' => 'string',
		 * ]
		 * will update the 'description' meta key with the translated string in the particular language.
		 *
		 * @param array<string, string> $meta_keys Meta keys.
		 */
		$meta_keys = (array) apply_filters( 'qrk_translation_meta_keys', $meta_keys );

		// Loop through post meta.
		foreach ( $post_meta as $meta_key => $meta_value ) {
			if ( ! is_string( $meta_key ) || ! is_array( $meta_value ) ) {
				continue;
			}

			// Get the meta value.
			$original_meta_value = maybe_unserialize( $meta_value[0] );

			// Loop through meta keys for translation.
			foreach ( $meta_keys as $key => $type ) {
				// Do a preg match because $key can be a string or a regex.
				if ( $meta_key !== $key || ( str_contains( $key, '\\' ) && ! preg_match( '/' . $key . '/', $meta_key ) ) ) {
					continue;
				}

				// Store the first value from original meta value.
				// Meta value should always be a string at this point.
				// If an array of values need to be translated, a custom function should be used.
				if ( is_array( $original_meta_value ) ) {
					$meta_value = $original_meta_value[0] ?? '';
				} else {
					$meta_value = $original_meta_value;
				}

				// Initialize updated meta value.
				$updated_meta_value = [];

				// Check the type of meta value.
				if ( 'attachment' === $type ) {
					// Get translated post(attachment) id.
					$post = get_translated_image(
						absint( $meta_value ),
						$source_site_id,
						$destination_site_id
					);

					// Check if post is an instance of WP_Post.
					if ( $post instanceof WP_Post ) {
						// Update meta value with translated attachment id.
						$updated_meta_value[] = $post->ID;
					}
				} elseif ( 'string' === $type ) {
					// Get language from site id.
					$language      = '';
					$from_language = '';
					$languages     = get_languages();

					// Loop through languages to get the language.
					foreach ( $languages as $site_language ) {
						if ( empty( $site_language['site_id'] ) || empty( $site_language['language'] ) ) {
							continue;
						}

						// Get the language of the source site.
						if ( $site_language['site_id'] === $source_site_id ) {
							$from_language = $site_language['language'];
						}

						// Get the language of the destination site.
						if ( $site_language['site_id'] === $destination_site_id ) {
							$language = $site_language['language'];
						}
					}

					// Translate string.
					$translated_string = translate_strings( [ $meta_value ], $language, $from_language );

					// Fall back to the original string if translation fails.
					if ( ! $translated_string instanceof WP_Error && is_array( $translated_string ) && ! empty( $translated_string ) ) {
						// Update meta value with translated string.
						$updated_meta_value[] = $translated_string[0];
					}
				} elseif ( 'taxonomy' === $type ) {
					// Get translated term id.
					$translated_terms = get_term_translations(
						absint( $meta_value ),
						$source_site_id,
					);

					// Loop through translated terms.
					foreach ( $translated_terms as $term ) {
						if ( $term['site_id'] === $destination_site_id ) {
							// Update meta value with translated term id.
							$updated_meta_value[] = $term['term_id'];
							break;
						}
					}
				} elseif ( 'post' === $type ) {
					// Get translated post id.
					$translated_posts = get_post_translations(
						absint( $meta_value ),
						$source_site_id,
					);

					// Loop through translated posts.
					foreach ( $translated_posts as $post ) {
						if ( $post['site_id'] === $destination_site_id ) {
							// Update meta value with translated post id.
							$updated_meta_value[] = $post['post_id'];
							break;
						}
					}
				} elseif ( is_callable( $type ) ) {
					// If meta value is an array after unserialize, loop through the array.
					if ( is_array( $original_meta_value ) ) {
						// Store the translated values.
						$translated_values = [];

						// Loop through the original meta value.
						foreach ( $original_meta_value as $value ) {
							// Call the function to get the translated value.
							$translated_value = $type( $meta_key, $value, $source_site_id, $destination_site_id );

							// Update meta value with translated value.
							if ( ! empty( $translated_value ) ) {
								$translated_values[] = $translated_value;
							}
						}

						// Update meta value with translated value.
						if ( ! empty( $translated_values ) ) {
							$updated_meta_value[] = $translated_values;
						}
					} else {
						// Call the function to get the translated value.
						$translated_value = $type( $meta_key, $meta_value, $source_site_id, $destination_site_id );

						// Update meta value with translated value.
						if ( ! empty( $translated_value ) ) {
							$updated_meta_value[] = $translated_value;
						}
					}
				}

				// Update post meta with translated values.
				if ( ! empty( $updated_meta_value ) ) {
					$post_meta[ $meta_key ] = $updated_meta_value;
				}
			}
		}

		// Return translated post meta.
		return $post_meta;
	}
}
