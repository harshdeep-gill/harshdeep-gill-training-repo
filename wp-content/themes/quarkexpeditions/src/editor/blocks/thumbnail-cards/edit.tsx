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
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Children blocks
 */
import * as card from './children/card';

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
		className: classnames(
			className,
			'thumbnail-cards',
		),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ className: 'thumbnail-cards__slides' },
		{
			allowedBlocks: [ card.name ],
			template: [ [ card.name ], [ card.name ], [ card.name ] ],
			renderAppender: InnerBlocks.ButtonBlockAppender,

			// @ts-ignore
			orientation: 'horizontal',
		}
	);

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Thumbnail Cards Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Carousel', 'qrk' ) }
						checked={ attributes.isCarousel }
						help={ __( 'Is this a carousel?', 'qrk' ) }
						onChange={ ( isCarousel: boolean ) => setAttributes( { isCarousel } ) }
					/>
					<ToggleControl
						label={ __( 'Full Width', 'qrk' ) }
						checked={ attributes.isFullWidth }
						help={ __( 'Does this span the full width of the screen?', 'qrk' ) }
						onChange={ ( isFullWidth: boolean ) => setAttributes( { isFullWidth } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps } fullWidth={ true } seamless={ true } >
				<div className="thumbnail-cards__carousel">
					<div { ...innerBlockProps } />
				</div>
				<div className="thumbnail-cards__nav" data-is-carousel={ attributes.isCarousel ? '1' : '' }>
					<div className="thumbnail-cards__arrow-button thumbnail-cards__arrow-button--left">
						{ icons.chevronLeft }
					</div>
					<div className="thumbnail-cards__arrow-button thumbnail-cards__arrow-button--right">
						{ icons.chevronLeft }
					</div>
				</div>
			</Section>
		</>
	);
}
