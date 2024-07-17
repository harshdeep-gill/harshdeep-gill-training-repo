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
		/>
	);
}
