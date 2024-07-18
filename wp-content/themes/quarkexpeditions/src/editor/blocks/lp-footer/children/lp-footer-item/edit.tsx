/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Child blocks.
 */
import * as column from '../column';

/**
 * Edit Component.
 */
export default function Edit(): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps();

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { className: 'lp-footer__row' }, {
		allowedBlocks: [ column.name ],
		template: [
			[ column.name ],
			[ column.name ],
			[ column.name ],
		],
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps } >
			<div { ...innerBlockProps } />
		</div>
	);
}
