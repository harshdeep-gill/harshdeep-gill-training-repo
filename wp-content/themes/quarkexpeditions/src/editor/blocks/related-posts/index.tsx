/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
} from '@wordpress/blocks';
import {
	PanelBody,
	SelectControl,
	Placeholder,
} from '@wordpress/components';
import {
	InspectorControls,
} from '@wordpress/block-editor';

// @ts-ignore No Module Declaration.
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Styles.
 */
import '../../../front-end/components/info-cards/style.scss';

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
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * Block name.
 */
export const name: string = 'quark/related-posts';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Related Posts', 'qrk' ),
	description: __( 'Related Posts block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'related', 'qrk' ),
		__( 'posts', 'qrk' ),
	],
	attributes: {
		selection: {
			type: 'string',
			default: 'recent',
		},
		ids: {
			type: 'array',
			default: [],
		},
		totalPosts: {
			type: 'number',
			default: 3,
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// Initialize.
		let allOptionsSelected = false;

		// Check if all required options are selected.
		if ( 'recent' === attributes.selection ) {
			allOptionsSelected = true;
		} else if ( 'manual' === attributes.selection && 0 !== attributes.ids.length ) {
			allOptionsSelected = true;
		}

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Related Posts Options', 'qrk' ) }>
						<SelectControl
							label={ __( 'Selection', 'qrk' ) }
							help={ __( 'Select how you would like to select posts', 'qrk' ) }
							value={ attributes.selection }
							options={ [
								{ label: __( 'Manual', 'qrk' ), value: 'manual' },
								{ label: __( 'Recent', 'qrk' ), value: 'recent' },
							] }
							onChange={ ( selection ) => setAttributes( { selection } ) }
						/>
						{ 'manual' === attributes.selection &&
							<PostRelationshipControl
								label={ __( 'Select Blog Posts', 'qrk' ) }
								help={ __( 'Select blog posts', 'qrk' ) }
								postTypes="post"
								value={ attributes.ids }
								onSelect={ ( blogPosts: any ) => setAttributes( { ids: blogPosts.map( ( post: any ) => post.ID ) } ) }
								button={ __( 'Select Blog Posts', 'qrk' ) }
							/>
						}
					</PanelBody>
				</InspectorControls>
				<Section className={ classnames( className ) }>
					{
						allOptionsSelected ? (
							<ServerSideRender
								block={ name }
								attributes={ attributes }
							/>
						) : (
							<Placeholder icon="layout" label={ __( 'Related Posts', 'qrk' ) }>
								<p>{ __( 'Select the related posts.', 'qrk' ) }</p>
							</Placeholder>
						)
					}
				</Section>
			</>
		);
	},
	save() {
		// Don't save any markup.
		return null;
	},
};
