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
export default function 	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'contact-cover-card__contact-info-item' ),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Contact Cover Info Item Options', 'qrk' ) }>
					<LinkControl
						label={ __( 'Select URL', 'qrk' ) }
						value={ attributes.url }
						help={ __( 'Enter an URL for this Info item', 'qrk' ) }
						onChange={ ( url: object ) => setAttributes( { url } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blocksProps }>
				<div className="btn btn--color-black btn--size-big">
					<RichText
						tagName="span"
						className="contact-cover-card__contact-info-item-label"
						placeholder={ __( 'Enter label… ', 'qrk' ) }
						value={ attributes.label }
						onChange={ ( label: string ) => setAttributes( { label } ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="strong"
						className="contact-cover-card__contact-info-item-value"
						placeholder={ __( 'Enter value…', 'qrk' ) }
						value={ attributes.value }
						onChange={ ( value: string ) => setAttributes( { value } ) }
						allowedFormats={ [] }
					/>
				</div>
			</div>
		</>
	);
}
