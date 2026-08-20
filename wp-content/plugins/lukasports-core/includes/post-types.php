<?php
/**
 * No custom post types in Phase 1. Leads and design requests are
 * planned as a custom table (wp_lukasports_leads, see Phase 0 DB design)
 * rather than a CPT, since they need fast filtering by status/source/
 * date that would otherwise mean querying wp_postmeta. Kept as an
 * empty module — not a stub function — so Phase 3 has an obvious home
 * for any CPT that does turn out to be needed (e.g. a design-request
 * gallery) without disturbing the bootstrap list in lukasports-core.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
