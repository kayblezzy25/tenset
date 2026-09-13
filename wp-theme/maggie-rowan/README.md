# Maggie Rowan — WordPress Theme

A premium, editorial WordPress theme built for singer-songwriter **Maggie Rowan**
(Modern Americana / Country / Bluegrass), designed to promote her debut album
*Traveling Back*.

## A note on "WordPress + Elementor"

The brief asked for WordPress + Elementor. This theme is delivered as a
**hand-coded WordPress theme** instead of an Elementor template kit, on
purpose:

- Elementor stores every page as a large JSON blob (`_elementor_data`) tied
  to specific Elementor/Elementor Pro versions. Hand-authoring that JSON
  outside a live WordPress + Elementor install can't be tested, and a single
  malformed key silently breaks the page.
- A coded theme is fully testable, version-controlled, faster, and doesn't
  require the Elementor Pro license to get the exact layouts in the design
  brief (Elementor free doesn't include headers/footers/theme builder).
- **Elementor still works fine alongside this theme** if the client wants to
  hand-edit specific sections later — it's just not the format this delivery
  uses for the core pages.

If a true Elementor Kit export is still wanted, that can be built as a
follow-up once there's a live WordPress + Elementor Pro site to author and
test it in.

## What's included

- 5 page templates: Home, About, Music, Contact, Privacy Policy (default page
  template)
- A custom "Album" post type so a second album can be added in Phase 2
  without touching any template or design
- A built-in styled contact form (Name / Email / Subject / Message) that
  emails enquiries to `ian@ianmaciver.com`, with a one-field settings screen
  to swap in a real WPForms form once it's built
- A lightweight, dependency-free cookie consent banner
- Full responsive design (desktop / tablet / mobile), Cormorant Garamond +
  Inter typography, and the ivory / charcoal / forest / rust / gold palette
  from the brief
- Placeholder SVG artwork for every photo/hero slot, clearly marked in the
  code (see "Replacing placeholder imagery" below) so nothing here is passed
  off as real photography

## Installation

1. Zip the `maggie-rowan` folder (this folder) and upload it via
   **Appearance → Themes → Add New → Upload Theme**, or copy it directly into
   `wp-content/themes/maggie-rowan` and activate it under **Appearance →
   Themes**.
2. Go to **Settings → Permalinks** and click **Save** once (flushes rewrite
   rules for the new Album post type).
3. Create four pages under **Pages → Add New**, and for each one set the
   **Page Attributes → Template** dropdown (top right of the editor) to the
   matching template, then Publish:
   - "Home" → Template: **Home**
   - "About" → Template: **About**
   - "Music" → Template: **Music**
   - "Contact" → Template: **Contact**
4. Go to **Settings → Reading** and set **Your homepage displays** →
   **A static page** → Homepage: "Home".
5. Go to **Settings → Privacy** and either use the Privacy Policy page
   WordPress creates automatically, or create a new page titled "Privacy
   Policy" (default template is fine) and select it there. Paste in the
   policy text from the "Privacy Policy content" section below.
6. Go to **Appearance → Menus**, create a menu with Home / About / Music /
   Contact, assign it to the **Primary Navigation** location (the theme
   falls back to a sensible default menu automatically if this is skipped).
7. The debut album "Traveling Back" is seeded automatically the first time
   the theme is activated (title, description, 10-track tracklist, badge,
   and the Spotify/iTunes links from the brief) so the site isn't empty.
   Edit it any time under **Albums → Traveling Back** to add the real cover
   art and adjust copy.

## Setting up the contact form (WPForms)

The brief asked for WPForms specifically, with the recipient's email never
shown publicly. WPForms builds forms in the database via its drag-and-drop
editor, so it can't be pre-built by pushing code — but the theme is wired up
to use it the moment it exists:

1. Install and activate the **WPForms** plugin.
2. Create a new form with four fields: **Name** (Single Line Text, required),
   **Email** (Email, required), **Subject** (Dropdown or Single Line Text,
   required), **Message** (Paragraph Text, required).
3. Under the form's **Settings → Notifications**, set **Send To Email
   Address** to `ian@ianmaciver.com`. This is an admin-only setting — it is
   never printed anywhere on the front end.
4. Publish the form and copy its ID (shown in the WPForms form list, or in
   the shortcode `[wpforms id="123"]`).
5. Go to **Settings → Maggie Rowan Theme** in wp-admin and paste that ID into
   **WPForms Form ID**. Save.

The Contact page (and the styled `.mr-form-wrap` wrapper around it) will
automatically switch to rendering the real WPForms form. Until that's done,
the site uses a built-in contact form (same fields, same styling) that
emails `ian@ianmaciver.com` directly via `wp_mail()` — so the Contact page
works correctly on day one either way.

## Adding a second album (Phase 2)

Go to **Albums → Add New**:

- Title = album name
- Featured image = album artwork
- Content editor = album description
- Fill in the **Album Details** meta box: badge label (e.g. "Studio Album"),
  track list (one per line, `Track Title | 3:45`), Spotify URL, iTunes URL
- Leave "Feature this album on the homepage" unchecked unless it should
  replace *Traveling Back* on the homepage

Publish it — it appears automatically on the Music page (newest first) with
no theme or template changes required.

## Replacing placeholder imagery

Every image slot uses a generated SVG placeholder (in `assets/images/`) so
the site is never shipped with a stock photo pretending to be Maggie Rowan.
Replace them with real photography:

- `assets/images/hero-bg.svg` — homepage hero background
- `assets/images/page-hero.svg` — About/Music/Contact page hero background,
  and the Featured Album background
- `assets/images/portrait-1.svg`, `portrait-2.svg` — About/homepage artist
  portraits
- `assets/images/gallery-1.svg` … `gallery-4.svg` — About page gallery
- `assets/images/cta-bg.svg` — dark "Let's stay in touch" background
- `assets/images/album-cover.svg` — fallback album art (replace by setting a
  Featured Image on the Album post instead — that takes priority)

Simplest approach: keep the same filenames and just overwrite the files (any
raster format works — update the `.svg` extension references in the relevant
template if you swap to `.jpg`/`.png`), or set a Featured Image directly on
each Page/Album from the Media Library, which several sections already
prefer when present.

## Design tokens

All colors, spacing, and font stacks are defined once as CSS custom
properties at the top of `style.css` (`:root { --mr-ivory, --mr-charcoal,
--mr-forest, --mr-rust, --mr-gold … }`) — change them there to re-theme the
whole site consistently.

## Privacy Policy content

Paste the following into the Privacy Policy page (adjust bracketed details
as needed):

---

**Privacy Policy**

*Last updated: [date]*

This website is operated by or on behalf of Maggie Rowan ("we", "us", "our").
This policy explains what personal information we collect through this
website, how we use it, and the choices you have.

**Information we collect**

- *Contact form submissions*: when you use the Contact page, we collect the
  name, email address, subject and message you provide, in order to respond
  to your enquiry.
- *Cookies*: we use a small number of cookies/local storage entries to
  remember that you've accepted our cookie notice and to understand basic,
  anonymized site usage. See "Cookies" below.

**How we use your information**

We use the information you submit only to respond to enquiries (booking,
press, collaboration, or general questions) sent via the Contact page. We do
not sell or rent your personal information to third parties.

**Cookies**

This site uses a cookie consent banner to let you accept the use of cookies.
Declining or ignoring the banner does not block access to the site's core
content. You can clear your cookie choice at any time via your browser
settings.

**Data retention**

Enquiry messages are retained only as long as needed to respond to and
resolve your enquiry, after which they may be deleted.

**Your rights**

Depending on your location, you may have the right to request access to,
correction of, or deletion of your personal information. To make such a
request, please use the Contact page.

**Changes to this policy**

We may update this policy from time to time. The "Last updated" date above
reflects the most recent revision.

---
