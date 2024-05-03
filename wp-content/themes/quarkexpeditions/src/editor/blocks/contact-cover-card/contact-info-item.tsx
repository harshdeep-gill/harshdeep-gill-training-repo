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
import {
	PanelBody,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { LinkControl } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/contact-cover-card-contact-info-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Contact Info Item', 'qrk' ),
	description: __( 'Individual content info item for contact cover card.', 'qrk' ),
	parent: [ 'quark/contact-cover-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'item', 'qrk' ), __( 'contact', 'qrk' ) ],
	attributes: {
		label: {
			type: 'string',
			default: '',
		},
		value: {
			type: 'string',
			default: '',
		},
		url: {
			type: 'object',
			default: {},
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
			className: classnames( className, 'contact-cover-card__contact-info-item' ),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Contact Cover Info Item Options', 'qrk' ) }>
						<LinkControl
							label={ __( 'Select URL', 'qrk' ) }
							value={ attributes.url }
							help={ __( 'Enter an URL for this Info item', 'qrk' ) }
							onChange={ ( url: object ) => setAttributes( { url } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blocksProps }>
					<div className="btn btn--color-black btn--size-big">
						<RichText
							tagName="span"
							className="contact-cover-card__contact-info-item-label"
							placeholder={ __( 'Enter label… ', 'qrk' ) }
							value={ attributes.label }
							onChange={ ( label: string ) => setAttributes( { label } ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="strong"
							className="contact-cover-card__contact-info-item-value"
							placeholder={ __( 'Enter value…', 'qrk' ) }
							value={ attributes.value }
							onChange={ ( value: string ) => setAttributes( { value } ) }
							allowedFormats={ [] }
						/>
					</div>
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return null;
	},
};
