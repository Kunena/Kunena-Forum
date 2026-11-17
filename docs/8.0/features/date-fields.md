# Date fields of the browser

> **Label:** `feature` · **Since:** Kunena 8.0 · **For:** Users, administrators

The date fields of the forum are now the date fields of the browser. The jQuery datepicker (bootstrap-datepicker) is no longer
part of Kunena.

## The fields

| Field | Where | Kind |
| --- | --- | --- |
| Birthdate | The profile (*Edit profile*) and the user form of the backend | Date (no date in the future) |
| Search at a specific date | The advanced search | Date |
| Date, publish up, publish down | The form of an announcement | Date and time |
| End of the poll | The poll dialog of SCEditor and of CKEditor | Date |

## How it works

- **The calendar of the browser:** each field opens the calendar of the browser, in the language and the date format of the user.
  It works with the keyboard and with a screen reader, and no script of Kunena is needed.
- **The value sent:** the fields always send `yyyy-mm-dd` (and `yyyy-mm-ddThh:mm` for an announcement).
- **The dates of an announcement:** they now keep their time. The datepicker set only a date in these date and time fields.
- **The end of a poll:** a day without a time is the end of that day (23:59:59). A date of the date field was read as the start of
  the day, so the poll ended a day early.
- **The search date:** the search form shows the date of the search again.

## Options

| Option | Where | Default |
| --- | --- | --- |
| Date format for datepicker | Kunena configuration | empty |

The fields no longer use this option: they show the date in the format of the user's language. The server still reads a date in
this format, so an older link of a search with a date like `15/03/2025` keeps working.

## For developers

- `KunenaDate::fromPicker()` reads `yyyy-mm-dd` first, then the format of the option, then the formats which PHP knows.
- `KunenaDate::toDateTimeLocal()` and `KunenaDate::fromDateTimeLocal()` convert between a date and time of the database
  (`Y-m-d H:i:s`) and the value of a date and time field (`Y-m-dTH:i`).
- The layout `Widget/Datepicker`, the files `bootstrap.datepicker.js` and `.css` and the texts `COM_KUNENA_BOOTSTRAP_DATEPICKER_*`
  are removed. A template override that still loads them gets no calendar of its own; a field with `type="date"` needs none.

---

[← All the new features of Kunena 8.0](../README.md)
