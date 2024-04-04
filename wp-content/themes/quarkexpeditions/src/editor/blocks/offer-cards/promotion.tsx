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
export const name: string = 'quark/offer-card-promotion';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card Promotion', 'qrk' ),
	description: __( 'Individual card promotion details for Offer Cards.', 'qrk' ),
	parent: [ 'quark/offer-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'promotion', 'qrk' ) ],
	attributes: {
		promotionText: {
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
			className: classnames( className ),
		} );

		// Return the block's markup.
		return (
			<RichText
				{ ...blockProps }
				tagName="div"
				className="offer-cards__promotion"
				placeholder={ __( 'Write Promotion Details…', 'qrk' ) }
				value={ attributes.promotionText }
				onChange={ ( promotionText: string ) => setAttributes( { promotionText } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Return.
		return null;
	},
};
