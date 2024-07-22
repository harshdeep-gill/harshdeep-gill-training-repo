/**
 * DomContentLoaded.
 */
class DomContentLoaded {
	/**
	 * Constructor.
	 */
	constructor() {
		// Add Event.
		window.addEventListener( 'DOMContentLoaded', () => this.addClassOnDomLoaded() );
	}

	/**
	 * Add a class to html on dom loaded.
	 */
	addClassOnDomLoaded() {
		// We add this class, which is used to add smooth scroll styles, when DOM content is loaded.
		document.documentElement.classList.add( 'dom-content-loaded' );
	}
}

/**
 * Instantiate Tracking.
 */
const domContentLoaded = new DomContentLoaded();
export default domContentLoaded;
