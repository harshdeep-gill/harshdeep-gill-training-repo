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
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// Prepare block props.
	const blocksProps = useBlockProps( {
		className: classnames( className, 'highlights__item-text' ),
	} );

	// Return the block's markup.
	return (
		<RichText
			{ ...blocksProps }
			tagName="p"
			placeholder={ __( 'Write Highlight Text...', 'qrk' ) }
			value={ attributes.text }
			onChange={ ( text: string ) => setAttributes( { text } ) }
			allowedFormats={ [] }
		/>
	);
}
