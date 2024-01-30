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
import * as lpFooter from './lp-footer';

/**
 * Add blocks.
 */
const blocks = [
	section,
	lpHeader,
	twoColumns,
	lpFooter,
];

/**
 * Register blocks.
 */
blocks.forEach( ( { name, settings } ) => registerBlockType( name, settings ) );
