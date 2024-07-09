/**
 * WordPress dependencies
 */
import { InspectorControls, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * Child blocks.
 */
import * as column from './children/column';
import * as info from './children/content-info';

/**
 * External components.
 */
const { ImageControl, Img } = gumponents.components;

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'media-content-card',
			attributes.isCompact ? 'media-content-card--compact' : '',
		),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'media-content-card__content' ),
	},
	{
		allowedBlocks: [ column.name ],
		template: [
			[
				column.name,
				{},
				[
					[ 'core/paragraph', { placeholder: __( 'Write content…', 'qrk' ) } ],
				],
			],
			[
				column.name,
				{},
				[
					[ info.name ],
					[ info.name ],
				],
			],
		],

		// @ts-ignore.
		orientation: 'horizontal',
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Media Content Card Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Compact Layout', 'qrk' ) }
						checked={ attributes.isCompact }
						help={ __( 'Change to a single column layout.', 'qrk' ) }
						onChange={ ( isCompact: boolean ) => setAttributes( { isCompact } ) }
					/>
					<ImageControl
						label={ __( 'Image', 'qrk' ) }
						value={ attributes.image ? attributes.image.id : null }
						size="full"
						help={ __( 'Choose an image.', 'qrk' ) }
						onChange={ ( image: object ) => setAttributes( { image } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps } >
				<figure className="media-content-card__image">
					<Img
						value={ attributes.image }
					/>
				</figure>
				<div { ...innerBlockProps } />
			</div>
		</>
	);
}
