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
import * as socialLink from './social-link';

/**
 * Register children blocks.
 */
registerBlockType( socialLink.name, socialLink.settings );

/**
 * Block name.
 */
export const name: string = 'quark/footer-social-links';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Social Links', 'qrk' ),
	description: __( 'Display the social links container.', 'qrk' ),
	parent: [ 'quark/footer-column' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'social', 'qrk' ),
		__( 'links', 'qrk' ),
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
			className: classnames( className, 'footer__social-icons' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [ socialLink.name ],
				template: [
					[ socialLink.name, { type: 'facebook' } ],
					[ socialLink.name, { type: 'instagram' } ],
					[ socialLink.name, { type: 'twitter' } ],
					[ socialLink.name, { type: 'youtube' } ],
				],
			}
		);

		// Return the block's markup.
		return ( <ul { ...innerBlockProps } /> );
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
