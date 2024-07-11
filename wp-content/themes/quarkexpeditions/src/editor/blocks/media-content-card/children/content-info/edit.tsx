/**
 * WordPress dependencies.
 */
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { LinkControl } = gumponents.components;

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
		className: classnames( className, 'media-content-card__content-info' ),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Media Content Info Options', 'qrk' ) }>
					<LinkControl
						label={ __( 'Select URL', 'qrk' ) }
						value={ attributes.url }
						help={ __( 'Enter an URL for this Info item', 'qrk' ) }
						onChange={ ( url: object ) => setAttributes( { url } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blocksProps }>
				<RichText
					tagName="span"
					className="media-content-card__content-info-label"
					placeholder={ __( 'Enter label… ', 'qrk' ) }
					value={ attributes.label }
					onChange={ ( label: string ) => setAttributes( { label } ) }
					allowedFormats={ [] }
				/>
				<RichText
					tagName="strong"
					className="media-content-card__content-info-value"
					placeholder={ __( 'Enter value…', 'qrk' ) }
					value={ attributes.value }
					onChange={ ( value: string ) => setAttributes( { value } ) }
					allowedFormats={ [] }
				/>
			</div>
		</>
	);
}
