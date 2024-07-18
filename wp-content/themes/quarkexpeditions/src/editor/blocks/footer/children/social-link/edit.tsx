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
 * Internal dependencies.
 */
import icons from '../../../icons';

/**
 * External dependencies.
 */
const { gumponents } = window;

/**
 * External components.
 */
const { LinkControl } = gumponents.components;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps();

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
							{ label: __( 'Facebook', 'qrk' ), value: 'facebook' },
							{ label: __( 'Instagram', 'qrk' ), value: 'instagram' },
							{ label: __( 'Youtube', 'qrk' ), value: 'youtube' },
							{ label: __( 'Twitter', 'qrk' ), value: 'twitter' },
						] }
						onChange={ ( type: string ) => setAttributes( { type } ) }
					/>
					<LinkControl
						label={ __( 'Select URL', 'qrk' ) }
						value={ attributes.url }
						help={ __( 'Enter an URL for this social icon', 'qrk' ) }
						onChange={ ( url: object ) => setAttributes( { url } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<li { ...blockProps }>
				<span className={ `footer__social-icons-${ attributes.type }` }>
					{ selectedIcon }
				</span>
			</li>
		</>
	);
}
