/**
 * WordPress dependencies.
 */
const { typenow } = window;

/**
 * Import blocks.
 */
import * as components from './components';
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
import * as header from './header';
import * as menuList from './menu-list';
import * as footer from './footer';
import * as accordion from './accordion';
import * as thumbnailCards from './thumbnail-cards';
import * as relatedPosts from './related-posts';
import * as breadcrumbs from './breadcrumbs';
import * as sidebarGrid from './sidebar-grid';
import * as authorInfo from './author-info';
import * as tableOfContents from './table-of-contents';
import * as blogPostCards from './blog-post-cards';
import * as adventureOptions from './adventure-options';
import * as itineraries from './itineraries';
import * as buttons from './buttons';
import * as staffMembers from './staff-members';
import * as includedActivities from './included-activities';
import * as tripExtensions from './trip-extensions';
import * as relatedAdventureOptions from './related-adventure-options';
import * as heroCardSlider from './hero-card-slider';
import * as highlights from './highlights';
import * as secondaryNavigation from './secondary-navigation';
import * as iconInfoGrid from './icon-info-grid';
import * as expeditionDetails from './expedition-details';
import * as ctaBanner from './cta-banner';
import * as ships from './ships';
import * as mediaDescriptionCards from './media-description-cards';
import * as FeaturedMediaAccordions from './featured-media-accordions';
import * as shipFeaturesAmenities from './ship-features-amenities';
import * as shipVesselFeatures from './ship-vessel-features';
import * as excursionAccordions from './excursion-accordions';
import * as shipRelatedAdventureOptions from './ship-related-adventure-options';
import * as mediaCarousel from './media-carousel';
import * as bookDeparturesExpeditions from './book-departures-expeditions';
import * as shipCabinCategories from './ship-cabin-categories';
import * as shipSpecifications from './ship-specifications';
import * as expeditionHero from './expedition-hero';
import * as specifications from './specifications';
import * as templateTitle from './template-title';
import * as staffMemberTitleMeta from './staff-member-title-meta';
import * as featuredImage from './featured-image';
import * as expeditions from './expeditions';
import * as bookDeparturesShip from './book-departures-ship';
import * as shipDecks from './ship-decks';
import * as InfoCards from './info-cards';
import * as linkDetailCards from './link-detail-cards';
import * as globalMessage from './global-message';
import * as datesAndRates from './dates-and-rates';
import * as mediaTextCtaCarousel from './media-text-cta-carousel';
import * as expeditionSearch from './expedition-search';
import * as pressReleases from './press-releases';
import * as tabs from './tabs';
import * as detailedExpeditionCarousel from './detailed-expedition-carousel';
import * as form from './form';
import * as formContactUs from './form-contact-us';
import * as formDoNotSellInformation from './form-do-not-sell-information';
import * as formSnowHillNewsletter from './form-snow-hill-newsletter';
import * as formJobApplication from './form-job-application';
import * as formNewsletter from './form-newsletter';
import * as formAccessDeletionRequest from './form-account-management';
import * as formCommunicationsOptIn from './form-communications-opt-in';
import * as formRequestAQuote from './form-request-quote';
import * as searchFiltersBar from './search-filters-bar';
import * as socialLinks from './social-links';
import * as currencySwitcher from './currency-switcher';
import * as searchHero from './search-hero';
import * as heroDetailsCardSlider from './hero-details-card-slider';
import * as instagramEmbed from './instagram-embed';
import * as bentoCollage from './bento-collage';

/**
 * Add blocks.
 */
let blocks = [
	components,
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
	header,
	menuList,
	footer,
	accordion,
	thumbnailCards,
	relatedPosts,
	breadcrumbs,
	sidebarGrid,
	authorInfo,
	tableOfContents,
	blogPostCards,
	adventureOptions,
	itineraries,
	buttons,
	staffMembers,
	includedActivities,
	tripExtensions,
	relatedAdventureOptions,
	heroCardSlider,
	highlights,
	secondaryNavigation,
	iconInfoGrid,
	expeditionDetails,
	ctaBanner,
	ships,
	mediaDescriptionCards,
	FeaturedMediaAccordions,
	shipFeaturesAmenities,
	shipVesselFeatures,
	excursionAccordions,
	shipRelatedAdventureOptions,
	mediaCarousel,
	bookDeparturesExpeditions,
	expeditionHero,
	specifications,
	templateTitle,
	staffMemberTitleMeta,
	featuredImage,
	expeditions,
	shipDecks,
	InfoCards,
	linkDetailCards,
	globalMessage,
	datesAndRates,
	mediaTextCtaCarousel,
	expeditionSearch,
	pressReleases,
	tabs,
	detailedExpeditionCarousel,
	form,
	formContactUs,
	formDoNotSellInformation,
	formSnowHillNewsletter,
	formJobApplication,
	formNewsletter,
	formAccessDeletionRequest,
	formCommunicationsOptIn,
	formRequestAQuote,
	searchFiltersBar,
	socialLinks,
	currencySwitcher,
	searchHero,
	heroDetailsCardSlider,
	instagramEmbed,
	bentoCollage,
];

// Register blocks for ships.
if ( typenow && 'qrk_ship' === typenow ) {
	blocks.push( shipCabinCategories );
	blocks.push( shipSpecifications );
	blocks.push( bookDeparturesShip );
}

// Check if the block should be disabled on the China site.
if ( window?.quarkSiteData && window.quarkSiteData?.isChinaSite ) {
	// List of blocks to disable on the China site.
	const disableOnChina = [
		form,
		formContactUs,
		formDoNotSellInformation,
		formSnowHillNewsletter,
		formJobApplication,
		formNewsletter,
		formAccessDeletionRequest,
		formCommunicationsOptIn,
		formRequestAQuote,
		relatedPosts,
		bookDeparturesExpeditions,
		datesAndRates,
		bookDeparturesShip,
		blogPostCards,
	];

	// Remove the blocks from the list of blocks to register.
	blocks = blocks.filter( ( block ) => ! disableOnChina.includes( block ) );
}

/**
 * Register blocks.
 */
blocks.forEach( ( { init } ) => init() );
