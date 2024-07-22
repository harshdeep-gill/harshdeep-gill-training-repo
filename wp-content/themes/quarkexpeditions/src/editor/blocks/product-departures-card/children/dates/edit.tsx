/**
 * WordPress dependencies.
 */
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
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
}
