/**
 * WordPress dependencies.
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set block attributes.
	const blocksProps = useBlockProps();

	// Return the block's markup.
	return (
		<div className="media-cta-banner__overline overline">
			<RichText
				{ ...blocksProps }
				tagName="p"
				placeholder={ __( 'Write Overline… ', 'qrk' ) }
				value={ attributes.text }
				onChange={ ( text: string ) => setAttributes( { text } ) }
				allowedFormats={ [] }
			/>
		</div>
	);
}
