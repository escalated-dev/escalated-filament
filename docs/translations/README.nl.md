<p align="center"><sub>
  <a href="README.ar.md">العربية</a> ·
  <a href="README.de.md">Deutsch</a> ·
  <a href="../../README.md">English</a> ·
  <a href="README.es.md">Español</a> ·
  <a href="README.fr.md">Français</a> ·
  <a href="README.it.md">Italiano</a> ·
  <a href="README.ja.md">日本語</a> ·
  <a href="README.ko.md">한국어</a> ·
  <b>Nederlands</b> ·
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
  Draai de Escalated-helpdesk binnen je bestaande Filament-adminpaneel.<br>
  Tickets, afdelingen, SLA-beleid, escalatieregels en macro's als native Filament-resources, gebouwd op Escalated for Laravel.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Website</b></a> ·
  <a href="https://escalated.dev/docs"><b>Documentatie</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>Wijzigingslog</b></a>
</p>

## Wat het doet

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Je helpdesk in het paneel dat je al gebruikt</h3>
      Ticketlijsten met filters, snelle tabbladen en bulkacties, en een ticketpagina met het gesprek, SLA-details en acties met één klik om te antwoorden, toe te wijzen en macro's toe te passen.
    </td>
    <td width="50%" valign="top">
      <h3>Dezelfde engine als Escalated for Laravel</h3>
      SLA's, escalatieregels, events en webhooks komen allemaal uit <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>, dus niets wordt gedupliceerd en niets loopt uit de pas.
    </td>
  </tr>
</table>

## Functies

<table>
<tr>
<td width="33%" valign="top">

**Tickets**
- Lijst-, detail- en aanmaakpagina's
- Filters op status, prioriteit, agent, SLA
- Tabbladen: mijn tickets, niet toegewezen, urgent, SLA dreigt te verlopen
- In bulk toewijzen, status, prioriteit, tags, sluiten
- Antwoorden, notitie, toewijzen, volgen, macro toepassen
- Eigen ticketacties vanuit je configuratie

</td>
<td width="33%" valign="top">

**Beheerresources**
- Afdelingen met toewijzing van agents
- Tags met kleurkiezer
- SLA-beleid per prioriteit
- Builder voor voorwaarden en acties van escalatieregels
- Standaardantwoorden met categorieën
- Macro's met herschikbare stappen

</td>
<td width="33%" valign="top">

**Dashboardwidgets**
- Statistieken voor open, niet toegewezen, overschreden en CSAT
- Grafieken van tickets per status en prioriteit
- CSAT-overzicht
- Recente tickets
- Tickets met overschreden SLA's

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Gesprek**
- Antwoordthread met interne notities
- Vastgezette notities en standaardantwoorden
- Activiteitenlog per ticket
- Volgers van een ticket
- Tevredenheidsbeoordeling met sterren

</td>
<td width="33%" valign="top">

**Pagina's**
- Supportdashboard met alle widgets
- Rapporten per afdeling over een periode
- Instellingen voor prefix, gasten, automatisch sluiten, bijlagen

</td>
<td width="33%" valign="top">

**Nieuwsbrieven (optioneel)**
- Markdown-nieuwsbrieven met geplande verzending
- Statische en dynamische ontvangerslijsten
- Herbruikbare sjablonen met thema's

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">Alle functies in detail</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Snel aan de slag

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Definieer daarna de gates `escalated-agent` en `escalated-admin` en voeg `EscalatedFilamentPlugin::make()` toe aan je paneel. Vereist PHP 8.2+, Laravel 11 of 12 en Filament 3.x, 4.x of 5.x. De [installatiehandleiding](../installation.md) beschrijft beide stappen.

## Meer informatie

- [Alle functies](../features.md) · [Hoe het werkt](../how-it-works.md) · [Installatie](../installation.md)
- [Configuratie, views en vertalingen](../configuration.md) · [Tickettoegang en routering](../ticket-access-and-routing.md) · [Plugins](../plugins.md)
- [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel) voor de referentie van de kernconfiguratie

Escalated is ook beschikbaar voor [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) en [WordPress](https://github.com/escalated-dev/escalated-wordpress). De gedeelde Vue-UI staat in [escalated](https://github.com/escalated-dev/escalated).

## Licentie

MIT. Copyright (c) 2026 Escalated.dev. Zie [LICENSE](../../LICENSE).
