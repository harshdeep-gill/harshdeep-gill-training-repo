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
export const name: string = 'quark/review-cards-rating';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Review Cards Rating', 'qrk' ),
	description: __( 'Individual review card item rating.', 'qrk' ),
	parent: [ 'quark/review-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'rating', 'qrk' ) ],
	attributes: {
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
			className: classnames( className ),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Review Cards Review Options', 'qrk' ) }>
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
				</div>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
