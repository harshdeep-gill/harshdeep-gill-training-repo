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
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps();

	// Return the block's markup.
	return (
		<div className="media-text-cta__description">
			<RichText
				{ ...blocksProps }
				tagName="p"
				placeholder={ __( 'Write Description… ', 'qrk' ) }
				value={ attributes.text }
				onChange={ ( text: string ) => setAttributes( { text } ) }
				allowedFormats={ [] }
			/>
		</div>
	);
}
