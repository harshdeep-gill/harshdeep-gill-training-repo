/**
 * TypeScript Definitions.
 *
 * Place all your global typescript interface definitions here.
 */

/**
 * Glightbox.
 */
interface Glightbox<GLightbox> {
	new( ...args: any ): GLightbox;
	openAt: Function;
	sourceElement: Object;
	on: Function;
	open: Function;
	slidesContainer: HTMLElement;
	modal: HTMLElement;
	elements: Array<Object>;
	index: number,
}

/**
 * Window Object.
 */
interface Window {
	quark?: {
		recaptchaSiteKey?: string;
		fetchPartial?: FetchPartial;
	},
	grecaptcha: {
		ready: Function;
		execute: Function;
	},
	GLightbox?: Glightbox<any>,
	Wistia: {
		api( matcher: String ) : WistiaVideo | null;
	},
	_wq: WistiaVideoMatcher[] | null,
	tpFormValidators: {
		[key: string]: any;
	},
	tpFormErrors: {
		[key: string]: any;
	},
	fetchPartial: Function;
}

/**
 * WistiaVideo interface
 */
interface WistiaVideo {
	play(): void;
	bind( eventName: String, callback: Function ): void;
	pause(): void;
}

/**
 * Video matcher interface.
 */
interface WistiaVideoMatcher { id: String, onReady: Function }

/**
 * FetchPartial interface.
 */
interface FetchPartial {
	url: string,
	method: string,
}

/**
 * Interface CustomException.
 */
interface CustomException {
	code: string;
	message: string;
	data?: any;
}

/**
 * Interface Partial Data.
 */
interface PartialData {
	markup: string;
	noResultsMarkup: string,
	data: SearchResponseData;
}

/**
 * Interface SearchResponseData
 */
interface SearchResponseData {
	resultCount?: number,
	hasNextPage?: boolean,
	nextPage?: number,
	page?: number,
}
