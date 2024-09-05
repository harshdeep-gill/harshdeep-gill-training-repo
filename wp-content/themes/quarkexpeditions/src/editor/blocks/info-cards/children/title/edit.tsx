/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
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
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// Set block attributes.
	const blocksProps = useBlockProps( {
		className: classnames( className ),
	} );

	// Return the block's markup.
	return (
		<RichText
			{ ...blocksProps }
			tagName="h4"
			className="info-cards__card-title"
			placeholder={ __( 'Write title…', 'qrk' ) }
			value={ attributes.title }
			onChange={ ( title: string ) => setAttributes( { title } ) }
			allowedFormats={ [] }
		/>
	);
}
