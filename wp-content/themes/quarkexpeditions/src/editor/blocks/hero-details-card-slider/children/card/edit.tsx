/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { useBlockProps, MediaUpload } from '@wordpress/block-editor';
import { select } from '@wordpress/data';

/**
 * External dependencies.
 */
import classnames from 'classnames';
import Section from '../../../../components/section';
const { gumponents } = window;

/**
 * External components.
 */
const { SelectImage, LinkControl } = gumponents.components;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Component attributes.
 * @param {Function} props.setAttributes Component set attributes.
 * @param {string}   props.clientId      Component client ID.
 */
export default function Edit( {
	attributes,
	setAttributes,
	clientId,
}: BlockEditAttributes ) {
	// Block props.
	const blockProps = useBlockProps( {
		className: classnames( 'hero-details-card-slider__card' ),
	} );

	// Block Index.
	const blockIndex = select( 'core/block-editor' ).getBlockIndex?.( clientId );

	// Return block.
	return (
		<>
			<div { ...blockProps }>
				<h4 className="hero-details-card-slider__slide-title">
					{ __( 'Slide', 'qrk' ) } { blockIndex + 1 }
				</h4>
				<Section>
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
						<SelectImage
							placeholder={ __( 'Choose image', 'qrk' ) }
							size="large"
							image={ attributes.media }
							onChange={ ( media: object ) => setAttributes( { media } ) }
						/>
					}
					{ 'video' === attributes.mediaType &&
						<MediaUpload
							onSelect={ ( video ) => setAttributes( { media: { id: video.id, url: video.url } } ) }
							allowedTypes={ [ 'video' ] }
							value={ attributes.media.id }
							render={ ( { open } ) => (
								<>
									{
										attributes.media.id && (
											<video
												className="hero-details-card-slider__video"
												controls
												src={ attributes.media.url }
											/>
										)
									}
									<button className="button" onClick={ open }>
										{ ! attributes.media.id ? __( 'Select video', 'qrk' ) : __( 'Change video', 'qrk' ) }
									</button>
								</>
							) }
						/>
					}
				</Section>
				<Section>
					<ToggleControl
						label={ __( 'Show tag', 'qrk' ) }
						checked={ attributes.hasTag }
						onChange={ ( hasTag: boolean ) => setAttributes( { hasTag } ) }
					/>
					{ attributes.hasTag &&
						<SelectControl
							label={ __( 'Tag Type', 'qrk' ) }
							value={ attributes.tagType }
							options={ [
								{ label: __( 'Tag', 'qrk' ), value: 'tag' },
								{ label: __( 'Overline', 'qrk' ), value: 'overline' },
							] }
							onChange={ ( tagType: string ) => setAttributes( { tagType } ) }
						/>
					}
					{ attributes.hasTag && (
						<TextControl
							label={ __( 'Tag', 'qrk' ) }
							value={ attributes.tagText }
							onChange={ ( tagText: string ) => setAttributes( { tagText } ) }
							help={ __(
								'This is the tag that will be displayed on the card.',
								'qrk',
							) }
						/>
					) }
				</Section>
				<Section>
					<TextControl
						label={ __( 'Title', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						help={ __(
							'This is the title that will be displayed on the card.',
							'qrk',
						) }
					/>
					<TextControl
						label={ __( 'Description', 'qrk' ) }
						value={ attributes.descriptionText }
						onChange={ ( descriptionText: string ) => setAttributes( { descriptionText } ) }
						help={ __(
							'This is the description that will be displayed on the card.',
							'qrk',
						) }
					/>
				</Section>
				<Section>
					<ToggleControl
						label={ __( 'Show CTA link', 'qrk' ) }
						checked={ attributes.hasCtaLink }
						onChange={ ( hasCtaLink: boolean ) => setAttributes( { hasCtaLink } ) }
					/>
					{ attributes.hasCtaLink && (
						<LinkControl
							label={ __( 'CTA link', 'qrk' ) }
							value={ attributes.cta }
							help={ __( 'This is the CTA link that will be displayed on the card.', 'qrk' ) }
							onChange={ ( cta: object ) => setAttributes( { cta } ) }
						/>
					) }
				</Section>
			</div>
		</>
	);
}
