/**
 * WordPress dependencies.
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Import blocks.
 */
import * as section from './section';

/**
 * Add blocks.
 */
const blocks = [
	section,
];

/**
 * Register blocks.
 */
blocks.forEach( ( { name, settings } ) => registerBlockType( name, settings ) );
