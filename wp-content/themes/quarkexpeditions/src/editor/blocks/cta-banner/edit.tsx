/**
 * WordPress dependencies
 */
import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * External dependencies
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl, Img } = gumponents.components;

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set the block props.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'media-cta-banner',
			attributes.darkMode ? 'media-cta-banner--dark color-context--dark' : 'media-cta-banner--light',
		),
	} );

	// Set the inner blocks props.
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'media-cta-banner__content' ),
	},
	{
		allowedBlocks: [ 'core/paragraph', 'core/heading', 'quark/buttons' ],
		template: [
			[ 'core/heading' ],
			[ 'core/paragraph', { placeholder: __( 'Write description…', 'qrk' ) } ],
			[ 'quark/buttons' ],
		],
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'CTA Banner Options', 'qrk' ) }>
					<ImageControl
						label={ __( 'Background Image', 'qrk' ) }
						value={ attributes.backgroundImage ? attributes.backgroundImage.id : null }
						size="large"
						help={ __( 'Choose an image for this collage item.', 'qrk' ) }
						onChange={ ( backgroundImage: object ) => setAttributes( { backgroundImage } ) }
					/>
					<ToggleControl
						label={ __( 'Dark Mode?', 'qrk' ) }
						checked={ attributes.darkMode }
						onChange={ ( darkMode: boolean ) => setAttributes( { darkMode } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps } >
				<figure className="media-cta-banner__media-wrap">
					<Img className="media-cta-banner__image" value={ attributes.backgroundImage } />
				</figure>
				<div { ...innerBlockProps } />
			</div>
		</>
	);
}
