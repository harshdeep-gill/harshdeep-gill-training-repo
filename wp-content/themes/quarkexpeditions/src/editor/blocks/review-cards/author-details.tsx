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
export const name: string = 'quark/review-cards-author-details';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Review Cards Author Details', 'qrk' ),
	description: __( 'Individual review card item author details.', 'qrk' ),
	parent: [ 'quark/review-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [
		__( 'author', 'qrk' ),
		__( 'details', 'qrk' ),
	],
	attributes: {
		authorDetails: {
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
				className="review-cards__author-details"
				placeholder={ __( 'Write details… eg. Expedition Name', 'qrk' ) }
				value={ attributes.authorDetails }
				onChange={ ( authorDetails: string ) => setAttributes( { authorDetails } ) }
				allowedFormats={ [] }
			/>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
