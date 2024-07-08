/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
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
} from '@wordpress/block-editor';

/**
 * Styles.
 */
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
import * as heroContentLeft from './children/hero-content-left';
import * as heroContentRight from './children/hero-content-right';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'hero',
			attributes.isImmersive ? 'hero--immersive' : ''
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
}
