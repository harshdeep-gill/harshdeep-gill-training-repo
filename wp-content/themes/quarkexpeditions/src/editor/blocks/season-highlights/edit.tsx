/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block - Season Highlights Season
 */
import * as item from './children/season';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'season-highlights', 'typography-spacing' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ item.name ],
		template: [
			[ item.name ],
			[ item.name ],
		],

		// @ts-ignore
		orientation: 'horizontal',
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps } >
			<div { ...innerBlockProps } />
		</div>
	);
}
