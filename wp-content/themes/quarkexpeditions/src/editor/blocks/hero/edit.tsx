/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { store as editorStore } from '@wordpress/editor';
import apiFetch from '@wordpress/api-fetch';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	useSelect,
	useDispatch,
} from '@wordpress/data';

/**
 * Styles.
 */
import './editor.scss';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';
import * as breadCrumbs from '../breadcrumbs';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const {
	ImageControl,
	Img,
} = gumponents.components;

/**
 * Children blocks
 */
import * as heroContent from './children/hero-content';

/**
 * Get formatted image details from a media object.
 *
 * @param {Object} media         Media object.
 * @param {string} thumbnailSize Thumbnail size.
 *
 * @return {Object} Formatted image details.
 */
export function getImageDetails( media: Record<string, any>, thumbnailSize: string ): Record<string, any> {
	// If media is not set, return empty object.
	if ( ! media ) {
		// Return empty object.
		return {};
	}

	// Initialize src, width, and height.
	let src, width, height;

	// If media has sizes, get the thumbnail size.
	if ( media.sizes ) {
		// If thumbnail size is not found, set thumbnail size to full.
		if ( ! media.sizes[ thumbnailSize ] ) {
			// Set thumbnail size to full.
			thumbnailSize = 'full';
		}
		width = media.sizes[ thumbnailSize ].width;
		height = media.sizes[ thumbnailSize ].height;
		src = media.sizes[ thumbnailSize ].url;
	}

	// Return formatted image details.
	return {
		id: media.id,
		src,
		width,
		height,
		alt: media.alt,
		caption: media.caption,
		title: media.title,
		size: thumbnailSize,
	};
}

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Get the post editor store.
	const { editPost } = useDispatch( editorStore );

	// Get the post's featured image ID.
	const postThumbnailId = useSelect(
		( select: any ) => select( editorStore )?.getEditedPostAttribute?.( 'featured_media' ),
		[],
	);

	// Sync post thumbnail whenever the image is changed from block.
	useEffect( () => {
		// Sync post thumbnail function.
		async function syncPostThumbnail() {
			// If post thumbnail is not synced, return.
			if ( ! attributes.syncPostThumbnail || ! postThumbnailId || postThumbnailId === attributes.image?.id ) {
				// Return.
				return;
			}

			// Set attributes.
			try {
				// Get post thumbnail.
				const postThumbnail = await apiFetch( {
					path: `/gumponents/media/v1/get?id=${ postThumbnailId }`,
				} );

				// If post thumbnail is not found, return.
				if ( ! postThumbnail ) {
					// Return.
					return;
				}

				// Set post thumbnail.
				setAttributes( { image: getImageDetails( postThumbnail, 'large' ) } );
			} catch ( error ) {
				setAttributes( { image: null } );
			}
		}

		// Sync post thumbnail.
		syncPostThumbnail();
	}, [ postThumbnailId, attributes.syncPostThumbnail, attributes.image?.id, setAttributes ] );

	/**
	 * Handle image change.
	 *
	 * @param {Object} image    Image.
	 * @param {number} image.id Image ID.
	 */
	const handleImageChange = ( image: Record<string, any> ) => {
		// Set attributes.
		setAttributes( { image } );

		// If post thumbnail is synced, update post thumbnail.
		if ( attributes.syncPostThumbnail && image?.id ) {
			editPost( { featured_media: image.id } );
		}
	};

	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'hero',
			[ 'top', 'bottom', 'all', 'no' ].find(
				( value: string ) => value === attributes.immersive
			) ? `hero--immersive-${ attributes.immersive }` : ''
		),
	} );

	// Set inner block props.
	const innerBlockProps = useInnerBlocksProps(
		{ className: 'hero__wrap' },
		{
			allowedBlocks: [ heroContent.name, breadCrumbs.name ],
			template: [ [ breadCrumbs.name ], [ heroContent.name ] ],
		}
	);

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Hero Options', 'qrk' ) }>
					<ImageControl
						label={ __( 'Image', 'qrk' ) }
						value={ attributes.image ? attributes.image.id : null }
						size="large"
						help={ __( 'Choose an image', 'qrk' ) }
						onChange={ handleImageChange }
					/>
					<RangeControl
						label={ __( 'Overlay opacity in percent', 'qrk' ) }
						value={ attributes.overlayOpacity }
						onChange={ ( overlayOpacity ) => setAttributes( { overlayOpacity } ) }
						min={ 0 }
						max={ 100 }
					/>
					<SelectControl
						label={ __( 'Immersive mode', 'qrk' ) }
						help={ __( 'Select the immersive mode', 'qrk' ) }
						value={ attributes.immersive }
						options={ [
							{ label: __( 'None', 'qrk' ), value: 'none' },
							{ label: __( 'Top', 'qrk' ), value: 'top' },
							{ label: __( 'Bottom', 'qrk' ), value: 'bottom' },
							{ label: __( 'All', 'qrk' ), value: 'all' },
						] }
						onChange={ ( immersive: string ) => setAttributes( { immersive } ) }
					/>
					<SelectControl
						label={ __( 'Text Alignment', 'qrk' ) }
						help={ __( 'Select the text alignment', 'qrk' ) }
						value={ attributes.textAlign }
						options={ [
							{ label: __( 'Left', 'qrk' ), value: 'left' },
							{ label: __( 'Center', 'qrk' ), value: 'center' },
						] }
						onChange={ ( textAlign: string ) => setAttributes( { textAlign } ) }
					/>
					<ToggleControl
						label={ __( 'Sync with Post Thumbnail', 'qrk' ) }
						checked={ attributes.syncPostThumbnail }
						onChange={ ( syncPostThumbnail ) => setAttributes( { syncPostThumbnail } ) }
						help={ __( 'Should the hero image be synced with the post thumbnail?', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps } fullWidth={ true } seamless={ true } >
				<div
					className="hero__overlay"
					style={ {
						backgroundColor: `rgba(0,0,0,${ attributes.overlayOpacity / 100 })`,
					} }
				></div>
				{ attributes.image &&
					<Img
						className="hero__image"
						value={ attributes.image }
					/>
				}
				<div { ...innerBlockProps } />
			</Section>
		</>
	);
}
