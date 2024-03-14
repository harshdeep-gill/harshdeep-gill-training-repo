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
export const name: string = 'quark/product-cards-description';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card Description', 'qrk' ),
	description: __( 'Individual Card Description for product cards.', 'qrk' ),
	parent: [ 'quark/product-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'description', 'qrk' ) ],
	attributes: {
		description: {
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
			className: classnames( className, 'product-cards__description' ),
		} );

		// Return the block's markup.
		return (
			<div { ...blockProps }>
				<RichText
					tagName="p"
					placeholder={ __( 'Enter Description…', 'qrk' ) }
					value={ attributes.description }
					onChange={ ( description: string ) => setAttributes( { description } ) }
					allowedFormats={ [] }
				/>
			</div>
		);
	},
	save() {
		// Return.
		return null;
	},
};
