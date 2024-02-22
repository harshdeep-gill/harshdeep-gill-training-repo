/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
} from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

/**
 * Styles.
 */
import '../../../front-end/components/video-icons-card/style.scss';
import './editor.scss';

/**
 * Internal dependencies.
 */
import icons from '../icons';
import * as iconColumns from '../icon-columns';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/video-icons-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Video Icons card', 'qrk' ),
	description: __( 'Display a video with an Icons card.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'video', 'qrk' ),
		__( 'card', 'qrk' ),
	],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
		variant: {
			type: 'string',
			default: '',
		},
		url: {
			type: 'string',
			default: '',
		},
		image: {
			type: 'object',
			default: null,
		},
	},
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
			className: classnames( className, 'video-icons-card', ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: 'video-icons-card__icons',
		}, {
			allowedBlocks: [ iconColumns.name ],
			template: [
				[ iconColumns.name, { variant: 'light' } ],
			],

			// @ts-ignore
			orientation: 'horizontal',
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Video Options', 'qrk' ) }>
						<TextControl
							label="Enter Wistia video URL"
							value={ attributes.url }
							onChange={ ( url ) => setAttributes( { url } ) }
						/>
						<ImageControl
							label="Choose thumbnail image"
							selectLabel="Choose thumbnail image"
							removeLabel="Remove this thumbnail image"
							size="full"
							value={ attributes.image }
							onChange={ ( image: any ) => setAttributes( { image } ) }
						/>
						<SelectControl
							label={ __( 'Variant', 'qrk' ) }
							help={ __( 'Select the variant.', 'qrk' ) }
							value={ attributes.variant }
							options={ [
								{ label: __( 'Select Variant…', 'qrk' ), value: '' },
								{ label: __( 'Dark', 'qrk' ), value: 'dark' },
								{ label: __( 'Light', 'qrk' ), value: 'light' },
							] }
							onChange={ ( variant: string ) => setAttributes( { variant } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps } >
					<div className={ `video-icons-card__container${ attributes.variant && 'dark' === attributes.variant ? ' color-context--dark' : '' }` }>
						<div className="video-icons-card__overlay">
							<RichText
								tagName="h2"
								className="video-icons-card__title"
								placeholder={ __( 'Write title…', 'qrk' ) }
								value={ attributes.title }
								onChange={ ( title: string ) => setAttributes( { title } ) }
								allowedFormats={ [] }
							/>
							<button type="button" className="btn btn--media video-icons-card__button">{ icons.play }</button>
							<div { ...innerBlockProps } />
						</div>
						<img src={ attributes.image?.src } alt={ attributes.image?.alt } className="video-icons-card__thumbnail" />
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
