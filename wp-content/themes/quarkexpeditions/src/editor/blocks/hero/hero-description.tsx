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
export const name: string = 'quark/hero-description';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero Description', 'qrk' ),
	description: __( 'Hero Description text.', 'qrk' ),
	category: 'widgets',
	keywords: [
		__( 'description', 'qrk' ),
	],
	attributes: {
		description: {
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
			className: classnames( className, 'hero__description' ),
		} );

		// Return the block's markup.
		return (
			<div { ...blockProps }>
				<RichText
					tagName="p"
					placeholder={ __( 'Write description…', 'qrk' ) }
					value={ attributes.description }
					onChange={ ( description: string ) => setAttributes( { description } ) }
					allowedFormats={ [] }
				/>
			</div>
		);
	},
	save() {
		// Save inner block content.
		return null;
	},
};
