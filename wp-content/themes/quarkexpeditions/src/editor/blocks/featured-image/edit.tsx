/**
 * WordPress dependencies.
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

/**
 * External dependencies.
 */
import Section from '../../components/section';

/**
 * Styles.
 */
import '../../../front-end/components/featured-image/style.scss';

/**
 * Edit component.
 *
 * @param {Object} props Component properties.
 */
export default function Edit( {}: BlockEditAttributes ): JSX.Element {
	// Get the block props.
	const blockProps = useBlockProps();

	// Get the featured image.
	const featuredImage = useSelect( ( select: any ) => {
		// Get the featured image.
		const image = select( 'core/editor' ).getEditedPostAttribute( 'featured_media' );

		// If there is no featured image.
		if ( ! image ) {
			// Return null.
			return null;
		}

		// Return featured image media.
		return select( 'core' ).getMedia( image );
	}, [] );

	// Render the block.
	return (
		<Section { ...blockProps }>
			{ featuredImage && (
				<figure className="featured-image typography-spacing">
					<img
						src={ featuredImage?.source_url }
						alt={ featuredImage?.alt_text }
					/>
				</figure>
			) }
			{ ! featuredImage && (
				<Placeholder
					icon="format-image"
					label={ __( 'Featured Image', 'quark' ) }
					instructions={ __( 'Upload an image to be displayed as the featured image.', 'quark' ) }
				/>
			) }
		</Section>
	);
}
