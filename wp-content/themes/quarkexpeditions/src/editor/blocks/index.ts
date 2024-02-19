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
import * as hero from './hero';
import * as inquiryForm from './inquiry-form';
import * as logoGrid from './logo-grid';
import * as lpFooter from './lp-footer';
import * as reviewCards from './review-cards';

/**
 * Add blocks.
 */
const blocks = [
	section,
	lpHeader,
	twoColumns,
	iconInfoColumns,
	reviewsCarousel,
	hero,
	inquiryForm,
	logoGrid,
	lpFooter,
	reviewCards,
];

/**
 * Register blocks.
 */
blocks.forEach( ( { name, settings } ) => registerBlockType( name, settings ) );
