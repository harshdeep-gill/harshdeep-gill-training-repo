/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * Child blocks.
 */
import * as title from './children/title';
import * as departures from './children/departures';
import * as cta from './children/cta';

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
		className: classnames( className, 'product-departures-card', 'typography-spacing' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: 'product-departures-card__content grid',
	}, {
		allowedBlocks: [ title.name, cta.name, departures.name ],
		template: [ [ title.name ], [ cta.name ], [ departures.name ] ],
		templateLock: 'all',
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
}
