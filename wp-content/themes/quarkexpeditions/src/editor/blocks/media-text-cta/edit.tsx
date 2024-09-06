/**
 * WordPress dependencies
 */
import { InspectorControls, RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { PanelBody, RadioControl, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

/**
 * Styles.
 */
import './editor.scss';

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
 * Internal dependencies
 */
import { convertToEmbedUrl } from '../utils';
import icons from '../icons';

/**
 * Child block.
 */
import * as secondaryText from './children/secondary-text';
import * as cta from './children/cta';
import * as overline from './children/overline';

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'media-text-cta',
			'grid',
			'right' === attributes.mediaAlignment ? 'media-text-cta--media-align-right' : 'media-text-cta--media-align-left',
		),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'media-text-cta__content' ),
	},
	{
		allowedBlocks: [
			'core/paragraph',
			'core/heading',
			secondaryText.name,
			cta.name,
			overline.name,
		],
		template: [
			[ 'core/heading', { level: 3 } ],
			[ 'core/paragraph', { placeholder: __( 'Write description…', 'qrk' ) } ],
			[ secondaryText.name ],
			[ cta.name ],
		],
	} );

	/**
	 * Handle a change in the Video URL.
	 *
	 * @param {string} videoUrl Video URL.
	 */
	const handleUrlChange = ( videoUrl: string ) => {
		// Convert to Embed URL.
		videoUrl = convertToEmbedUrl( videoUrl );

		// Set attributes.
		setAttributes( { videoUrl } );
	};

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Media Text CTA Options', 'qrk' ) }>
					<RadioControl
						label={ __( 'Media Type', 'qrk' ) }
						help={ __( 'Select the media type.', 'qrk' ) }
						selected={ attributes.mediaType }
						options={ [
							{ label: __( 'Image', 'qrk' ), value: 'image' },
							{ label: __( 'Video', 'qrk' ), value: 'video' },
						] }
						onChange={ ( mediaType: string ) => setAttributes( { mediaType } ) }
					/>
					<ImageControl
						label={ __( 'Image', 'qrk' ) }
						value={ attributes.image ? attributes.image.id : null }
						size="full"
						help={ __( 'Choose an image for this collage item.', 'qrk' ) }
						onChange={ ( image: object ) => setAttributes( { image } ) }
					/>
					{
						'image' === attributes.mediaType &&
							<SelectControl
								label={ __( 'Image Aspect Ratio', 'qrk' ) }
								help={ __( 'Select the image aspect ratio.', 'qrk' ) }
								value={ attributes.imageAspectRatio }
								options={ [
									{ label: __( 'Landscape', 'qrk' ), value: 'landscape' },
									{ label: __( 'Square', 'qrk' ), value: 'square' },
								] }
								onChange={ ( imageAspectRatio: string ) => setAttributes( { imageAspectRatio } ) }
							/>
					}
					{
						'video' === attributes.mediaType &&
						<TextControl
							label={ __( 'Video URL', 'qrk' ) }
							help={ __( 'Enter the Video URL.', 'qrk' ) }
							value={ attributes.videoUrl }
							onChange={ handleUrlChange }
						/>
					}
					<RadioControl
						label={ __( 'Media Alignment', 'qrk' ) }
						help={ __( 'Select the media alignment.', 'qrk' ) }
						selected={ attributes.mediaAlignment }
						options={ [
							{ label: __( 'Left', 'qrk' ), value: 'left' },
							{ label: __( 'Right', 'qrk' ), value: 'right' },
						] }
						onChange={ ( mediaAlignment: string ) => setAttributes( { mediaAlignment } ) }
					/>
					<ToggleControl
						label={ __( 'Has CTA Badge?', 'qrk' ) }
						checked={ attributes.hasCtaBadge }
						onChange={ () => setAttributes( { hasCtaBadge: ! attributes.hasCtaBadge } ) }
						help={ __( 'Does the image have a CTA Badge on top-left like \'Featured Expedition\'?', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps } >
				<div className={ `media-text-cta__media-wrap media-text-cta__media-wrap--${ attributes.imageAspectRatio }` }>
					<Img className="media-text-cta__image" value={ attributes.image } />
					{
						'video' === attributes.mediaType &&
						<div className="fancy-video__play-btn-wrapper">
							<button className="fancy-video__play-btn">
								{ icons.play }
							</button>
						</div>
					}
					{ attributes.hasCtaBadge &&
						<RichText
							tagName="div"
							className="media-text-cta__badge body-small"
							placeholder={ __( 'CTA Badge Text…', 'qrk' ) }
							value={ attributes.ctaBadgeText }
							onChange={ ( ctaBadgeText: string ) => setAttributes( { ctaBadgeText } ) }
							allowedFormats={ [] }
						/>
					}
				</div>
				<div { ...innerBlockProps } />
			</div>
		</>
	);
}
