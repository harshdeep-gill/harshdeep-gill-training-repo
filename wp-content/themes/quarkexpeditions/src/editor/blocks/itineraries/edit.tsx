/**
 * WordPress dependencies.
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import metadata from './block.json';
import Section from '../../components/section';

// @ts-ignore No module declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * External dependencies.
 */
import classnames from 'classnames';

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
	// Get block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'included-activities' ),
	} );

	// Return the block's markup.
	return (
		<Section { ...blockProps }>
			<ServerSideRender
				block={ name }
				EmptyResponsePlaceholder={ () => (
					<Placeholder
						icon="palmtree"
						label={ __( 'Itineraries', 'qrk' ) }
						instructions={ __(
							'Please select one or Itineraries.',
							'qrk',
						) }
					/>
				) }
			/>
		</Section>
	);
}
