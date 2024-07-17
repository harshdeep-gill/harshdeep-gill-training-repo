/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
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
import RatingStars from '../../../../components/rating-stars';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
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
}
