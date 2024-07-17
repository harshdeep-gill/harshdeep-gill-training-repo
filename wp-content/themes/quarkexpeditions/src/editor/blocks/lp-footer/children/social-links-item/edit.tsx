/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Icons.
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
	const blocksProps = useBlockProps( {
		className: classnames( className, 'lp-footer__social-link' ),
		title: attributes.type,
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Social Link Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Social Link type', 'qrk' ) }
						help={ __( 'Select the type of social media.', 'qrk' ) }
						value={ attributes.type }
						options={ [
							{ label: __( 'Facebook', 'qrk' ), value: 'faccebook' },
							{ label: __( 'Twitter', 'qrk' ), value: 'twitter' },
							{ label: __( 'Instagram', 'qrk' ), value: 'instagram' },
							{ label: __( 'Youtube', 'qrk' ), value: 'youtube' },
						] }
						onChange={ ( type: string ) => setAttributes( { type } ) }
					/>
					<TextControl
						label={ __( 'Social media Link', 'qrk' ) }
						help={ __( 'Add social media link.', 'qrk' ) }
						value={ attributes.url }
						onChange={ ( url: string ) => setAttributes( { url } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<span { ...blocksProps }>
				<span className="screen-reader-text">{ attributes.type }</span>
				{ icons[ `${ attributes.type }` ] }
			</span>
		</>
	);
}
