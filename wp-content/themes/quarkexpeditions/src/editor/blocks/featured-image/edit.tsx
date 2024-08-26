/**
 * WordPress dependencies.
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
 */
import Section from '../../components/section';

/**
 * Edit component.
 *
 * @param {Object} props Component properties.
 */
export default function Edit( {}: BlockEditAttributes ): JSX.Element {
	// Get the block props.
	const blockProps = useBlockProps();

	// TODO: Add comment.
	return (
		<Section { ...blockProps }>
			<Placeholder
				icon="format-image"
				label={ __( 'Featured Image', 'quark' ) }
				instructions={ __( 'Upload an image to be displayed as the featured image.', 'quark' ) }
			/>
		</Section>
	);
}
