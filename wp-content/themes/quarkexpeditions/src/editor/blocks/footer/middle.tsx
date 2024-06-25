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
import * as footerNavigation from './navigation';

/**
 * Register children blocks.
 */
registerBlockType( footerNavigation.name, footerNavigation.settings );

/**
 * Block name.
 */
export const name: string = 'quark/footer-middle';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Middle', 'qrk' ),
	description: __( 'Display the Middle section of a footer.', 'qrk' ),
	parent: [ 'quark/footer' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'middle', 'qrk' ),
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
			className: classnames( className, 'footer__middle' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [ footerColumn.name, footerNavigation.name ],
				template: [
					[ footerColumn.name ],
					[ footerNavigation.name ],
					[ footerNavigation.name ],
					[ footerColumn.name ],
					[ footerNavigation.name ],
					[ footerNavigation.name ],
					[ footerColumn.name ],
				],

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
