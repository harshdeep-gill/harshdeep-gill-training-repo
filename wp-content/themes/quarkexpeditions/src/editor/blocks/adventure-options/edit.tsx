/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	SelectControl,
	Placeholder,
	Tooltip,
	RangeControl,
} from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { store as editorStore } from '@wordpress/editor';
import {
	useSelect,
} from '@wordpress/data';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { TaxonomyRelationshipControl } = gumponents.components;

/**
 * Internal dependencies.
 */
import Section from '../../components/section';
import metadata from './block.json';

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
	// If automatic destination is selected, fetch the destination from the editor store.
	if ( 'auto' === attributes.selectionType ) {
		// Get the destination IDs.
		const destinationIDs = useSelect(
			( select: any ) => select( editorStore )?.getEditedPostAttribute( 'qrk_destination' ),
			[],
		);

		// Set the destination IDs.
		setAttributes( { destinationIDs } );
	}

	// Return the markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Adventure Options Settings', 'qrk' ) }>
					<SelectControl
						label={ __( 'Selection', 'qrk' ) }
						help={ __( 'Select how you would like to select posts', 'qrk' ) }
						value={ attributes.selectionType }
						options={ [
							{ label: __( 'Automatic', 'qrk' ), value: 'auto' },
							{ label: __( 'By Category', 'qrk' ), value: 'byCategory' },
						] }
						onChange={ ( selectionType: string ) => setAttributes( { selectionType } ) }
					/>
					{
						// If automatic and no destination is selected, show a tooltip.
						'auto' === attributes.selectionType && 0 === attributes.destinationIDs.length &&
						<Tooltip>
							<p>{ __( 'Please select Destination(s) for this block to display adventure options.', 'qrk' ) }</p>
						</Tooltip>
					}
					{
						'byCategory' === attributes.selectionType &&
						<TaxonomyRelationshipControl
							label={ __( 'Select Adventure Option Category.', 'et' ) }
							help={ __( 'Select Adventure Option Category', 'et' ) }
							taxonomies="qrk_adventure_option_category"
							value={ attributes.termIDs }
							onSelect={ ( terms: Array<{ term_id: number }> ) => setAttributes( { termIDs: terms.map( ( term ) => term.term_id ) } ) }
							buttonLabel={ __( 'Select Adventure Option Category', 'qrk' ) }
						/>
					}
					<RangeControl
						label={ __( 'Total Posts', 'qrk' ) }
						help={ __( 'Select the total number of options to be displayed', 'qrk' ) }
						value={ attributes.total }
						onChange={ ( total ) => setAttributes( { total } ) }
						min={ 1 }
						max={ 20 }
					/>
				</PanelBody>
			</InspectorControls>
			<Section className={ classnames( className ) }>
				<ServerSideRender
					block={ name }
					attributes={ attributes }
					EmptyResponsePlaceholder={ () => (
						<Placeholder
							icon="location-alt"
							label={ __( 'Adventure Options', 'qrk' ) }
							instructions={ __(
								'Please select a way to display Adventure Options.',
								'qrk',
							) }
						/>
					) }
				/>
			</Section>
		</>
	);
}
