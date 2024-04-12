/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/review-cards-title';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Review Cards Title', 'qrk' ),
	description: __( 'Individual review card item title.', 'qrk' ),
	parent: [ 'quark/review-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'title', 'qrk' ) ],
	attributes: {
		title: {
			type: 'string',
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
			className: classnames( className ),
		} );

		// Return the block's markup.
		return (
			<RichText
				{ ...blocksProps }
				tagName="h5"
				className="review-cards__card-title"
				placeholder={ __( 'Write title…', 'qrk' ) }
				value={ attributes.title }
				onChange={ ( title: string ) => setAttributes( { title } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
