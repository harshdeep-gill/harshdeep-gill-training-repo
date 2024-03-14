/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/product-cards-itinerary';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card Itinerary', 'qrk' ),
	description: __( 'Individual Card Itinerary for product cards.', 'qrk' ),
	parent: [ 'quark/product-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'itinerary', 'qrk' ) ],
	attributes: {
		departureDate: {
			type: 'string',
			default: '',
		},
		durationText: {
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
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'product-cards__itinerary' ),
		} );

		// Return the block's markup.
		return (
			<div { ...blockProps }>
				<RichText
					tagName="span"
					className="product-cards__departure-date"
					placeholder={ __( 'Departs Month DD, YYYY…', 'qrk' ) }
					value={ attributes.departureDate }
					onChange={ ( departureDate: string ) => setAttributes( { departureDate } ) }
					allowedFormats={ [] }
				/>
				&nbsp;|&nbsp;
				<RichText
					tagName="span"
					className="product-cards__duration"
					placeholder={ __( 'X Days…', 'qrk' ) }
					value={ attributes.durationText }
					onChange={ ( durationText: string ) => setAttributes( { durationText } ) }
					allowedFormats={ [] }
				/>
			</div>
		);
	},
	save() {
		// Return.
		return null;
	},
};
