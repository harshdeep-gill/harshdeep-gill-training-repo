/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	Placeholder,
} from '@wordpress/components';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { LinkControl } = gumponents.components;

/**
 * Internal dependencies.
 */
import metadata from './block.json';
import Section from '../../components/section';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Set Block Props
	const blockProps = useBlockProps( {
		className: classnames( className, 'quark-form-account-management' ),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form - Access Deletion Request', 'qrk' ) }>
					<LinkControl
						label={ __( 'Thank You Page', 'qrk' ) }
						value={ attributes.thankYouPage }
						help={ __( 'Select the page you want the user to be redirected to after submitting the form.', 'qrk' ) }
						onChange={ ( thankYouPage: object ) => setAttributes( { thankYouPage } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps }>
				<Placeholder icon="layout" label={ __( 'Form - CCPA Account Deletion Request', 'qrk' ) }>
					<p>{ __( 'CCPA Account Deletion Request Form will be displayed on the front end.', 'qrk' ) }</p>
				</Placeholder>
			</Section>
		</>
	);
}
