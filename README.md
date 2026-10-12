# ALTURA Properties — luxury real estate demo (WordPress)

A WordPress twin of the ALTURA concept site. **ALTURA is a fictional agency.** The name, villas,
places, prices and pictures are sample content, and nothing here can be bought or booked.

**Live demo:** https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/hugooblak/altura-demo/main/blueprint.json

It runs in WordPress Playground: a whole WordPress site in the browser tab, nothing installed.
Login is `admin` / `password`.

## What it shows

- **Properties the agent edits.** Each property is its own content type with place, price (0 shows
  "Price on request"), status (available, under offer, sold), bedrooms, bathrooms, size, plot and
  a photo. Change one in the admin and it changes on the home slider, the properties page and its
  own page.
- **Full-screen hero slider** with wipe transitions, counter, caption and arrows. Without
  JavaScript the first slide still shows. Reduced motion is respected.
- **Enquiry form.** Name, email, phone, property, budget, message. The property is pre-selected
  from the button you clicked. Every enquiry is emailed to the agent and saved in the admin with a
  follow-up status (new, contacted, viewing, offer, lost, spam) and a CSV export.
- **Spam protection without a captcha:** a hidden field, a signed timestamp and a per-visitor limit.
- **Tracking, switched off.** Google Analytics, Meta Pixel and Search Console IDs live in
  **Settings > ALTURA**. Nothing loads until filled in.
- Privacy export and erase use WordPress's own tools.

## Deliberately not here

Property feeds (IDX/MLS), map search, saved searches, multi-language and payments. A real job adds
these as a plugin. The Properties content type is where they plug in.

## How it is built

- Block theme, `theme.json`, native blocks and patterns. No page builder, no Custom HTML blocks.
- Plugin **ALTURA Properties** holds the properties, the enquiry form and the inbox, so business
  data survives a change of theme. Four custom blocks: hero, properties, property facts, enquiry form.
- Self-hosted fonts (Cormorant Garamond, DM Sans). No third-party scripts.
- Checks: see `proof/`.

## Folder layout

```
theme/altura/                block theme
plugins/altura-leads/        properties, enquiry form, inbox, settings
plugins/altura-demo-notes/   portfolio-only bar and notes. Deleted on a real site.
content/setup.php            builds the demo on a brand-new install
blueprint.json               what WordPress Playground runs
proof/                       test results and screenshots
```
