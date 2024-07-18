/**
 * WordPress dependencies.
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
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
	const blocksProps = useBlockProps( {
		className: classnames( className, 'media-text-cta__secondary-text' ),
	} );

	// Return the block's markup.
	return (
		<RichText
			{ ...blocksProps }
			tagName="div"
			placeholder={ __( 'Write Secondary Text… ', 'qrk' ) }
			value={ attributes.secondaryText }
			onChange={ ( secondaryText: string ) => setAttributes( { secondaryText } ) }
			allowedFormats={ [] }
		/>
	);
}
