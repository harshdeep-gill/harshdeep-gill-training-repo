/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';
import icons from '../icons';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * Block name.
 */
export const name: string = 'quark/accordion-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Accordion Item', 'qrk' ),
	description: __( 'Accordion Item.', 'qrk' ),
	parent: [ 'quark/accordion' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'item', 'qrk' ) ],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
		isOpen: {
			type: 'boolean',
			default: false,
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blocksProps = useBlockProps( {
			className: classnames( className, 'accordion__item' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {}, {
			template: [
				[ 'core/paragraph', { placeholder: __( 'Content…', 'qrk' ) } ],
			],

			// @ts-ignore
			orientation: 'vertical',
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Accordion Item Options.', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Is Open?', 'qrk' ) }
							checked={ attributes.isOpen }
							onChange={ ( isOpen: boolean ) => setAttributes( { isOpen } ) }
							help={ __( 'Should this accordion item be open by default.', 'qrk' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blocksProps }>
					<div className="accordion__handle">
						<button className="accordion__handle-btn">
							<RichText
								tagName="h3"
								className={ 'h5 accordion__handle-btn-text body-text-large' }
								placeholder={ __( 'Write title…', 'qrk' ) }
								value={ attributes.title }
								onChange={ ( title: string ) => setAttributes( { title } ) }
								allowedFormats={ [] }
							/>
							<span className="accordion__handle-icon">
								{ icons.chevronLeft }
							</span>
						</button>
					</div>
					<div className="accordion__content">
						<div className="accordion__content-inner">
							<p { ...innerBlockProps } />
						</div>
					</div>
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
