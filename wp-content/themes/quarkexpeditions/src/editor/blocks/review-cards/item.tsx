/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
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
import * as title from './title';
import * as review from './review';
import * as rating from './rating';
import * as author from './author';
import * as authorDetails from './author-details';

/**
 * Register child block.
 */
registerBlockType( title.name, title.settings );
registerBlockType( rating.name, rating.settings );
registerBlockType( review.name, review.settings );
registerBlockType( author.name, author.settings );
registerBlockType( authorDetails.name, authorDetails.settings );

/**
 * Block name.
 */
export const name: string = 'quark/review-cards-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Review Cards Card', 'qrk' ),
	description: __( 'Individual review card item.', 'qrk' ),
	parent: [ 'quark/review-cards' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'item', 'qrk' ) ],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'review-cards__card' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps },
			{
				allowedBlocks: [ rating.name, title.name, review.name, author.name, authorDetails.name ],
				template: [
					[ rating.name ],
					[ title.name ],
					[ review.name ],
					[ author.name ],
					[ authorDetails.name ],
				],
				templateLock: 'insert',
			},
		);

		// Return the block's markup.
		return (
			<div { ...innerBlockProps } />
		);
	},
	save() {
		// Save InnerBlocks Content.
		return <InnerBlocks.Content />;
	},
};
