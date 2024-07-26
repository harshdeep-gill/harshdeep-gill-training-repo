/**
 * WordPress dependencies.
 */
import { useBlockProps } from '@wordpress/block-editor';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Block name.
 */
export const name: string = 'quark/expedition-details';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Add classnames.
	const blockProps = useBlockProps( { className } );

	// Return the markup.
	return (
		<div { ...blockProps }>
			<ServerSideRender
				block={ name }
			/>
		</div>
	);
}
