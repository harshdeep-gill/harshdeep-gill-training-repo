/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import './../../../front-end/components/icon-info-grid/style.scss';
import './editor.scss';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Prepare block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'icon-info-grid' ),
	} );

	// Prepare inner block props.
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ 'quark/icon-info-grid-item' ],
		template: [ [ 'quark/icon-info-grid-item' ], [ 'quark/icon-info-grid-item' ], [ 'quark/icon-info-grid-item' ] ],
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Desktop Carousel', 'qrk' ) }>
					<ToggleControl
						label={ __( 'is Carousel', 'qrk' ) }
						checked={ attributes.desktopCarousel }
						help={ __( 'Should this require to be a carousel on desktop?', 'qrk' ) }
						onChange={ ( desktopCarousel: boolean ) => setAttributes( { desktopCarousel } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps } >
				<div { ...innerBlockProps } />
			</Section>
		</>
	);
}
