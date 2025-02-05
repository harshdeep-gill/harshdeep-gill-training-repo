<?php
/**
 * Custom MultilingualPress Term Meta Module.
 *
 * @package quark-multilingual
 */

namespace Quark\Multilingual\MultilingualPress_Modules;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Term\Metabox;
use Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxTab;
use Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxField;
use Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Term\TermRelationSaveHelper;
use WP_Post;
use WP_Error;

use function Travelopia\Multilingual\get_languages;
use function Travelopia\Multilingual\get_post_translations;
use function Travelopia\Multilingual\get_term_translations;
use function Travelopia\Multilingual\get_translated_image;
use function Travelopia\Translation\translate_strings;

/**
 * Class Term_Meta.
 */
class Term_Meta implements ModuleServiceProvider {
	// phpcs:disable Travelopia.Whitespace.GroupedConst.AddEmptyLineBeforeConstGroup
	/**
	 * Module ID.
	 *
	 * @var string
	 */
	protected $module_id = 'qrk_term_meta';

	/**
	 * Metabox field ID.
	 *
	 * @var string
	 */
	protected $tab = 'tab-term-meta';

	/**
	 * Metabox field key.
	 *
	 * @var string
	 */
	const FIELD_COPY_META = 'remote-term-meta-copy';
	// phpcs:enable Travelopia.Whitespace.GroupedConst.AddEmptyLineBeforeConstGroup

	/**
	 * Registers the provided services on the given container.
	 *
	 * @param Container|null $container Container.
	 *
	 * @return void
	 */
	public function register( ?Container $container = null ): void {
		// Do nothing.
	}

	/**
	 * Registers the module at the module manager.
	 *
	 * @param ModuleManager|null $module_manager Module manager.
	 *
	 * @throws ModuleAlreadyRegistered If the module is already registered.
	 *
	 * @return bool
	 */
	public function registerModule( ?ModuleManager $module_manager = null ): bool {
		// Bail if module manager is not provided.
		if ( ! $module_manager instanceof ModuleManager ) {
			return false;
		}

		// Register the module.
		return $module_manager->register(
			new Module(
				$this->module_id,
				[
					'description' => 'Enable Term Meta synchronization support for MultilingualPress.',
					'name'        => 'Term Meta',
					'active'      => true,
				]
			)
		);
	}

	/**
	 * Performs various tasks on module activation.
	 *
	 * @param Container|null $container Container.
	 *
	 * @return void
	 */
	public function activateModule( ?Container $container = null ): void {
		// Register the module.
		$this->setup_metabox_fields();
		$this->enable_term_meta_sync_support();
	}

	/**
	 * Setup Metabox Fields.
	 *
	 * @return void
	 */
	private function setup_metabox_fields(): void {
		// Add the term meta field to the metabox.
		$meta_fields = new MetaboxTab(
			$this->tab,
			'Term Meta',
			new MetaboxField(
				self::FIELD_COPY_META,
				[
					__CLASS__,
					'render_term_meta_metabox',
				],
				static function ( $value ) {
					return filter_var( $value, FILTER_VALIDATE_BOOLEAN ) ? '1' : '';
				}
			)
		);

		// Register the term meta field.
		add_filter(
			Metabox::HOOK_PREFIX . 'tabs',
			static function ( array $tabs ) use ( $meta_fields ) {
				return array_merge( $tabs, [ $meta_fields ] );
			},
			10,
			2
		);
	}

	/**
	 * Render the term meta metabox.
	 *
	 * @param MetaboxFieldsHelper|null $helper Helper.
	 * @param RelationshipContext|null $context Context.
	 *
	 * @return void
	 */
	public static function render_term_meta_metabox( ?MetaboxFieldsHelper $helper = null, ?RelationshipContext $context = null ): void {
		// Bail out, if helper or context is not provided.
		if ( ! $helper instanceof MetaboxFieldsHelper || ! $context instanceof RelationshipContext ) {
			return;
		}

		// Get the term meta field id and name.
		$id   = $helper->fieldId( self::FIELD_COPY_META );
		$name = $helper->fieldName( self::FIELD_COPY_META );

		// HTML markup for the term meta field.
		?>
		<tr>
			<th scope="row">
				Copy Term Meta
			</th>
			<td>
				<label for="<?php echo esc_attr( $id ); ?>">
					<input
						type="checkbox"
						name="<?php echo esc_attr( $name ); ?>"
						value="1"
						id="<?php echo esc_attr( $id ); ?>"
					/>
					Overwrites the term meta on translated term with the term meta of source term.
				</label>
			</td>
		</tr>
		<?php
	}

	/**
	 * Enable term meta sync support.
	 *
	 * @return void
	 */
	private function enable_term_meta_sync_support(): void {
		// Add filter to sync term meta keys.
		add_filter(
			TermRelationSaveHelper::FILTER_SYNC_META_KEYS,
			[
				__CLASS__,
				'sync_meta_keys',
			],
			10,
			3
		);
	}

	/**
	 * Sync meta keys.
	 *
	 * @param string[]                 $meta_keys Meta keys.
	 * @param RelationshipContext|null $context   Context.
	 * @param Request|null             $request   Request.
	 *
	 * @return string[]
	 */
	public static function sync_meta_keys( array $meta_keys = [], ?RelationshipContext $context = null, ?Request $request = null ): array {
		// Bail if context or request is not provided.
		if ( ! $context instanceof RelationshipContext || ! $request instanceof Request ) {
			return $meta_keys;
		}

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
		$translation = $multilingualpress[ "site-{$context->remoteSiteId()}" ] ?? '';
		$relation    = $translation[ MetaboxFields::FIELD_RELATION ] ?? '';
		$copy_meta   = $translation[ self::FIELD_COPY_META ] ?? false;

		// Check if term meta should be copied.
		if (
			MetaboxFields::FIELD_RELATION_NEW === $relation
			|| $copy_meta
		) {
			// Get the term id of the term being translated.
			$term_id = $context->sourceTermId();

			/*
			 * Since we need all the meta keys of the particular term,
			 * we will get all the metadata of the term and later extract the meta keys.
			 */
			$meta = get_term_meta( $term_id );

			// Bail if meta is not an array.
			if ( ! is_array( $meta ) ) {
				return $meta_keys;
			}

			// Get all the meta keys of the term.
			$meta_keys = array_merge( $meta_keys, array_keys( $meta ) );

			// Filter term meta values.
			add_filter(
				TermRelationSaveHelper::FILTER_METADATA,
				[ __CLASS__, 'filter_meta_values' ],
				10,
				2
			);
		}

		// Return meta keys.
		return $meta_keys;
	}

	/**
	 * Filter term meta values.
	 *
	 * @param mixed[]                  $term_meta Term meta.
	 * @param RelationshipContext|null $context   Context.
	 *
	 * @return array<string, mixed>
	 */
	public static function filter_meta_values( array $term_meta = [], ?RelationshipContext $context = null ): array {
		// Bail if context is not provided.
		if ( ! $context instanceof RelationshipContext ) {
			return $term_meta;
		}

		// Get the source and destination site ids.
		$source_site_id      = $context->sourceSiteId();
		$destination_site_id = $context->remoteSiteId();

		// Store any attachment ids that are found in the meta.
		// We need to update these attachment ids for the translated term.
		$meta_keys = [];

		/**
		 * Update meta keys with translated values by meta type.
		 *
		 * Example:
		 * $meta_keys = [
		 *   'featured_image' => 'attachment',
		 * ]
		 * will update the 'featured_image' meta key with the translated attachment id.
		 *
		 * $meta_keys = [
		 *   'description' => 'string',
		 * ]
		 * will update the 'description' meta key with the translated string in the particular language.
		 *
		 * @param array<string, string> $meta_keys Meta keys.
		 */
		$meta_keys = (array) apply_filters( 'qrk_translation_term_meta_keys', $meta_keys );

		// Loop through term meta.
		foreach ( $term_meta as $meta_key => $meta_value ) {
			if ( ! is_string( $meta_key ) || ! is_array( $meta_value ) ) {
				continue;
			}

			// Store updated meta value.
			$updated_meta_value = [];

			// Loop through meta keys for translation.
			foreach ( $meta_keys as $key => $type ) {
				// Do a preg match because $key can be a string or a regex.
				if ( ! preg_match( '/' . $key . '/', $meta_key ) ) {
					continue;
				}

				// Get the meta value.
				$meta_value = maybe_unserialize( $meta_value[0] );

				// Check if meta value is an array after unserialize.
				if ( is_array( $meta_value ) ) {
					$meta_value = $meta_value[0];
				}

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
				}

				// Update term meta with translated values.
				if ( ! empty( $updated_meta_value ) ) {
					$term_meta[ $meta_key ] = $updated_meta_value;
				}
			}
		}

		// Return translated term meta.
		return $term_meta;
	}
}
