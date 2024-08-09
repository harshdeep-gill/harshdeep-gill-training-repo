/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import metadata from './block.json';
import Section from '../../components/section';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Add classnames.
	const blockProps = useBlockProps( { className } );

	// Return the block's markup.
	return (
		<>
			<Section { ...blockProps }>
				<Placeholder icon="layout" label={ __( 'Book Departure (Expeditions) Block', 'qrk' ) }>
					<p>{ __( 'The book departure cards will render on the front-end.', 'qrk' ) }</p>
				</Placeholder>
			</Section>
		</>
	);
}
