# Test proof — ALTURA Properties

Run on 2026-10-12 against a fresh WordPress install with the demo content. Local test server, no compression.

| Check | Result | Details |
|---|---|---|
| 1. Markup lint | ✅ Pass | clean |
| 2. Pages load | ✅ Pass | 9 pages |
| 3. Link crawl | ✅ Pass | 68 URLs checked |
| 4. Editor: no invalid blocks | ✅ Pass | 6 posts/pages, 12 patterns, all templates |
| 5. Editor font sizes = live site | ✅ Pass | 15 text elements compared |
| 6. Accessibility (axe, WCAG 2.2 AA) | ✅ Pass | 9 pages × 2 widths, 0 violations |
| 7. Keyboard | ✅ Pass | skip link, focus outline, FAQ, mobile menu |
| 8. Forms | ✅ Pass | 2 forms: every field labelled (CF7: errors shown) |
| 9. Layout 320–1440px | ✅ Pass | no overflow, header fits |
| 10. Lighthouse (mobile) | ✅ Pass | / 94/100/100/100  /properties/ 98/100/100/100  /property/villa-kastello/ 97/100/100/100  /property/dar-zitoun/ 97/100/100/100  /philosophy/ 99/100/100/100  /enquire/ 92/100/100/100 |
| 11. Screenshots | ✅ Pass | 24 saved to proof/screenshots |

## Lighthouse (mobile)

| Page | Speed | Accessibility | Best practices | SEO | LCP | CLS |
|---|---|---|---|---|---|---|
| / | 94 | 100 | 100 | 100 | 2.9 s | 0.008 |
| /properties/ | 98 | 100 | 100 | 100 | 2.4 s | 0.004 |
| /property/villa-kastello/ | 97 | 100 | 100 | 100 | 2.4 s | 0.004 |
| /property/dar-zitoun/ | 97 | 100 | 100 | 100 | 2.6 s | 0.004 |
| /philosophy/ | 99 | 100 | 100 | 100 | 1.9 s | 0.004 |
| /enquire/ | 92 | 100 | 100 | 100 | 1.8 s | 0.17 |

Automated tests cannot catch every accessibility or design problem. A real project adds manual screen reader testing.
