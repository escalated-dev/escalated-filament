<p align="center"><sub>
  <a href="README.ar.md">العربية</a> ·
  <a href="README.de.md">Deutsch</a> ·
  <a href="../../README.md">English</a> ·
  <a href="README.es.md">Español</a> ·
  <a href="README.fr.md">Français</a> ·
  <b>Italiano</b> ·
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
  Usa l'helpdesk di Escalated dentro il pannello di amministrazione Filament che hai già.<br>
  Ticket, reparti, policy SLA, regole di escalation e macro come risorse native di Filament, basate su Escalated for Laravel.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Sito web</b></a> ·
  <a href="https://escalated.dev/docs"><b>Documentazione</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>Registro delle modifiche</b></a>
</p>

## Cosa fa

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Il tuo helpdesk nel pannello che usi già</h3>
      Elenchi di ticket con filtri, schede rapide e azioni di massa, e una pagina del ticket con la conversazione, i dettagli SLA e azioni con un clic per rispondere, assegnare e applicare macro.
    </td>
    <td width="50%" valign="top">
      <h3>Lo stesso motore di Escalated for Laravel</h3>
      SLA, regole di escalation, eventi e webhook arrivano tutti da <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>, quindi niente è duplicato e niente va fuori sincronia.
    </td>
  </tr>
</table>

## Funzionalità

<table>
<tr>
<td width="33%" valign="top">

**Ticket**
- Pagine di elenco, dettaglio e creazione
- Filtri per stato, priorità, agente, SLA
- Schede: miei, non assegnati, urgenti, SLA a rischio
- Assegnazione, stato, priorità, tag e chiusura in blocco
- Rispondi, nota, assegna, segui, applica macro
- Azioni sui ticket personalizzate dalla tua configurazione

</td>
<td width="33%" valign="top">

**Risorse di amministrazione**
- Reparti con assegnazione degli agenti
- Tag con selettore colore
- Policy SLA per priorità
- Editor di condizioni e azioni per le regole di escalation
- Risposte predefinite con categorie
- Macro con passaggi riordinabili

</td>
<td width="33%" valign="top">

**Widget della dashboard**
- Statistiche su ticket aperti, non assegnati, con SLA violato e CSAT
- Grafici dei ticket per stato e priorità
- Panoramica CSAT
- Ticket recenti
- Ticket con SLA violati

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Conversazione**
- Thread di risposte con note interne
- Note fissate e risposte predefinite
- Registro attività per ticket
- Follower del ticket
- Valutazione della soddisfazione a stelle

</td>
<td width="33%" valign="top">

**Pagine**
- Dashboard di supporto con tutti i widget
- Report per reparto su un intervallo di date
- Impostazioni per prefisso, ospiti, chiusura automatica, allegati

</td>
<td width="33%" valign="top">

**Newsletter (opzionale)**
- Newsletter in Markdown con invio programmato
- Liste di destinatari statiche e dinamiche
- Template riutilizzabili con temi

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">Tutte le funzionalità in dettaglio</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Avvio rapido

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Poi definisci i gate `escalated-agent` e `escalated-admin` e aggiungi `EscalatedFilamentPlugin::make()` al tuo pannello. Richiede PHP 8.2+, Laravel 11 o 12 e Filament 3.x, 4.x o 5.x. La [guida all'installazione](../installation.md) spiega entrambi i passaggi.

## Per saperne di più

- [Tutte le funzionalità](../features.md) · [Come funziona](../how-it-works.md) · [Installazione](../installation.md)
- [Configurazione, viste e traduzioni](../configuration.md) · [Accesso ai ticket e instradamento](../ticket-access-and-routing.md) · [Plugin](../plugins.md)
- [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel) per il riferimento della configurazione di base

Escalated è disponibile anche per [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) e [WordPress](https://github.com/escalated-dev/escalated-wordpress). L'interfaccia Vue condivisa si trova in [escalated](https://github.com/escalated-dev/escalated).

## Licenza

MIT. Copyright (c) 2026 Escalated.dev. Vedi [LICENSE](../../LICENSE).
