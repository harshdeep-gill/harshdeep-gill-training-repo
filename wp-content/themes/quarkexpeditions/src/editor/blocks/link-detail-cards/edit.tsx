/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as linkDetailCard from './children/card';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set block properties.
	const blockProps = useBlockProps( {
		className: classnames( className, 'link-detail-cards' ),
	} );

	// Set inner blocks properties.
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ linkDetailCard.name ],
		template: [
			[ linkDetailCard.name ],
			[ linkDetailCard.name ],
			[ linkDetailCard.name ],
		],
		renderAppender: InnerBlocks.ButtonBlockAppender,

		// @ts-ignore
		orientation: 'horizontal',
	} );

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
