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
import * as iconColumns from './icon-columns';
import * as collage from './collage';
import * as reviewCards from './review-cards';
import * as videoIconsCard from './video-icons-card';
import * as seasonHighlights from './season-highlights';
import * as simpleCards from './simple-cards';
import * as productCards from './product-cards';
import * as iconBadge from './icon-badge';
import * as lpFormModalCta from './lp-form-modal-cta';
import * as mediaContentCard from './media-content-card';
import * as button from './button';
import * as mediaTextCta from './media-text-cta';
import * as fancyVideo from './fancy-video';

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
	iconColumns,
	collage,
	reviewCards,
	videoIconsCard,
	seasonHighlights,
	simpleCards,
	productCards,
	iconBadge,
	lpFormModalCta,
	mediaContentCard,
	button,
	mediaTextCta,
	fancyVideo,
];

/**
 * Register blocks.
 */
blocks.forEach( ( { name, settings } ) => registerBlockType( name, settings ) );
