# Preparedness Education Network — WordPress Theme

A classic WordPress theme for **preparednesseducation.network**. It is built around training classes and preparedness education, with a rugged olive, gunmetal and blaze-orange look.

## Features

| Feature | Where it lives |
| --- | --- |
| Dismissible announcement bar | Customize → PEN Theme Options → Announcement Bar |
| Sticky dark header with CTA button, search, mobile off-canvas menu | `header.php`, Customize → Header |
| Cart and account icons (when WooCommerce is active) | `inc/woocommerce/template-functions.php` |
| Full-bleed hero with two CTAs | Customize → Home: Hero |
| Stats / trust strip | Customize → Home: Stats Strip |
| Training-track tiles (icon, image, link) | Customize → Home: Training Tracks |
| **Class schedule** with date blocks, price, skill level, seats-left badges, sold-out state and register buttons | *Classes* post type, `/classes/` |
| Track filters and a past-classes view | `/classes/`, `/track/<slug>/`, `/classes/?when=past` |
| Class detail page with a sticky booking box, prerequisites, what-to-bring checklist and instructors | `single-pen_course.php` |
| Instructor directory and bios, each listing that instructor's upcoming classes | *Instructors* post type, `/instructors/` |
| Star-rated testimonials | *Testimonials* post type |
| Mission split section with checklist | Customize → Home: Mission |
| Resource library (blog) cards | Posts |
| Featured gear grid | WooCommerce (optional), `inc/woocommerce/template-parts/home-shop.php` |
| Newsletter / lead-magnet band (Mailchimp, ConvertKit, etc.) | Customize → Newsletter |
| Footer with about text, social icons, three widget columns, disclaimer and legal menu | Customize → Footer / Social Links, Widgets |
| Site colors (13 colors, presets, contrast checks, live preview) | Appearance → Theme Settings → Colors, or Customize → PEN Theme Options → Colors |
| Sidebars for posts, pages and archives (on/off, left/right, per-post override) | Appearance → Theme Settings → Layout & Sidebars |
| Multisite network defaults and locks | Network Admin → Themes → PEN Theme Defaults |
| WooCommerce, FunnelKit, Elementor and Amelia integrations | Appearance → Theme Settings → Integrations |
| Distraction-free (logo-only) header and footer on checkout and funnel steps | Theme Settings → Integrations |

## Stylesheets

`style.css` holds only the theme header. The styles are split by area in `assets/css/` and load in this order: `tokens`, `base`, `buttons`, `header`, `hero`, `cards`, `schedule`, `sections`, `content`, `footer`, `wordpress`. Three more load only when their plugin is active: `woocommerce`, `elementor` and `amelia` (Amelia's only while its theme matching is on). Admin screen styles are in `assets/css/admin/`. Colors from Theme Settings are printed as CSS custom properties after `tokens.css`, so they override its defaults. To add or reorder parts, filter `pen_style_parts`.

## Theme Settings

**Appearance → Theme Settings** has three tabs.

- **Layout & Sidebars** controls posts, pages, and the blog/archives/search separately. For each one, choose whether a sidebar shows and whether it sits on the left or right. Posts and archives use the *Post & Blog Sidebar* widget area. Pages use the *Page Sidebar* widget area. A sidebar only appears when its widget area has widgets. Each post and page also has a **Sidebar** box on its edit screen (Theme default / Show / No sidebar). The front page, classes, instructors, WooCommerce pages and the *Full Width* template keep their own layouts.
- **Colors** sets 13 site colors: accent, accent hover, text on accent, secondary, muted accent, header and footer, dark panels, page background, cards, alternate sections, text, secondary text and borders. It includes four presets, a live preview and readability (contrast) checks. A blank color uses the default. The same colors can be edited in the Customizer with live preview.
- **Integrations** shows whether WooCommerce, FunnelKit, Elementor and Amelia are active (with install and activate links) and holds their settings. See *Plugin integrations* below.

Settings are stored in the `pen_settings` option. Read a value with `pen_setting( 'color_accent' )`.

## Plugin integrations

Each integration is optional. Its code only runs while its plugin is active.

### WooCommerce (`inc/woocommerce/`)

- **Update-safe.** The theme ships no WooCommerce template overrides (there is no `/woocommerce/` folder). Shop, product, cart, checkout and account pages are styled with WooCommerce's hooks and CSS only. WooCommerce updates can't leave the theme with outdated templates, and the Integrations tab confirms this on your site. Keep it that way: make layout changes with hooks in a child theme rather than copying WooCommerce templates.
- **Product image sizes and grid.** The default image widths and grid are declared in `setup.php`. They stay editable under Customize → WooCommerce.
- **Styling.** Shop grid, product pages, notices, forms, cart, checkout (classic and block-based) and My Account follow the theme colors, fonts and knife-edge buttons (`assets/css/woocommerce.css`).
- **Header icons.** Account and cart icons sit in the header, with a live cart count.
- **Homepage gear section.** A featured gear grid appears on the front page.
- **Distraction-free checkout.** Checkout uses a logo-only header with a "Secure checkout" label and a minimal footer (Integrations → Checkout page header).

| File | Purpose |
| --- | --- |
| `woocommerce.php` | Loader; loads the rest only when WooCommerce is active |
| `setup.php` | Theme support, image sizes, stylesheet, body class |
| `hooks.php` | Content wrappers, breadcrumbs, related products and upsells, checkout header, template-override check |
| `template-functions.php` | Header icons, cart count, front-page gear section |
| `template-parts/home-shop.php` | Front-page gear grid (uses WooCommerce's `[products]` shortcode) |
| `funnelkit.php` | FunnelKit funnel steps (loads even without WooCommerce) |

### FunnelKit (`inc/woocommerce/funnelkit.php`)

FunnelKit funnel steps are sales, opt-in, checkout, one-click upsell and thank-you pages. On them the theme shows only the step's own content:

- no page banner, sidebar, navigation, announcement bar or breadcrumbs,
- a logo-only header and minimal footer (Integrations → Funnel step header can switch to the full header),
- for a completely blank page, choose FunnelKit's *Canvas* template on the step.

FunnelKit's checkout designs keep their own form styles. The step post types are listed in `pen_funnel_post_types()` and can be changed with the `pen_funnel_post_types` filter.

### Elementor (`inc/integrations/elementor.php`)

- **Default editor.** "Add New" for pages, posts, classes and instructors opens Elementor. "Add New (Block Editor)" stays in each menu. Switch the default back under Integrations.
- **Full-width layouts.** Pages built with Elementor run full width under the theme header, with no banner, container or sidebar.
- **Elementor Pro.** The Theme Builder can replace the header and footer, since all core locations are registered.
- **Global kit.** Elementor's global colors and fonts follow Theme Settings: Primary = Header & footer, Secondary = Secondary, Text = Text, Accent = Accent, with Oswald and Inter fonts and a 1240px content width. The rest of the theme palette is added as "PEN" custom colors. Any custom colors you add in Elementor are kept. Turn this off under Integrations to manage Elementor's globals yourself.
- **Buttons.** Elementor buttons get the knife-edge shape (`assets/css/elementor.css`). Per-widget settings still win.

### Amelia (`inc/integrations/amelia.php`)

- **Class booking.** In a class's details, set **Amelia event ID**. The class page then shows Amelia's event booking form under "Reserve Your Seat", and every Register button for that class jumps to it.
- **Private sessions.** In an instructor's details, set **Amelia employee ID** (and optionally **Amelia service ID**). Their page then shows a "Book a Private Session" form.
- **Matching style.** Booking forms use the theme colors and fonts (`assets/css/amelia.css`). The Integrations tab also lists the matching values to enter in Amelia → Customize, for Amelia screens the theme styles don't reach, such as emails and the customer panel.
- **Shortcodes.** The newer Amelia 2.x shortcodes are used when available (`ameliaeventslistbooking`, `ameliastepbooking`), with fallbacks to the older ones (`ameliaevents`, `ameliabooking`).
- **Payments.** Amelia can take payment through WooCommerce, which sends bookings through the WooCommerce checkout (and FunnelKit's, if you use it).

### Hooks for developers

| Hook | Use |
| --- | --- |
| `pen_bare_content` (filter) | Render a singular view without banner, container or sidebar |
| `pen_minimal_header` (filter) | Use the logo-only header and minimal footer |
| `pen_sidebar_context` (filter) | Change or remove the sidebar for a view |
| `pen_header_actions` (action) | Add icons to the header |
| `pen_front_page_sections` (filter), `pen_home_section_{name}` (action) | Add or render front-page sections |
| `pen_course_data` (filter) | Change class details, such as the Register link |
| `pen_course_after_content`, `pen_instructor_after_content` (actions) | Add sections to class and instructor pages |
| `pen_meta_fields` (filter) | Add fields to the Class, Instructor and Testimonial boxes |
| `pen_style_parts` (filter) | Add or reorder stylesheet parts |
| `pen_funnel_post_types`, `pen_amelia_shortcodes` (filters) | Adjust the FunnelKit and Amelia integrations |

## Multisite

The theme works on a WordPress multisite network. Settings are per site, and each site's content, widgets and settings stay separate.

- A super admin can set network defaults under **Network Admin → Themes → PEN Theme Defaults**. Every site that leaves a setting on *Default* (or a color blank) uses the network value, including sites created later.
- **Lock colors** and **Lock layout** force every site to use the network values. Site admins then see those settings as read-only.
- Values resolve as: site setting → network default → theme default.
- Network-enable the theme under **Network Admin → Themes**. The network page appears in Network Admin when the main site uses this theme, because Network Admin loads the main site's theme.

## Class fields

Each class has these fields: start date, end date, time, duration, location, price, capacity, seats left, skill level, registration URL, what to bring, prerequisites and instructors, plus an Amelia event ID when Amelia is active. Point **Registration URL** at a WooCommerce product, a booking tool or a form. If you leave it empty, the Register button links to the class page. When seats left is set to `0`, the class shows as sold out.

## Install

1. Zip this folder (without `bin/` if you like) and upload it under **Appearance → Themes → Add New → Upload**, or copy it to `wp-content/themes/preparedness-network`.
2. Activate it, then go to **Settings → Permalinks** and click Save.
3. Set a static front page under **Settings → Reading**. The front-page template renders every section automatically.
4. Assign a menu to **Primary Menu**.

## Local test server

```bash
bin/test-server.sh              # http://localhost:8080  (admin / admin)
MULTISITE=1 bin/test-server.sh  # multisite network with a second site at /second/
WITH_WOOCOMMERCE=1 bin/test-server.sh  # also install WooCommerce with demo products
```

This script installs WordPress 7.1 with SQLite next to the repo (`../pen-test-site`), links the theme, loads demo content from `bin/seed-demo.php` and starts PHP's built-in server. You need `php` with `pdo_sqlite`, `git` and `curl`. With `MULTISITE=1`, the script sets up a subdirectory network with a second site at `/second/`. The network gets its own folder, `../pen-test-network`. `WITH_WOOCOMMERCE=1` downloads WooCommerce from its GitHub releases. WooCommerce doesn't officially support SQLite, so its background tasks may log database errors on this test server; a real site on MySQL won't have them.
