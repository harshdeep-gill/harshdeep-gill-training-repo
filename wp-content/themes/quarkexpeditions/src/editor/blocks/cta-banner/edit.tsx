/**
 * WordPress dependencies
 */
import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { PanelBody, SelectControl } from '@wordpress/components';

/**
 * External dependencies
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl, Img, ColorPaletteControl } = gumponents.components;

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
			{
				[ `media-cta-banner--${ attributes.appearance }` ]: attributes.appearance,
				'color-context--dark': 'dark' === attributes.appearance,
				[ `media-cta-banner--has-background-${ attributes.backgroundColor }` ]: attributes.backgroundColor && 'solid' === attributes.appearance,
			}
		),
	} );

	// Set the inner blocks props.
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'media-cta-banner__content' ),
	},
	{
		allowedBlocks: [ 'quark/cta-banner-overline', 'core/paragraph', 'core/heading', 'quark/buttons' ],
		template: [
			[ 'quark/cta-banner-overline' ],
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
					<SelectControl
						label={ __( 'Appearance', 'qrk' ) }
						value={ attributes.appearance }
						options={ [
							{ label: __( 'Light', 'qrk' ), value: 'light' },
							{ label: __( 'Dark', 'qrk' ), value: 'dark' },
							{ label: __( 'Solid', 'qrk' ), value: 'solid' },
						] }
						onChange={ ( appearance: string ) => setAttributes( { appearance } ) }
					/>
					{ 'solid' !== attributes.appearance &&
						<ImageControl
							label={ __( 'Background Image', 'qrk' ) }
							value={ attributes.backgroundImage ? attributes.backgroundImage.id : null }
							size="large"
							help={ __( 'Choose an image for this collage item.', 'qrk' ) }
							onChange={ ( backgroundImage: object ) => setAttributes( { backgroundImage } ) }
						/>
					}
					{ 'solid' === attributes.appearance &&
						<ColorPaletteControl
							label={ __( 'Background Color', 'qrk' ) }
							help={ __( 'Select the background color.', 'qrk' ) }
							value={ attributes.backgroundColor }
							colors={ [ { color: '#F5F7FB', slug: 'gray' } ] }
							onChange={ ( backgroundColor: {
								color: string;
								slug: string;
							} ): void => {
								// Set the background color attribute.
								setAttributes( { backgroundColor: backgroundColor.slug } );
							} }
						/>
					}
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
