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
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className ),
	} );

	// Return the block's markup.
	return (
		<RichText
			{ ...blockProps }
			className="review-cards__author-details"
			placeholder={ __( 'Write details… eg. Expedition Name', 'qrk' ) }
			value={ attributes.authorDetails }
			onChange={ ( authorDetails: string ) => setAttributes( { authorDetails } ) }
			allowedFormats={ [] }
		/>
	);
}
