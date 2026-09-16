# Front-end libraries served by aldar-emlak.com

Spec item S4: "Front-end libraries are listed in a report only." Per the spec's non-goals they are **not upgraded** in this project. Versions come from each served file's own banner (Phase 2 research, 2026-09-15), not from `bower.json` or `package.json`.

## Public pages

Loaded on every public page by `Modules/Frontend/Resources/views/partials/scripts.blade.php` and `partials/css.blade.php`.

| Library | Version served | Served from |
|---|---|---|
| jQuery | 3.2.1 | `public/modules/frontend/js/jquery.min.js` |
| Bootstrap (JS) | 4.1.0 | `https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js` |
| Bootstrap (CSS) | 4.0.0-alpha.6 | `public/modules/frontend/css/bootstrap.css` |
| bootstrap-select (JS) | 1.13.14 | `public/modules/frontend/bootstrapselect/bootstrap-select.js` |
| bootstrap-select (CSS) | 1.13.18 | `https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.18/dist/css/bootstrap-select.min.css` |
| Swiper | 4.5.0 | `public/modules/frontend/js/swiper.min.js`, `public/modules/frontend/css/swiper.min.css` |
| Owl Carousel | 2.2.1 | `public/modules/frontend/js/owl.carousel.js` |
| Slick | no version banner (source directory `libs/slick-1.8.1`) | `public/modules/frontend/js/slick.min.js` |
| mmenu | 6.1.8 | `public/modules/frontend/js/mmenu.min.js` |
| vanilla-lazyload | 17.5.0 | `https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.5.0/dist/lazyload.min.js` |

## Staff-only admin pages

| Library | Version served | Served from |
|---|---|---|
| jQuery (Metronic plugin bundle) | 3.4.1 | `public/modules/cms/metronic/plugins/global/plugins.bundle.js` |
| DataTables | 1.10.21 | `public/modules/cms/metronic/plugins/custom/datatables/datatables.bundle.js` |
| TinyMCE | 4.7.9 | `public/modules/cms/js/tinymce/tinymce.min.js` |

## Notes

- Most of these are several major versions behind current releases. Releases of that age have had published XSS advisories, for example jQuery before 3.5.0. The advisories for each pinned version were not cross-checked against an advisory database: that belongs to a front-end upgrade project, which this project's scope excludes.
- Visitor text reaches these libraries escaped: see spec S5 and `tests/e2e/specs/security/lead-lists.spec.ts`.
- The Report-Only Content-Security-Policy (S3, `app/Http/Middleware/SecurityHeaders.php`) lists the CDN origins above.
- `npm audit` is not a gate. `tests/e2e/package.json` holds test tooling only, and the root `package.json` is Laravel Mix build tooling whose output in `public/` stays byte-identical.
- PHP dependencies are gated by `scripts/check-composer-audit.sh`: zero known advisories before any deploy.
