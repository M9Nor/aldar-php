// Admin URLs for the parity suite. Model ids come from the committed dump
// (_db-backup/aldar-db-20260914-1704.sql.gz) and name public records only.

export const CONTENT_TYPES = [
  'sliders', 'stories', 'articles', 'faqs', 'playlist_videos', 'agents', 'testimonials', 'services', 'achievements',
  'pages', 'offices', 'filters', 'advertisements', 'currencies', 'first_banners', 'second_banners', 'balance',
];

export const CATEGORY_TYPES = [
  'articles', 'agents', 'faqs', 'filters', 'playlist_videos', 'contracts', 'property_classifications',
  'opportunity_classifications', 'property_status', 'property_features', 'facilities', 'payments',
];

export interface AdminSection {
  name: string;
  url: string;
  /** The table lists customer or staff data: record its shape, never its size. */
  structureOnly?: boolean;
}

export const ADMIN_SECTIONS: AdminSection[] = [
  { name: 'projects', url: '/en/admin/projects' },
  { name: 'opportunities', url: '/en/admin/opportunity' },
  { name: 'project-requests', url: '/en/admin/projects/requests', structureOnly: true },
  { name: 'project-property-forms', url: '/en/admin/projects/properties', structureOnly: true },
  { name: 'opportunity-requests', url: '/en/admin/opportunity/requests', structureOnly: true },
  { name: 'opportunity-property-forms', url: '/en/admin/opportunity/properties', structureOnly: true },
  ...CONTENT_TYPES.map(type => ({ name: `contents-${type}`, url: `/en/admin/contents/${type}` })),
  ...CATEGORY_TYPES.map(type => ({ name: `categories-${type}`, url: `/en/admin/categories/${type}` })),
  { name: 'cities', url: '/en/admin/cities' },
  { name: 'areas', url: '/en/admin/areas' },
  { name: 'countries', url: '/en/admin/countries' },
  { name: 'tags', url: '/en/admin/tags' },
  { name: 'configs', url: '/en/admin/configs' },
  { name: 'users', url: '/en/admin/users', structureOnly: true },
  { name: 'roles', url: '/en/admin/roles' },
  { name: 'landing-pages', url: '/en/admin/landing_pages' },
  { name: 'notifications', url: '/en/admin/notification' },
];

const crud = (base: string, id: number) => [base, `${base}/data`, `${base}/create`, `${base}/${id}/edit`];

/** Every admin GET route, with ids filled in. */
export const MATRIX_URLS: string[] = [
  '/en/admin',
  '/en/admin/users', '/en/admin/users/data', '/en/admin/users/create', '/en/admin/users/summary?model=31',
  '/en/admin/users/show', '/en/admin/users/myprofile', '/en/admin/users/31/edit',
  '/en/admin/users/identity/validate?name=username&keyword=parity-probe',
  '/en/admin/users/identity/validate_?name=username&keyword=parity-probe', '/en/admin/users/get-user-select2',
  ...CONTENT_TYPES.flatMap(type => [`/en/admin/contents/${type}`, `/en/admin/contents/${type}/data`, `/en/admin/contents/${type}/create`]),
  '/en/admin/contents/articles/251/edit',
  ...CATEGORY_TYPES.flatMap(type => [`/en/admin/categories/${type}`, `/en/admin/categories/${type}/data`, `/en/admin/categories/${type}/create`, `/en/admin/categories/${type}/get-categories`]),
  '/en/admin/categories/contracts/318/edit',
  ...crud('/en/admin/landing_pages', 11),
  '/en/admin/tags/list', ...crud('/en/admin/tags', 145),
  ...crud('/en/admin/areas', 98),
  ...crud('/en/admin/cities', 12),
  ...crud('/en/admin/countries', 2),
  ...crud('/en/admin/configs', 49), '/en/admin/configs/49/edit-config',
  ...(['projects', 'opportunity'] as const).flatMap(section => [
    ...crud(`/en/admin/${section}`, section === 'projects' ? 133 : 320),
    `/en/admin/${section}/show`, `/en/admin/${section}/requests`, `/en/admin/${section}/data_requests`, `/en/admin/${section}/request_summary`,
    `/en/admin/${section}/properties`, `/en/admin/${section}/data_properties`, `/en/admin/${section}/properties_summary`, `/en/admin/${section}/show_details/0`,
  ]),
  '/en/admin/roles', '/en/admin/roles/data', '/en/admin/roles/create', '/en/admin/roles/show', '/en/admin/roles/3/edit',
  '/en/admin/notification', '/en/admin/notification/config', '/en/admin/notification/create',
];

/**
 * Admin GET routes deliberately left out of the matrix, and why. Empty since Phase 2: the four admin GET routes
 * that changed state are POST-only (clear-cache, users/login_as/{model}; S7, S10) or removed
 * (projects/update_prices, categories/asdwadwadwdaw; S10). See specs/security/maintenance-routes.spec.ts and
 * specs/security/state-changing-gets.spec.ts.
 */
export const MATRIX_EXCLUDED: Record<string, string> = {};
