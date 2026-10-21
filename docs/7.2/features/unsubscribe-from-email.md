# Notification e-mails: unsubscribe from the e-mail itself

> **Label:** `feature` · **Since:** Kunena 7.2 · **For:** Users

Every subscription e-mail has a link, and the headers for one-click unsubscribe.

Subscription e-mails get a **signed unsubscribe link** (in the HTML and the plain text) and the `List-Unsubscribe` and
`List-Unsubscribe-Post` headers, so a mail program can offer one-click unsubscribe.

- An e-mail about a reply unsubscribes from the **topic**; an e-mail about a new topic unsubscribes from the **category**.
- The link is signed with the secret of the site: no login is needed and nobody can unsubscribe somebody else.
- Opening the link only shows a confirmation page. Only a post (the button, or the one-click request of the mail program) removes the
  subscription, so a program which scans the links can not unsubscribe a user.

---

[← All the new features of Kunena 7.2](../README.md)
