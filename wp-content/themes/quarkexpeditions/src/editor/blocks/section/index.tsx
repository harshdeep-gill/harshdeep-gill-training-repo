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
const { LinkButton } = gumponents.components;

/**
 * Styles.
 */
import '../../../front-end/components/section/style.scss';

/**
 * Block data.
 */
export const name: string = 'quark/section';
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Section', 'quark' ),
	description: __( 'Section block.', 'quark' ),
	category: 'layout',
	keywords: [ __( 'section', 'quark' ) ],
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
		headingLevel: {
			type: 'string',
			default: 'h2',
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
		const innerBlocksProps = useInnerBlocksProps();

		// Prepare heading class.
		const largeHeadingClassNames = [ 'h1', 'h2' ];
		const headingClassName = largeHeadingClassNames.includes( attributes.headingLevel ) ? 'h2' : attributes.headingLevel;

		// Return block.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Section Options', 'quark' ) }>
						<ToggleControl
							label={ __( 'Has Title', 'quark' ) }
							checked={ attributes.hasTitle }
							onChange={ () => setAttributes( { hasTitle: ! attributes.hasTitle } ) }
							help={ __( 'Does this section have a title?', 'quark' ) }
						/>
						{ attributes.hasTitle && (
							<SelectControl
								label="Heading Level"
								value={ attributes.headingLevel }
								options={ [
									{ label: 'H1', value: 'h1' },
									{ label: 'H2', value: 'h2' },
								] }
								onChange={ ( headingLevel ) => setAttributes( { headingLevel } ) }
							/>
						) }
						<ToggleControl
							label={ __( 'Has Description', 'quark' ) }
							checked={ attributes.hasDescription }
							onChange={ () =>
								setAttributes( { hasDescription: ! attributes.hasDescription } )
							}
							help={ __( 'Does this section have an description?', 'quark' ) }
						/>
						<ToggleControl
							label={ __( 'Has Border', 'quark' ) }
							checked={ attributes.hasBorder }
							onChange={ () => setAttributes( { hasBorder: ! attributes.hasBorder } ) }
							help={ __( 'Does this section have a border?', 'quark' ) }
						/>
						<ToggleControl
							label={ __( 'Has Background', 'quark' ) }
							checked={ attributes.hasBackground }
							onChange={ () => setAttributes( { hasBackground: ! attributes.hasBackground } ) }
							help={ __( 'Does this section have a background colour?', 'quark' ) }
						/>
						{ attributes.hasBackground &&
							<ToggleControl
								label={ __( 'Has Padding', 'quark' ) }
								checked={ attributes.hasPadding }
								onChange={ () => setAttributes( { hasPadding: ! attributes.hasPadding } ) }
								help={ __( 'Does this section have a padding?', 'quark' ) }
							/>
						}
						<ToggleControl
							label={ __( 'Has CTA', 'quark' ) }
							checked={ attributes.hasCta }
							onChange={ () => setAttributes( { hasCta: ! attributes.hasCta } ) }
							help={ __( 'Does this section have a CTA button?', 'quark' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Section
					className={ classnames( className, 'section', { 'section--no-border': ! attributes.hasBorder } ) }
					background={ attributes.hasBackground }
					padding={ attributes.hasPadding }
					seamless={ attributes.hasBackground }
				>
					{ attributes.hasTitle && (
						<RichText
							tagName="h2"
							className={ 'section__title ' + headingClassName }
							placeholder={ __( 'Write title…', 'quark' ) }
							value={ attributes.title }
							onChange={ ( title ) => setAttributes( { title } ) }
							allowedFormats={ [] }
						/>
					) }
					{ attributes.hasDescription && (
						<div className="section__description">
							<RichText
								tagName="p"
								placeholder={ __( 'Write description…', 'quark' ) }
								value={ attributes.description }
								onChange={ ( description ) => setAttributes( { description } ) }
								allowedFormats={ [] }
							/>
						</div>
					) }
					<div className="section__content">
						<div { ...innerBlocksProps } />
						{ attributes.hasCta && (
							<div className="section__cta-button">
								<LinkButton
									className="btn btn--teal-outline"
									placeholder={ __( 'CTA Button' ) }
									value={ attributes.ctaButton }
									onChange={ ( ctaButton: Object ) => setAttributes( { ctaButton } ) }
								/>
							</div>
						) }
					</div>
				</Section>
			</>
		);
	},
	save() {
		// Return inner block content.
		return <InnerBlocks.Content />;
	},
};
