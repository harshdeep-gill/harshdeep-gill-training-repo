/**
 * WordPress dependencies.
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Import blocks.
 */
import * as section from './section';
import * as lpHeader from './lp-header';
import * as twoColumns from './two-columns';
import * as iconInfoColumns from './icon-info-columns';
import * as reviewsCarousel from './reviews-carousel';
import * as logoGrid from './logo-grid';
import * as lpFooter from './lp-footer';

/**
 * Add blocks.
 */
const blocks = [
	section,
	lpHeader,
	twoColumns,
	iconInfoColumns,
	reviewsCarousel,
	logoGrid,
	lpFooter,
];

/**
 * Register blocks.
 */
blocks.forEach( ( { name, settings } ) => registerBlockType( name, settings ) );
