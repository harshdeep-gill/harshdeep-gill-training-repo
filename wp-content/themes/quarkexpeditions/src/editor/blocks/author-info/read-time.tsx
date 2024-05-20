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
export const name: string = 'quark/author-info-read-time';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Read Time', 'qrk' ),
	description: __( 'Read Time.', 'qrk' ),
	category: 'widgets',
	keywords: [
		__( 'read', 'qrk' ),
		__( 'time', 'qrk' ),
	],
	attributes: {
		duration: {
			type: 'number',
			default: 0,
		},
	},
	parent: [ 'quark/author-info' ],
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
			className: classnames( className, 'post-author-info__duration' ),
		} );

		// Return the block's markup.
		return (
			<>
				<RichText
					{ ...blockProps }
					tagName="span"
					placeholder={ __( 'Duration in minutes (eg: \'5\')', 'qrk' ) }
					value={ attributes.duration.toString() }
					onChange={ ( value: string ) => {
						// Parse the duration value.
						const duration = parseInt( value );

						// Check if it is a valid number.
						if ( Number.isNaN( duration ) ) {
							// Bail.
							return;
						}

						// set the value.
						setAttributes( { duration } );
					} }
					allowedFormats={ [] }
				/>
				<span>&nbsp;min read</span>
			</>
		);
	},
	save() {
		// Return null.
		return null;
	},
};
