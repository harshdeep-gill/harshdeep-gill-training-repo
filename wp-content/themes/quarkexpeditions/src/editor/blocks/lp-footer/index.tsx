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
import '../../../front-end/components/lp-footer/style.scss';
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block - LP Footer column.
 */
import * as column from './item';

/**
 * Register child block.
 */
registerBlockType( column.name, column.settings );

/**
 * Block name.
 */
export const name: string = 'quark/lp-footer';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'LP Footer', 'qrk' ),
	description: __( 'Display a footer for a landing page.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'lp', 'qrk' ),
		__( 'footer', 'qrk' ),
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
			className: classnames( className, 'lp-footer', 'full-width', 'section', 'section--seamless' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: 'lp-footer__columns',
		}, {
			allowedBlocks: [ column.name ],
			template: [
				[ column.name ],
			],

			// @ts-ignore
			orientation: 'horizontal',
		} );

		// Return the block's markup.
		return (
			<footer { ...blockProps } >
				<div className="lp-footer__wrap">
					<div { ...innerBlockProps } />
				</div>
			</footer>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
