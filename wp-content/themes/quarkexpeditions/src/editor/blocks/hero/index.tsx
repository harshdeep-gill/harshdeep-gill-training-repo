/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
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
 * Children blocks
 */
import * as heroContentLeft from './hero-content-left';
import * as heroContentRight from './hero-content-right';

/**
 * Register child block.
 */
registerBlockType( heroContentLeft.name, heroContentLeft.settings );
registerBlockType( heroContentRight.name, heroContentRight.settings );

/**
 * Block name.
 */
export const name: string = 'quark/hero';

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
		isImmersive: {
			type: 'boolean',
			default: false,
		},
		textAlign: {
			type: 'string',
			default: '',
		},
		darkMode: {
			type: 'boolean',
			default: false,
		},
		overlayOpacity: {
			type: 'number',
			default: 0,
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
				attributes.darkMode ? 'color-context--dark' : ''
			),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps(
			{ className: 'hero__content' },
			{
				allowedBlocks: [ heroContentLeft.name, heroContentRight.name ],
				template: [ [ heroContentLeft.name ], [ heroContentRight.name ] ],
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
						<RangeControl
							label={ __( 'Overlay opacity in percent', 'qrk' ) }
							value={ attributes.overlayOpacity }
							onChange={ ( overlayOpacity ) => setAttributes( { overlayOpacity } ) }
							min={ 0 }
							max={ 100 }
						/>
						<ToggleControl
							label={ __( 'Immersive Mode', 'qrk' ) }
							checked={ attributes.isImmersive }
							help={ __( 'Is this hero immersive?', 'qrk' ) }
							onChange={ ( isImmersive: boolean ) => setAttributes( { isImmersive } ) }
						/>
						<ToggleControl
							label={ __( 'Dark Mode', 'qrk' ) }
							checked={ attributes.darkMode }
							help={ __( 'Is this hero in dark mode?', 'qrk' ) }
							onChange={ ( darkMode: boolean ) => setAttributes( { darkMode } ) }
						/>
						<SelectControl
							label={ __( 'Text Alignment', 'qrk' ) }
							help={ __( 'Select the text alignment', 'qrk' ) }
							value={ attributes.textAlign }
							options={ [
								{ label: __( 'Left', 'qrk' ), value: 'left' },
								{ label: __( 'Center', 'qrk' ), value: 'center' },
							] }
							onChange={ ( textAlign: string ) => setAttributes( { textAlign } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Section { ...blockProps } fullWidth={ true } seamless={ true } >
					<div
						className="hero__overlay"
						style={ {
							backgroundColor: `rgba(0,0,0,${ attributes.overlayOpacity / 100 })`,
						} }
					></div>
					<div className="hero__wrap">
						{ attributes.image &&
							<Img
								className="hero__image"
								value={ attributes.image }
							/>
						}
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
