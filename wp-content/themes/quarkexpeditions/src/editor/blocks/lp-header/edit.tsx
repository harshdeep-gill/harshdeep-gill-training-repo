/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * Styles.
 */
import './editor.scss';

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
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) {
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
}
