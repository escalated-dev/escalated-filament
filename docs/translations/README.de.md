<p align="center"><sub>
  <a href="README.ar.md">العربية</a> ·
  <b>Deutsch</b> ·
  <a href="../../README.md">English</a> ·
  <a href="README.es.md">Español</a> ·
  <a href="README.fr.md">Français</a> ·
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
  Betreibe den Escalated-Helpdesk in deinem bestehenden Filament-Adminpanel.<br>
  Tickets, Abteilungen, SLA-Richtlinien, Eskalationsregeln und Makros als native Filament-Ressourcen, auf Basis von Escalated for Laravel.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Website</b></a> ·
  <a href="https://escalated.dev/docs"><b>Dokumentation</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>Änderungsprotokoll</b></a>
</p>

## Was es kann

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Dein Helpdesk in dem Panel, das du schon nutzt</h3>
      Ticketlisten mit Filtern, Schnell-Tabs und Massenaktionen sowie eine Ticketseite mit der Konversation, SLA-Details und Aktionen zum Antworten, Zuweisen und Anwenden von Makros mit einem Klick.
    </td>
    <td width="50%" valign="top">
      <h3>Dieselbe Engine wie Escalated for Laravel</h3>
      SLAs, Eskalationsregeln, Events und Webhooks kommen alle aus <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>, daher wird nichts doppelt gepflegt und nichts läuft auseinander.
    </td>
  </tr>
</table>

## Funktionen

<table>
<tr>
<td width="33%" valign="top">

**Tickets**
- Listen-, Detail- und Erstellungsseiten
- Filter nach Status, Priorität, Agent, SLA
- Tabs: meine, nicht zugewiesen, dringend, SLA gefährdet
- Massenweise zuweisen, Status, Priorität, Tags, schließen
- Antworten, Notiz, zuweisen, folgen, Makro anwenden
- Eigene Ticketaktionen aus deiner Konfiguration

</td>
<td width="33%" valign="top">

**Admin-Ressourcen**
- Abteilungen mit Agentenzuweisung
- Tags mit Farbauswahl
- SLA-Richtlinien pro Priorität
- Builder für Bedingungen und Aktionen von Eskalationsregeln
- Textbausteine mit Kategorien
- Makros mit sortierbaren Schritten

</td>
<td width="33%" valign="top">

**Dashboard-Widgets**
- Statistiken zu offenen, nicht zugewiesenen und verletzten Tickets sowie CSAT
- Diagramme zu Tickets nach Status und Priorität
- CSAT-Übersicht
- Neueste Tickets
- Tickets mit verletzten SLAs

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Konversation**
- Antwortverlauf mit internen Notizen
- Angeheftete Notizen und Textbausteine
- Aktivitätsprotokoll pro Ticket
- Ticket-Follower
- Zufriedenheitsbewertung mit Sternen

</td>
<td width="33%" valign="top">

**Seiten**
- Support-Dashboard mit allen Widgets
- Berichte nach Abteilung für einen Zeitraum
- Einstellungen für Präfix, Gäste, automatisches Schließen, Anhänge

</td>
<td width="33%" valign="top">

**Newsletter (optional)**
- Markdown-Newsletter mit geplantem Versand
- Statische und dynamische Empfängerlisten
- Wiederverwendbare Vorlagen mit Themes

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">Alle Funktionen im Detail</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Schnellstart

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Definiere danach die Gates `escalated-agent` und `escalated-admin` und füge `EscalatedFilamentPlugin::make()` zu deinem Panel hinzu. Voraussetzungen: PHP 8.2+, Laravel 11 oder 12 und Filament 3.x, 4.x oder 5.x. Die [Installationsanleitung](../installation.md) beschreibt beide Schritte.

## Mehr erfahren

- [Alle Funktionen](../features.md) · [So funktioniert es](../how-it-works.md) · [Installation](../installation.md)
- [Konfiguration, Views und Übersetzungen](../configuration.md) · [Ticketzugriff und Routing](../ticket-access-and-routing.md) · [Plugins](../plugins.md)
- [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel) für die Referenz der Kernkonfiguration

Escalated gibt es auch für [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) und [WordPress](https://github.com/escalated-dev/escalated-wordpress). Die gemeinsame Vue-Oberfläche liegt in [escalated](https://github.com/escalated-dev/escalated).

## Lizenz

MIT. Copyright (c) 2026 Escalated.dev. Siehe [LICENSE](../../LICENSE).
