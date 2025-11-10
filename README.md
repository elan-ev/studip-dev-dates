# Stud.IP Dev Dates Plugin

Ein Stud.IP-Plugin zur Anzeige der Meilensteine und wichtigen Termine der Stud.IP-Entwicklung.

## Beschreibung

Das "Dev Dates" Plugin zeigt die Entwicklungstermine von Stud.IP in einem übersichtlichen Widget auf der Startseite an. Die Termine werden aus einer konfigurierbaren iCal-URL geladen und nach Versionen gruppiert dargestellt.

### Funktionen

- **Automatische Terminanzeige**: Lädt und parst iCal-Daten aus einer konfigurierbaren URL
- **Versionsgrupierung**: Termine werden automatisch nach Versionsnummern gruppiert
- **Intelligente Filterung**: Zeigt nur Versionen an, die noch mindestens einen zukünftigen Termin haben
- **Visuelle Hervorhebung**:
  - Aktive Termine (aktuell laufend) werden grün markiert
  - Der nächste anstehende Termin wird gelb hervorgehoben
  - Vergangene Termine werden ausgegraut dargestellt
- **Zeitraumdarstellung**: Unterstützt sowohl einzelne Termine als auch Zeiträume mit Start- und Enddatum
- **Konfigurierbar**: Root-Benutzer können die iCal-URL über eine Einstellungsseite anpassen

## Installation

1. Laden Sie das Plugin in das Stud.IP-Plugin-Verzeichnis herunter
2. Aktivieren Sie das Plugin in der Stud.IP-Administration
3. Die Migration wird automatisch ausgeführt und erstellt die benötigte Konfigurationsoption

## Konfiguration

Als Root-Benutzer können Sie die iCal-URL konfigurieren:

1. Klicken Sie auf das Bearbeiten-Icon im Widget
2. Geben Sie die URL zur iCal-Datei ein
3. Speichern Sie die Einstellungen

Die Konfigurationsoption wird unter dem Schlüssel `DEVDATES_ICAL_URL` gespeichert.

## Anforderungen

- Stud.IP 6.0 oder höher
- PHP 8.1 oder höher
- Zugriff auf die iCal-URL (keine Authentifizierung erforderlich)

## iCal-Format

Das Plugin erwartet iCal-Dateien im Standard-Format mit folgenden Eigenschaften:

- `SUMMARY`: Titel des Ereignisses (sollte mit Versionsnummer beginnen, z.B. "5.4 Feature Freeze")
- `DTSTART`: Startdatum des Ereignisses
- `DTEND` (optional): Enddatum des Ereignisses

### Formatierung der Titel

Die Titel der Termine sollten mit der Versionsnummer beginnen (z.B. "6.2"). Optional können Sie Zeitraum-Indikatoren verwenden:

#### Einzeltermin
Format: `6.2 Feature Freeze`

Wird als einzelnes Datum angezeigt.

#### Ab einem Datum
Format: `6.2 > Testphase`

Wird als "ab [Datum]" angezeigt. Verwenden Sie das `>` Zeichen nach der Versionsnummer, um anzuzeigen, dass ein Ereignis ab einem bestimmten Datum gilt.

#### Bis zu einem Datum
Format: `6.2 < Release-Vorbereitung`

Wird als "bis [Datum]" angezeigt. Verwenden Sie das `<` Zeichen nach der Versionsnummer, um anzuzeigen, dass ein Ereignis bis zu einem bestimmten Datum gilt.

#### Zeitraum
Format: `6.2 - Beta-Phase`

Wird als Datumsbereich "[Startdatum] - [Enddatum]" angezeigt. Verwenden Sie das `-` Zeichen nach der Versionsnummer für Ereignisse mit Start- und Enddatum. Benötigt sowohl `DTSTART` als auch `DTEND` im iCal.

**Hinweis:** Die Versionsnummer wird automatisch erkannt und die Termine werden entsprechend gruppiert. Versionen, bei denen alle Termine in der Vergangenheit liegen, werden automatisch ausgeblendet.

## Beispiel für einen Eintrag

```
BEGIN:VEVENT
SUMMARY:5.4 Feature Freeze
DTSTART:20250115
DTEND:20250115
END:VEVENT
```

## Struktur

```
studip-dev-dates/
├── StudipDevDates.php          # Hauptklasse des Plugins
├── plugin.manifest              # Plugin-Manifest
├── controllers/
│   └── devdates.php            # Controller für Einstellungen
├── views/
│   └── devdates/
│       └── settings.php        # Einstellungsformular
├── templates/
│   └── widget.php              # Widget-Template
└── migrations/
    └── 001_add_dev_dates_config.php  # Datenbank-Migration
```

## Lizenz

Dieses Programm ist freie Software. Sie können es unter den Bedingungen der GNU Affero General Public License, wie von der Free Software Foundation veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung dieses Programms erfolgt in der Hoffnung, dass es Ihnen von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne die implizite Garantie der MARKTREIFE oder der VERWENDBARKEIT FÜR EINEN BESTIMMTEN ZWECK. Details finden Sie in der GNU Affero General Public License.

Sie sollten ein Exemplar der GNU Affero General Public License zusammen mit diesem Programm erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

## Autoren

- Till Glöggler <gloeggler@elan-ev.de>

## Copyright

© 2025 ELAN e.V.

## Support

Bei Fragen oder Problemen wenden Sie sich bitte an die Stud.IP-Community oder erstellen Sie ein Issue im Repository.