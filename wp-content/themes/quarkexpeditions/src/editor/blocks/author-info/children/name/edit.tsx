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
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'post-author-info__name' ),
	} );

	// Return the block's markup.
	return (
		<RichText
			{ ...blockProps }
			tagName="p"
			placeholder={ __( 'Write name…', 'qrk' ) }
			value={ attributes.title }
			onChange={ ( title: string ) => setAttributes( { title } ) }
			allowedFormats={ [] }
		/>
	);
}
