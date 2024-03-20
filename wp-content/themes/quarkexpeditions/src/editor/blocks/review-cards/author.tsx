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
export const name: string = 'quark/review-cards-author';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Review Cards Author', 'qrk' ),
	description: __( 'Individual review card item author.', 'qrk' ),
	parent: [ 'quark/review-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'author', 'qrk' ) ],
	attributes: {
		author: {
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
		const blockProps = useBlockProps( {
			className: classnames( className ),
		} );

		// Return the block's markup.
		return (
			<RichText
				{ ...blockProps }
				tagName="strong"
				className="review-cards__author"
				placeholder={ __( 'Write name…', 'qrk' ) }
				value={ attributes.author }
				onChange={ ( author: string ) => setAttributes( { author } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
