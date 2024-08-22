/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
} from '@wordpress/block-editor';

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
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set Block Props
	const blockProps = useBlockProps( {
		className: classnames( className, 'specifications__item' ),
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<RichText
				className="specifications__label"
				tagName="div"
				placeholder={ __( 'Write the Label', 'qrk' ) }
				value={ attributes.label }
				onChange={ ( label: string ) => setAttributes( { label } ) }
				allowedFormats={ [] }
			/>
			<RichText
				className="specifications__value h5"
				tagName="div"
				placeholder={ __( 'Write the Value', 'qrk' ) }
				value={ attributes.value }
				onChange={ ( value: string ) => setAttributes( { value } ) }
				allowedFormats={ [] }
			/>
		</div>
	);
}
