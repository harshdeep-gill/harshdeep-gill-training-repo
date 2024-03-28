/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { SelectImage } = gumponents.components;

/**
 * Styles.
 */
import '../../../front-end/components/lp-header/style.scss';
import './editor.scss';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * Block name.
 */
export const name: string = 'quark/lp-header';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'LP Header', 'qrk' ),
	description: __( 'LP Header block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'lp', 'qrk' ),
		__( 'header', 'qrk' ),
	],
	attributes: {
		tcImage: {
			type: 'object',
		},
		ctaText: {
			type: 'string',
		},
		ctaNumber: {
			type: 'string',
		},
		darkMode: {
			type: 'boolean',
			default: false,
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( { className: classnames( `lp-header full-width ${ attributes.darkMode ? 'color-context--dark' : '' }`, className ) } );

		// Return block.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'LP Header Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Is dark mode?', 'qrk' ) }
							checked={ attributes.darkMode }
							help={ __( 'Is this a dark mode header?', 'qrk' ) }
							onChange={ ( darkMode: boolean ) => setAttributes( { darkMode } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="lp-header__wrap">
						<span className="lp-header__logo">
							{ icons.logo }
						</span>
						<span className="lp-header__cta">
							<figure className="lp-header__cta-avatar">
								<SelectImage
									image={ attributes.tcImage }
									size="thumbnail"
									onChange={ ( tcImage: Object ): void => {
										// Set image.
										setAttributes( { tcImage: null } );
										setAttributes( { tcImage } );
									} }
								/>
							</figure>
							<span className="lp-header__cta-content">
								<RichText
									tagName="span"
									className="lp-header__cta-content-text"
									placeholder={ __( 'Write CTA Text…', 'qrk' ) }
									value={ attributes.ctaText }
									onChange={ ( ctaText: string ) => setAttributes( { ctaText } ) }
									allowedFormats={ [] }
								/>
								<span className="lp-header__cta-content-phone-number">
									<RichText
										tagName="span"
										placeholder={ __( 'Write CTA Number…', 'qrk' ) }
										value={ attributes.ctaNumber }
										onChange={ ( ctaNumber: string ) => setAttributes( { ctaNumber } ) }
										allowedFormats={ [] }
									/>
								</span>
							</span>
						</span>
					</div>
				</div>
			</>
		);
	},
	save() {
		// Don't save any content.
		return null;
	},
};
