# ShadcnTheme — Kanboard Plugin

> **Deprecated.** ShadcnTheme 1.3.0 is the final release. To switch to Harbor,
> open ModMenu and choose **Migrate**. [Harbor on GitHub](https://github.com/carmelosantana/kanboard-harbor)

A modern Kanboard theme inspired by [shadcn/ui](https://ui.shadcn.com/) design
principles.  Ships dark mode as the default, with a per-user light / dark / system
toggle, accessible task card colors and a branded login card. The site logo, favicon
and name come from the [Steward](https://github.com/carmelosantana/kanboard-steward)
plugin (recommended).

> **Note:** Screenshots will be added in a follow-up.

---

## Features

| Feature | Detail |
|---|---|
| **Dark-first with no FOUC** | A synchronous script in `<head>` reads `localStorage` and sets the theme before first paint — no flash on page load. |
| **Light / dark / system toggle** | Dropdown in the page header.  Preference stored per user in `user_metadata`; guest users fall back to the session. |
| **shadcn/ui "neutral" design tokens** | Pure neutral-zinc palette with a near-white primary (near-black in light mode); color reserved for destructive + status. CSS custom properties (`--background`, `--foreground`, `--accent`, `--border`, `--radius`, etc.) plus `kbx-` primitive variables for a consistent palette across both themes. |
| **Dark-mode task colors** | All Kanboard task card color classes are overridden in dark mode with perceptually appropriate, WCAG AA-compliant shades. |
| **Header / navigation** | Top bar and sidebar correctly contrast in both light and dark themes. |
| **Login card** | Centered card layout with Steward's logo or site name (or the product name without Steward) above the form. |

---

## Requirements

- **Kanboard** >= 1.2.47
- **PHP** >= 8.4
- **Browser** — an evergreen browser that supports `:has()` and `color-mix()` (Chrome 105+, Firefox 121+, Safari 15.4+)

---

## Installation

1. Download or clone this repository into your Kanboard `plugins/` directory:

   ```
   plugins/
   └── ShadcnTheme/
       ├── Plugin.php
       ├── plugin.json
       └── ...
   ```

   The directory name **must** be `ShadcnTheme` (case-sensitive).

2. In Kanboard, go to **Settings → Plugins** and confirm ShadcnTheme appears as
   enabled.  No further configuration is required — the dark theme loads immediately.

---

## Using the theme toggle

The theme toggle appears in the user dropdown in the top-right of every page.
Click it to cycle between **Dark**, **Light**, and **System** (follows the OS
preference).  Your choice is saved per-user and remembered across sessions.

---

## Logo, favicon and site name

Branding lives in the [Steward](https://github.com/carmelosantana/kanboard-steward)
plugin (1.0.0 or later); ShadcnTheme no longer has its own upload page. The login card
shows Steward's logo, or its site name, and falls back to "Kanboard" without Steward.

Upgrading from ShadcnTheme 1.1? Your uploaded logo and favicon stay where they were —
use **Import** in Steward's branding settings to bring them over.

---

## Development

Tests live in `Test/` and run against the Kanboard 1.2.47 core source:

```bash
# From the kanboard-plugins repo root:
./testing/run-plugin-tests.sh ShadcnTheme
```

See [`CHANGELOG.md`](CHANGELOG.md) for what shipped in each release.

---

## License

MIT — see [LICENSE](LICENSE) for full text.
