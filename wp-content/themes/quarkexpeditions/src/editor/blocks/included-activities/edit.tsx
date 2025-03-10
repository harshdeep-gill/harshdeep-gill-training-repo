/**
 * WordPress dependencies.
 */
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	Placeholder,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

// @ts-ignore No module declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Style dependencies.
 */
import './editor.scss';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * Internal dependencies.
 */
import metadata from './block.json';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {string}   props.attributes    Attributes.
 * @param {Function} props.setAttributes Set attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'included-activities' ),
	} );

	// Return the block's markup.
	return (
		<Section { ...blockProps } >
			<InspectorControls>
				<PanelBody title="Included Activities" initialOpen={ true }>
					<ToggleControl
						label="Show Title"
						checked={ attributes.showTitle }
						onChange={ ( value ) => setAttributes( { showTitle: value } ) }
					/>
					<ToggleControl
						label="Show Description"
						checked={ attributes.showDescription }
						onChange={ ( value ) => setAttributes( { showDescription: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block={ name }
				attributes={ attributes }
				EmptyResponsePlaceholder={ () => (
					<Placeholder
						icon="palmtree"
						label={ __( 'Included Activities', 'qrk' ) }
						instructions={ __(
							'Please select one or more Activities.',
							'qrk',
						) }
					/>
				) }
			/>
		</Section>
	);
}
