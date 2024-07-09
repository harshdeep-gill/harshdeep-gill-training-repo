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
		className: classnames( className, 'post-author-info__duration' ),
	} );

	// Return the block's markup.
	return (
		<>
			<RichText
				{ ...blockProps }
				tagName="span"
				placeholder={ __( 'Duration in minutes (eg: \'5\')', 'qrk' ) }
				value={ attributes.duration.toString() }
				onChange={ ( value: string ) => {
					// Parse the duration value.
					const duration = parseInt( value );

					// Check if it is a valid number.
					if ( Number.isNaN( duration ) ) {
						// Bail.
						return;
					}

					// set the value.
					setAttributes( { duration } );
				} }
				allowedFormats={ [] }
			/>
			<span>&nbsp;min read</span>
		</>
	);
}
