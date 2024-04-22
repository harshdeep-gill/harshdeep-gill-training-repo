/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	RadioControl,
	ToggleControl,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */
import icons from '../icons';
import { convertToEmbedUrl } from '../utils';

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
 * Styles.
 */
import '../../../front-end/components/media-text-cta/style.scss';
import '../../../front-end/components/fancy-video/style.scss';
import './editor.scss';

/**
 * Child block.
 */
import * as secondaryText from './secondary-text';
import * as cta from './cta';

/**
 * Register child block.
 */
registerBlockType( secondaryText.name, secondaryText.settings );
registerBlockType( cta.name, cta.settings );

/**
 * Block name.
 */
export const name: string = 'quark/media-text-cta';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Media Text CTA', 'qrk' ),
	description: __( 'Add a 2-column layout of media, text and a cta', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'media', 'qrk' ),
		__( 'text', 'qrk' ),
		__( 'cta', 'qrk' ),
	],
	attributes: {
		mediaType: {
			type: 'string',
			default: 'image',
			enum: [ 'image', 'video' ],
		},
		mediaAlignment: {
			type: 'string',
			default: 'left',
			enum: [ 'left', 'right' ],
		},
		image: {
			type: 'object',
			default: {},
		},
		videoUrl: {
			type: 'string',
			default: '',
		},
		hasCtaBadge: {
			type: 'boolean',
			default: false,
		},
		ctaBadgeText: {
			type: 'string',
			default: '',
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
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
			allowedBlocks: [ 'core/paragraph', 'core/heading', secondaryText.name, cta.name ],
			template: [
				[ 'core/heading' ],
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
					<div className="media-text-cta__media-wrap">
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
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
