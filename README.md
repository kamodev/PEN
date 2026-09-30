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
| Brand colors | Customize → Brand Colors |

## Stylesheets

`style.css` holds only the theme header. The styles are split by area in `assets/css/` and load in this order: `tokens`, `base`, `buttons`, `header`, `hero`, `cards`, `schedule`, `sections`, `content`, `footer`, `wordpress`. Brand colors from the Customizer override `tokens.css`. To add or reorder parts, filter `pen_style_parts`.

## Class fields

Each class has these fields: start date, end date, time, duration, location, price, capacity, seats left, skill level, registration URL, what to bring, prerequisites and instructors. Point **Registration URL** at a WooCommerce product, a booking tool or a form. If you leave it empty, the Register button links to the class page. When seats left is set to `0`, the class shows as sold out.

## Install

1. Zip this folder (without `bin/` if you like) and upload it under **Appearance → Themes → Add New → Upload**, or copy it to `wp-content/themes/preparedness-network`.
2. Activate it, then go to **Settings → Permalinks** and click Save.
3. Set a static front page under **Settings → Reading**. The front-page template renders every section automatically.
4. Assign a menu to **Primary Menu**.

## Local test server

```bash
bin/test-server.sh        # http://localhost:8080  (admin / admin)
```

This script installs WordPress with SQLite next to the repo (`../pen-test-site`), links the theme, loads demo content from `bin/seed-demo.php` and starts PHP's built-in server. You need `php` with `pdo_sqlite`, `git` and `curl`.
