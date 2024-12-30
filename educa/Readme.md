n#StuPla
##Allgemein
StuPla ist ein Stundenplanungsprogramm für Schulen. Betandteile:
- Stundenplanung
- Vertretungsplanung
- digitales Klassenbuch
##Database
Nutzer verwalten x Schulen. Schulen können von mehreren Benutzern verwaltet werden.
Schulen können mehrere Schuljahre haben. Schulen bieten x Fächer an. Schulen haben n Korrigular. Korrigular bestehen aus einer Liste von Fächern und eine Anzahl an Wochenstunden.
Schuljahre von Schulen haben Klassen. Auf Klassen kann ein Korrigular ausgewirkt werden. Klassen sollten ein int für den Jahrgang haben und ein Buchstaben. 
Klassen haben x Schüler. Räume haben eine Kapazität und ein Namen.

## Technical

### Quickstart
 - ``cp .env.example .env`` and adapt database data
 - ``composer dump-autoload``
 - ``php artisan migrate``
 - ``npm run watch`` or if no JS is changed ``npm run dev``

## Glossary

| Name/Entity   | Meaning      | Comment  |
| ------------- |:-------------:| -----:|
| Schuler | Student | |
| Kohorte | Cohort| |
| Schule | School| Location/Standort |
| Studium | Study | |
| Lehrplan | Curriculum| |
| Lehrer | Teacher/Lecturer| |
| Fach | Subject | |
| Klasse | Course | | 
| Lehrplaneinheiten | - | describes 1 tree-node of a curriculum either wrapping a module or a category |
| Modul | Module |
| User | Employee | Frontend: Administration user or employee


