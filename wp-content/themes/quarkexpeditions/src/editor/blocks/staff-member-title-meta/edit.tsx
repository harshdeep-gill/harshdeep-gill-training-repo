/**
 * WordPress dependencies.
 */
import { useBlockProps } from '@wordpress/block-editor';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * External dependencies.
 */
import Section from '../../components/section';
import { name } from '../staff-members/edit';

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
			/>
		</Section>
	);
}
