/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/lp-offer-masthead/style.scss';
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child Block.
 */
import * as offerCards from '../offer-cards';

/**
 * Block name.
 */
export const name: string = 'quark/lp-offer-masthead-content';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'LP Offer Masthead Content', 'qrk' ),
	description: __( 'Inner Content of Offer Masthead.', 'qrk' ),
	parent: [ 'quark/lp-offer-masthead' ],
	category: 'layout',
	keywords: [
		__( 'content', 'qrk' ),
	],
	attributes: {},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'lp-offer-masthead__inner-content' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps },
			{
				allowedBlocks: [ offerCards.name ],
				template: [ [ offerCards.name ] ],
			}
		);

		// Return the block's markup.
		return (
			<div { ...innerBlockProps } />
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
