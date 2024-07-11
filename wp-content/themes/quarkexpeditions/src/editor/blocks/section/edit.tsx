/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	RichText,
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
 * Styles.
 */
import './editor.scss';

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
	LinkControl,
} = gumponents.components;

// Background colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
	{ name: __( 'Gray', 'qrk' ), color: '#F5F7FB', slug: 'gray' },
];

/**
 * Styles.
 */
import '../../../front-end/components/section/style.scss';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) {
	// Block props.
	const blockProps = useBlockProps( { className: 'section' } );
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
					<SelectControl
						label={ __( 'Title Alignment', 'qrk' ) }
						value={ attributes.titleAlignment }
						options={ [
							{ label: __( 'Left', 'qrk' ), value: 'left' },
							{ label: __( 'Center', 'qrk' ), value: 'center' },
						] }
						onChange={ ( titleAlignment ) => setAttributes( { titleAlignment } ) }
					/>
					<ToggleControl
						label={ __( 'Has heading link', 'qrk' ) }
						checked={ attributes.hasHeadingLink }
						onChange={ () => {
							// Get the new state.
							const newState = ! attributes.hasHeadingLink;

							// set the new state.
							setAttributes( { hasHeadingLink: newState } );
						} }
						help={ __( 'Does this section have heading link?', 'qrk' ) }
					/>
					{ attributes.hasHeadingLink &&
						<LinkControl
							label={ __( 'Select URL', 'qrk' ) }
							value={ attributes.headingLink }
							help={ __( 'Enter an URL for this Info item', 'qrk' ) }
							onChange={ ( headingLink: object ) => setAttributes( { headingLink } ) }
						/>
					}
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
				<div className="section__heading">
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
					{ attributes.hasHeadingLink &&
						<span className={ `section__heading-link` }>
							{ attributes.headingLink.text }
						</span>
					}
				</div>
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
				<div { ...blockProps }>
					<div { ...innerBlocksProps } />
				</div>
				{ attributes.hasCta && (
					<div className={
						`section__cta-button ${ 'black' === attributes.backgroundColor && attributes.hasBackground ? 'color-context--dark' : '' }`
					}>
						<LinkButton
							className={ classnames( 'btn', 'btn--color-black' ) }
							placeholder={ __( 'Enter CTA Text' ) }
							value={ attributes.ctaButton }
							onChange={ ( ctaButton: Object ) => setAttributes( { ctaButton } ) }
						/>
					</div>
				) }
			</Section>
		</>
	);
}
