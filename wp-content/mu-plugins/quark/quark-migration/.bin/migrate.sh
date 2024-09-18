##
## Migration Scripts.
##

# Media Migration
wp quark-migrate media all
wp quark-migrate media pdf_files --user=1

# Taxonomy Migration
wp quark-migrate taxonomy all --user=1

# Port terms migration
wp quark-migrate port all --user=1

# Blog Post Migration
wp quark-migrate blog authors --user=1
wp quark-migrate blog posts --user=1

# Press Release Migration
wp quark-migrate press-release all --user=1

# Terms and Condition pages Migration
wp quark-migrate policy-page all --user=1

# Region Landing page Migration
wp quark-migrate region all --user=1

# Pre-post trip options Migration
wp quark-migrate post-trip-options all --user=1

# Inclusion and Exclusion sets Migration
wp quark-migrate inclusion-exclusion-sets all --user=1

# Adventure Options Migration
wp quark-migrate adventure-option all --user=1

# Ship migration
wp quark-migrate ship all --user=1

# Ship deck migration
wp quark-migrate ship-deck all --user=1

# Cabin Categories migration
wp quark-migrate cabin-category all --user=1

# Staff Member migration
wp quark-migrate staff-member all --user=1

# Itinerary Days migration
wp quark-migrate itinerary-day all --user=1

# Itinerary migration
wp quark-migrate itinerary all --user=1

# Expedition migration
wp quark-migrate expedition all --user=1

# Departure migration
wp quark-migrate departure all --user=1

# Marketing Promotion Landing Page migration
wp quark-migrate offer all --user=1

# Landing pages migration
wp quark-migrate landing-page all --user=1

# SEO redirect migration
wp quark-migrate seo redirect-new-permalinks
wp quark-migrate seo url-redirects
wp quark-migrate seo update-internal-links

