/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import { PanelBody, TextControl } from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	RichText,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/fancy-video/style.scss';
import './editor.scss';

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
 * Internal dependencies.
 */
import { convertToEmbedUrl } from '../utils';

/**
 * Block name.
 */
export const name: string = 'quark/fancy-video';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Fancy Video', 'qrk' ),
	description: __( 'Display a fancy video block.', 'qrk' ),
	category: 'layout',
	keywords: [ __( 'fancy', 'qrk' ), __( 'video', 'qrk' ) ],
	attributes: {
		image: {
			type: 'object',
			default: {},
		},
		title: {
			type: 'string',
		},
		videoUrl: {
			type: 'string',
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( {
		className,
		attributes,
		setAttributes,
	}: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'fancy-video typography-spacing' ),
		} );

		/**
		 * Handle a change in URL.
		 *
		 * @param {string} url URL.
		 */
		const handleUrlChange = ( url: string ): void => {
			// Get the converted embed url.
			const videoUrl = convertToEmbedUrl( url );

			// Set attributes.
			setAttributes( { videoUrl } );
		};

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Fancy Video Options', 'qrk' ) }>
						<ImageControl
							label={ __( 'Image', 'qrk' ) }
							value={ attributes.image ? attributes.image.id : null }
							size="large"
							help={ __( 'Choose a cover image for this video.', 'qrk' ) }
							onChange={ ( image: object ) => setAttributes( { image } ) }
						/>
						<TextControl
							label={ __( 'Video URL', 'qrk' ) }
							help={ __( 'Enter a YouTube Video URL.', 'qrk' ) }
							value={ attributes.videoUrl }
							onChange={ handleUrlChange }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="fancy-video__wrapper">
						<div className="fancy-video__cover">
							{ attributes.image && (
								<Img className="fancy-video__image" value={ attributes.image } />
							) }
						</div>
						<div className="fancy-video__content">
							<RichText
								tagName="p"
								className="fancy-video__title h2"
								placeholder={ __( 'Write title…', 'qrk' ) }
								value={ attributes.title }
								onChange={ ( title: string ) => setAttributes( { title } ) }
								allowedFormats={ [] }
							/>
						</div>
						<div className="fancy-video__play-btn-wrapper">
							<button className="fancy-video__play-btn">
								{ icons.play }
							</button>
						</div>
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
