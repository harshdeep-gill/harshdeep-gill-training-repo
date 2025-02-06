/**
 * BlockEditAttributes.
 */
interface BlockEditAttributes {
	className: string,
	attributes: Record<string, any>,
	setAttributes: Function;
	isSelected: boolean;
	clientId: string;
	context: Record<string, any>;
}

/**
 * Icons.
 */
interface Icons {
    [key: string]: any;
}

/**
 * Svgs.
 */
interface Svgs {
	[key: string]: JSX.Element;
}

/**
 * Window Object.
 */
interface Window {
	gumponents: {
		components: {
			Img: typeof React.Component,
			Figure: typeof React.Component,
			SelectImage: typeof React.Component,
			LinkButton: typeof React.Component,
			PostRelationshipControl: typeof React.Component,
			TaxonomyRelationshipControl: typeof React.Component,
			ImageControl: typeof React.Component,
			FocalPointPickerControl: typeof React.Component,
			FileControl: typeof React.Component,
			MultiSelectControl: typeof React.Component,
			LinkControl: typeof React.Component,
			GalleryControl: typeof React.Component,
			ColorPaletteControl: typeof React.Component,
			RelationshipControl: typeof React.Component,
		}
	},
	typenow: string,
	travelopiaMedia: {
		DynamicImage: new () => React.Component<any>,
		getDynamicImageUrl: Function,
	},
	quarkSiteData: {
		isChinaSite: boolean,
	},
}
