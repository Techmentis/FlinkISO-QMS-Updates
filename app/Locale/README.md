# FlinkISO languages and translations

Run the normal updater: `app/webroot/updates/updates.sql` creates the language table and inserts English (`eng`) and German (`deu`) only when missing. The same language migration is available in `app/Config/Schema/languages.sql`. Existing language rows and user preferences remain intact. The existing `users.language_id` field stores each user's selection. No `core.php` change is required.

All users choose an available language from the globe menu after the database icon. Administrators and super users also have Add language and Update PO file actions there. Language controls are no longer part of the profile menu.

The language editor shows original text on the left and translations on the right, with search and a Save action. Empty translations fall back to English. Keep placeholders and plural forms intact. Catalog saves retain unchanged entries and reject edits based on an older catalog version.

The language reference includes known three-letter codes and plural rules. Use this code fills name, code and the available rule together. Other unique three-letter codes are allowed. Supply a plural rule manually when none is known. ApplicationL10n registers custom locale folders, including Marathi (`mar`), without altering CakePHP's core files.

On the Languages page, administrators can use the configured AI provider to translate a catalog. Select the target language and Share application POT file with AI. ApplicationPotExtractor generates `Locale/default.pot` directly in PHP using CakePHP's extractor. No server upload or console command is needed. Accepted batches are saved to `Locale/<code>/LC_MESSAGES/default.po`; Retry continues a failed batch. At completion, translation stays disabled for that language on the current page, and Save PO file downloads a copy. AI can replace existing translations; retain a copy before retranslating.

The bundled German PO is the translated catalog from the working instance, copied without regenerating or retranslating it. The updater preserves installed customer PO/MO files and updates the shared POT. A bundled language catalog is installed only if absent. Existing customers can review new entries through the editor and AI workflow. Customer configuration, uploads and runtime files are not part of this migration.

The web-server account needs write access to Locale folders and the application's tmp directory. Saving through the editor removes stale compiled MO files and clears the catalog cache. Application JavaScript uses TranslationMessages through translation_strings; vendor source files remain unchanged. Database records and uploaded documents are not automatically translated.

Help: https://www.flinkiso.com/manual/languages-and-translations.html
