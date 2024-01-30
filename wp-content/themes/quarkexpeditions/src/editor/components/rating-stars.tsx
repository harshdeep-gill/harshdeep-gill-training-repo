/**
 * Styles.
 */
import '../../front-end/components/rating-stars/style.scss';
import React from 'react';

/**
 * Rating Stars component.
 *
 * @param {Object} props        Component properties.
 * @param {Object} props.rating Rating.
 *
 * @return {JSX.Element} Section component.
 */
function RatingStars( { rating }: any ): JSX.Element {
	// Return component.
	return (
		<span className="rating-stars" style={ {
			'--rating': rating,
		} as React.CSSProperties }></span>
	);
}

// Export component.
export default RatingStars;
