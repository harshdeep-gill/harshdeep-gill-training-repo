/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
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
import * as heroContentLeft from './hero-content-left';
import * as heroContentRight from './hero-content-right';

/**
 * Register children blocks
 */
registerBlockType( heroContentLeft.name, heroContentLeft.settings );
registerBlockType( heroContentRight.name, heroContentRight.settings );

/**
 * Block name.
 */
export const name: string = 'quark/hero-content';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero Content', 'qrk' ),
	description: __( 'Container of hero content.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'hero', 'qrk' ),
		__( 'content', 'qrk' ),
	],
	attributes: {},
	parent: [ 'quark/hero' ],
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
			className: classnames( className, 'hero__content' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ ...blockProps },
			{
				allowedBlocks: [ heroContentLeft.name, heroContentRight.name ],
				template: [ [ heroContentLeft.name ], [ heroContentRight.name ] ],
			}
		);

		// Return the block's markup.
		return <div { ...innerBlockProps } />;
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
