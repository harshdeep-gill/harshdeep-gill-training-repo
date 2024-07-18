/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { Icon, PanelBody, SelectControl } from '@wordpress/components';

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
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'footer__icon' ),
	} );

	// Prepare icon.
	let selectedIcon: any = '';

	// Set icon.
	if ( attributes.type && '' !== attributes.type ) {
		// Setting icon.
		selectedIcon = icons[ attributes.type ] ?? '';
	}

	// Fallback icon.
	if ( ! selectedIcon || '' === selectedIcon ) {
		selectedIcon = <Icon icon="no" />;
	}

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Icon Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Icon', 'qrk' ) }
						help={ __( 'Select icon.', 'qrk' ) }
						value={ attributes.type }
						options={ [
							{ label: __( 'Select Icon…', 'qrk' ), value: '' },
							{ label: __( 'Call', 'qrk' ), value: 'call' },
							{ label: __( 'Brochure', 'qrk' ), value: 'brochure' },
							{ label: __( 'Mail', 'qrk' ), value: 'mail' },
						] }
						onChange={ ( type: string ) => setAttributes( { type } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<span { ...blockProps }>
				{ selectedIcon }
			</span>
		</>
	);
}
