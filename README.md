<p align="center"><sub>
  <a href="docs/translations/README.ar.md">العربية</a> ·
  <a href="docs/translations/README.de.md">Deutsch</a> ·
  <b>English</b> ·
  <a href="docs/translations/README.es.md">Español</a> ·
  <a href="docs/translations/README.fr.md">Français</a> ·
  <a href="docs/translations/README.it.md">Italiano</a> ·
  <a href="docs/translations/README.ja.md">日本語</a> ·
  <a href="docs/translations/README.ko.md">한국어</a> ·
  <a href="docs/translations/README.nl.md">Nederlands</a> ·
  <a href="docs/translations/README.pl.md">Polski</a> ·
  <a href="docs/translations/README.pt-BR.md">Português (BR)</a> ·
  <a href="docs/translations/README.ru.md">Русский</a> ·
  <a href="docs/translations/README.tr.md">Türkçe</a> ·
  <a href="docs/translations/README.zh-CN.md">简体中文</a>
</sub></p>

<p align="center">
  <a href="https://escalated.dev"><img src="https://raw.githubusercontent.com/escalated-dev/escalated/main/docs/assets/readme/logo.png" width="72" height="72" alt="Escalated"></a>
</p>

<h1 align="center">Escalated for Filament</h1>

<p align="center">
  Run the Escalated helpdesk inside your existing Filament admin panel.<br>
  Tickets, departments, SLA policies, escalation rules and macros as native Filament resources, backed by Escalated for Laravel.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Website</b></a> ·
  <a href="https://escalated.dev/docs"><b>Docs</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="CHANGELOG.md"><b>Changelog</b></a>
</p>

## What it does

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Your helpdesk in the panel you already use</h3>
      Ticket lists with filters, quick tabs and bulk actions, and a ticket page with the conversation, SLA details and one-click reply, assign and macro actions.
    </td>
    <td width="50%" valign="top">
      <h3>The same engine as Escalated for Laravel</h3>
      SLAs, escalation rules, events and webhooks all come from <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>, so nothing is duplicated and nothing drifts.
    </td>
  </tr>
</table>

## Features

<table>
<tr>
<td width="33%" valign="top">

**Tickets**
- List, view and create pages
- Filters for status, priority, agent, SLA
- Tabs: mine, unassigned, urgent, breaching
- Bulk assign, status, priority, tags, close
- Reply, note, assign, follow, apply macro
- Custom ticket actions from your config

</td>
<td width="33%" valign="top">

**Admin resources**
- Departments with agent assignment
- Tags with a color picker
- SLA policies per priority
- Escalation rule condition/action builder
- Canned responses with categories
- Macros with reorderable steps

</td>
<td width="33%" valign="top">

**Dashboard widgets**
- Open, unassigned, breached and CSAT stats
- Tickets by status and priority charts
- CSAT overview
- Recent tickets
- Tickets with breached SLAs

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Conversation**
- Reply thread with internal notes
- Pinned notes and canned replies
- Activity audit log per ticket
- Ticket followers
- Star satisfaction rating

</td>
<td width="33%" valign="top">

**Pages**
- Support dashboard with every widget
- Date-range reports by department
- Settings for prefix, guests, auto-close, attachments

</td>
<td width="33%" valign="top">

**Newsletters (optional)**
- Markdown newsletters with scheduled send
- Static and dynamic recipient lists
- Reusable templates with themes

</td>
</tr>
</table>

<p align="center"><sub><a href="docs/features.md">Every feature in detail</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Quick start

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Then define the `escalated-agent` and `escalated-admin` gates and add `EscalatedFilamentPlugin::make()` to your panel. Needs PHP 8.2+, Laravel 11 or 12 and Filament 3.x, 4.x or 5.x. The [installation guide](docs/installation.md) has both steps.

## Learn more

- [All features](docs/features.md) · [How it works](docs/how-it-works.md) · [Installation](docs/installation.md)
- [Configuration, views and translations](docs/configuration.md) · [Ticket access and routing](docs/ticket-access-and-routing.md) · [Plugins](docs/plugins.md)
- [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel) for the core configuration reference

Escalated is also available for [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) and [WordPress](https://github.com/escalated-dev/escalated-wordpress). The shared Vue UI lives in [escalated](https://github.com/escalated-dev/escalated).

## License

MIT. Copyright (c) 2026 Escalated.dev. See [LICENSE](LICENSE).
