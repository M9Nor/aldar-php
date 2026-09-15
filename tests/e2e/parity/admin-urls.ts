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
