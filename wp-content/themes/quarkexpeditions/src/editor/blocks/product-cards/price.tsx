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
export const name: string = 'quark/product-cards-price';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card Price', 'qrk' ),
	description: __( 'Individual Card Price for product cards.', 'qrk' ),
	parent: [ 'quark/product-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'price', 'qrk' ) ],
	attributes: {
		price: {
			type: 'string',
			default: '',
		},
		originalPrice: {
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
			className: classnames( className, 'product-cards__price-wrap' ),
		} );

		// Return the block's markup.
		return (
			<div { ...blockProps }>
				<div className="product-cards__price-title">{ __( 'Sale Price From', 'qrk' ) }</div>
				<RichText
					tagName="strong"
					className="product-cards__price product-cards__price-now h4"
					placeholder={ __( '$ X,XXX USD…', 'qrk' ) }
					value={ attributes.price }
					onChange={ ( price: string ) => setAttributes( { price } ) }
					allowedFormats={ [] }
				/>
				<strong>
					<RichText
						tagName="del"
						className="product-cards__price product-cards__price--original"
						placeholder={ __( '$ X,XXX USD…', 'qrk' ) }
						value={ attributes.originalPrice }
						onChange={ ( originalPrice: string ) => setAttributes( { originalPrice } ) }
						allowedFormats={ [] }
					/>
				</strong>
			</div>
		);
	},
	save() {
		// Return.
		return null;
	},
};
