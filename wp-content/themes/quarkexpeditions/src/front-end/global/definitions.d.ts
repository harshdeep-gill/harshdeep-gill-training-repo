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
	},
	grecaptcha: {
		ready: Function;
		execute: Function;
	},
	GLightbox?: Glightbox<any>,
}
