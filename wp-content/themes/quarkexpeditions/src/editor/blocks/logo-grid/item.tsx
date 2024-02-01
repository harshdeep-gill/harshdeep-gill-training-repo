/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { SelectImage } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/logo-grid-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Logo Grid Item', 'qrk' ),
	description: __( 'Individual logo grid item.', 'qrk' ),
	parent: [ 'quark/logo-grid' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'item', 'qrk' ) ],
	attributes: {
		image: {
			type: 'object',
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
			className: classnames( className, 'logo-grid__logo' ),
		} );

		// Return the block's markup.
		return (
			<figure { ...blocksProps }>
				<SelectImage
					image={ attributes.image }
					className="logo-grid__img"
					size="medium"
					onChange={ ( image: object ): void => {
						// Set attributes.
						setAttributes( { image: null } );
						setAttributes( { image } );
					} }
				/>
			</figure>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
