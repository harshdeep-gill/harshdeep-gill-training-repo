/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl, Img } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/collage-media-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Collage Media Item', 'qrk' ),
	description: __( 'Individual collage media item.', 'qrk' ),
	parent: [ 'quark/collage' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'item', 'qrk' ) ],
	attributes: {
		mediaType: {
			type: 'string',
			default: 'image',
		},
		size: {
			type: 'string',
			default: 'small',
		},
		image: {
			type: 'object',
		},
		caption: {
			type: 'string',
		},
		videoUrl: {
			type: 'string',
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blocksProps = useBlockProps( {
			className: classnames(
				className,
				'image' === attributes.mediaType
					? `collage__image-item collage__image-item--${ attributes.size }`
					: `collage__video-item collage__video-item--${ attributes.size }`,
			),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Collage Media Item Options', 'qrk' ) }>
						<SelectControl
							label={ __( 'Media Type', 'qrk' ) }
							help={ __( 'Select the media type', 'qrk' ) }
							value={ attributes.mediaType }
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
						<SelectControl
							label={ __( 'Size', 'qrk' ) }
							help={ __( 'Select the size of the image.', 'qrk' ) }
							value={ attributes.size }
							options={ [
								{ label: __( 'Small (25%)', 'qrk' ), value: 'small' },
								{ label: __( 'Medium (50%)', 'qrk' ), value: 'medium' },
								{ label: __( 'Large (75%)', 'qrk' ), value: 'large' },
								{ label: __( 'X-Large (100%)', 'qrk' ), value: 'x-large' },
							] }
							onChange={ ( size: string ) => setAttributes( { size } ) }
						/>
						{
							'video' === attributes.mediaType &&
							<TextControl
								label={ __( 'Video URL', 'qrk' ) }
								help={ __( 'Enter the Video URL.', 'qrk' ) }
								value={ attributes.videoUrl }
								onChange={ ( videoUrl: string ) => setAttributes( { videoUrl } ) }
							/>
						}
						<TextControl
							label={ __( 'Caption', 'qrk' ) }
							help={ __( 'Enter the caption.', 'qrk' ) }
							value={ attributes.caption }
							onChange={ ( caption: string ) => setAttributes( { caption } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blocksProps }>
					<div className="media-lightbox__link">
						<figure className="media-lightbox__image-wrap">
							<Img
								value={ attributes.image }
							/>
							{
								'video' === attributes.mediaType &&
								<div className="collage__video-button-wrapper">
									<button className="collage__video-button">
										{ icons.play }
									</button>
								</div>
							}
						</figure>
					</div>
				</div>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
