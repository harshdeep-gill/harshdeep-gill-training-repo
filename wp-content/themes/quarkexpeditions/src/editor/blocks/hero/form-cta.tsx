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
export const name: string = 'quark/hero-form-cta';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Form modal CTA', 'qrk' ),
	description: __( 'A CTA to open up the inquiry form modal.', 'qrk' ),
	parent: [ 'qrk/hero' ],
	icon: 'button',
	category: 'widgets',
	keywords: [ __( 'cta', 'qrk' ), __( 'form', 'qrk' ), __( 'modal', 'qrk' ) ],
	attributes: {
		text: {
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
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blocksProps = useBlockProps( {
			className: classnames( className, 'hero__form-modal-cta' ),
		} );

		// Return the block's markup.
		return (
			<div { ...blocksProps }>
				<RichText
					tagName="p"
					className={ classnames( 'btn', 'btn--size-big' ) }
					placeholder={ __( 'Write CTA text…', 'qrk' ) }
					value={ attributes.text }
					onChange={ ( text: string ) => setAttributes( { text } ) }
					allowedFormats={ [] }
				/>
			</div>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
