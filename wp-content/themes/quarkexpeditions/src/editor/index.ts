/**
 * Styles.
 */
import './editor.scss';

/**
 * Blocks.
 */
import './blocks';

/**
 * WordPress dependencies.
 */
import domReady from '@wordpress/dom-ready';
import {
	unregisterBlockType,
	unregisterBlockStyle,
	registerBlockStyle,
} from '@wordpress/blocks';

/**
 * Blacklist Blocks.
 */
const blacklistBlocks = [
	'core/audio',
	'core/video',
	'core/code',
	'core/preformatted',
	'core/verse',
	'core/media-text',
	'core/nextpage',
	'core/more',
	'core/spacer',
	'core/archives',
	'core/calendar',
	'core/categories',
	'core/latest-comments',
	'core/latest-posts',
	'core/rss',
	'core/search',
	'core/tag-cloud',
	'core/gallery',
	'core/cover',
	'core/group',
	'core/site-logo',
	'core/site-tagline',
	'core/site-title',
	'core/query-title',
	'core/post-terms',
	'core/page-list',
	'core/query',
	'core/post-title',
	'core/post-content',
	'core/post-date',
	'core/post-excerpt',
	'core/post-featured-image',
	'core/loginout',
	'core/social-links',
	'core/separator',
	'core/pullquote',
	'core/navigation',
	'core/avatar',
	'core/post-navigation-link',
	'core/comments',
	'core/table',
	'core/comment-template',
	'core/comment-reply-link',
	'core/post-comments-form',
	'core/term-description',
	'core/post-author',
	'core/read-more',
	'core/post-author-biography',
	'core/buttons',
	'core/button',
	'yoast/how-to-block',
	'yoast/faq-block',
	'yoast-seo/breadcrumbs',
];

// Blacklist all the blocks when DOM is ready.
domReady( () => blacklistBlocks.forEach( ( block ) => unregisterBlockType( block ) ) );

/**
 * Block Styles.
 */
domReady( () => {
	// Buttons.
	unregisterBlockStyle( 'core/button', 'outline' );
	unregisterBlockStyle( 'core/button', 'fill' );
	unregisterBlockStyle( 'core/quote', 'plain' );
	unregisterBlockStyle( 'core/quote', 'default' );
	unregisterBlockStyle( 'core/image', 'default' );

	// Buttons.
	registerBlockStyle( 'core/button', {
		name: 'outline',
		label: 'Outline',
		isDefault: true,
	} );
	registerBlockStyle( 'core/button', {
		name: 'solid',
		label: 'Solid',
	} );

	// Paragraph.
	registerBlockStyle( 'core/paragraph', {
		name: 'default',
		label: 'Default',
		isDefault: true,
	} );
	registerBlockStyle( 'core/paragraph', {
		name: 'template-title',
		label: 'Template Title',
	} );
} );
