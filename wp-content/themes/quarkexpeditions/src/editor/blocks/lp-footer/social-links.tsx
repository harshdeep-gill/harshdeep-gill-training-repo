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
 * Child block.
 */
import * as item from './social-links-item';

/**
 * Register child block.
 */
registerBlockType( item.name, item.settings );

/**
 * Block name.
 */
export const name: string = 'quark/lp-footer-social-links';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Social Links', 'qrk' ),
	description: __( 'Footer Social Links block.', 'qrk' ),
	parent: [ 'quark/lp-footer-column' ],
	icon: 'share-alt2',
	category: 'layout',
	keywords: [ __( 'social', 'qrk' ), __( 'links', 'qrk' ) ],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit(): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: 'lp-footer__social-links',
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
			allowedBlocks: [ item.name ],
			template: [
				[ item.name, { type: 'facebook', url: 'https://www.facebook.com/' } ],
				[ item.name, { type: 'instagram', url: 'https://www.instagram.com/' } ],
				[ item.name, { type: 'twitter', url: 'https://www.twitter.com/' } ],
				[ item.name, { type: 'youtube', url: 'https://www.youtube.com/' } ],
			],
		} );

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
