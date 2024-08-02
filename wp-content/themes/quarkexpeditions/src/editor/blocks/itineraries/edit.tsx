/**
 * WordPress dependencies.
 */
import { Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import metadata from './block.json';

// @ts-ignore No module declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Edit Component.
 */
export default function Edit(): JSX.Element {
	// Return the block's markup.
	return (
		<ServerSideRender
			block={ name }
			EmptyResponsePlaceholder={ () => (
				<Placeholder
					icon="palmtree"
					label={ __( 'Itineraries', 'qrk' ) }
					instructions={ __(
						'Please select one or Itinerary.',
						'qrk',
					) }
				/>
			) }
		/>
	);
}
