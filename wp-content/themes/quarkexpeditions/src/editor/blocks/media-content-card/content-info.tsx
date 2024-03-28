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
export const name: string = 'quark/media-content-info';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Media Content Info', 'qrk' ),
	description: __( 'Individual content info for media content card column.', 'qrk' ),
	parent: [ 'quark/media-content-card-column' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'content info', 'qrk' ) ],
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
			className: classnames( className, 'media-content-card__content-info' ),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Media Content Info Options', 'qrk' ) }>
						<LinkControl
							label={ __( 'Select URL', 'qrk' ) }
							value={ attributes.url }
							help={ __( 'Enter an URL for this Info item', 'qrk' ) }
							onChange={ ( url: object ) => setAttributes( { url } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blocksProps }>
					<RichText
						tagName="span"
						className="media-content-card__content-info-label"
						placeholder={ __( 'Enter label… ', 'qrk' ) }
						value={ attributes.label }
						onChange={ ( label: string ) => setAttributes( { label } ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="strong"
						className="media-content-card__content-info-value"
						placeholder={ __( 'Enter value…', 'qrk' ) }
						value={ attributes.value }
						onChange={ ( value: string ) => setAttributes( { value } ) }
						allowedFormats={ [] }
					/>
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return null;
	},
};
