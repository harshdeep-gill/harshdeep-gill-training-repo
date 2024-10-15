/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { store as editorStore } from '@wordpress/editor';
import apiFetch from '@wordpress/api-fetch';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import {
	useSelect,
	useDispatch,
} from '@wordpress/data';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';
import { getImageDetails } from '../utils';

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
 * Styles.
 */
import './editor.scss';

/**
 * Child Blocks.
 */
import * as searchHeroLeft from './children/content-left';
import * as searchHeroRight from './children/content-right';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
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
			if ( ! attributes.syncPostThumbnail || ! postThumbnailId || postThumbnailId === attributes.backgroundImage?.id ) {
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
				setAttributes( { backgroundImage: getImageDetails( postThumbnail, 'large' ) } );
			} catch ( error ) {
				setAttributes( { backgroundImage: null } );
			}
		}

		// Sync post thumbnail.
		syncPostThumbnail();
	}, [ postThumbnailId, attributes.syncPostThumbnail, attributes.backgroundImage?.id, setAttributes ] );

	/**
	 * Handle image change.
	 *
	 * @param {Object} backgroundImage Background image.
	 */
	const handleImageChange = ( backgroundImage: Record<string, any> ) => {
		// Set attributes.
		setAttributes( { backgroundImage } );

		// If post thumbnail is synced, update post thumbnail.
		if ( attributes.syncPostThumbnail && backgroundImage?.id ) {
			editPost( { featured_media: backgroundImage.id } );
		}
	};

	// Set Content overlap on none immersive mode.
	if ( 'none' === attributes.immersive || 'top' === attributes.immersive ) {
		setAttributes( { contentOverlap: false } );
	}

	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'search-hero',
			attributes.contentOverlap ? '' : 'search-hero--content-no-overlap',
			[ 'top', 'bottom', 'all', 'no' ].find(
				( value: string ) => value === attributes.immersive
			) ? `search-hero--immersive-${ attributes.immersive }` : ''
		),
	} );

	// Set inner block props - Only Allow Hero Card Slider block.
	const innerBlockProps = useInnerBlocksProps(
		{
			className: classnames( className, 'search-hero__content' ),
		},
		{
			allowedBlocks: [ searchHeroLeft.name, searchHeroRight.name ],
			template: [ [ searchHeroLeft.name ], [ searchHeroRight.name ] ],
		}
	);

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Hero Options', 'qrk' ) }>
					<ImageControl
						label={ __( 'Image', 'qrk' ) }
						value={ attributes.backgroundImage ? attributes.backgroundImage.id : null }
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
					{ ( attributes.immersive === 'bottom' || attributes.immersive === 'all' ) &&
						<ToggleControl
							label={ __( 'Overlap Following Content', 'qrk' ) }
							checked={ attributes.contentOverlap }
							onChange={ ( contentOverlap ) => setAttributes( { contentOverlap } ) }
							help={ __( 'Should the hero overlap the following content?', 'qrk' ) }
						/>
					}
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
					className={ 'search-hero__overlay' }
					style={ {
						'--search-hero-overlay-background-opacity': attributes.overlayOpacity / 100,
					} as React.CSSProperties }
				/>
				<div className="search-hero__wrap">
					<Img
						className="search-hero__image"
						value={ attributes.backgroundImage }
					/>
					<div { ...innerBlockProps } />
				</div>
			</Section>
		</>
	);
}
