# Preparedness Education Network — WordPress Theme

A classic WordPress theme for **preparednesseducation.network**. It is built around training classes and preparedness education, with a rugged olive, gunmetal and blaze-orange look.

## Features

| Feature | Where it lives |
| --- | --- |
| Dismissible announcement bar | Customize → PEN Theme Options → Announcement Bar |
| Sticky dark header with CTA button, search, mobile off-canvas menu | `header.php`, Customize → Header |
| Cart and account icons (when WooCommerce is active) | `header.php` |
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
| Featured gear grid | WooCommerce (optional) |
| Newsletter / lead-magnet band (Mailchimp, ConvertKit, etc.) | Customize → Newsletter |
| Footer with about text, social icons, three widget columns, disclaimer and legal menu | Customize → Footer / Social Links, Widgets |
| Site colors (13 colors, presets, contrast checks, live preview) | Appearance → Theme Settings → Colors, or Customize → PEN Theme Options → Colors |
| Sidebars for posts, pages and archives (on/off, left/right, per-post override) | Appearance → Theme Settings → Layout & Sidebars |
| Multisite network defaults and locks | Network Admin → Themes → PEN Theme Defaults |

## Stylesheets

`style.css` holds only the theme header. The styles are split by area in `assets/css/` and load in this order: `tokens`, `base`, `buttons`, `header`, `hero`, `cards`, `schedule`, `sections`, `content`, `footer`, `wordpress`. Colors from Theme Settings are printed as CSS custom properties after `tokens.css`, so they override its defaults. To add or reorder parts, filter `pen_style_parts`.

## Theme Settings

**Appearance → Theme Settings** has two tabs.

- **Layout & Sidebars** controls posts, pages, and the blog/archives/search separately. For each one, choose whether a sidebar shows and whether it sits on the left or right. Posts and archives use the *Post & Blog Sidebar* widget area. Pages use the *Page Sidebar* widget area. A sidebar only appears when its widget area has widgets. Each post and page also has a **Sidebar** box on its edit screen (Theme default / Show / No sidebar). The front page, classes, instructors, WooCommerce pages and the *Full Width* template keep their own layouts.
- **Colors** sets 13 site colors: accent, accent hover, text on accent, secondary, muted accent, header and footer, dark panels, page background, cards, alternate sections, text, secondary text and borders. It includes four presets, a live preview and readability (contrast) checks. A blank color uses the default. The same colors can be edited in the Customizer with live preview.

Settings are stored in the `pen_settings` option. Read a value with `pen_setting( 'color_accent' )`.

## Multisite

The theme works on a WordPress multisite network. Settings are per site, and each site's content, widgets and settings stay separate.

- A super admin can set network defaults under **Network Admin → Themes → PEN Theme Defaults**. Every site that leaves a setting on *Default* (or a color blank) uses the network value, including sites created later.
- **Lock colors** and **Lock layout** force every site to use the network values. Site admins then see those settings as read-only.
- Values resolve as: site setting → network default → theme default.
- Network-enable the theme under **Network Admin → Themes**. The network page appears in Network Admin when the main site uses this theme, because Network Admin loads the main site's theme.

## Class fields

Each class has these fields: start date, end date, time, duration, location, price, capacity, seats left, skill level, registration URL, what to bring, prerequisites and instructors. Point **Registration URL** at a WooCommerce product, a booking tool or a form. If you leave it empty, the Register button links to the class page. When seats left is set to `0`, the class shows as sold out.

## Install

1. Zip this folder (without `bin/` if you like) and upload it under **Appearance → Themes → Add New → Upload**, or copy it to `wp-content/themes/preparedness-network`.
2. Activate it, then go to **Settings → Permalinks** and click Save.
3. Set a static front page under **Settings → Reading**. The front-page template renders every section automatically.
4. Assign a menu to **Primary Menu**.

## Local test server

```bash
bin/test-server.sh              # http://localhost:8080  (admin / admin)
MULTISITE=1 bin/test-server.sh  # multisite network with a second site at /second/
```

This script installs WordPress with SQLite next to the repo (`../pen-test-site`), links the theme, loads demo content from `bin/seed-demo.php` and starts PHP's built-in server. You need `php` with `pdo_sqlite`, `git` and `curl`. With `MULTISITE=1`, the script sets up a subdirectory network with a second site at `/second/`. The network gets its own folder, `../pen-test-network`.
