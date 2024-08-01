/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	SelectControl,
	Placeholder,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';

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
const { PostRelationshipControl } = gumponents.components;

/**
 * Styles.
 */
import './editor.scss';

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
	// Initialize.
	let allOptionsSelected = false;

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'quark-ships' ),
	} );

	// Check if all required options are selected.
	if ( 'auto' === attributes.selectionType ) {
		allOptionsSelected = true;
	} else if ( 'manual' === attributes.selectionType && 0 !== attributes.ships.length ) {
		allOptionsSelected = true;
	}

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Ships Settings', 'qrk' ) }>
					<SelectControl
						label={ __( 'Selection', 'qrk' ) }
						help={ __( 'Select the way to display the ships.', 'qrk' ) }
						value={ attributes.selectionType }
						options={ [
							{ label: __( 'Manual', 'qrk' ), value: 'manual' },
							{ label: __( 'Auto', 'qrk' ), value: 'auto' },
						] }
						onChange={ ( selectionType ) => setAttributes( { selectionType } ) }
					/>
					{ 'manual' === attributes.selectionType &&
						<PostRelationshipControl
							label={ __( 'Select Ships', 'qrk' ) }
							help={ __( 'Select the ships to display.', 'qrk' ) }
							postTypes="qrk_ship"
							value={ attributes.ships }
							onSelect={ ( ships: any ) => setAttributes( { ships: ships.map( ( ship: any ) => ship.ID ) } ) }
							button={ __( 'Select Ships', 'qrk' ) }
						/>
					}
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps }>
				{
					allOptionsSelected ? (
						<ServerSideRender
							block={ name }
							attributes={ attributes }
						/>
					) : (
						<Placeholder icon="layout" label={ __( 'Ships', 'qrk' ) }>
							<p>{ __( 'Select a way to display the ships.', 'qrk' ) }</p>
						</Placeholder>
					)
				}
			</Section>
		</>
	);
}
