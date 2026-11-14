# highlight.js

- Library: highlight.js, https://highlightjs.org/
- Version: 11.11.1
- Downloaded from: https://github.com/highlightjs/cdn-release/archive/refs/tags/11.11.1.tar.gz
- Build: unmodified official build, common languages (the CDN build of the release, folder `build/`)
- Licence: BSD-3-Clause, see `LICENSE` (the `LICENSE` of the release)

The files are copied byte for byte from the release:

| File | From | SHA-256 |
| --- | --- | --- |
| `highlight.min.js` | `build/highlight.min.js` | `c4a399dd6f488bc97a3546e3476747b3e714c99c57b9473154c6fb8d259b9381` |
| `styles/github-dark.min.css` | `build/styles/github-dark.min.css` | `9f208d022102b1d0c7aebfecd8e42ca7997d5de636649d2b31ea63093d809019` |

The SHA-384 of `highlight.min.js` matches the one in `DIGESTS.md` of the release
(`sha384-RH2xi4eIQ/gjtbs9fUXM68sLSi99C7ZWBRX1vDrVv6GQXRibxXLbwO2NGZB74MbU`).

Kunena loads them with `media/kunena/core/js/highlight-code.js` when the option *Enable Code Highlighting* is on and a page shows a
`[code]` block. Do not edit these files: replace them with the files of a new release and update this file.
