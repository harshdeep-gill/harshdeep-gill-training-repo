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
import '../../../front-end/components/review-cards/style.scss';
import './editor.scss';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';

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
export const name: string = 'quark/review-cards';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Review Cards', 'qrk' ),
	description: __( 'Display a carousel of review cards.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'reviews', 'qrk' ),
		__( 'cards', 'qrk' ),
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
			className: classnames( className, 'review-cards' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: classnames( 'review-cards__slider' ),
		}, {
			allowedBlocks: [ item.name ],
			template: [ [ item.name ] ],
			renderAppender: InnerBlocks.ButtonBlockAppender,

			// @ts-ignore
			orientation: 'horizontal',
		} );

		// Return the block's markup.
		return (
			<>
				<Section { ...blockProps }>
					<div { ...innerBlockProps } />
				</Section>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
