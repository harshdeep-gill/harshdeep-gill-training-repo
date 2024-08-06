/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import icons from '../../../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl } = gumponents.components;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'accordion__item', 'open' ),
		open: true,
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ className: 'accordion__content' },
		{
			allowedBlocks: [ 'core/paragraph' ],
			template: [
				[
					'core/paragraph',
					{
						placeholder: __( 'Write Content…', 'qrk' ),
						templateLock: 'all',
						lock: { move: true, remove: true },
					},
				],
			],

			// @ts-ignore
			orientation: 'horizontal',
		}
	);

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Accordion Options', 'qrk' ) }>
					<ImageControl
						label={ __( 'Accordion Item Image', 'qrk' ) }
						value={ attributes.image ? attributes.image.id : null }
						size="medium"
						help={ __( 'Choose an image for this accordian item.', 'qrk' ) }
						onChange={ ( image: object ) => setAttributes( { image } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div className="accordion__handle">
					<button className="accordion__handle-btn">
						<RichText
							tagName="h3"
							className="h5 accordion__handle-btn-text body-text-large"
							placeholder={ __( 'Title here…', 'qrk' ) }
							value={ attributes.title }
							onChange={ ( title ) => setAttributes( { title } ) }
							allowedFormats={ [] }
						/>
						<span className="accordion__handle-icon">
							{ icons.chevronLeft }
						</span>
					</button>
				</div>
				<div { ...innerBlockProps } />
			</div>
		</>
	);
}
