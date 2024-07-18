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
		className: classnames( className, 'product-cards__description' ),
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<RichText
				tagName="p"
				placeholder={ __( 'Enter Description…', 'qrk' ) }
				value={ attributes.description }
				onChange={ ( description: string ) => setAttributes( { description } ) }
				allowedFormats={ [] }
			/>
		</div>
	);
}
