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
 * Child blocks.
 */
import * as column from './column';

/**
 * Register child blocks.
 */
registerBlockType( column.name, column.settings );

/**
 * Block name.
 */
export const name: string = 'quark/lp-footer-row';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Row', 'qrk' ),
	description: __( 'Footer row block.', 'qrk' ),
	parent: [ 'quark/lp-footer' ],
	icon: 'layout',
	category: 'layout',
	keywords: [ __( 'row', 'qrk' ) ],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit(): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps();

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { className: 'lp-footer__row' }, {
			allowedBlocks: [ column.name ],
			template: [
				[ column.name ],
				[ column.name ],
				[ column.name ],
			],
		} );

		// Return the block's markup.
		return (
			<div { ...blockProps } >
				<div { ...innerBlockProps } />
			</div>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
