# Image lightbox without Fancybox

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Users, administrators

A click on an image in a message opens it over the page, in Kunena's own lightbox. Fancybox is no longer part of Kunena.

The lightbox works for the images of `[img]`, image attachments and their thumbnails. You can go through all the images of the page
without closing it.

## How it works

- **Going through the images:** the arrow buttons, the arrow keys (**Home** and **End** for the first and the last image), or a
  swipe on a touch screen. The counter shows where you are, for example *Image 2 of 5*.
- **The same image twice:** when a thumbnail and a link point to the same file, the lightbox shows it once.
- **Captions:** the words of `[img alt=...]`, or the file name of an attachment, are shown as the caption, as text only.
- **Download:** the download button saves the image. An attachment is saved under the name it was uploaded with, through the
  forum, which checks who may read it. An image from another site can't be saved by the browser that way, so the button
  opens it in a new tab instead, and its label says so.
- **Closing:** the close button, **Escape**, or a click beside the image. The focus goes back to the image that was clicked.
- **New tab:** a click with **Ctrl**, **Cmd**, **Shift** or the middle button still opens the image in a new tab.
- **Missing images:** an image that cannot be loaded shows a message instead.
- **Dark backdrop:** the lightbox always uses a dark backdrop, in the light and the dark scheme, so the image is seen as it is. It
  follows right-to-left languages.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Enable Lightbox for Images | Kunena configuration, *Layout and display* › *Topics* | Yes |

- **Yes** uses Kunena's lightbox.
- **Use existing one** loads no script of Kunena. The image links get `data-fancybox="gallery" data-type="image"` for the lightbox
  of your site, as before.
- **No** opens the image itself.

## For developers and template overrides

- The links get `data-kunena-lightbox="<group>"`. All links of a page with the same group form one gallery.
- `KunenaLightbox::attributes($group, $download, $filename)` (`Kunena\Forum\Libraries\Html`) returns the attributes for the current
  option. An attachment passes the address which the forum sends as a file and its name (`data-kunena-download`,
  `data-kunena-filename`); without them the download button takes the address of the link.
- A template override that still prints `data-fancybox` keeps working: Kunena's lightbox opens those links too.
- The script is `media/kunena/core/js/lightbox.js` and the styles are `media/kunena/core/css/lightbox.css`. The layout
  `Widget/Lightbox` loads them, together with the texts `COM_KUNENA_LIGHTBOX_*`.
- `window.KunenaLightbox.open(link)` opens the lightbox on a link from a script.

---

[← All the new features of Kunena 8.0](../README.md)
