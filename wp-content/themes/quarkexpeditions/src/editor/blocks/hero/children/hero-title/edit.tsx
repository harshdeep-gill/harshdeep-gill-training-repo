/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useEntityProp } from '@wordpress/core-data';
import { useEffect } from '@wordpress/element';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const {
	ColorPaletteControl,
} = gumponents.components;

// Text colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
	{ name: __( 'White', 'qrk' ), color: '#ffffff', slug: 'white' },
];

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 * @param {Object}   props.context       Block context.
 */
export default function Edit( { className, attributes, setAttributes, context }: BlockEditAttributes ): JSX.Element {
	// Get post ID and post type from context.
	const { postId, postType } = context;

	// Get value and setter for the post title.
	const [ postTitle, setPostTitle ] = useEntityProp( 'postType', postType, 'title', postId );

	// Sync post title whenever the syncPostTitle attribute is enabled and the post title changes.
	useEffect( () => {
		// Sync post title.
		if ( attributes.syncPostTitle && postTitle !== attributes.title ) {
			setAttributes( { title: postTitle } );
		}
	}, [ postTitle, attributes.syncPostTitle, attributes.title, setAttributes ] );

	/**
	 * Handle title change.
	 *
	 * @param {string} title Title.
	 */
	const handleTitleChange = ( title: string ) => {
		// Set attributes.
		setAttributes( { title } );

		// If post title is synced, update post title.
		if ( attributes.syncPostTitle ) {
			setPostTitle( title );
		}
	};

	// Set the block properties.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'hero__title',
			'white' === attributes.textColor ? 'color-context--dark' : '',
			attributes.usePromoFont ? 'font-family--promo' : '',
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Title Options', 'qrk' ) }>
					<ColorPaletteControl
						label={ __( 'Title Color', 'qrk' ) }
						help={ __( 'Select the text color for the Title', 'qrk' ) }
						value={ colors.find( ( color ) => color.slug === attributes.textColor )?.color }
						colors={ colors.filter( ( color ) => [ 'white', 'black' ].includes( color.slug ) ) }
						onChange={ ( textColor: {
							color: string;
							slug: string;
						} ): void => {
							// Set the background color attribute.
							if ( textColor.slug && [ 'white', 'black' ].includes( textColor.slug ) ) {
								setAttributes( { textColor: textColor.slug } );
							}
						} }
					/>
					<ToggleControl
						label={ __( 'Sync with Post Title', 'qrk' ) }
						checked={ attributes.syncPostTitle }
						onChange={ ( syncPostTitle ) => setAttributes( { syncPostTitle } ) }
						help={ __( 'Should the hero title be synced with the post title?', 'qrk' ) }
					/>
					<ToggleControl
						label={ __( 'Use Promo Font', 'qrk' ) }
						checked={ attributes.usePromoFont }
						onChange={ ( usePromoFont ) => setAttributes( { usePromoFont } ) }
						help={ __( 'Should this text be in the Promo Font?', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<RichText
				{ ...blockProps }
				tagName="h1"
				placeholder={ __( 'Write the Title…', 'qrk' ) }
				value={ attributes.title }
				onChange={ handleTitleChange }
				allowedFormats={ [] }
			/>
		</>
	);
}
