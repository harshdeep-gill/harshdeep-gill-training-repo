/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	PanelBody,
	RangeControl,
} from '@wordpress/components';
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import RatingStars from '../../components/rating-stars';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/reviews-carousel-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Reviews Carousel Item', 'qrk' ),
	description: __( 'Individual reviews carousel item.', 'qrk' ),
	parent: [ 'quark/reviews-carousel' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'item', 'qrk' ) ],
	attributes: {
		title: {
			type: 'string',
		},
		review: {
			type: 'string',
		},
		author: {
			type: 'string',
		},
		rating: {
			type: 'string',
			default: '5',
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blocksProps = useBlockProps( {
			className: classnames( className, 'reviews-carousel__slide' ),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Reviews Carousel Options', 'qrk' ) }>
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
				<div { ...blocksProps }>
					<RichText
						tagName="h4"
						className="reviews-carousel__slide-title"
						placeholder={ __( 'Write title…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
					<div className="reviews-carousel__slide-content">
						<RichText
							tagName="div"
							className="reviews-carousel__content"
							placeholder={ __( 'Write review…', 'qrk' ) }
							value={ attributes.review }
							onChange={ ( review: string ) => setAttributes( { review } ) }
							allowedFormats={ [] }
						/>
						<div className="reviews-carousel__name-rating">
							<RichText
								tagName="strong"
								className="reviews-carousel__name"
								placeholder={ __( 'Write name…', 'qrk' ) }
								value={ attributes.author }
								onChange={ ( author: string ) => setAttributes( { author } ) }
								allowedFormats={ [] }
							/>
							<div className="reviews-carousel__rating">
								<RatingStars rating={ attributes.rating } />
							</div>
						</div>
					</div>
				</div>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
