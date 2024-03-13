/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks,
	RichText,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * External dependencies.
 */
const { gumponents } = window;

/**
 * External components.
 */
const { LinkButton, LinkControl } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/product-cards-card-buttons';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card Buttons', 'qrk' ),
	description: __( 'Individual Card Buttons for product cards.', 'qrk' ),
	parent: [ 'quark/product-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'buttons', 'qrk' ) ],
	attributes: {
		hasCallCta: {
			type: 'boolean',
			default: false,
		},
		callCtaText: {
			type: 'string',
			default: '',
		},
		callCtaUrl: {
			type: 'object',
			default: {},
		},
		secondaryBtn: {
			text: 'object',
			default: {},
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( {
		className,
		attributes,
		setAttributes,
	}: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'product-cards__buttons product-cards__buttons--cols-2' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {},
			{
				allowedBlocks: [ 'quark/form-modal-cta' ],
				template: [
					[ 'quark/form-modal-cta' ],
				],
				templateLock: 'all',
			},
		);

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Card Buttons Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Has Call CTA?', 'qrk' ) }
							checked={ attributes.hasCallCta }
							onChange={ () => setAttributes( { hasCallCta: ! attributes.hasCallCta } ) }
							help={ __( 'Does the card have a Call CTA?', 'qrk' ) }
						/>
						{
							attributes.hasCallCta &&
							<LinkControl
								label={ __( 'Call CTA URL', 'qrk' ) }
								value={ attributes.callCtaUrl }
								help={ __( 'Choose an URL for this Call CTA', 'qrk' ) }
								onChange={ ( callCtaUrl: object ) => setAttributes( { callCtaUrl } ) }
							/>
						}
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps } >
					{ attributes.hasCallCta &&
						<button className="btn btn--has-icon btn--size-big">
							<span className="btn__icon btn__icon-left">
								{ icons.phone }
							</span>
							<RichText
								tagName="span"
								className="btn__content"
								placeholder={ __( 'Book: +9 (999) 999-999', 'qrk' ) }
								value={ attributes.callCtaText }
								onChange={ ( callCtaText: string ) => setAttributes( { callCtaText } ) }
								allowedFormats={ [] }
							/>
						</button>
					}
					{ ! attributes.hasCallCta &&
						<>
							<div { ...innerBlockProps } />
							<LinkButton
								className="btn btn--outline btn--size-big"
								placeholder={ __( 'Enter Text' ) }
								value={ attributes.secondaryBtn }
								onChange={ ( secondaryBtn: object ) => setAttributes( { secondaryBtn } ) }
							/>
						</>
					}
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
