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
		className: classnames( className, 'highlights__item-title', 'h5' ),
	} );

	// Return the block's markup.
	return (
		<RichText
			{ ...blocksProps }
			tagName="h2"
			placeholder={ __( 'Write highlight title…', 'qrk' ) }
			value={ attributes.title }
			onChange={ ( title: string ) => setAttributes( { title } ) }
			allowedFormats={ [] }
		/>
	);
}
