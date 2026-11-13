<p align="center"><sub>
  <a href="README.ar.md">العربية</a> ·
  <a href="README.de.md">Deutsch</a> ·
  <a href="../../README.md">English</a> ·
  <a href="README.es.md">Español</a> ·
  <b>Français</b> ·
  <a href="README.it.md">Italiano</a> ·
  <a href="README.ja.md">日本語</a> ·
  <a href="README.ko.md">한국어</a> ·
  <a href="README.nl.md">Nederlands</a> ·
  <a href="README.pl.md">Polski</a> ·
  <a href="README.pt-BR.md">Português (BR)</a> ·
  <a href="README.ru.md">Русский</a> ·
  <a href="README.tr.md">Türkçe</a> ·
  <a href="README.zh-CN.md">简体中文</a>
</sub></p>

<p align="center">
  <a href="https://escalated.dev"><img src="https://raw.githubusercontent.com/escalated-dev/escalated/main/docs/assets/readme/logo.png" width="72" height="72" alt="Escalated"></a>
</p>

<h1 align="center">Escalated for Filament</h1>

<p align="center">
  Utilisez le helpdesk Escalated dans votre panneau d'administration Filament existant.<br>
  Tickets, services, politiques SLA, règles d'escalade et macros sous forme de ressources Filament natives, reposant sur Escalated for Laravel.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Site web</b></a> ·
  <a href="https://escalated.dev/docs"><b>Documentation</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>Journal des modifications</b></a>
</p>

## Ce qu'il fait

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Votre helpdesk dans le panneau que vous utilisez déjà</h3>
      Des listes de tickets avec filtres, onglets rapides et actions groupées, et une page de ticket avec la conversation, les détails SLA et des actions en un clic pour répondre, assigner et appliquer une macro.
    </td>
    <td width="50%" valign="top">
      <h3>Le même moteur qu'Escalated for Laravel</h3>
      Les SLA, règles d'escalade, événements et webhooks viennent tous de <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>, donc rien n'est dupliqué et rien ne diverge.
    </td>
  </tr>
</table>

## Fonctionnalités

<table>
<tr>
<td width="33%" valign="top">

**Tickets**
- Pages de liste, de détail et de création
- Filtres par statut, priorité, agent, SLA
- Onglets : les miens, non assignés, urgents, SLA bientôt dépassé
- Assignation, statut, priorité, tags et fermeture groupés
- Répondre, note, assigner, suivre, appliquer une macro
- Actions de ticket personnalisées depuis votre configuration

</td>
<td width="33%" valign="top">

**Ressources d'administration**
- Services avec assignation des agents
- Tags avec sélecteur de couleur
- Politiques SLA par priorité
- Éditeur de conditions et d'actions pour les règles d'escalade
- Réponses prédéfinies avec catégories
- Macros avec étapes réordonnables

</td>
<td width="33%" valign="top">

**Widgets du tableau de bord**
- Statistiques des tickets ouverts, non assignés, hors SLA et CSAT
- Graphiques des tickets par statut et priorité
- Vue d'ensemble CSAT
- Tickets récents
- Tickets avec SLA dépassé

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Conversation**
- Fil de réponses avec notes internes
- Notes épinglées et réponses prédéfinies
- Journal d'activité par ticket
- Abonnés au ticket
- Note de satisfaction par étoiles

</td>
<td width="33%" valign="top">

**Pages**
- Tableau de bord du support avec tous les widgets
- Rapports par service sur une période
- Réglages pour le préfixe, les invités, la fermeture automatique, les pièces jointes

</td>
<td width="33%" valign="top">

**Newsletters (optionnel)**
- Newsletters en Markdown avec envoi programmé
- Listes de destinataires statiques et dynamiques
- Modèles réutilisables avec thèmes

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">Toutes les fonctionnalités en détail</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Démarrage rapide

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Définissez ensuite les gates `escalated-agent` et `escalated-admin` et ajoutez `EscalatedFilamentPlugin::make()` à votre panneau. Nécessite PHP 8.2+, Laravel 11 ou 12 et Filament 3.x, 4.x ou 5.x. Le [guide d'installation](../installation.md) détaille les deux étapes.

## En savoir plus

- [Toutes les fonctionnalités](../features.md) · [Fonctionnement](../how-it-works.md) · [Installation](../installation.md)
- [Configuration, vues et traductions](../configuration.md) · [Accès aux tickets et routage](../ticket-access-and-routing.md) · [Plugins](../plugins.md)
- [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel) pour la référence de configuration du cœur

Escalated est aussi disponible pour [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) et [WordPress](https://github.com/escalated-dev/escalated-wordpress). L'interface Vue partagée se trouve dans [escalated](https://github.com/escalated-dev/escalated).

## Licence

MIT. Copyright (c) 2026 Escalated.dev. Voir [LICENSE](../../LICENSE).
