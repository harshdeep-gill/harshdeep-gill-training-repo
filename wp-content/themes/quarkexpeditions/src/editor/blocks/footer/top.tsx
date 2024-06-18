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
import * as footerColumn from './column';

/**
 * Register children blocks.
 */
registerBlockType( footerColumn.name, footerColumn.settings );

/**
 * Block name.
 */
export const name: string = 'quark/footer-top';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Top', 'qrk' ),
	description: __( 'Display the top section of a footer.', 'qrk' ),
	parent: [ 'quark/footer' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'top', 'qrk' ),
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
			className: classnames( className, 'footer__top', 'grid' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [ footerColumn.name ],
				template: [ [ footerColumn.name ], [ footerColumn.name ], [ footerColumn.name ] ],
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
