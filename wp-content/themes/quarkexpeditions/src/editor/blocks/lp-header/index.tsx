/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
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

/**
 * Block data.
 */
export const name: string = 'quark/lp-header';
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
		const blockProps = useBlockProps( { className: classnames( 'lp-header full-width', className ) } );

		// Return block.
		return (
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
							<RichText
								tagName="span"
								className="lp-header__cta-content-phone-number"
								placeholder={ __( 'Write CTA Number…', 'qrk' ) }
								value={ attributes.ctaNumber }
								onChange={ ( ctaNumber: string ) => setAttributes( { ctaNumber } ) }
								allowedFormats={ [] }
							/>
						</span>
					</span>
				</div>
			</div>
		);
	},
	save() {
		// Don't save any content.
		return null;
	},
};
