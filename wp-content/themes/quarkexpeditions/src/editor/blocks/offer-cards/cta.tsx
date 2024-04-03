/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/offer-cards-cta';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card CTA', 'qrk' ),
	description: __( 'Individual Card CTA for offer cards.', 'qrk' ),
	parent: [ 'quark/offer-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'cta', 'qrk' ), __( 'button', 'qrk' ) ],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'offer-cards__cta' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps },
			{
				allowedBlocks: [ 'quark/button' ],
				template: [
					[ 'quark/button', { isSizeBig: true, backgroundColor: 'black' } ],
				],
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
