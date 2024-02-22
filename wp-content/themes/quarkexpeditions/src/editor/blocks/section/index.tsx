/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useInnerBlocksProps,
	InspectorControls,
	RichText,
	InnerBlocks,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	SelectControl,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const {
	LinkButton,
	ColorPaletteControl,
} = gumponents.components;

/**
 * Styles.
 */
import '../../../front-end/components/section/style.scss';

/**
 * Block name.
 */
export const name: string = 'quark/section';

// Background colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
	{ name: __( 'Gray', 'qrk' ), color: '#F5F7FB', slug: 'gray' },
];

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Section', 'qrk' ),
	description: __( 'Section block.', 'qrk' ),
	category: 'layout',
	keywords: [ __( 'section', 'qrk' ) ],
	attributes: {
		anchor: {
			type: 'string',
		},
		hasTitle: {
			type: 'boolean',
			default: true,
		},
		title: {
			type: 'string',
		},
		titleAlignment: {
			type: 'string',
			default: 'center',
		},
		headingLevel: {
			type: 'string',
			default: '3',
		},
		hasDescription: {
			type: 'boolean',
			default: false,
		},
		description: {
			type: 'string',
		},
		hasBorder: {
			type: 'boolean',
			default: true,
		},
		hasBackground: {
			type: 'boolean',
			default: false,
		},
		backgroundColor: {
			type: 'string',
			default: 'gray',
			enum: [ 'black', 'gray' ],
		},
		hasPadding: {
			type: 'boolean',
			default: false,
		},
		hasCta: {
			type: 'boolean',
			default: false,
		},
		ctaButton: {
			type: 'object',
		},
		isNarrow: {
			type: 'boolean',
			default: false,
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlocksProps = useInnerBlocksProps( { className: 'section__content' } );

		// Return block.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Section Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Has Title', 'qrk' ) }
							checked={ attributes.hasTitle }
							onChange={ () => setAttributes( { hasTitle: ! attributes.hasTitle } ) }
							help={ __( 'Does this section have a title?', 'qrk' ) }
						/>
						{ attributes.hasTitle &&
							<>
								<SelectControl
									label="Heading Level"
									value={ attributes.headingLevel }
									options={ [
										{ label: 'H1', value: '1' },
										{ label: 'H2', value: '2' },
										{ label: 'H3', value: '3' },
									] }
									onChange={ ( headingLevel ) => setAttributes( { headingLevel } ) }
								/>
								<SelectControl
									label={ __( 'Text Alignment', 'qrk' ) }
									help={ __( 'Select the text alignment for the title.', 'qrk' ) }
									value={ attributes.titleAlignment }
									options={ [
										{ label: 'Center', value: 'center' },
										{ label: 'Left', value: 'left' },
									] }
									onChange={ ( titleAlignment ) => setAttributes( { titleAlignment } ) }
								/>
							</>
						}
						<ToggleControl
							label={ __( 'Has Description', 'qrk' ) }
							checked={ attributes.hasDescription }
							onChange={ () =>
								setAttributes( { hasDescription: ! attributes.hasDescription } )
							}
							help={ __( 'Does this section have an description?', 'qrk' ) }
						/>
						<ToggleControl
							label={ __( 'Has Background', 'qrk' ) }
							checked={ attributes.hasBackground }
							onChange={ () => setAttributes( {
								hasBackground: ! attributes.hasBackground,
								hasPadding: ! attributes.hasBackground,
							} ) }
							help={ __( 'Does this section have a background colour?', 'qrk' ) }
						/>
						{ attributes.hasBackground &&
							<ColorPaletteControl
								label={ __( 'Background Color', 'qrk' ) }
								help={ __( 'Select the background color.', 'qrk' ) }
								value={ colors.find( ( color ) => color.slug === attributes.backgroundColor )?.color }
								colors={ colors.filter( ( color ) => [ 'black', 'gray' ].includes( color.slug ) ) }
								onChange={ ( backgroundColor: {
									color: string;
									slug: string;
								} ): void => {
									// Set the background color attribute.
									if ( backgroundColor.slug && [ 'black', 'gray' ].includes( backgroundColor.slug ) ) {
										setAttributes( { backgroundColor: backgroundColor.slug } );
									}
								} }
							/>
						}
						<ToggleControl
							label={ __( 'Is Narrow', 'qrk' ) }
							checked={ attributes.isNarrow }
							onChange={ () => setAttributes( {
								isNarrow: ! attributes.isNarrow,
							} ) }
							help={ __( 'Does this section have narrow width?', 'qrk' ) }
						/>
						<ToggleControl
							label={ __( 'Has Padding', 'qrk' ) }
							checked={ attributes.hasPadding }
							onChange={ () => setAttributes( { hasPadding: ! attributes.hasPadding } ) }
							help={ __( 'Does this section have a padding?', 'qrk' ) }
						/>
						<ToggleControl
							label={ __( 'Has CTA', 'qrk' ) }
							checked={ attributes.hasCta }
							onChange={ () => setAttributes( { hasCta: ! attributes.hasCta } ) }
							help={ __( 'Does this section have a CTA button?', 'qrk' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Section
					className={ classnames( className, 'section' ) }
					background={ attributes.hasBackground }
					backgroundColor={ attributes.backgroundColor }
					padding={ attributes.hasPadding }
					seamless={ attributes.hasBackground }
					narrow={ attributes.isNarrow }
				>
					{ attributes.hasTitle && (
						<RichText
							tagName="h2"
							className={ `section__title section__title--${ attributes.titleAlignment } h${ attributes.headingLevel }` }
							placeholder={ __( 'Write title…', 'qrk' ) }
							value={ attributes.title }
							onChange={ ( title ) => setAttributes( { title } ) }
							allowedFormats={ [] }
						/>
					) }
					{ attributes.hasDescription && (
						<RichText
							tagName="p"
							className="section__description"
							placeholder={ __( 'Write description…', 'qrk' ) }
							value={ attributes.description }
							onChange={ ( description ) => setAttributes( { description } ) }
							allowedFormats={ [] }
						/>
					) }
					<div { ...innerBlocksProps } />
					{ attributes.hasCta && (
						<div className="section__cta-button">
							<LinkButton
								className={ classnames( 'btn' ) }
								placeholder={ __( 'CTA Button' ) }
								value={ attributes.ctaButton }
								onChange={ ( ctaButton: Object ) => setAttributes( { ctaButton } ) }
							/>
						</div>
					) }
				</Section>
			</>
		);
	},
	save() {
		// Return inner block content.
		return <InnerBlocks.Content />;
	},
};
