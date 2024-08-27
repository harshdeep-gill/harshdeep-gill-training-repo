/**
 * WordPress dependencies.
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Internal dependencies.
 */
import metadata from './block.json';

/**
 * Block name.
 */
const { name }: { name: string } = metadata;

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

	// Render the block.
	return (
		<Section { ...blockProps }>
			<ServerSideRender
				block={ name }
				EmptyResponsePlaceholder={ () => (
					<Placeholder
						icon="palmtree"
						label={ __( 'Staff Member Title & Meta', 'qrk' ) }
						instructions={ __(
							'Data will be displayed here once you add metadata.',
							'qrk',
						) }
					/>
				) }
			/>
		</Section>
	);
}
