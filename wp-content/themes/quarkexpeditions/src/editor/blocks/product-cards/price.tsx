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
export const name: string = 'quark/product-cards-card-price';

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
		priceTitle: {
			type: 'string',
			default: '',
		},
		priceNow: {
			type: 'string',
			default: '',
		},
		priceWas: {
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
				<RichText
					tagName='div'
					className='product-cards__price-title'
					placeholder={ __( 'Enter Price Title', 'qrk' ) }
					value={ attributes.priceTitle }
					onChange={ ( priceTitle: string ) => setAttributes( { priceTitle } ) }
					allowedFormats={ [] }
				/>
				<RichText
					tagName='strong'
					className='product-cards__price product-cards__price-now h4'
					placeholder={ __( '$ XXXX USD', 'qrk' ) }
					value={ attributes.priceNow }
					onChange={ ( priceNow: string ) => setAttributes( { priceNow } ) }
					allowedFormats={ [] }
				/>
				<strong>
					<RichText
						tagName='del'
						className='product-cards__price price-was'
						placeholder={ __( '$ XXXX USD', 'qrk' ) }
						value={ attributes.priceWas }
						onChange={ ( priceWas: string ) => setAttributes( { priceWas } ) }
						allowedFormats={ [] }
					/>
				</strong>
			</div>
		);
	},
	save() {
		return null;
	},
};
