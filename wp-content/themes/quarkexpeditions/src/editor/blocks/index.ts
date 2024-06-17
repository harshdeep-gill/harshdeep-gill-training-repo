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
import * as productDeparturesCard from './product-departures-card';
import * as contactCoverCard from './contact-cover-card';
import * as offerCards from './offer-cards';
import * as lpOfferMasthead from './lp-offer-masthead';
import * as mediaTextCta from './media-text-cta';
import * as fancyVideo from './fancy-video';
import * as formTwoStep from './form-two-step';
import * as formTwoStepCompact from './form-two-step-compact';
<<<<<<< HEAD
import * as accordion from './accordion';
=======
import * as thumbnailCards from './thumbnail-cards';
>>>>>>> c7da07f873e53a9e024e6d0137f21547d4b5657d

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
	productDeparturesCard,
	contactCoverCard,
	offerCards,
	lpOfferMasthead,
	mediaTextCta,
	fancyVideo,
	formTwoStep,
	formTwoStepCompact,
	accordion,
	thumbnailCards,
];

/**
 * Register blocks.
 */
blocks.forEach( ( { name, settings } ) => registerBlockType( name, settings ) );
