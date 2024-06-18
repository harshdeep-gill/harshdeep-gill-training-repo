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
import * as footerNavigation from './navigation';
import * as footerCopyrightText from './copyright';

/**
 * Register children blocks.
 */
registerBlockType( footerCopyrightText.name, footerCopyrightText.settings );

/**
 * Block name.
 */
export const name: string = 'quark/footer-bottom';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Bottom', 'qrk' ),
	description: __( 'Display the Bottom section of a footer.', 'qrk' ),
	parent: [ 'quark/footer' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'bottom', 'qrk' ),
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
			className: classnames( className, 'footer__bottom' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [ footerNavigation.name, footerCopyrightText.name ],
				template: [ [ footerNavigation.name ], [ footerCopyrightText.name ] ],

				// @ts-ignore
				orientation: 'horizontal',
			}
		);

		// Return the block's markup.
		return ( <div { ...innerBlockProps } /> );
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
