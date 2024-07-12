/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
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
 * Child blocks.
 */
import * as raqButton from './raq-button';
import * as contactButton from './contact-button';

/**
 * Register Child blocks.
 */
registerBlockType( raqButton.name, raqButton.settings );
registerBlockType( contactButton.name, contactButton.settings );

/**
 * Block name.
 */
export const name: string = 'quark/header-cta-buttons';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Header CTA Buttons', 'qrk' ),
	description: __( 'Individual CTA Buttons for Header', 'qrk' ),
	parent: [ 'quark/header' ],
	icon: 'button',
	category: 'layout',
	keywords: [ __( 'cta', 'qrk' ), __( 'buttons', 'qrk' ) ],
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
			className: classnames( className, 'header__cta-buttons', 'color-context--dark' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps },
			{
				allowedBlocks: [ raqButton.name, contactButton.name ],
				template: [
					[ contactButton.name, { isSizeBig: true, backgroundColor: 'black', appearance: 'outline' } ],
					[ raqButton.name, { isSizeBig: true } ],
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
