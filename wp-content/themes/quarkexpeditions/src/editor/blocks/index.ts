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

/**
 * Add blocks.
 */
const blocks = [
	section,
	lpHeader,
	twoColumns,
];

/**
 * Register blocks.
 */
blocks.forEach( ( { name, settings } ) => registerBlockType( name, settings ) );
