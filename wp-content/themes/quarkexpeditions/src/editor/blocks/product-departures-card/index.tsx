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
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * Styles.
 */
import '../../../front-end/components/product-departures-card/style.scss';
import './editor.scss';

/**
 * Child blocks.
 */
import * as title from './title';
import * as cta from './cta';
import * as departures from './departures';

/**
 * Register child block.
 */
registerBlockType( title.name, title.settings );
registerBlockType( cta.name, cta.settings );
registerBlockType( departures.name, departures.settings );

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { SelectImage } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/product-departures-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Product Departures Card', 'qrk' ),
	description: __( 'Display a product along with its departure data.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'product', 'qrk' ),
		__( 'departures', 'qrk' ),
		__( 'card', 'qrk' ),
	],
	attributes: {
		image1: {
			type: 'object',
			default: null,
		},
		image2: {
			type: 'object',
			default: null,
		},
		hasCtaBadge: {
			type: 'boolean',
			default: false,
		},
		ctaBadgeText: {
			type: 'string',
			default: '',
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
		const blockProps = useBlockProps( {
			className: classnames( className, 'product-departures-card', 'typography-spacing' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: 'product-departures-card__content grid',
		}, {
			allowedBlocks: [ title.name, cta.name, departures.name ],
			template: [ [ title.name ], [ cta.name ], [ departures.name ] ],
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Product Departures Card Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Has CTA Badge?', 'qrk' ) }
							checked={ attributes.hasCtaBadge }
							help={ __( 'Does this card have a badge on the top left corner like \'Free Cabin Upgrade\'?', 'qrk' ) }
							onChange={ ( hasCtaBadge: boolean ) => setAttributes( { hasCtaBadge } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps } >
					<div className="product-departures-card__images-wrap">
						<div className="product-departures-card__images">
							<figure className="product-departures-card__image">
								<SelectImage
									image={ attributes.image1 }
									placeholder="Choose an image"
									size="large"
									onChange={ ( image1: Object ): void => {
										// Set image.
										setAttributes( { image1: null } );
										setAttributes( { image1 } );
									} }
								/>
							</figure>
							<figure className="product-departures-card__image">
								<SelectImage
									image={ attributes.image2 }
									placeholder="Choose an image"
									size="large"
									onChange={ ( image2: Object ): void => {
										// Set image.
										setAttributes( { image2: null } );
										setAttributes( { image2 } );
									} }
								/>
							</figure>
							{
								attributes.hasCtaBadge &&
								<RichText
									tagName="div"
									className="product-departures-card__badge-cta body-small"
									placeholder={ __( 'CTA Badge text…', 'qrk' ) }
									value={ attributes.ctaBadgeText }
									onChange={ ( ctaBadgeText: string ) => setAttributes( { ctaBadgeText } ) }
									allowedFormats={ [] }
								/>
							}
						</div>
					</div>
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
