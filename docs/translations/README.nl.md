<p align="center">
  <a href="README.ar.md">العربية</a> •
  <a href="README.de.md">Deutsch</a> •
  <a href="../../README.md">English</a> •
  <a href="README.es.md">Español</a> •
  <a href="README.fr.md">Français</a> •
  <a href="README.it.md">Italiano</a> •
  <a href="README.ja.md">日本語</a> •
  <a href="README.ko.md">한국어</a> •
  <b>Nederlands</b> •
  <a href="README.pl.md">Polski</a> •
  <a href="README.pt-BR.md">Português (BR)</a> •
  <a href="README.ru.md">Русский</a> •
  <a href="README.tr.md">Türkçe</a> •
  <a href="README.zh-CN.md">简体中文</a>
</p>

# Escalated voor Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Een [Filament](https://filamentphp.com) adminpaneel-plugin voor het [Escalated](https://github.com/escalated-dev/escalated-laravel) support-ticketsysteem. Beheer tickets, afdelingen, SLA-beleid, escalatieregels, macro's en meer — allemaal vanuit uw bestaande Filament-adminpaneel.

> **[escalated.dev](https://escalated.dev)** — Meer informatie, demo's bekijken en Cloud- vs Self-Hosted-opties vergelijken.

## Hoe het werkt

Escalated voor Filament is een **Filament-plugin-wrapper** rond [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). Het dupliceert geen bedrijfslogica. In plaats daarvan biedt het Filament Resources, Pages, Widgets en Actions die dezelfde services, modellen en events van het Laravel-kernpakket aanroepen. Dit betekent:

- Alle ticket-levenscycluslogica, SLA-berekeningen en escalatieregels komen uit `escalated-laravel`
- Databasetabellen, migraties en configuratie worden beheerd door het kernpakket
- Events, meldingen en webhooks werken precies zoals in de Inertia-interface
- U krijgt een native Filament-ervaring zonder een aparte codebasis te hoeven onderhouden

> **Opmerking:** Dit pakket gebruikt de native Livewire + Blade-componenten van Filament (tabellen, formulieren, informatieoverzichten, acties, widgets) in plaats van de aangepaste Vue 3 + Inertia.js UI uit het [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated) frontend-pakket. De kernfunctionaliteit is dezelfde — dezelfde modellen, services, database en bedrijfslogica — maar het uiterlijk volgt het Filament-designsysteem. Sommige interacties kunnen licht afwijken (bijv. Filament-modals vs. inline formulieren, Filament-tabelfilters vs. aangepaste filtercomponenten). Als u pixel-perfecte overeenkomst met de Inertia-frontend nodig heeft, gebruik dan `escalated-laravel` rechtstreeks met de gedeelde Vue-componenten.

## Vereisten

- PHP 8.2+
- Laravel 11 of 12
- Filament 3.x, 4.x of 5.x
- escalated-dev/escalated-laravel ^0.5

### Versiecompatibiliteit

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## Installatie

### 1. Pakketten installeren

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

Als `escalated-laravel` al geïnstalleerd is, voeg alleen de Filament-plugin toe:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Escalated-installer uitvoeren (indien nog niet gedaan)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Autorisatiegates definiëren

In een serviceprovider (bijv. `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Plugin registreren in uw Filament-paneel

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

Klaar. Bezoek uw Filament-paneel — een **Support**-navigatiegroep verschijnt met alle ticketbeheer-resources.

## Functies

### Resources

- **TicketResource** — Volledig ticketbeheer met lijst-, weergave- en aanmaakpagina's
  - Filterbaar op status, prioriteit, afdeling, agent, tags, SLA
  - Snelfiltertabs: Alle, Mijn Tickets, Niet-toegewezen, Urgent, SLA-schending
  - Bulkacties: Toewijzen, Status wijzigen, Prioriteit wijzigen, Tags toevoegen, Sluiten, Verwijderen
  - Weergavepagina met conversatiethread, zijbalkdetails, SLA-info, tevredenheidsbeoordeling
  - Header-acties: Beantwoorden, Notitie, Toewijzen, Status, Prioriteit, Volgen, Macro, Oplossen, Sluiten, Heropenen
- **DepartmentResource** — CRUD voor supportafdelingen met agenttoewijzing
- **TagResource** — CRUD voor tickettags met kleurenkiezer
- **SlaPolicyResource** — SLA-beleidsbeheer met respons-/oplostijden per prioriteit
- **EscalationRuleResource** — Conditie-/actiebuilder voor automatische escalatieregels
- **CannedResponseResource** — Vooraf geschreven antwoordsjablonen met categorieën
- **MacroResource** — Multi-actie automatiseringsmacro's met hersorteerbare stappen

### Dashboard-widgets

- **TicketStatsOverview** — Kernmetrieken: Mijn Open, Niet-toegewezen, Totaal Open, SLA-schending, Vandaag opgelost, CSAT
- **TicketsByStatusChart** — Donutgrafiek van ticketverdeling per status
- **TicketsByPriorityChart** — Staafdiagram van open tickets per prioriteit
- **CsatOverviewWidget** — Klanttevredenheidsmetrieken: Gemiddelde beoordeling, Totaal beoordelingen, Tevredenheidspercentage
- **RecentTicketsWidget** — Tabel van de 5 meest recente tickets
- **SlaBreachWidget** — Tabel van tickets met geschonden SLA-doelen

### Pagina's

- **Dashboard** — Supportdashboard met alle widgets
- **Reports** — Datumbereikanalyse met statistieken, afdelingsoverzicht en tijdlijn
- **Settings** — Beheerdersinstellingen voor referentieprefix, gasttickets, automatisch sluiten, bijlagelimieten

### Relatiemanagers

- **RepliesRelationManager** — Antwoordthread met interne notities, vastpinnen en invoegen van standaardantwoorden
- **ActivitiesRelationManager** — Alleen-lezen auditlog van alle ticketactiviteiten
- **FollowersRelationManager** — Ticketvolgers beheren

### Herbruikbare acties

- `AssignTicketAction` — Ticket toewijzen aan een agent
- `ChangeStatusAction` — Ticketstatus wijzigen
- `ChangePriorityAction` — Ticketprioriteit wijzigen
- `ApplyMacroAction` — Macro toepassen op een ticket
- `FollowTicketAction` — Ticket volgen aan-/uitzetten
- `PinReplyAction` — Interne notities vastpinnen/losmaken

### Aangepaste Livewire-componenten

- **TicketConversation** — Volledige conversatiethread met antwoordeditor, invoegen van standaardantwoorden en notities vastpinnen
- **SatisfactionRating** — Weergave van klanttevredenheidsbeoordeling met sterrenvisualisatie

## Configuratie

De plugin wordt geconfigureerd via method chaining op de plugin-instantie:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Navigatiegroeplabel (standaard: 'Support')
    ->agentGate('escalated-agent')  // Gate voor agenttoegang (standaard: 'escalated-agent')
    ->adminGate('escalated-admin')  // Gate voor admintoegang (standaard: 'escalated-admin')
```

Alle overige configuratie (SLA, hostingmodi, meldingen, enz.) wordt beheerd door het kernpakket `escalated-laravel` in `config/escalated.php`. Zie de [escalated-laravel README](https://github.com/escalated-dev/escalated-laravel) voor de volledige configuratiereferentie.

## Views publiceren

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Schermafbeeldingen

_Binnenkort beschikbaar._

## Ook beschikbaar voor

- **[Escalated voor Laravel](https://github.com/escalated-dev/escalated-laravel)** — Laravel Composer-pakket
- **[Escalated voor Rails](https://github.com/escalated-dev/escalated-rails)** — Ruby on Rails Engine
- **[Escalated voor Django](https://github.com/escalated-dev/escalated-django)** — Herbruikbare Django-app
- **[Escalated voor AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — AdonisJS v6 pakket
- **[Escalated voor Filament](https://github.com/escalated-dev/escalated-filament)** — Filament adminpaneel-plugin (u bent hier)
- **[Gedeelde frontend](https://github.com/escalated-dev/escalated)** — Vue 3 + Inertia.js UI-componenten

Dezelfde architectuur, hetzelfde ticketsysteem — native Filament-ervaring voor Laravel-adminpanelen.

## Licentie

MIT
