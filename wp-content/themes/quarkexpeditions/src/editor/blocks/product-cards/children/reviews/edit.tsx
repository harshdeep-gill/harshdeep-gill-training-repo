/**
 * WordPress Dependencies
 */
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';
import { PanelBody, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal Components.
 */
import RatingStars from '../../../../components/rating-stars';

// TODO: Add comment.
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
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
}
