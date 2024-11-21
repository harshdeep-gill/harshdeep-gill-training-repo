/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
	MediaUpload,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl, Img, LinkControl } = gumponents.components;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'thumbnail-cards__card',
			attributes.size && [ 'small', 'medium', 'large' ].includes( attributes.size ) ? `thumbnail-cards__card--size-${ attributes.size }` : '',
			attributes.orientation && [ 'portrait', 'landscape' ].includes( attributes.orientation ) ? `thumbnail-cards__card--orient-${ attributes.orientation }` : ''
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Thumbnail Card Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Media type', 'qrk' ) }
						help={ __( 'This is the type of media that will be displayed on the card.', 'qrk' ) }
						value={ attributes.mediaType }
						options={ [
							{ label: __( 'Image', 'qrk' ), value: 'image' },
							{ label: __( 'Video', 'qrk' ), value: 'video' },
						] }
						onChange={ ( mediaType: string ) => setAttributes( { mediaType } ) }
					/>
					{ 'image' === attributes.mediaType &&
						<ImageControl
							label={ __( 'Image', 'qrk' ) }
							value={ attributes.image ? attributes.image.id : null }
							size="full"
							help={ __( 'Choose an image for this card.', 'qrk' ) }
							onChange={ ( image: object ) => setAttributes( { image } ) }
						/>
					}
					{ 'video' === attributes.mediaType &&
						<MediaUpload
							onSelect={ ( video ) => setAttributes( { video: { id: video.id, url: video.url } } ) }
							allowedTypes={ [ 'video' ] }
							value={ attributes.video.id }
							render={ ( { open } ) => (
								<>
									{
										attributes.video.id && ( <video controls src={ attributes.video.url } /> )
									}
									<button className="thumbnail-cards__video-select button" onClick={ open }>
										{ ! attributes.video.id ? __( 'Select Video', 'qrk' ) : __( 'Change Video', 'qrk' ) }
									</button>
								</>
							) }
						/>
					}
					<LinkControl
						label={ __( 'Select URL', 'qrk' ) }
						value={ attributes.url }
						help={ __( 'Enter an URL for this card', 'qrk' ) }
						onChange={ ( url: object ) => setAttributes( { url } ) }
					/>
					<SelectControl
						label={ __( 'Size', 'qrk' ) }
						help={ __( 'Select the size.', 'qrk' ) }
						value={ attributes.size }
						options={ [
							{ label: __( 'Small', 'qrk' ), value: 'small' },
							{ label: __( 'Medium', 'qrk' ), value: 'medium' },
							{ label: __( 'Large', 'qrk' ), value: 'large' },
						] }
						onChange={ ( size: string ) => setAttributes( { size } ) }
					/>
					<SelectControl
						label={ __( 'Orientation', 'qrk' ) }
						help={ __( 'Select the Orientation.', 'qrk' ) }
						value={ attributes.orientation }
						options={ [
							{ label: __( 'Portrait', 'qrk' ), value: 'portrait' },
							{ label: __( 'Landscape', 'qrk' ), value: 'landscape' },
						] }
						onChange={ ( orientation: string ) => setAttributes( { orientation } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ 'image' === attributes.mediaType && attributes.image &&
					<Img
						value={ attributes.image }
						className={ classnames(
							'thumbnail-cards__image',
							attributes.size && [ 'small', 'medium', 'large' ].includes( attributes.size ) ? `thumbnail-cards__image--size-${ attributes.size }` : '',
							attributes.orientation && [ 'portrait', 'landscape' ].includes( attributes.orientation ) ? `thumbnail-cards__image--orient-${ attributes.orientation }` : ''
						) }
					/>
				}
				{ 'video' === attributes.mediaType && attributes.video &&
					<video
						className={ classnames(
							'thumbnail-cards__video',
							attributes.size && [ 'small', 'medium', 'large' ].includes( attributes.size ) ? `thumbnail-cards__video--size-${ attributes.size }` : '',
							attributes.orientation && [ 'portrait', 'landscape' ].includes( attributes.orientation ) ? `thumbnail-cards__video--orient-${ attributes.orientation }` : ''
						) }
						controls
						src={ attributes.video.url }
					/>
				}
				<RichText
					tagName="p"
					className="thumbnail-cards__card-title h5 thumbnail-cards__card-title--align-bottom"
					placeholder={ __( 'Title here…', 'qrk' ) }
					value={ attributes.title }
					onChange={ ( title ) => setAttributes( { title } ) }
					allowedFormats={ [] }
				/>
			</div>
		</>
	);
}
