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
import * as socialLinks from './social-links';

/**
 * Register child blocks.
 */
registerBlockType( socialLinks.name, socialLinks.settings );

/**
 * Block name.
 */
export const name: string = 'quark/lp-footer-column';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Column', 'qrk' ),
	description: __( 'Footer column block.', 'qrk' ),
	parent: [ 'quark/lp-footer' ],
	icon: 'columns',
	category: 'layout',
	keywords: [ __( 'column', 'qrk' ) ],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit(): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( { className: 'lp-footer__column' } );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {}, {
			allowedBlocks: [ 'core/paragraph', 'core/list', socialLinks.name, 'core/heading', 'quark/logo-grid' ],
			template: [
				[ 'core/paragraph' ],
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
