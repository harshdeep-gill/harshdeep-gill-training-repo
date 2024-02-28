/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
} from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	Icon,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/season-highlights-highlight';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Season Highlight Item', 'qrk' ),
	description: __( 'Highlight Item within a Season Item', 'qrk' ),
	parent: [ 'quark/season-highlights-season-item' ],
	icon: 'layout',
	category: 'layout',
	keywords: [ __( 'highlight', 'qrk' ) ],
	attributes: {
		icon: {
			type: 'string',
			default: '',
		},
		title: {
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
			className: classnames( className, 'season-highlights__highlight' ),
		} );

		// Prepare icon.
		let selectedIcon: any = '';

		// Set icon.
		if ( attributes.icon && '' !== attributes.icon ) {
			// Kebab-case to camel-case.
			const iconName: string = attributes.icon.replace( /-([a-z])/g, ( _: any, group:string ) => group.toUpperCase() );
			selectedIcon = icons[ iconName ] ?? '';
		}

		// Fallback icon.
		if ( ! selectedIcon || '' === selectedIcon ) {
			selectedIcon = <Icon icon="no" />;
		}

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Season Highlight Options', 'qrk' ) }>
						<SelectControl
							label={ __( 'Icon', 'qrk' ) }
							help={ __( 'Select the icon for this highlight', 'qrk' ) }
							value={ attributes.icon }
							options={ [
								{ label: __( 'Select Icon…', 'qrk' ), value: '' },
								{ label: __( 'Penguin Courting', 'qrk' ), value: 'court' },
								{ label: __( 'Penguin Hatching', 'qrk' ), value: 'hatch' },
								{ label: __( 'Penguin Nesting', 'qrk' ), value: 'nest' },
								{ label: __( 'Penguin Chicks', 'qrk' ), value: 'penguin-chicks' },
								{ label: __( 'Seabird', 'qrk' ), value: 'seabird' },
								{ label: __( 'Whale', 'qrk' ), value: 'whale' },
								{ label: __( 'Seal', 'qrk' ), value: 'seal' },
								{ label: __( 'Elephant Seal', 'qrk' ), value: 'elephant-seal' },
								{ label: __( 'Glacier', 'qrk' ), value: 'glacier' },
							] }
							onChange={ ( icon: string ) => setAttributes( { icon } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="season-highlights__icon">
						{ selectedIcon }
					</div>
					<RichText
						tagName="p"
						className="season-highlights__highlight-title"
						placeholder={ __( 'Highlight Name…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
