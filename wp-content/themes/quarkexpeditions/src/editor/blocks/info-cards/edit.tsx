/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	InnerBlocks,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import './editor.scss';

/**
 * Child blocks.
 */
import * as infoCard from './children/card';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set block properties.
	const blockProps = useBlockProps( {
		className: classnames( className, 'info-cards', 'quark-info-cards' ),
		title: attributes.type,
	} );

	// Set inner blocks properties.
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'info-cards__slides' ),
	}, {
		allowedBlocks: [ infoCard.name ],
		template: [
			[ infoCard.name ],
			[ infoCard.name ],
		],
		renderAppender: InnerBlocks.ButtonBlockAppender,

		// @ts-ignore
		orientation: 'horizontal',
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Info Cards Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Layout', 'qrk' ) }
						value={ attributes.layout }
						options={ [
							{ label: __( 'Carousel', 'qrk' ), value: 'carousel' },
							{ label: __( 'Grid', 'qrk' ), value: 'grid' },
							{ label: __( 'Collage', 'qrk' ), value: 'collage' },
						] }
						onChange={ ( layout: string ) => setAttributes( { layout } ) }
					/>
					<ToggleControl
						label={ __( 'Is a Carousel on Mobile?', 'qrk' ) }
						checked={ attributes.mobileCarousel }
						onChange={ ( mobileCarousel: boolean ) => setAttributes( { mobileCarousel } ) }
						help={ __( 'Enable this option to show a carousel on mobile.', 'qrk' ) }
					/>
					<ToggleControl
						label={ __( 'Horizontal Overflow?', 'qrk' ) }
						checked={ attributes.carouselOverflow }
						onChange={ ( carouselOverflow: boolean ) => setAttributes( { carouselOverflow } ) }
						help={ __( 'Enable this option to allow the carousel to overflow horizontally.', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps } data-layout={ attributes.layout } >
				<div className="info-cards__carousel">
					<div className="info-cards__slider">
						<div { ...innerBlockProps } />
					</div>
				</div>
			</div>
		</>
	);
}
