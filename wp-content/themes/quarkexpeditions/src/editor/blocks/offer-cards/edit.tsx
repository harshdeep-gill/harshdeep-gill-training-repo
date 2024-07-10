/**
 * WordPress dependencies.
 */
import { InnerBlocks, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Styles.
 */
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as card from './children/card';

/**
 * Edit component.
 *
 * @param {Object} props           Component properties.
 * @param {Object} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'offer-cards', 'grid', 'offer-cards--cols-2' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ card.name ],
			template: [ [ card.name ], [ card.name ] ],
			renderAppender: InnerBlocks.ButtonBlockAppender,

			// @ts-ignore
			orientation: 'horizontal',
		},
	);

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
