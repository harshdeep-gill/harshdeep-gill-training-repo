/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
} from '@wordpress/blocks';
import {
	PanelBody,
	Placeholder,
	RadioControl,
	ToggleControl,
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
export const name: string = 'quark/blog-post-cards';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Blog Post Cards', 'qrk' ),
	description: __( 'Blog Post Cards Block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'blog', 'qrk' ),
		__( 'post', 'qrk' ),
	],
	attributes: {
		layout: {
			type: 'string',
			default: 'collage',
		},
		ids: {
			type: 'array',
			default: [],
		},
		isMobileCarousel: {
			type: 'boolean',
			default: true,
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
		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Related Posts Options', 'qrk' ) }>
						<RadioControl
							label={ __( 'Layout', 'qrk' ) }
							help={ __( 'Select the layout of the cards', 'qrk' ) }
							selected={ attributes.layout }
							options={ [
								{ label: __( 'Grid', 'qrk' ), value: 'grid' },
								{ label: __( 'Collage', 'qrk' ), value: 'collage' },
							] }
							onChange={ ( layout: string ) => setAttributes( { layout } ) }
						/>
						<PostRelationshipControl
							label={ __( 'Select Blog Posts', 'qrk' ) }
							help={ __( 'Select blog posts', 'qrk' ) }
							postTypes="post"
							value={ attributes.ids }
							onSelect={ ( blogPosts: any ) => setAttributes( { ids: blogPosts.map( ( post: any ) => post.ID ) } ) }
							button={ __( 'Select Blog Posts', 'qrk' ) }
						/>
						<ToggleControl
							label={ __( 'Is Mobile Carousel?', 'qrk' ) }
							checked={ attributes.isMobileCarousel }
							help={ __( 'Show the blog posts in a carousel on mobile devices', 'qrk' ) }
							onChange={ ( isMobileCarousel: boolean ) => setAttributes( { isMobileCarousel } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Section className={ classnames( className ) }>
					{
						( attributes.ids.length >= 5 && 'collage' === attributes.layout ) ||
						( attributes.ids.length && 'grid' === attributes.layout )
							? (
								<ServerSideRender
									block={ name }
									attributes={ attributes }
								/>
							) : (
								<Placeholder icon="layout" label={ __( 'Blog Post Cards', 'qrk' ) }>
									{
										'collage' === attributes.layout
											? <p>{ __( 'Select at least 5 blog posts to be displayed.', 'qrk' ) }</p>
											: <p>{ __( 'Select the blog posts to be displayed.', 'qrk' ) }</p>
									}
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
