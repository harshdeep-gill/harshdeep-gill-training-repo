/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	RichText,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/hero-overline';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero Overline', 'qrk' ),
	description: __( 'Overline text.', 'qrk' ),
	category: 'widgets',
	keywords: [
		__( 'hero', 'qrk' ),
		__( 'overline', 'qrk' ),
		__( 'text', 'qrk' ),
	],
	attributes: {
		overline: {
			type: 'string',
			default: '',
		},
	},
	parent: [ 'quark/hero-content-left' ],
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// Return the block's markup.
		return (
			<RichText
				tagName="span"
				className={ classnames( className, 'hero__overline', 'overline' ) }
				placeholder={ __( 'Write overline text…', 'qrk' ) }
				value={ attributes.overline }
				onChange={ ( overline: string ) => setAttributes( { overline } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Save inner block content.
		return null;
	},
};
