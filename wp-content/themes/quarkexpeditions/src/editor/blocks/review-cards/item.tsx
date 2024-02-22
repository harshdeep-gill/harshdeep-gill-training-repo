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
export const name: string = 'quark/review-cards-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Review Cards Card', 'qrk' ),
	description: __( 'Individual review card item.', 'qrk' ),
	parent: [ 'quark/review-cards' ],
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
		authorDetails: {
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
			className: classnames( className, 'review-cards__card' ),
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
					<div className="review-cards__rating">
						<RatingStars rating={ attributes.rating } />
					</div>
					<RichText
						tagName="h5"
						className="review-cards__card-title"
						placeholder={ __( 'Write title…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
					<div className="review-cards__card-content">
						<RichText
							tagName="div"
							className="review-cards__content"
							placeholder={ __( 'Write review…', 'qrk' ) }
							value={ attributes.review }
							onChange={ ( review: string ) => setAttributes( { review } ) }
							allowedFormats={ [] }
						/>
					</div>
					<RichText
						tagName="strong"
						className="review-cards__author"
						placeholder={ __( 'Write name…', 'qrk' ) }
						value={ attributes.author }
						onChange={ ( author: string ) => setAttributes( { author } ) }
						allowedFormats={ [] }
					/>
					<RichText
						className="review-cards__author-details"
						placeholder={ __( 'Write details… eg. Expedition Name', 'qrk' ) }
						value={ attributes.authorDetails }
						onChange={ ( authorDetails: string ) => setAttributes( { authorDetails } ) }
						allowedFormats={ [] }
					/>
				</div>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
