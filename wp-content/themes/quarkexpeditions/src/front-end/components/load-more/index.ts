/**
 * Global variables.
 */
const { customElements, HTMLElement, fetchPartial } = window;

/**
 * LoadMore Class.
 */
export class LoadMore extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
	 */
	private loadMoreButton: HTMLElement | null;
	private url: string | null;
	private partialName: string | null;
	private itemsContainerSelector: string | null;
	private itemsContainer: HTMLElement | null;
	private nextPage: number;
	private payload: any;

	/**
	 * Constructor.
	 */
	constructor() {
		// Parent.
		super();

		// Settings.
		this.url = window.quark?.fetchPartial?.url ?? '';
		this.partialName = this.getAttribute( 'partial' );
		this.itemsContainerSelector = this.getAttribute( 'selector' );
		this.payload = JSON.parse( this.getAttribute( 'payload' ) || '' );
		this.nextPage = this.payload?.page ?? 0;

		// Elements.
		this.loadMoreButton = this.querySelector( '.load-more__button' );
		this.itemsContainer = this.itemsContainerSelector ? this.querySelector( this.itemsContainerSelector ) : null;
	}

	/**
	 * Connected callback.
	 */
	connectedCallback() {
		// Events.
		this.loadMoreButton?.addEventListener( 'click', this.fetchPosts.bind( this ) );
	}

	/**
	 * Fetch posts.
	 */
	fetchPosts() {
		// Set loading and fetch partial.
		this.setAttribute( 'loading', 'true' );

		// Set load more button to inert state.
		if ( this.loadMoreButton ) {
			this.loadMoreButton.inert = true;
		}

		// Fetch partial.
		fetchPartial(
			this.partialName,
			{
				selectedFilters: {
					...this.payload,
					page: this.nextPage,
				},
				base_url: this.url,
			},
			this.updateResults.bind( this ),
			this.itemsContainerSelector,
		).catch( () => {
			// Set loading to false.
			this.setAttribute( 'loading', 'false' );

			// Set load more button to be not inert.
			if ( this.loadMoreButton ) {
				this.loadMoreButton.inert = false;
			}
		} );
	}

	/**
	 * Update results with API response.
	 *
	 * @param {Object} response Response.
	 */
	updateResults( response: PartialData ): void {
		// Set Loading to false.
		this.setAttribute( 'loading', 'false' );

		// If items container does not exist, return.
		if ( ! this.itemsContainer ) {
			// Return.
			return;
		}

		// If markup not available return.
		if ( ! response.markup ) {
			// return.
			return;
		}

		// Append the response markup.
		this.itemsContainer.innerHTML += response.markup;

		// Set the value of the nextPage.
		this.nextPage = Number( response.data.nextPage ) || 0;
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-load-more', LoadMore );
