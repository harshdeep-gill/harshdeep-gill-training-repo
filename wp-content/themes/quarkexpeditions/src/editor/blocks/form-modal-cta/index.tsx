/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	RichText,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * External dependencies.
 */
const { gumponents } = window;

/**
 * External components.
 */
const { ColorPaletteControl } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/form-modal-cta';

// Background colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Yellow', 'qrk' ), color: '#fdb52b', slug: 'yellow' },
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
];

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Form modal CTA', 'qrk' ),
	description: __( 'A CTA to open up a form modal.', 'qrk' ),
	icon: 'button',
	category: 'widgets',
	keywords: [ __( 'cta', 'qrk' ), __( 'form', 'qrk' ), __( 'modal', 'qrk' ) ],
	attributes: {
		text: {
			type: 'string',
			default: '',
		},
		backgroundColor: {
			type: 'string',
			default: 'yellow',
			enum: [ 'yellow', 'black' ],
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: true,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( { className: classnames( className, 'form-modal-cta' ) } );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Form Modal CTA Options', 'qrk' ) }>
						<ColorPaletteControl
							label={ __( 'Background Color', 'qrk' ) }
							help={ __( 'Select the background color.', 'qrk' ) }
							value={ colors.find( ( color ) => color.slug === attributes.backgroundColor )?.color }
							colors={ colors.filter( ( color ) => [ 'black', 'yellow' ].includes( color.slug ) ) }
							onChange={ ( backgroundColor: {
								color: string;
								slug: string;
							} ): void => {
								// Set the background color attribute.
								if ( backgroundColor.slug && [ 'black', 'yellow' ].includes( backgroundColor.slug ) ) {
									setAttributes( { backgroundColor: backgroundColor.slug } );
								}
							} }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<RichText
						tagName="span"
						className={ classnames( 'btn', 'btn--size-big', 'black' === attributes.backgroundColor ? 'btn--color-black' : '', ) }
						placeholder={ __( 'Write CTA text…', 'qrk' ) }
						value={ attributes.text }
						onChange={ ( text: string ) => setAttributes( { text } ) }
						allowedFormats={ [] }
					/>
				</div>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
