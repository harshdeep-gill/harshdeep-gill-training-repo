/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as menuItem from '../menu-item';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ) {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'header__primary-nav' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'header__nav-menu' ),
	},
	{
		allowedBlocks: [ menuItem.name ],
		template: [
			[ menuItem.name ],
			[ menuItem.name ],
			[ menuItem.name ],
			[ menuItem.name ],
			[ menuItem.name ],
		],
		renderAppender: InnerBlocks.DefaultBlockAppender,

		// @ts-ignore
		orientation: 'horizontal',
	}
	);

	// Return block.
	return (
		<nav { ...blockProps } >
			<ul { ...innerBlockProps } />
		</nav>
	);
}
