/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * External components.
 */
const { gumponents } = window;
const { ImageControl, LinkControl, Img } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/simple-cards-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Simple Cards Item', 'qrk' ),
	description: __( 'Individual card item for simple cards.', 'qrk' ),
	parent: [ 'quark/simple-cards' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'card', 'qrk' ) ],
	attributes: {
		title: {
			type: 'string',
		},
		image: {
			type: 'object',
			default: {},
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
	edit( {
		className,
		attributes,
		setAttributes,
	}: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blocksProps = useBlockProps( {
			className: classnames( className, 'simple-cards__card' ),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Simple Card Options', 'qrk' ) }>
						<ImageControl
							label={ __( 'Image', 'qrk' ) }
							value={ attributes.image ? attributes.image.id : null }
							size="medium"
							help={ __( 'Select an image for the card', 'qrk' ) }
							onChange={ ( image: object ) => setAttributes( { image } ) }
						/>
						<LinkControl
							label={ __( 'Select URL', 'qrk' ) }
							value={ attributes.url }
							help={ __( 'Enter an URL for this card', 'qrk' ) }
							onChange={ ( url: object ) => setAttributes( { url } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<article { ...blocksProps }>
					<figure className="simple-cards__image-wrap">
						<Img value={ attributes.image } />
					</figure>
					<RichText
						tagName="h3"
						className="simple-cards__title h5"
						placeholder={ __( 'Write title…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
				</article>
			</>
		);
	},
	save() {
		// Save inner block content.
		return null;
	},
};
