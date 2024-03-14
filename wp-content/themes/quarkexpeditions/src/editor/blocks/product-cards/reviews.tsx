/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal Components.
 */
import RatingStars from '../../components/rating-stars';

/**
 * Block name.
 */
export const name: string = 'quark/product-cards-reviews';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card Reviews', 'qrk' ),
	description: __( 'Individual Card Reviews for product cards.', 'qrk' ),
	parent: [ 'quark/product-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'reviews', 'qrk' ) ],
	attributes: {
		rating: {
			type: 'string',
			default: '5',
		},
		reviewsText: {
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
			className: classnames( className, 'product-cards__reviews' ),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Card Reviews Options', 'qrk' ) }>
						<RangeControl
							label={ __( 'Rating', 'qrk' ) }
							value={ parseInt( attributes.rating ) }
							help={ __( 'Choose the rating', 'qrk' ) }
							onChange={ ( rating: any ) => setAttributes( { rating: rating.toString() } ) }
							min={ 1 }
							max={ 5 }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					{
						attributes.rating &&
						<RatingStars rating={ attributes.rating } />
					}
					<RichText
						tagName="span"
						className="product-cards__reviews-text"
						placeholder={ __( 'X Reviews…', 'qrk' ) }
						value={ attributes.reviewsText }
						onChange={ ( reviewsText: string ) => setAttributes( { reviewsText } ) }
						allowedFormats={ [] }
					/>
				</div>
			</>
		);
	},
	save() {
		// Return.
		return null;
	},
};
