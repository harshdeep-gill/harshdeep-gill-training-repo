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
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/hero-title';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero Title', 'qrk' ),
	description: __( 'Hero Title text.', 'qrk' ),
	category: 'widgets',
	keywords: [
		__( 'hero', 'qrk' ),
		__( 'title', 'qrk' ),
		__( 'text', 'qrk' ),
	],
	attributes: {
		title: {
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
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'hero__title' ),
		} );

		// Return the block's markup.
		return (
			<RichText
				{ ...blockProps }
				tagName="h1"
				placeholder={ __( 'Write the Title…', 'qrk' ) }
				value={ attributes.title }
				onChange={ ( title: string ) => setAttributes( { title } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Save inner block content.
		return null;
	},
};
