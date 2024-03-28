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
export const name: string = 'quark/product-cards-subtitle';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card Subtitle', 'qrk' ),
	description: __( 'Individual Card Subtitle for product cards.', 'qrk' ),
	parent: [ 'quark/product-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'subtitle', 'qrk' ) ],
	attributes: {
		subtitle: {
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
		const blockProps = useBlockProps( {
			className: classnames( className, 'product-cards__subtitle' ),
		} );

		// Return the block's markup.
		return (
			<RichText
				{ ...blockProps }
				tagName="p"
				placeholder={ __( 'Write Subtitle…', 'qrk' ) }
				value={ attributes.subtitle }
				onChange={ ( subtitle: string ) => setAttributes( { subtitle } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Return.
		return null;
	},
};
