/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	Placeholder,
	SelectControl,
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
 * Form Options.
 */
const formOptions = [
	{ label: __( 'None', 'qrk' ), value: 'none', thankYouPage: false },
	{ label: __( 'Job Application', 'qrk' ), value: 'job-application', thankYouPage: true },
	{ label: __( 'Contact Us', 'qrk' ), value: 'contact-us', thankYouPage: true },
	{ label: __( 'Newsletter', 'qrk' ), value: 'newsletter', thankYouPage: false },
];

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
		className: classnames( className, 'quark-form' ),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form', 'qrk' ) }>
					<SelectControl
						label={ __( 'Selection', 'qrk' ) }
						help={ __( 'Select which form to display.', 'qrk' ) }
						options={ formOptions }
						value={ attributes.form }
						onChange={ ( value ) => setAttributes( { form: value } ) }
					/>
					{ attributes.form && formOptions.find( ( option ) => option.value === attributes.form )?.thankYouPage && (
						<LinkControl
							label={ __( 'Thank You Page', 'qrk' ) }
							value={ attributes.thankYouPage }
							help={ __( 'Select the page you want the user to be redirected to after submitting the form.', 'qrk' ) }
							onChange={ ( thankYouPage: object ) => setAttributes( { thankYouPage } ) }
						/>
					) }
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps }>
				{ 'none' === attributes.form ? (
					<Placeholder icon="layout" label={ __( 'Form', 'qrk' ) }>
						<p>{ __( 'Select a form to display.', 'qrk' ) }</p>
					</Placeholder>
				) : (
					<Placeholder icon="layout" label={ __( 'Form', 'qrk' ) }>
						<p>{ __( 'Form will be displayed on the front end.', 'qrk' ) }</p>
					</Placeholder>
				) }
			</Section>
		</>
	);
}
