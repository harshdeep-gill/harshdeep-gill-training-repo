/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
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
import './editor.scss';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as item from './children/item';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'review-cards' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'review-cards__slider' ),
	}, {
		allowedBlocks: [ item.name ],
		template: [ [ item.name ] ],
		renderAppender: InnerBlocks.ButtonBlockAppender,

		// @ts-ignore
		orientation: 'horizontal',
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Review Cards Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Is Carousel?', 'qrk' ) }
						checked={ attributes.isCarousel }
						help={ __( 'Show carousel navigation arrows in desktop', 'qrk' ) }
						onChange={ ( isCarousel: boolean ) => setAttributes( { isCarousel } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps }>
				<div { ...innerBlockProps } />
			</Section>
		</>
	);
}
