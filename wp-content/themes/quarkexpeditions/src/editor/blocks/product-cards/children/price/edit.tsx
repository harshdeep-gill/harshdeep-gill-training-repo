/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
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
}
