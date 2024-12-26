/**
 * TypeScript Definitions.
 *
 * Place all your global typescript interface definitions here.
 */

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
	zustand: {
		persist: Function;
		create: Function;
		stores: {
			[key: string]: {
				getState: Function;
				setState: Function;
				subscribe: Function;
			};
		};
	};
	queryString?: QueryString;
	YT?: typeof YT;
	onYouTubeIframeAPIReady: Function;
}

/**
 * YouTube Player API.
 */
declare namespace YT {
	interface PlayerOptions {
		height: string;
		width: string;
		videoId: string;
		events?: {
			onReady?: Function;
			onStateChange?: Function;
		};
	}

	/**
	 * YouTube Player Class.
	 */
	class Player {
		/**
		 * Constructs new YouTube Player Instance.
		 *
		 * @param id The ID of the DOM element that is to be attached to the player.
		 * @param options The configuration options for the player.
		 */
		constructor( id: string, options: YT.PlayerOptions );

		playVideo(): void;
	}

	const PlayerState: {
		UNSTARTED: -1,
		ENDED: 0,
		PLAYING: 1,
		PAUSED: 2,
		BUFFERING: 3,
		QUEUED: 4,
	};
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
	noResultsMarkup?: string,
	data: any;
	filtersMarkup?: string;
}

/**
 * Interface Query String.
 */
interface QueryString {
	stringifyUrl: Function,
	stringify: Function;
	parse: Function;
}

// Global state type.
interface GlobalState {
	currency: string,
	phoneNumberRule: {
		phoneNumber: string,
		prefix: string,
	},
	dynamicPhoneNumber: {
		apiEndpoint: string,
		defaultPhoneNumber: string,
	},
}
