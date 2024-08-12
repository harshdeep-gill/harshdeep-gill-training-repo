/**
 * WordPress dependencies
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { PanelBody, Placeholder } from '@wordpress/components';

/**
 * Internal dependencies
 */
import icons from '../icons';

/**
 * External dependencies
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { GalleryControl, Img } = gumponents.components;

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
			'media-carousel',
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Media Carousel Options', 'qrk' ) }>
					<GalleryControl
						label={ __( 'Carousel Images', 'qrk' ) }
						value={ attributes.media }
						size="large"
						help={ __( 'Choose images for the carousel.', 'qrk' ) }
						onSelect={ ( media: any ) => {
							// Update the gallery.
							setAttributes( { media: [] } );
							setAttributes( { media } );
						} }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps } >
				<figure className="media-carousel__image-wrap">
					{
						attributes.media && attributes.media.length ? (
							<Img
								className="media-carousel__image"
								value={ attributes.media[ 0 ] }
							/>
						) : (
							<Placeholder
								label={ __( 'The selected images will show up here.', 'qrk' ) }
								icon="layout"
							/>
						)
					}
				</figure>
				<div className="media-carousel__arrows">
					<div className="media-carousel__arrow-button">{ icons.chevronLeft }</div>
					<div className="media-carousel__arrow-button media-carousel__arrow-button--right">{ icons.chevronLeft }</div>
				</div>
			</div>
		</>
	);
}
