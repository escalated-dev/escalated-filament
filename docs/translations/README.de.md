<p align="center">
  <a href="README.ar.md">العربية</a> •
  <b>Deutsch</b> •
  <a href="../../README.md">English</a> •
  <a href="README.es.md">Español</a> •
  <a href="README.fr.md">Français</a> •
  <a href="README.it.md">Italiano</a> •
  <a href="README.ja.md">日本語</a> •
  <a href="README.ko.md">한국어</a> •
  <a href="README.nl.md">Nederlands</a> •
  <a href="README.pl.md">Polski</a> •
  <a href="README.pt-BR.md">Português (BR)</a> •
  <a href="README.ru.md">Русский</a> •
  <a href="README.tr.md">Türkçe</a> •
  <a href="README.zh-CN.md">简体中文</a>
</p>

# Escalated für Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Ein [Filament](https://filamentphp.com) Admin-Panel-Plugin für das [Escalated](https://github.com/escalated-dev/escalated-laravel) Support-Ticket-System. Verwalten Sie Tickets, Abteilungen, SLA-Richtlinien, Eskalationsregeln, Makros und mehr — alles innerhalb Ihres bestehenden Filament-Admin-Panels.

> **[escalated.dev](https://escalated.dev)** — Erfahren Sie mehr, sehen Sie Demos und vergleichen Sie Cloud- und Self-Hosted-Optionen.

## Funktionsweise

Escalated für Filament ist ein **Filament-Plugin-Wrapper** um [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). Es dupliziert keine Geschäftslogik. Stattdessen bietet es Filament-Resources, Pages, Widgets und Actions, die dieselben Services, Models und Events des Laravel-Kernpakets aufrufen. Das bedeutet:

- Die gesamte Ticket-Lebenszyklus-Logik, SLA-Berechnungen und Eskalationsregeln stammen aus `escalated-laravel`
- Datenbanktabellen, Migrationen und Konfiguration werden vom Kernpaket verwaltet
- Events, Benachrichtigungen und Webhooks werden genau wie in der Inertia-Oberfläche ausgelöst
- Sie erhalten ein natives Filament-Erlebnis ohne eine separate Codebasis pflegen zu müssen

> **Hinweis:** Dieses Paket verwendet die nativen Livewire + Blade-Komponenten von Filament (Tabellen, Formulare, Info-Listen, Actions, Widgets) anstelle der benutzerdefinierten Vue 3 + Inertia.js-Oberfläche aus dem [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated) Frontend-Paket. Die Kernfunktionalität ist identisch — gleiche Models, Services, Datenbank und Geschäftslogik — aber das UI-Design folgt dem Filament-Designsystem. Einige Interaktionen können leicht abweichen (z.B. Filament-Modals vs. Inline-Formulare, Filament-Tabellenfilter vs. benutzerdefinierte Filterkomponenten). Wenn Sie pixelgenaue Übereinstimmung mit dem Inertia-Frontend benötigen, verwenden Sie `escalated-laravel` direkt mit den gemeinsamen Vue-Komponenten.

## Voraussetzungen

- PHP 8.2+
- Laravel 11 oder 12
- Filament 3.x, 4.x oder 5.x
- escalated-dev/escalated-laravel ^0.5

### Versionskompatibilität

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## Installation

### 1. Pakete installieren

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

Wenn `escalated-laravel` bereits installiert ist, fügen Sie nur das Filament-Plugin hinzu:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Escalated-Installer ausführen (falls noch nicht geschehen)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Autorisierungs-Gates definieren

In einem Service Provider (z.B. `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Plugin in Ihrem Filament-Panel registrieren

```php
use Escalated\Filament\EscalatedFilamentPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(
            EscalatedFilamentPlugin::make()
                ->navigationGroup('Support')
                ->agentGate('escalated-agent')
                ->adminGate('escalated-admin')
        );
}
```

Fertig. Besuchen Sie Ihr Filament-Panel — eine **Support**-Navigationsgruppe erscheint mit allen Ticket-Management-Resources.

## Funktionen

### Resources

- **TicketResource** — Vollständige Ticketverwaltung mit Listen-, Ansichts- und Erstellungsseiten
  - Filterbar nach Status, Priorität, Abteilung, Agent, Tags, SLA
  - Schnellfilter-Tabs: Alle, Meine Tickets, Nicht zugewiesen, Dringend, SLA-Verletzung
  - Massenaktionen: Zuweisen, Status ändern, Priorität ändern, Tags hinzufügen, Schließen, Löschen
  - Ansichtsseite mit Konversationsverlauf, Seitenleisten-Details, SLA-Info, Zufriedenheitsbewertung
  - Header-Aktionen: Antworten, Notiz, Zuweisen, Status, Priorität, Folgen, Makro, Lösen, Schließen, Wiederöffnen
- **DepartmentResource** — CRUD für Support-Abteilungen mit Agentenzuweisung
- **TagResource** — CRUD für Ticket-Tags mit Farbauswahl
- **SlaPolicyResource** — SLA-Richtlinienverwaltung mit Antwort-/Lösungszeiten pro Priorität
- **EscalationRuleResource** — Bedingungs-/Aktions-Builder für automatische Eskalationsregeln
- **CannedResponseResource** — Vorgefertigte Antwortvorlagen mit Kategorien
- **MacroResource** — Multi-Aktions-Automatisierungsmakros mit sortierbaren Schritten

### Dashboard-Widgets

- **TicketStatsOverview** — Schlüsselmetriken: Meine Offenen, Nicht zugewiesen, Gesamt Offen, SLA-Verletzung, Heute gelöst, CSAT
- **TicketsByStatusChart** — Donut-Diagramm der Ticketverteilung nach Status
- **TicketsByPriorityChart** — Balkendiagramm der offenen Tickets nach Priorität
- **CsatOverviewWidget** — Kundenzufriedenheitsmetriken: Durchschnittsbewertung, Gesamtbewertungen, Zufriedenheitsrate
- **RecentTicketsWidget** — Tabelle der 5 neuesten Tickets
- **SlaBreachWidget** — Tabelle der Tickets mit verletzten SLA-Zielen

### Seiten

- **Dashboard** — Support-Dashboard mit allen Widgets
- **Reports** — Zeitraumbasierte Analysen mit Statistiken, Abteilungsübersicht und Zeitverlauf
- **Settings** — Admin-Einstellungen für Referenzpräfix, Gast-Tickets, Auto-Schließen, Anhangslimits

### Relation Managers

- **RepliesRelationManager** — Antwortverlauf mit internen Notizen, Anheften und Einfügen vorgefertigter Antworten
- **ActivitiesRelationManager** — Schreibgeschütztes Audit-Log aller Ticket-Aktivitäten
- **FollowersRelationManager** — Ticket-Follower verwalten

### Wiederverwendbare Aktionen

- `AssignTicketAction` — Ticket einem Agenten zuweisen
- `ChangeStatusAction` — Ticketstatus ändern
- `ChangePriorityAction` — Ticketpriorität ändern
- `ApplyMacroAction` — Makro auf ein Ticket anwenden
- `FollowTicketAction` — Ticket-Verfolgung ein-/ausschalten
- `PinReplyAction` — Interne Notizen anheften/lösen

### Benutzerdefinierte Livewire-Komponenten

- **TicketConversation** — Vollständiger Konversationsverlauf mit Antwort-Editor, Einfügen vorgefertigter Antworten und Notizen-Anheftung
- **SatisfactionRating** — Anzeige der Kundenzufriedenheitsbewertung mit Sterne-Visualisierung

## Konfiguration

Das Plugin wird durch Methoden-Verkettung auf der Plugin-Instanz konfiguriert:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Navigationsgruppen-Label (Standard: 'Support')
    ->agentGate('escalated-agent')  // Gate für Agentenzugriff (Standard: 'escalated-agent')
    ->adminGate('escalated-admin')  // Gate für Adminzugriff (Standard: 'escalated-admin')
```

Alle anderen Konfigurationen (SLA, Hosting-Modi, Benachrichtigungen usw.) werden vom Kernpaket `escalated-laravel` in `config/escalated.php` verwaltet. Siehe die [escalated-laravel README](https://github.com/escalated-dev/escalated-laravel) für die vollständige Konfigurationsreferenz.

## Views veröffentlichen

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Screenshots

_Demnächst._

## Auch verfügbar für

- **[Escalated für Laravel](https://github.com/escalated-dev/escalated-laravel)** — Laravel Composer-Paket
- **[Escalated für Rails](https://github.com/escalated-dev/escalated-rails)** — Ruby on Rails Engine
- **[Escalated für Django](https://github.com/escalated-dev/escalated-django)** — Wiederverwendbare Django-App
- **[Escalated für AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — AdonisJS v6 Paket
- **[Escalated für Filament](https://github.com/escalated-dev/escalated-filament)** — Filament Admin-Panel-Plugin (Sie sind hier)
- **[Gemeinsames Frontend](https://github.com/escalated-dev/escalated)** — Vue 3 + Inertia.js UI-Komponenten

Gleiche Architektur, gleiches Ticket-System — natives Filament-Erlebnis für Laravel-Admin-Panels.

## Lizenz

MIT
