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

/**
 * Styles.
 */
import '../../../front-end/components/section/style.scss';

/**
 * Block name.
 */
export const name: string = 'quark/section';

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
		headingLevel: {
			type: 'string',
			default: 'h3',
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

		// Prepare heading class.
		const largeHeadingClassNames = [ 'h1', 'h2', 'h3' ];
		const headingClassName = largeHeadingClassNames.includes( attributes.headingLevel ) ? 'h3' : attributes.headingLevel;

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
						{ attributes.hasTitle && (
							<SelectControl
								label="Heading Level"
								value={ attributes.headingLevel }
								options={ [
									{ label: 'H1', value: 'h1' },
									{ label: 'H2', value: 'h2' },
									{ label: 'H3', value: 'h3' },
								] }
								onChange={ ( headingLevel ) => setAttributes( { headingLevel } ) }
							/>
						) }
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
								hasPadding: true,
							} ) }
							help={ __( 'Does this section have a background colour?', 'qrk' ) }
						/>
						<ToggleControl
							label={ __( 'Is Narrow', 'qrk' ) }
							checked={ attributes.isNarrow }
							onChange={ () => setAttributes( {
								isNarrow: ! attributes.isNarrow,
							} ) }
							help={ __( 'Does this section have narrow width?', 'qrk' ) }
						/>
						{ attributes.hasBackground &&
							<ToggleControl
								label={ __( 'Has Padding', 'qrk' ) }
								checked={ attributes.hasPadding }
								onChange={ () => setAttributes( { hasPadding: ! attributes.hasPadding } ) }
								help={ __( 'Does this section have a padding?', 'qrk' ) }
							/>
						}
					</PanelBody>
				</InspectorControls>
				<Section
					className={ classnames( className, 'section' ) }
					background={ attributes.hasBackground }
					padding={ attributes.hasPadding }
					seamless={ attributes.hasBackground }
					narrow={ attributes.isNarrow }
				>
					{ attributes.hasTitle && (
						<RichText
							tagName="h2"
							className={ 'section__title ' + headingClassName }
							placeholder={ __( 'Write title…', 'qrk' ) }
							value={ attributes.title }
							onChange={ ( title ) => setAttributes( { title } ) }
							allowedFormats={ [] }
						/>
					) }
					{ attributes.hasDescription && (
						<div className="section__description">
							<RichText
								tagName="p"
								placeholder={ __( 'Write description…', 'qrk' ) }
								value={ attributes.description }
								onChange={ ( description ) => setAttributes( { description } ) }
								allowedFormats={ [] }
							/>
						</div>
					) }
					<div { ...innerBlocksProps } />
				</Section>
			</>
		);
	},
	save() {
		// Return inner block content.
		return <InnerBlocks.Content />;
	},
};
