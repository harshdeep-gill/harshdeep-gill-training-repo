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
 * Styles.
 */
import '../../../front-end/components/footer/style.scss';
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Children blocks
 */
import * as footerTop from './top';
import * as footerMiddle from './middle';
import * as footerBottom from './bottom';

/**
 * Register children blocks.
 */
registerBlockType( footerTop.name, footerTop.settings );
registerBlockType( footerMiddle.name, footerMiddle.settings );
registerBlockType( footerBottom.name, footerBottom.settings );

/**
 * Block name.
 */
export const name: string = 'quark/footer';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer', 'qrk' ),
	description: __( 'Display a footer.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'global', 'qrk' ),
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
			className: classnames(
				className,
				'footer',
				'full-width',
				'color-context--dark'
			),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: 'footer__wrap',
		}, {
			allowedBlocks: [ footerTop.name, footerMiddle.name, footerBottom.name ],
			template: [ [ footerTop.name ], [ footerMiddle.name ], [ footerBottom.name ] ],

			// @ts-ignore
			orientation: 'horizontal',
		} );

		// Return the block's markup.
		return (
			<footer { ...blockProps } >
				<div { ...innerBlockProps } />
			</footer>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
