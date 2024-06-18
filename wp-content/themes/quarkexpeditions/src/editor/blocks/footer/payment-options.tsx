/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
	registerBlockType,
} from '@wordpress/blocks';
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
 * Children blocks
 */
import * as paymentOption from './payment-option';

/**
 * Register children blocks.
 */
registerBlockType( paymentOption.name, paymentOption.settings );

/**
 * Block name.
 */
export const name: string = 'quark/footer-payment-options';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Payment options', 'qrk' ),
	description: __( 'Display the payment options container.', 'qrk' ),
	parent: [ 'quark/footer-column' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'payment', 'qrk' ),
		__( 'options', 'qrk' ),
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
			className: classnames( className, 'footer__payment-options' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [ paymentOption.name ],
				template: [
					[ paymentOption.name, { type: 'visa' } ],
					[ paymentOption.name, { type: 'mastercard' } ],
					[ paymentOption.name, { type: 'amex' } ],
					[ paymentOption.name, { type: 'discover' } ],
				],
			}
		);

		// Return the block's markup.
		return ( <ul { ...innerBlockProps } /> );
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
