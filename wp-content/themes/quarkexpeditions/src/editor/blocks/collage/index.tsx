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
import '../../../front-end/components/collage/style.scss';
import '../../../front-end/components/media-lightbox/style.scss';
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * Child block.
 */
import * as item from './item';

/**
 * Register child block.
 */
registerBlockType( item.name, item.settings );

/**
 * Block name.
 */
export const name: string = 'quark/collage';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Collage', 'qrk' ),
	description: __( 'Display gallery of images and videos.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'collage', 'qrk' ),
		__( 'gallery', 'qrk' ),
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
			className: classnames( className, 'collage' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: classnames( 'collage__slides-container' ),
		}, {
			allowedBlocks: [ item.name ],
			template: [ [ item.name ], [ item.name ], [ item.name ], [ item.name ] ],

			// @ts-ignore
			orientation: 'horizontal',
			renderAppender: InnerBlocks.ButtonBlockAppender,
		} );

		// Return the block's markup.
		return (
			<Section { ...blockProps } >
				<div { ...innerBlockProps } />
			</Section>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
