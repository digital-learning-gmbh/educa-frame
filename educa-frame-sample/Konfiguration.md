
# Educa Frame Config: Individuelle Seiten erstellen

Die **Frame Config** dient als zentrale Konfigurationsdatei für die Definition von Widgets und Layouts innerhalb des Educa LMS. Sie ermöglicht die Erstellung und Verwaltung individueller Seiten mit spezifischen Widgets, die sowohl lokal als auch extern geladen werden können.

---

## Aufbau der Konfigurationsdatei

Die Konfiguration besteht aus einer `pages`-Struktur, die aus einer Liste von Seiten besteht. Jede Seite ist als Array definiert und hat folgende Eigenschaften:

### Eigenschaften einer Seite
- **`display_name`**: Der Name der Seite, der im Frontend angezeigt wird.
- **`key`**: Ein eindeutiger Schlüssel zur Identifizierung der Seite (z. B. "dashboard").
- **`layout`**: Ein Array, das das Rasterlayout der Widgets beschreibt. Jede Zeile im Layout kann mehrere Widgets enthalten.

---

## Beispielkonfiguration

### 1. Dashboard-Seite
```php
[
    'display_name' => 'Dashboard',
    'key' => 'dashboard',
    'layout' => [
        [
            // Zeile 1
            [
                'type' => 'local',
                'component' => 'ClassbookMarkWidget',
                'size' => 6, // Bootstrap-Spaltengröße
            ],
            [
                'type' => 'local',
                'component' => 'ClassbookExamList',
                'size' => 6,
            ],
        ],
        [
            // Zeile 2
            [
                'type' => 'url',
                'url' => 'https://example.com/widgets/ClassbookAbsenteeism',
                'size' => 12,
                'height' => "70vh",
            ],
        ],
    ],
]
```

### 2. Bericht-Seite
```php
[
    'display_name' => 'Berichte',
    'key' => 'reports',
    'layout' => [
        [
            // Zeile 1
            [
                'type' => 'local',
                'component' => 'ReportSummaryWidget',
                'size' => 12,
            ],
        ],
        [
            // Zeile 2
            [
                'type' => 'url',
                'url' => 'https://example.com/widgets/ReportDetails',
                'size' => 6,
            ],
            [
                'type' => 'local',
                'component' => 'RecentActivitiesWidget',
                'size' => 6,
            ],
        ],
    ],
]
```

---

## Widget-Typen

Widgets können verschiedene Typen haben, die unterschiedliche Inhalte laden:

1. **Lokale Widgets (`local`)**:
    - Laden eine React-Komponente aus dem Educa-Projekt.
    - Beispiel:
      ```php
      [
          'type' => 'local',
          'component' => 'ClassbookMarkWidget',
          'size' => 6,
      ]
      ```

2. **Externe Widgets (`url`)**:
    - Laden Inhalte von einer externen URL, wie z. B. iFrames.
    - Beispiel:
      ```php
      [
          'type' => 'url',
          'url' => 'https://example.com/widgets/ClassbookAbsenteeism',
          'size' => 12,
          'height' => "70vh",
      ]
      ```

3. **Custom-Komponenten (`customComponent`)**:
    - Laden eine Remote-Komponente von einer externen Quelle.
    - Beispiel:
      ```php
      [
          'type' => 'customComponent',
          'url' => 'https://raw.githubusercontent.com/Paciolan/remote-component/master/examples/remote-components/HelloWorld.js',
          'size' => 6,
      ]
      ```

---
