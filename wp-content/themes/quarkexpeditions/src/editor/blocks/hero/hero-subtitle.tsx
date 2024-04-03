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
export const name: string = 'quark/hero-subtitle';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero subtitle', 'qrk' ),
	description: __( 'Hero subtitle text.', 'qrk' ),
	category: 'widgets',
	keywords: [
		__( 'hero', 'qrk' ),
		__( 'subtitle', 'qrk' ),
		__( 'text', 'qrk' ),
	],
	attributes: {
		subtitle: {
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
			className: classnames( className, 'hero__sub-title' ),
		} );

		// Return the block's markup.
		return (
			<RichText
				{ ...blockProps }
				tagName="h5"
				placeholder={ __( 'Write the Subtitle…', 'qrk' ) }
				value={ attributes.subtitle }
				onChange={ ( subtitle: string ) => setAttributes( { subtitle } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Save inner block content.
		return null;
	},
};
