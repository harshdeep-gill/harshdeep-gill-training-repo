/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	PanelBody,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
	RichText,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/hero/style.scss';
import './editor.scss';

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
	ImageControl,
	Img,
} = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'qrk/hero';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero', 'qrk' ),
	description: __( 'Display a hero block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'hero', 'qrk' ),
	],
	attributes: {
		image: {
			type: 'object',
		},
		title: {
			type: 'string',
		},
		subTitle: {
			type: 'string',
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
			className: classnames( className, 'hero' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { className: 'hero__form color-context--dark' }, {
			allowedBlocks: [ 'qrk/inquiry-form' ],
			template: [ [ 'qrk/inquiry-form' ] ],
			templateLock: 'all',
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Hero Options', 'qrk' ) }>
						<ImageControl
							label={ __( 'Image', 'qrk' ) }
							value={ attributes.image ? attributes.image.id : null }
							size="large"
							help={ __( 'Choose an image', 'qrk' ) }
							onChange={ ( image: object ) => setAttributes( { image } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Section { ...blockProps } fullWidth={ true } seamless={ true }>
					<div className="hero__wrap">
						{ attributes.image &&
							<Img
								className="hero__image"
								value={ attributes.image }
							/>
						}
						<div className="hero__content">
							<RichText
								tagName="h1"
								className="hero__title"
								placeholder={ __( 'Write title…', 'qrk' ) }
								value={ attributes.title }
								onChange={ ( title: string ) => setAttributes( { title } ) }
								allowedFormats={ [] }
							/>
							<div className="hero__sub-title">
								<RichText
									tagName="h5"
									className="h5"
									placeholder={ __( 'Write sub-title…', 'qrk' ) }
									value={ attributes.subTitle }
									onChange={ ( subTitle: string ) => setAttributes( { subTitle } ) }
									allowedFormats={ [] }
								/>
							</div>
						</div>
						<div { ...innerBlockProps } />
					</div>
				</Section>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
