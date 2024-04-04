/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/lp-offer-masthead/style.scss';
import './editor.scss';

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
export const name: string = 'quark/lp-offer-masthead-offer-image';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'LP Offer Masthead Offer Image', 'qrk' ),
	description: __( 'Offer Image within Offer Masthead.', 'qrk' ),
	parent: [ 'quark/lp-offer-masthead' ],
	category: 'layout',
	keywords: [
		__( 'offer', 'qrk' ),
		__( 'image', 'qrk' ),
	],
	attributes: {
		offerImage: {
			type: 'object',
			default: null,
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
		const blockProps = useBlockProps( {
			className: classnames( className, 'lp-offer-masthead__offer-image' ),
		} );

		// Return the block's markup.
		return (
			<figure { ...blockProps } >
				<SelectImage
					image={ attributes.offerImage }
					placeholder="Choose an Offer Image"
					size="large"
					onChange={ ( offerImage: Object ): void => {
						// Set image.
						setAttributes( { offerImage: null } );
						setAttributes( { offerImage } );
					} }
				/>
			</figure>
		);
	},
	save() {
		// Return null.
		return null;
	},
};
