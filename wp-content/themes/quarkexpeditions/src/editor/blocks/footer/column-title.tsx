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
export const name: string = 'quark/footer-column-title';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Column Title', 'qrk' ),
	description: __( 'Display the title of a footer column.', 'qrk' ),
	parent: [ 'quark/footer-column' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'column', 'qrk' ),
		__( 'title', 'qrk' ),
	],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( { className: classnames( className, 'footer__column-title' ) } );

		// Return the block's markup.
		return (
			<RichText
				{ ...blockProps }
				tagName="p"
				placeholder={ __( 'Write Column Title…', 'qrk' ) }
				value={ attributes.title }
				onChange={ ( title: string ) => setAttributes( { title } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Return null;
		return null;
	},
};
