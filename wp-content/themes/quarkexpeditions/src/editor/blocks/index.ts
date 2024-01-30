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

/**
 * Add blocks.
 */
const blocks = [
	section,
	lpHeader,
	twoColumns,
	iconInfoColumns,
];

/**
 * Register blocks.
 */
blocks.forEach( ( { name, settings } ) => registerBlockType( name, settings ) );
