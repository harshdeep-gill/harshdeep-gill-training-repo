/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	RangeControl,
	SelectControl,
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
import * as breadCrumbs from '../breadcrumbs';

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
import * as heroContent from './children/hero-content';

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
			[ 'top', 'bottom', 'all', 'no' ].find(
				( value: string ) => value === attributes.immersive
			) ? `hero--immersive-${ attributes.immersive }` : ''
		),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ className: 'hero__wrap' },
		{
			allowedBlocks: [ heroContent.name, breadCrumbs.name ],
			template: [ [ breadCrumbs.name ], [ heroContent.name ] ],
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
					<SelectControl
						label={ __( 'Immersive mode', 'qrk' ) }
						help={ __( 'Select the immersive mode', 'qrk' ) }
						value={ attributes.immersive }
						options={ [
							{ label: __( 'None', 'qrk' ), value: 'none' },
							{ label: __( 'Top', 'qrk' ), value: 'top' },
							{ label: __( 'Bottom', 'qrk' ), value: 'bottom' },
							{ label: __( 'All', 'qrk' ), value: 'all' },
						] }
						onChange={ ( immersive: string ) => setAttributes( { immersive } ) }
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
				{ attributes.image &&
					<Img
						className="hero__image"
						value={ attributes.image }
					/>
				}
				<div { ...innerBlockProps } />
			</Section>
		</>
	);
}
