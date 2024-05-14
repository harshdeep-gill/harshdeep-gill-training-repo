/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	useInnerBlocksProps,
	InspectorControls,
	InnerBlocks,
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
 * Block name.
 */
export const name: string = 'quark/product-departures-card-dates';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card Departure Dates', 'qrk' ),
	description: __( 'Departures dates for Product Departures Card', 'qrk' ),
	parent: [ 'quark/product-departures-card-departures' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'departures', 'qrk' ), __( 'dates', 'qrk' ) ],
	attributes: {
		offer: {
			type: 'string',
			default: '',
		},
		offerText: {
			type: 'string',
			default: '',
		},
		isSoldOut: {
			type: 'boolean',
			default: false,
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'product-departures-card__dates' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {},
			{
				allowedBlocks: [ 'core/paragraph' ],
				template: [
					[ 'core/paragraph', { placeholder: __( 'Write Date…', 'qrk' ) } ],
				],
			},
		);

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Product Departures Card Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Is Sold Out?', 'qrk' ) }
							checked={ attributes.isSoldOut }
							help={ __( 'Is this departure date sold out?', 'qrk' ) }
							onChange={ ( isSoldOut: boolean ) => setAttributes( { isSoldOut } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps } >
					<div className="product-departures-card__departure-dates">
						<div { ...innerBlockProps } />
					</div>
					<div className="product-departures-card__offer-wrap">
						<RichText
							tagName="span"
							className="product-departures-card__offer h5"
							placeholder={ __( 'Write Offer…', 'qrk' ) }
							value={ attributes.offer }
							onChange={ ( offer: string ) => setAttributes( { offer } ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="span"
							className="product-departures-card__offer-text"
							placeholder={ __( 'Write Offer Details…', 'qrk' ) }
							value={ attributes.offerText }
							onChange={ ( offerText: string ) => setAttributes( { offerText } ) }
							allowedFormats={ [] }
						/>
					</div>
					{
						attributes.isSoldOut && (
							<div className="product-departures-card__badge-sold-out h5">
								{ __( 'Sold Out', 'qrk' ) }
							</div>
						)
					}
				</div>
			</>
		);
	},
	save() {
		// Save InnerBlocks Content.
		return <InnerBlocks.Content />;
	},
};
