
# educa LMS Frame: Ein Framework für Drittanbieter-Integrationen

Das **educa LMS Frame** ist ein Framework, das von der **Digital Learning GmbH** in Deutschland entwickelt wurde, um Drittanbieter-Integrationen für das educa LMS einfach und effizient zu gestalten.

educa LMS selbst stellt dabei den "Rahmen" (Frame) bereit – ein flexibles, modulares System, das nahtlos erweiterbare Funktionen durch Drittanbieter ermöglicht.

---

## Was ist das educa LMS Frame?

Das educa LMS Frame bietet Entwicklern die Möglichkeit, eigene Widgets und Funktionen als Erweiterungen für das educa LMS zu integrieren. Durch die Verwendung eines strukturierten Layout- und Komponentensystems können Drittanbieter schnell und unkompliziert ihre eigenen Anwendungen oder Inhalte bereitstellen, ohne tief in die Kernstruktur des LMS eingreifen zu müssen.

---

## Hauptfunktionen des educa LMS Frame

### 1. **Flexible Seitenkonfiguration**
Das Frame ermöglicht die Definition individueller Seitenlayouts, die flexibel mit verschiedenen Widgets bestückt werden können. Jede Seite besteht aus:
- **Reihen** (Rows): Die Grundstruktur der Seite.
- **Widgets**: Bausteine, die Inhalte oder Funktionen darstellen.

Widgets können dabei entweder:
- **lokal** im educa LMS gehostet,
- **extern** über URLs geladen oder
- als **Remote-Komponenten** eingebunden werden.

### 2. **Grid-System**
Das Layout basiert auf einem **Grid-System**, das von Bootstrap inspiriert ist. Jede Seite ist in Zeilen und Spalten unterteilt, wodurch Widgets sauber und responsiv angeordnet werden können.

Beispiel einer Seitenkonfiguration:
```php
'layout' => [
    [
        // Erste Reihe
        [
            'type' => 'local',
            'component' => 'ClassbookMarkWidget',
            'size' => 6,
        ],
        [
            'type' => 'url',
            'url' => 'https://example.com/widgets/ExternalWidget',
            'size' => 6,
        ],
    ],
    [
        // Zweite Reihe
        [
            'type' => 'customComponent',
            'url' => 'https://example.com/remote-components/HelloWorld.js',
            'size' => 12,
            'height' => "400px",
        ],
    ],
],
```

### 3. **Widget-Typen**
Das Frame unterstützt drei Haupttypen von Widgets:
1. **Lokale Widgets**: React-Komponenten, die im educa LMS implementiert sind.
2. **Externe Widgets**: Inhalte, die von einer externen URL geladen werden, wie z. B. iFrames.
3. **Remote-Komponenten**: Dynamische React-Komponenten, die von Drittanbietern gehostet werden.

---

## Vorteile des educa LMS Frame

1. **Einfache Integration**:
    - Drittanbieter können ihre eigenen Tools schnell in das LMS einfügen.

2. **Flexibilität**:
    - Layouts und Widgets können frei angepasst und kombiniert werden.

3. **Erweiterbarkeit**:
    - Entwickler können neue Funktionen nahtlos in das bestehende System integrieren.

4. **Datenschutzkonform**:
    - Entspricht den höchsten Datenschutzstandards (z. B. DSGVO).

---

## Beispielanwendung

Angenommen, ein Drittanbieter möchte ein externes Analyse-Tool in das educa LMS integrieren. Das Tool könnte einfach als `url`-Widget konfiguriert werden:

```php
[
    'type' => 'url',
    'url' => 'https://example.com/widgets/AnalyticsTool',
    'size' => 12,
    'height' => "500px",
],
```

Dieses Widget würde dann als Bestandteil einer Seite im LMS angezeigt und könnte mit anderen lokalen oder Remote-Komponenten kombiniert werden.

---

Erfahren Sie mehr und starten Sie Ihre Integration:
[www.digitallearning.de](https://www.digitallearning.de)
