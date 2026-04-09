<p align="center">
  <a href="README.ar.md">العربية</a> •
  <a href="README.de.md">Deutsch</a> •
  <a href="../../README.md">English</a> •
  <a href="README.es.md">Español</a> •
  <b>Français</b> •
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

# Escalated pour Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Un plugin de panneau d'administration [Filament](https://filamentphp.com) pour le système de tickets de support [Escalated](https://github.com/escalated-dev/escalated-laravel). Gérez les tickets, départements, politiques SLA, règles d'escalade, macros et plus encore — le tout depuis votre panneau d'administration Filament existant.

> **[escalated.dev](https://escalated.dev)** — En savoir plus, voir les démos et comparer les options Cloud et Auto-hébergé.

## Comment ça fonctionne

Escalated pour Filament est un **wrapper de plugin Filament** autour de [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). Il ne duplique aucune logique métier. Au lieu de cela, il fournit des Resources, Pages, Widgets et Actions Filament qui appellent les mêmes services, modèles et événements du package Laravel principal. Cela signifie :

- Toute la logique du cycle de vie des tickets, les calculs SLA et les règles d'escalade proviennent de `escalated-laravel`
- Les tables de base de données, migrations et configuration sont gérées par le package principal
- Les événements, notifications et webhooks se déclenchent exactement comme dans l'interface Inertia
- Vous obtenez une expérience Filament native sans maintenir une base de code séparée

> **Remarque :** Ce package utilise les composants natifs Livewire + Blade de Filament (tableaux, formulaires, listes d'informations, actions, widgets) plutôt que l'interface personnalisée Vue 3 + Inertia.js du package frontend [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated). La fonctionnalité de base est la même — mêmes modèles, services, base de données et logique métier — mais l'apparence de l'interface suit le système de design de Filament. Certaines interactions peuvent différer légèrement (ex. modales Filament vs. formulaires en ligne, filtres de tableau Filament vs. composants de filtres personnalisés). Si vous avez besoin d'une correspondance parfaite avec le frontend Inertia, utilisez `escalated-laravel` directement avec les composants Vue partagés.

## Prérequis

- PHP 8.2+
- Laravel 11 ou 12
- Filament 3.x, 4.x ou 5.x
- escalated-dev/escalated-laravel ^0.5

### Compatibilité des versions

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## Installation

### 1. Installer les packages

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

Si `escalated-laravel` est déjà installé, ajoutez simplement le plugin Filament :

```bash
composer require escalated-dev/escalated-filament
```

### 2. Exécuter l'installateur Escalated (si ce n'est pas déjà fait)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Définir les portes d'autorisation

Dans un fournisseur de services (ex. `AppServiceProvider`) :

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Enregistrer le plugin dans votre panneau Filament

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

C'est prêt. Visitez votre panneau Filament — un groupe de navigation **Support** apparaîtra avec toutes les ressources de gestion des tickets.

## Fonctionnalités

### Ressources

- **TicketResource** — Gestion complète des tickets avec pages de liste, vue et création
  - Filtrable par statut, priorité, département, agent, tags, SLA
  - Onglets de filtre rapide : Tous, Mes Tickets, Non assignés, Urgent, Violation SLA
  - Actions en masse : Assigner, Changer le statut, Changer la priorité, Ajouter des tags, Fermer, Supprimer
  - Page de vue avec fil de conversation, détails de la barre latérale, info SLA, note de satisfaction
  - Actions d'en-tête : Répondre, Note, Assigner, Statut, Priorité, Suivre, Macro, Résoudre, Fermer, Rouvrir
- **DepartmentResource** — CRUD pour les départements de support avec assignation d'agents
- **TagResource** — CRUD pour les tags de tickets avec sélecteur de couleur
- **SlaPolicyResource** — Gestion des politiques SLA avec temps de réponse/résolution par priorité
- **EscalationRuleResource** — Constructeur de conditions/actions pour les règles d'escalade automatique
- **CannedResponseResource** — Modèles de réponses prédéfinies avec catégories
- **MacroResource** — Macros d'automatisation multi-actions avec étapes réordonnables

### Widgets du tableau de bord

- **TicketStatsOverview** — Métriques clés : Mes ouverts, Non assignés, Total ouverts, SLA violé, Résolus aujourd'hui, CSAT
- **TicketsByStatusChart** — Graphique en anneau de la distribution des tickets par statut
- **TicketsByPriorityChart** — Graphique en barres des tickets ouverts par priorité
- **CsatOverviewWidget** — Métriques de satisfaction client : Note moyenne, Total des notes, Taux de satisfaction
- **RecentTicketsWidget** — Tableau des 5 tickets les plus récents
- **SlaBreachWidget** — Tableau des tickets avec objectifs SLA non respectés

### Pages

- **Dashboard** — Tableau de bord du support avec tous les widgets
- **Reports** — Analyses par plage de dates avec statistiques, répartition par département et chronologie
- **Settings** — Paramètres admin pour préfixe de référence, tickets invités, fermeture automatique, limites de pièces jointes

### Gestionnaires de relations

- **RepliesRelationManager** — Fil de réponses avec notes internes, épinglage et insertion de réponses prédéfinies
- **ActivitiesRelationManager** — Journal d'audit en lecture seule de toutes les activités de tickets
- **FollowersRelationManager** — Gérer les abonnés aux tickets

### Actions réutilisables

- `AssignTicketAction` — Assigner un ticket à un agent
- `ChangeStatusAction` — Changer le statut du ticket
- `ChangePriorityAction` — Changer la priorité du ticket
- `ApplyMacroAction` — Appliquer un macro à un ticket
- `FollowTicketAction` — Basculer le suivi d'un ticket
- `PinReplyAction` — Épingler/désépingler les notes internes

### Composants Livewire personnalisés

- **TicketConversation** — Fil de conversation complet avec éditeur de réponses, insertion de réponses prédéfinies et épinglage de notes
- **SatisfactionRating** — Affichage de la note de satisfaction client avec visualisation en étoiles

## Configuration

Le plugin est configuré par chaînage de méthodes sur l'instance du plugin :

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Libellé du groupe de navigation (par défaut : 'Support')
    ->agentGate('escalated-agent')  // Porte d'accès agent (par défaut : 'escalated-agent')
    ->adminGate('escalated-admin')  // Porte d'accès admin (par défaut : 'escalated-admin')
```

Toute autre configuration (SLA, modes d'hébergement, notifications, etc.) est gérée par le package principal `escalated-laravel` dans `config/escalated.php`. Consultez le [README de escalated-laravel](https://github.com/escalated-dev/escalated-laravel) pour la référence complète de configuration.

## Publication des vues

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Captures d'écran

_Bientôt disponible._

## Également disponible pour

- **[Escalated pour Laravel](https://github.com/escalated-dev/escalated-laravel)** — Package Composer Laravel
- **[Escalated pour Rails](https://github.com/escalated-dev/escalated-rails)** — Moteur Ruby on Rails
- **[Escalated pour Django](https://github.com/escalated-dev/escalated-django)** — Application Django réutilisable
- **[Escalated pour AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — Package AdonisJS v6
- **[Escalated pour Filament](https://github.com/escalated-dev/escalated-filament)** — Plugin de panneau d'administration Filament (vous êtes ici)
- **[Frontend partagé](https://github.com/escalated-dev/escalated)** — Composants d'interface Vue 3 + Inertia.js

Même architecture, même système de tickets — expérience Filament native pour les panneaux d'administration Laravel.

## Licence

MIT
