/**
 * WordPress dependencies.
 */
import {
	BlockConfiguration,
	registerBlockType,
} from '@wordpress/blocks';

/**
 * Internal dependencies.
 */
import metadata from './block.json';
import edit from './edit';
import save from './save';

/**
 * Styles.
 */
import '../../../front-end/components/review-cards/style.scss';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	...metadata,
	edit,
	save,
};

/**
 * Children.
 */
import * as child from './children/item';
import * as title from './children/title';
import * as review from './children/review';
import * as rating from './children/rating';
import * as author from './children/author';
import * as authorDetails from './children/author-details';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register children.
	registerBlockType( child.name, child.settings );
	registerBlockType( title.name, title.settings );
	registerBlockType( review.name, review.settings );
	registerBlockType( rating.name, rating.settings );
	registerBlockType( author.name, author.settings );
	registerBlockType( authorDetails.name, authorDetails.settings );
};
