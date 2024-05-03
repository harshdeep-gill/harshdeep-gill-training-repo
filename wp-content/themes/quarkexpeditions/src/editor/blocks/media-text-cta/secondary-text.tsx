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
export const name: string = 'quark/media-text-cta-secondary-text';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Media Text CTA Secondary Text', 'qrk' ),
	description: __( 'Secondary text for Media Text CTA Content', 'qrk' ),
	parent: [ 'quark/media-text-cta' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'secondary', 'qrk' ), __( 'text', 'qrk' ) ],
	attributes: {
		secondaryText: {
			type: 'string',
			default: '',
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
			className: classnames( className, 'media-text-cta__secondary-text' ),
		} );

		// Return the block's markup.
		return (
			<RichText
				{ ...blocksProps }
				tagName="div"
				placeholder={ __( 'Write Secondary Text… ', 'qrk' ) }
				value={ attributes.secondaryText }
				onChange={ ( secondaryText: string ) => setAttributes( { secondaryText } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
