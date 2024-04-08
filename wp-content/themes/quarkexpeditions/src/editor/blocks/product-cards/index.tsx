/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import '../../../front-end/components/product-cards/style.scss';
import './editor.scss';

/**
 * Child block.
 */
import * as card from './card';
import { PanelBody, SelectControl } from '@wordpress/components';

/**
 * Register child block.
 */
registerBlockType( card.name, card.settings );

/**
 * Block name.
 */
export const name: string = 'quark/product-cards';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Product Cards', 'qrk' ),
	description: __( 'Add product cards to the page.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'product', 'qrk' ),
		__( 'cards', 'qrk' ),
		__( 'expeditions', 'qrk' ),
	],
	attributes: {
		align: {
			type: 'string',
			default: 'left',
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps();

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{
				className: classnames( className, 'product-cards', 'grid', 'center' === attributes.align ? 'product-cards--align-center' : 'grid--cols-3' ),
			},
			{
				allowedBlocks: [ card.name ],
				template: [ [ card.name ], [ card.name ], [ card.name ] ],
				renderAppender: InnerBlocks.ButtonBlockAppender,

				// @ts-ignore
				orientation: 'horizontal',
			},
		);

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Product Cards Grid Options', 'qrk' ) }>
						<SelectControl
							label={ __( 'Product Cards Alignment', 'qrk' ) }
							help={ __( 'Select the cards alignment', 'qrk' ) }
							value={ attributes.align }
							options={ [
								{ label: __( 'Left', 'qrk' ), value: 'left' },
								{ label: __( 'Center', 'qrk' ), value: 'center' },
							] }
							onChange={ ( align: string ) => setAttributes( { align } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div { ...innerBlockProps } />
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
