# FlinkISO languages

Run `app/Config/Schema/languages.sql` once for an existing installation, or run the normal updater (the same migration is included in `app/webroot/updates/updates.sql`). It creates English and German entries in `languages`. The existing `users.language_id` field stores each user’s preference.

Users select their language using **Profile → Language** or **user menu → Language**. Saving takes effect immediately after redirect and survives login. Missing, disabled, or unselected preferences use English. No core.php edits are required.

Administrators and super users can open **Settings → Languages** (or `/languages`) to add a language and edit translations. Other users can only change their own preference. Language codes are recognized three-letter CakePHP codes such as `deu`, `fra`, or `spa`. The name, availability and plural rule can be edited. Codes are fixed after creation so existing folders retain their identity. English remains available as the fallback.

`default.pot` is the source template. Each language’s translations live in `Locale/<code>/LC_MESSAGES/default.po`; separate translated views are unnecessary. The editor merges source messages into the existing catalog, provides 40 entries per page, checks placeholders and rejects a stale catalog version. PHP needs write permission on the selected language’s `LC_MESSAGES` folder. It removes stale `default.mo` files and invalidates the matching CakePHP translation cache when saving.

The updater preserves existing PO/MO catalogs while updating POT templates. New bundled catalogs can be installed when absent. Preserve and deploy customer catalogs separately if making manual file copies.

To refresh templates from a full CakePHP installation, run from the project root:

```sh
app/Console/cake i18n extract --paths app/Controller,app/Model,app/View --output app/Locale --merge no --extract-core no --overwrite
```

The update repository is a partial application; use a full installation’s CakePHP console to run extraction against these source folders. Review extraction warnings for dynamic strings. The browser editor edits translations rather than rewriting application source or the POT template. The default domain is supported by the editor; separate `cake` and `cake_dev` domains can be managed with gettext editors.

German coverage remains partial. Shared navigation and common labels are translated. Unwrapped HTML, explicit labels, JavaScript messages, generated templates and database-defined field labels still need conversion/collection. Empty translations fall back to English; they do not imply that all application text is covered.
