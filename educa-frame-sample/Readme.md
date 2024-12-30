# educa Frame Playground

So meldest du dich bei der GitLab Container Registry unter `registry.digitallearning.gmbh` an:

---

## Voraussetzungen

- Zugang zu `gitlab.digitallearning.gmbh`
- Docker auf deinem Rechner installiert ([Docker installieren](https://docs.docker.com/get-docker/))
- Zugriff auf ein GitLab-Projekt

---

## Schritt 1: Personal Access Token oder Login-Daten

Du brauchst entweder:

1. **Personal Access Token**:
    - Gehe zu **Einstellungen > Access Tokens**.
    - Erstelle ein Token mit `read_registry` und/oder `write_registry`.
    - Kopiere das Token (es wird nur einmal angezeigt).

2. **Alternativ**: Deinen GitLab-Benutzernamen und -Passwort (nicht empfohlen).

---

## Schritt 2: Anmeldung bei der Registry

Melde dich im Terminal bei der Registry an:

```bash
docker login registry.digitallearning.gmbh
```

### Abmelden von der GitLab Container Registry

Um dich von der GitLab Container Registry abzumelden, führe den folgenden Befehl im Terminal aus:

```bash
docker logout registry.digitallearning.gmbh
```

## Schritt 3: Installation der Datenbank

Besuche die Seite http://localhost, beim ersten Aufruf wird die Datenbank automatisch installiert. 
Danach wird eine Erfolgsmeldung angezeigt. Anschließend kann das educa LMS genutzt werden.

## Schritt 4: Login mit Demo-Daten

Du kannst die Demo-Nutzer test0 - test9 mit dem Passwort test nutzen.