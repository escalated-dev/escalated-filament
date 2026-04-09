<p align="center">
  <a href="README.ar.md">العربية</a> •
  <a href="README.de.md">Deutsch</a> •
  <a href="../../README.md">English</a> •
  <a href="README.es.md">Español</a> •
  <a href="README.fr.md">Français</a> •
  <b>Italiano</b> •
  <a href="README.ja.md">日本語</a> •
  <a href="README.ko.md">한국어</a> •
  <a href="README.nl.md">Nederlands</a> •
  <a href="README.pl.md">Polski</a> •
  <a href="README.pt-BR.md">Português (BR)</a> •
  <a href="README.ru.md">Русский</a> •
  <a href="README.tr.md">Türkçe</a> •
  <a href="README.zh-CN.md">简体中文</a>
</p>

# Escalated per Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Un plugin per il pannello di amministrazione [Filament](https://filamentphp.com) per il sistema di ticket di supporto [Escalated](https://github.com/escalated-dev/escalated-laravel). Gestisci ticket, dipartimenti, politiche SLA, regole di escalation, macro e altro — tutto dal tuo pannello di amministrazione Filament esistente.

> **[escalated.dev](https://escalated.dev)** — Scopri di più, guarda le demo e confronta le opzioni Cloud e Self-Hosted.

## Come funziona

Escalated per Filament è un **wrapper plugin Filament** attorno a [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). Non duplica alcuna logica di business. Fornisce invece Resources, Pages, Widgets e Actions di Filament che richiamano gli stessi servizi, modelli ed eventi del pacchetto Laravel principale. Questo significa:

- Tutta la logica del ciclo di vita dei ticket, i calcoli SLA e le regole di escalation provengono da `escalated-laravel`
- Le tabelle del database, le migrazioni e la configurazione sono gestite dal pacchetto principale
- Gli eventi, le notifiche e i webhook si attivano esattamente come nell'interfaccia Inertia
- Ottieni un'esperienza Filament nativa senza dover mantenere una base di codice separata

> **Nota:** Questo pacchetto utilizza i componenti nativi Livewire + Blade di Filament (tabelle, form, liste informative, azioni, widget) invece dell'interfaccia personalizzata Vue 3 + Inertia.js del pacchetto frontend [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated). La funzionalità di base è la stessa — stessi modelli, servizi, database e logica di business — ma l'aspetto dell'interfaccia segue il sistema di design di Filament. Alcune interazioni potrebbero differire leggermente (es. modali Filament vs. form inline, filtri tabella Filament vs. componenti filtro personalizzati). Se hai bisogno di una corrispondenza perfetta con il frontend Inertia, usa `escalated-laravel` direttamente con i componenti Vue condivisi.

## Requisiti

- PHP 8.2+
- Laravel 11 o 12
- Filament 3.x, 4.x o 5.x
- escalated-dev/escalated-laravel ^0.5

### Compatibilità versioni

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## Installazione

### 1. Installare i pacchetti

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

Se `escalated-laravel` è già installato, aggiungi solo il plugin Filament:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Eseguire l'installer di Escalated (se non già fatto)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Definire i gate di autorizzazione

In un service provider (es. `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Registrare il plugin nel tuo pannello Filament

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

Tutto pronto. Visita il tuo pannello Filament — apparirà un gruppo di navigazione **Support** con tutte le risorse di gestione ticket.

## Funzionalità

### Risorse

- **TicketResource** — Gestione completa dei ticket con pagine di elenco, visualizzazione e creazione
  - Filtrabile per stato, priorità, dipartimento, agente, tag, SLA
  - Tab di filtro rapido: Tutti, I miei ticket, Non assegnati, Urgente, Violazione SLA
  - Azioni di massa: Assegna, Cambia stato, Cambia priorità, Aggiungi tag, Chiudi, Elimina
  - Pagina di visualizzazione con thread della conversazione, dettagli barra laterale, info SLA, valutazione soddisfazione
  - Azioni dell'intestazione: Rispondi, Nota, Assegna, Stato, Priorità, Segui, Macro, Risolvi, Chiudi, Riapri
- **DepartmentResource** — CRUD per i dipartimenti di supporto con assegnazione agenti
- **TagResource** — CRUD per i tag dei ticket con selettore colore
- **SlaPolicyResource** — Gestione politiche SLA con tempi di risposta/risoluzione per priorità
- **EscalationRuleResource** — Costruttore condizioni/azioni per regole di escalation automatica
- **CannedResponseResource** — Modelli di risposta predefiniti con categorie
- **MacroResource** — Macro di automazione multi-azione con passaggi riordinabili

### Widget della dashboard

- **TicketStatsOverview** — Metriche chiave: I miei aperti, Non assegnati, Totale aperti, SLA violati, Risolti oggi, CSAT
- **TicketsByStatusChart** — Grafico a ciambella della distribuzione ticket per stato
- **TicketsByPriorityChart** — Grafico a barre dei ticket aperti per priorità
- **CsatOverviewWidget** — Metriche di soddisfazione del cliente: Valutazione media, Totale valutazioni, Tasso di soddisfazione
- **RecentTicketsWidget** — Tabella dei 5 ticket più recenti
- **SlaBreachWidget** — Tabella dei ticket con obiettivi SLA violati

### Pagine

- **Dashboard** — Dashboard di supporto con tutti i widget
- **Reports** — Analisi per intervallo di date con statistiche, dettaglio dipartimenti e cronologia
- **Settings** — Impostazioni admin per prefisso riferimento, ticket ospiti, chiusura automatica, limiti allegati

### Gestori delle relazioni

- **RepliesRelationManager** — Thread delle risposte con note interne, fissaggio e inserimento risposte predefinite
- **ActivitiesRelationManager** — Log di audit in sola lettura di tutte le attività dei ticket
- **FollowersRelationManager** — Gestire i follower dei ticket

### Azioni riutilizzabili

- `AssignTicketAction` — Assegnare un ticket a un agente
- `ChangeStatusAction` — Cambiare lo stato del ticket
- `ChangePriorityAction` — Cambiare la priorità del ticket
- `ApplyMacroAction` — Applicare un macro a un ticket
- `FollowTicketAction` — Attivare/disattivare il seguimento di un ticket
- `PinReplyAction` — Fissare/rimuovere note interne

### Componenti Livewire personalizzati

- **TicketConversation** — Thread completo della conversazione con editor risposte, inserimento risposte predefinite e fissaggio note
- **SatisfactionRating** — Visualizzazione della valutazione di soddisfazione del cliente con stelle

## Configurazione

Il plugin viene configurato tramite concatenazione di metodi sull'istanza del plugin:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Etichetta del gruppo di navigazione (predefinito: 'Support')
    ->agentGate('escalated-agent')  // Gate per l'accesso agente (predefinito: 'escalated-agent')
    ->adminGate('escalated-admin')  // Gate per l'accesso admin (predefinito: 'escalated-admin')
```

Tutta la restante configurazione (SLA, modalità di hosting, notifiche, ecc.) è gestita dal pacchetto principale `escalated-laravel` in `config/escalated.php`. Consulta il [README di escalated-laravel](https://github.com/escalated-dev/escalated-laravel) per il riferimento completo alla configurazione.

## Pubblicazione delle viste

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Screenshot

_In arrivo._

## Disponibile anche per

- **[Escalated per Laravel](https://github.com/escalated-dev/escalated-laravel)** — Pacchetto Composer Laravel
- **[Escalated per Rails](https://github.com/escalated-dev/escalated-rails)** — Motore Ruby on Rails
- **[Escalated per Django](https://github.com/escalated-dev/escalated-django)** — Applicazione Django riutilizzabile
- **[Escalated per AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — Pacchetto AdonisJS v6
- **[Escalated per Filament](https://github.com/escalated-dev/escalated-filament)** — Plugin pannello admin Filament (sei qui)
- **[Frontend condiviso](https://github.com/escalated-dev/escalated)** — Componenti interfaccia Vue 3 + Inertia.js

Stessa architettura, stesso sistema di ticket — esperienza Filament nativa per i pannelli di amministrazione Laravel.

## Licenza

MIT
