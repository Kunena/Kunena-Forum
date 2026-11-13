# Own domains: links without nofollow

> **Label:** `feature` · **Since:** Kunena 7.2 · **For:** Administrators · **Issue:** [#6073](https://github.com/Kunena/Kunena-Forum/issues/6073)

The links in messages to the domains of a list get no rel="nofollow noopener noreferrer".

A link in a message to another site gets `rel="nofollow noopener noreferrer"`, so search engines do not follow it. A link to a site of your
own, for example the shop or the blog on a subdomain of the forum, should count like a link inside the site. List these domains in the option
*Own domains* and their links are printed without the `rel` attribute.

## How it works

- One domain on each line, for example `example.com`. Its subdomains are included: `example.com` also covers `shop.example.com`.
- Only the host of the link counts. A domain written as an address (`https://example.com/forum`) is read as `example.com`, and an entry
  which is not a domain name is ignored.
- It is your list: Kunena does not guess your domains or the subdomains of the forum. With an empty list every link to another site keeps
  its `rel`.
- It applies to the links written with the `[url]` tag and to the pasted links. A link to an own domain still opens like the links to
  other sites; only the `rel` attribute is left out.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Own domains | Kunena configuration, tab *BBCode Settings* (one domain on each line, subdomains included) | empty |

---

[← All the new features of Kunena 7.2](../README.md)
