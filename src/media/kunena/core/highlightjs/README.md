# highlight.js

- Library: highlight.js, https://highlightjs.org/
- Version: 11.12.0
- Downloaded from: https://github.com/highlightjs/cdn-release/archive/refs/tags/11.12.0.tar.gz
- Build: unmodified official build, common languages (the CDN build of the release, folder `build/`)
- Licence: BSD-3-Clause, see `LICENSE` (the `LICENSE` of the release)

The files are copied byte for byte from the release:

| File | From | SHA-256 |
| --- | --- | --- |
| `highlight.min.js` | `build/highlight.min.js` | `8ab71eb09c51f501e5e25157d9cff100e46cc29bcbfc744d0b746d451fca7f53` |
| `styles/github-dark.min.css` | `build/styles/github-dark.min.css` | `9f208d022102b1d0c7aebfecd8e42ca7997d5de636649d2b31ea63093d809019` |

The SHA-384 of `highlight.min.js` matches the one in `DIGESTS.md` of the release
(`sha384-wjfDDhOPPdjtva8vWBhWeVprSpmxisEu5aYT3q1JyACqXpdKpo3PWZTMVq24MBix`).

Kunena loads them with `media/kunena/core/js/highlight-code.js` when the option *Enable Code Highlighting* is on and a page shows a
`[code]` block. Do not edit these files: replace them with the files of a new release and update this file.
