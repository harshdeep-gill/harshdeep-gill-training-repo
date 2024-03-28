/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * External components.
 */
const { gumponents } = window;
const { ImageControl, Img } = gumponents.components;

/**
 * Child blocks.
 */
import * as title from './title';
import * as subtitle from './subtitle';
import * as description from './description';
import * as reviews from './reviews';
import * as price from './price';
import * as itinerary from './itinerary';
import * as buttons from './buttons';

/**
 * Register child block.
 */
registerBlockType( title.name, title.settings );
registerBlockType( subtitle.name, subtitle.settings );
registerBlockType( description.name, description.settings );
registerBlockType( reviews.name, reviews.settings );
registerBlockType( price.name, price.settings );
registerBlockType( itinerary.name, itinerary.settings );
registerBlockType( buttons.name, buttons.settings );

/**
 * Block name.
 */
export const name: string = 'quark/product-cards-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Product Cards Card', 'qrk' ),
	description: __( 'Individual Card Item for product cards.', 'qrk' ),
	parent: [ 'quark/product-cards' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'card', 'qrk' ) ],
	attributes: {
		image: {
			type: 'object',
			default: {},
		},
		isImmersiveImage: {
			type: 'boolean',
			default: false,
		},
		hasCtaBadge: {
			type: 'boolean',
			default: false,
		},
		ctaBadgeText: {
			type: 'string',
			default: '',
		},
		hasTimeBadge: {
			type: 'boolean',
			default: false,
		},
		timeBadgeText: {
			type: 'string',
			default: '',
		},
		hasInfoRibbon: {
			type: 'boolean',
			default: false,
		},
		infoRibbonText: {
			type: 'string',
			default: '',
		},
		isSoldOut: {
			type: 'boolean',
			default: false,
		},
		soldOutText: {
			type: 'string',
			default: '',
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( {
		className,
		attributes,
		setAttributes,
	}: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'product-cards__card' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {},
			{
				allowedBlocks: [
					title.name, subtitle.name, description.name, reviews.name, price.name, itinerary.name, buttons.name,
				],
				template: [
					[ reviews.name ],
					[ itinerary.name ],
					[ title.name ],
					[ subtitle.name ],
					[ description.name ],
					[ price.name ],
					[ buttons.name ],
				],
			},
		);

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Product Card Options', 'qrk' ) }>
						<ImageControl
							label={ __( 'Image', 'qrk' ) }
							value={ attributes.image ? attributes.image.id : null }
							size="medium"
							help={ __( 'Select an image for the card', 'qrk' ) }
							onChange={ ( image: object ) => setAttributes( { image } ) }
						/>
						<ToggleControl
							label={ __( 'Is Image Immersive?', 'qrk' ) }
							checked={ attributes.isImmersiveImage }
							onChange={ () => setAttributes( { isImmersiveImage: ! attributes.isImmersiveImage } ) }
							help={ __( 'Does the image blend into the card content?', 'qrk' ) }
						/>
						<ToggleControl
							label={ __( 'Has CTA Badge?', 'qrk' ) }
							checked={ attributes.hasCtaBadge }
							onChange={ () => setAttributes( { hasCtaBadge: ! attributes.hasCtaBadge } ) }
							help={ __( 'Does the image have a CTA Badge on top-left like \'Save x%\'?', 'qrk' ) }
						/>
						<ToggleControl
							label={ __( 'Has Time Badge?', 'qrk' ) }
							checked={ attributes.hasTimeBadge }
							onChange={ () => setAttributes( { hasTimeBadge: ! attributes.hasTimeBadge } ) }
							help={ __( 'Does the image have a Time Badge like \'Just Added\'?', 'qrk' ) }
						/>
						<ToggleControl
							label={ __( 'Has Info Ribbon?', 'qrk' ) }
							checked={ attributes.hasInfoRibbon }
							onChange={ () => setAttributes(
								{
									hasInfoRibbon: ! attributes.hasInfoRibbon,
									isImmersiveImage: !! attributes.hasInfoRibbon,
								} )
							}
							help={ __( 'Does the image have a Info Ribbon like \'Additional x% Savings\'?', 'qrk' ) }
						/>
						<ToggleControl
							label={ __( 'Is Sold out?', 'qrk' ) }
							checked={ attributes.isSoldOut }
							onChange={ () => setAttributes( { isSoldOut: ! attributes.isSoldOut } ) }
							help={ __( 'Is the product sold out?', 'qrk' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<article { ...blockProps }>
					<figure className={ `product-cards__image ${ true === attributes.isImmersiveImage ? 'product-cards__image-immersive' : '' }` }>
						<Img value={ attributes.image } />
						{ attributes.hasCtaBadge &&
							<RichText
								tagName="div"
								className="product-cards__badge-cta body-small"
								placeholder={ __( 'CTA Badge Text…', 'qrk' ) }
								value={ attributes.ctaBadgeText }
								onChange={ ( ctaBadgeText: string ) => setAttributes( { ctaBadgeText } ) }
								allowedFormats={ [] }
							/>
						}
						{ attributes.hasTimeBadge &&
							<div className="product-cards__badge-time body-small">
								{ icons.time }
								<RichText
									placeholder={ __( 'Time Badge Text…', 'qrk' ) }
									value={ attributes.timeBadgeText }
									onChange={ ( timeBadgeText: string ) => setAttributes( { timeBadgeText } ) }
									allowedFormats={ [] }
								/>
							</div>
						}
						{ attributes.isSoldOut &&
							<RichText
								tagName="div"
								className="product-cards__badge-sold-out h5"
								placeholder={ __( 'Sold Out Text…', 'qrk' ) }
								value={ attributes.soldOutText }
								onChange={ ( soldOutText: string ) => setAttributes( { soldOutText } ) }
								allowedFormats={ [] }
							/>
						}
						{ attributes.hasInfoRibbon &&
							<RichText
								tagName="div"
								className="product-cards__info-ribbon body-small"
								placeholder={ __( 'Info Ribbon Text…', 'qrk' ) }
								value={ attributes.infoRibbonText }
								onChange={ ( infoRibbonText: string ) => setAttributes( { infoRibbonText } ) }
								allowedFormats={ [] }
							/>
						}
					</figure>
					<div { ...innerBlockProps } />
				</article>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
