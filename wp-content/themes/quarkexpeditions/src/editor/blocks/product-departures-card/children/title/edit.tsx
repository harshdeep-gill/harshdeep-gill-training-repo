/**
 * WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import classnames from 'classnames';

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
		className: classnames( className ),
	} );

	// Return the block's markup.
	return (
		<RichText
			{ ...blockProps }
			tagName="h2"
			className="product-departures-card__title h4"
			placeholder={ __( 'Write Title…', 'qrk' ) }
			value={ attributes.title }
			onChange={ ( title: string ) => setAttributes( { title } ) }
			allowedFormats={ [] }
		/>
	);
}
