<p align="center"><sub>
  <a href="README.ar.md">العربية</a> ·
  <a href="README.de.md">Deutsch</a> ·
  <a href="../../README.md">English</a> ·
  <a href="README.es.md">Español</a> ·
  <a href="README.fr.md">Français</a> ·
  <a href="README.it.md">Italiano</a> ·
  <a href="README.ja.md">日本語</a> ·
  <a href="README.ko.md">한국어</a> ·
  <a href="README.nl.md">Nederlands</a> ·
  <b>Polski</b> ·
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
  Uruchom helpdesk Escalated w swoim obecnym panelu administracyjnym Filament.<br>
  Zgłoszenia, działy, polityki SLA, reguły eskalacji i makra jako natywne zasoby Filament, oparte na Escalated for Laravel.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Strona</b></a> ·
  <a href="https://escalated.dev/docs"><b>Dokumentacja</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>Lista zmian</b></a>
</p>

## Co robi

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Helpdesk w panelu, którego już używasz</h3>
      Listy zgłoszeń z filtrami, szybkimi zakładkami i akcjami zbiorczymi oraz strona zgłoszenia z rozmową, szczegółami SLA i akcjami jednym kliknięciem: odpowiedź, przypisanie i makro.
    </td>
    <td width="50%" valign="top">
      <h3>Ten sam silnik co Escalated for Laravel</h3>
      SLA, reguły eskalacji, zdarzenia i webhooki pochodzą z <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>, więc nic nie jest powielane i nic się nie rozjeżdża.
    </td>
  </tr>
</table>

## Funkcje

<table>
<tr>
<td width="33%" valign="top">

**Zgłoszenia**
- Strony listy, podglądu i tworzenia
- Filtry według statusu, priorytetu, agenta, SLA
- Zakładki: moje, nieprzypisane, pilne, zagrożone SLA
- Zbiorcze przypisanie, status, priorytet, tagi, zamknięcie
- Odpowiedź, notatka, przypisanie, obserwowanie, makro
- Własne akcje zgłoszeń z Twojej konfiguracji

</td>
<td width="33%" valign="top">

**Zasoby administracyjne**
- Działy z przypisaniem agentów
- Tagi z wyborem koloru
- Polityki SLA dla każdego priorytetu
- Kreator warunków i akcji reguł eskalacji
- Gotowe odpowiedzi z kategoriami
- Makra z krokami o zmiennej kolejności

</td>
<td width="33%" valign="top">

**Widżety pulpitu**
- Statystyki otwartych, nieprzypisanych, z przekroczonym SLA i CSAT
- Wykresy zgłoszeń według statusu i priorytetu
- Przegląd CSAT
- Ostatnie zgłoszenia
- Zgłoszenia z przekroczonym SLA

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Rozmowa**
- Wątek odpowiedzi z notatkami wewnętrznymi
- Przypięte notatki i gotowe odpowiedzi
- Dziennik aktywności dla każdego zgłoszenia
- Obserwujący zgłoszenie
- Ocena zadowolenia w gwiazdkach

</td>
<td width="33%" valign="top">

**Strony**
- Pulpit wsparcia ze wszystkimi widżetami
- Raporty według działu dla zakresu dat
- Ustawienia prefiksu, gości, automatycznego zamykania, załączników

</td>
<td width="33%" valign="top">

**Newslettery (opcjonalnie)**
- Newslettery w Markdown z planowaną wysyłką
- Statyczne i dynamiczne listy odbiorców
- Szablony wielokrotnego użytku z motywami

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">Wszystkie funkcje w szczegółach</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Szybki start

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Następnie zdefiniuj bramki dostępu (gates) `escalated-agent` i `escalated-admin` i dodaj `EscalatedFilamentPlugin::make()` do swojego panelu. Wymaga PHP 8.2+, Laravel 11 lub 12 oraz Filament 3.x, 4.x lub 5.x. [Przewodnik instalacji](../installation.md) opisuje oba kroki.

## Dowiedz się więcej

- [Wszystkie funkcje](../features.md) · [Jak to działa](../how-it-works.md) · [Instalacja](../installation.md)
- [Konfiguracja, widoki i tłumaczenia](../configuration.md) · [Dostęp do zgłoszeń i routing](../ticket-access-and-routing.md) · [Wtyczki](../plugins.md)
- [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel), gdzie znajduje się opis konfiguracji rdzenia

Escalated jest dostępny również dla [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) i [WordPress](https://github.com/escalated-dev/escalated-wordpress). Wspólny interfejs Vue znajduje się w [escalated](https://github.com/escalated-dev/escalated).

## Licencja

MIT. Copyright (c) 2026 Escalated.dev. Zobacz [LICENSE](../../LICENSE).
