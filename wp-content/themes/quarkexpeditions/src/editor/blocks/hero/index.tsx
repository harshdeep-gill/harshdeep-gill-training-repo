/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	PanelBody,
	ToggleControl,
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
 * Child block.
 */
import * as item from './item';

/**
 * Register child block.
 */
registerBlockType( item.name, item.settings );

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
		isImmersive: {
			type: 'boolean',
			default: false,
		},
		showForm: {
			type: 'boolean',
			default: true,
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
			className: classnames(
				className,
				'hero',
				attributes.isImmersive ? 'hero--immersive' : '',
				attributes.showForm ? '' : 'hero--big'
			),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{
				className: `${
					attributes.showForm
						? 'hero__form '
						: ''
				} color-context--dark`,
			},
			{
				allowedBlocks: [ 'quark/inquiry-form', item.name ],
				template: [ [ attributes.showForm ? 'quark/inquiry-form' : item.name ] ],
				templateLock: 'all',
			}
		);

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
						<ToggleControl
							label={ __( 'Immersive Mode', 'qrk' ) }
							checked={ attributes.isImmersive }
							help={ __( 'Is this hero immersive?', 'qrk' ) }
							onChange={ ( isImmersive: boolean ) => setAttributes( { isImmersive } ) }
						/>
						<ToggleControl
							label={ __( 'Has Form', 'qrk' ) }
							checked={ attributes.showForm }
							help={ __( 'Does the hero have a form', 'qrk' ) }
							onChange={ ( showForm: boolean ) => setAttributes( { showForm } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Section { ...blockProps } fullWidth={ true } seamless={ true } >
					<div
						className={
							`hero__wrap ${
								attributes.showForm
									? ''
									: 'hero__wrap--column'
							}`
						}
					>
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
