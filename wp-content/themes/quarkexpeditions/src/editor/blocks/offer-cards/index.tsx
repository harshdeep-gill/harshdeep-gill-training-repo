/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
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
 * Styles.
 */
import '../../../front-end/components/offer-cards/style.scss';
import './editor.scss';

/**
 * Child block.
 */
import * as card from './card';

/**
 * Register child block.
 */
registerBlockType( card.name, card.settings );

/**
 * Block name.
 */
export const name: string = 'quark/offer-cards';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Offer Cards', 'qrk' ),
	description: __( 'Add offer cards to the page.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'product cards', 'qrk' ),
		__( 'expeditions', 'qrk' ),
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
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
