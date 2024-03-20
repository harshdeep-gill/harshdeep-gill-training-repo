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
export const name: string = 'quark/review-cards-review';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Review Cards Review', 'qrk' ),
	description: __( 'Individual review card item review content.', 'qrk' ),
	parent: [ 'quark/review-cards-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'review', 'qrk' ) ],
	attributes: {
		review: {
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
			className: classnames( className, 'review-cards__content' ),
		} );

		// Return the block's markup.
		return (
			<div { ...blocksProps }>
				<RichText
					tagName="p"
					placeholder={ __( 'Write review…', 'qrk' ) }
					value={ attributes.review }
					onChange={ ( review: string ) => setAttributes( { review } ) }
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
