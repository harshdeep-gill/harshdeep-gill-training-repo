/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../../../icons';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Prepare block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'icon-info-grid__item' ),
	} );

	// Prepare inner block props.
	const innerBlockProps = useInnerBlocksProps( {}, {
		allowedBlocks: [ 'core/heading', 'core/paragraph' ],
		template: [ [ 'core/heading', { level: 4 } ], [ 'core/paragraph' ] ],
	} );

	// Prepare icon.
	let selectedIcon: any = '';

	// Set icon.
	if ( attributes.icon && '' !== attributes.icon ) {
		// Kebab-case to camel-case.
		const iconName: string = attributes.icon.replace( /-([a-z])/g, ( _: any, group:string ) => group.toUpperCase() );
		selectedIcon = icons[ iconName ] ?? '';
	}

	// Return the block's markup.
	return (
		<div { ...blockProps } >
			<InspectorControls>
				<PanelBody title={ __( 'Icon Info Grid Item Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'has Icon?', 'qrk' ) }
						checked={ attributes.hasIcon }
						onChange={ ( hasIcon: boolean ) => setAttributes( { hasIcon } ) }
						help={ __( 'Does the card require Icon.', 'qrk' ) }
					/>
					{ attributes.hasIcon &&
						<SelectControl
							label={ __( 'Icon', 'qrk' ) }
							value={ attributes.icon }
							options={ [
								{ label: 'Star', value: 'star' },
								{ label: 'Ship', value: 'ship' },
								{ label: 'Sun', value: 'sun' },
								{ label: 'Compass', value: 'compass' },
								{ label: 'Aid Icon', value: 'aid-icon' },
								{ label: 'Departure Notification', value: 'departure-notification' },
								{ label: 'Training', value: 'training' },
							] }
							onChange={ ( value ) => setAttributes( { icon: value } ) }
						/>
					}
				</PanelBody>
			</InspectorControls>
			{	attributes.hasIcon &&
				<div className={ 'icon-info-grid__icon' }>
					{ selectedIcon }
				</div>
			}
			<div { ...innerBlockProps } />
		</div>
	);
}
