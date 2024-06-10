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
 * Block name.
 */
export const name: string = 'quark/thumbnail-cards-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Thumbnail Card', 'qrk' ),
	description: __( 'Thumbnail Card.', 'qrk' ),
	category: 'widgets',
	keywords: [
		__( 'thumbnail', 'qrk' ),
		__( 'card', 'qrk' ),
	],
	attributes: {
		image: {
			type: 'object',
			default: {},
		},
		url: {
			type: 'object',
			default: {},
		},
		title: {
			type: 'string',
			default: '',
		},
		size: {
			type: 'string',
			enum: [ 'small', 'medium', 'large' ],
			default: 'medium',
		},
		orientation: {
			type: 'string',
			enum: [ 'portrait', 'landscape' ],
			default: 'portrait',
		},
	},
	parent: [ 'quark/thumbnail-cards' ],
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
				'thumbnail-cards__card',
				attributes.size && [ 'small', 'medium', 'large', ].includes( attributes.size ) ? `thumbnail-cards__card--size-${attributes.size}` : '',
				attributes.orientation && [ 'portrait', 'landscape', ].includes( attributes.orientation ) ? `thumbnail-cards__card--orient-${attributes.orientation}` : ''
			),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Thumbnail Card Options', 'qrk' ) }>
						<ImageControl
							label={ __( 'Image', 'qrk' ) }
							value={ attributes.image ? attributes.image.id : null }
							size="full"
							help={ __( 'Choose an image for this card.', 'qrk' ) }
							onChange={ ( image: object ) => setAttributes( { image } ) }
						/>
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
					<Img
						value={ attributes.image }
						className={ classnames(
							'thumbnail-cards__image',
							attributes.size && [ 'small', 'medium', 'large', ].includes( attributes.size ) ? `thumbnail-cards__image--size-${attributes.size}` : '',
							attributes.orientation && [ 'portrait', 'landscape', ].includes( attributes.orientation ) ? `thumbnail-cards__image--orient-${attributes.orientation}` : ''
						) }
					/>
					<RichText
						tagName="h3"
						className="thumbnail-cards__card-title h5 thumbnail-cards__card-title--align-bottom"
						placeholder={ __( 'Title here…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
