/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * External components.
 */
const { gumponents } = window;
const { ImageControl, Img, LinkControl } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/header-menu-item-featured-section';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Header Menu Item Featured Section', 'qrk' ),
	description: __( 'Featured Section within Content Column', 'qrk' ),
	parent: [ 'quark/header-menu-item-content-column' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [
		__( 'content', 'qrk' ),
		__( 'featured', 'qrk' ),
		__( 'section', 'qrk' ),
	],
	attributes: {
		image: {
			type: 'object',
			default: {},
		},
		title: {
			type: 'string',
		},
		subtitle: {
			type: 'string',
		},
		ctaText: {
			type: 'string',
		},
		url: {
			type: 'object',
			default: null,
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'header__nav-item-featured', 'color-context--dark' ),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Featured Section Options', 'qrk' ) }>
						<ImageControl
							label={ __( 'Featured Image', 'qrk' ) }
							value={ attributes.image ? attributes.image.id : null }
							size="large"
							help={ __( 'Select the featured image for this section', 'qrk' ) }
							onChange={ ( image: object ) => setAttributes( { image } ) }
						/>
						<LinkControl
							label={ __( 'Select URL for this section', 'qrk' ) }
							value={ attributes.url }
							help={ __( 'Enter an URL for this section', 'qrk' ) }
							onChange={ ( url: object ) => setAttributes( { url } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<Img value={ attributes.image } className="header__nav-item-featured-image" />
					<div className="header__nav-item-featured-content">
						<RichText
							tagName="h2"
							className="header__nav-item-featured-title h4"
							placeholder={ __( 'Write title…', 'qrk' ) }
							value={ attributes.title }
							onChange={ ( title: string ) => setAttributes( { title } ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="div"
							className="header__nav-item-featured-subtitle"
							placeholder={ __( 'Write subtitle…', 'qrk' ) }
							value={ attributes.subtitle }
							onChange={ ( subtitle: string ) => setAttributes( { subtitle } ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="a"
							className="btn btn--size-big"
							placeholder={ __( 'Write CTA text…', 'qrk' ) }
							value={ attributes.ctaText }
							onChange={ ( ctaText: string ) => setAttributes( { ctaText } ) }
							allowedFormats={ [] }
						/>
					</div>
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
