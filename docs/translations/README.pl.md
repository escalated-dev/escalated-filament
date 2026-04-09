<p align="center">
  <a href="README.ar.md">العربية</a> •
  <a href="README.de.md">Deutsch</a> •
  <a href="../../README.md">English</a> •
  <a href="README.es.md">Español</a> •
  <a href="README.fr.md">Français</a> •
  <a href="README.it.md">Italiano</a> •
  <a href="README.ja.md">日本語</a> •
  <a href="README.ko.md">한국어</a> •
  <a href="README.nl.md">Nederlands</a> •
  <b>Polski</b> •
  <a href="README.pt-BR.md">Português (BR)</a> •
  <a href="README.ru.md">Русский</a> •
  <a href="README.tr.md">Türkçe</a> •
  <a href="README.zh-CN.md">简体中文</a>
</p>

# Escalated dla Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Wtyczka panelu administracyjnego [Filament](https://filamentphp.com) dla systemu zgłoszeń wsparcia [Escalated](https://github.com/escalated-dev/escalated-laravel). Zarządzaj zgłoszeniami, działami, politykami SLA, regułami eskalacji, makrami i nie tylko — wszystko z poziomu istniejącego panelu administracyjnego Filament.

> **[escalated.dev](https://escalated.dev)** — Dowiedz się więcej, obejrzyj dema i porównaj opcje Cloud vs Self-Hosted.

## Jak to działa

Escalated dla Filament to **wrapper wtyczki Filament** wokół [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). Nie powiela żadnej logiki biznesowej. Zamiast tego dostarcza zasoby, strony, widżety i akcje Filament, które wywołują te same usługi, modele i zdarzenia z pakietu bazowego Laravel. Oznacza to:

- Cała logika cyklu życia zgłoszeń, obliczenia SLA i reguły eskalacji pochodzą z `escalated-laravel`
- Tabele bazy danych, migracje i konfiguracja są zarządzane przez pakiet bazowy
- Zdarzenia, powiadomienia i webhooki działają dokładnie tak samo jak w interfejsie Inertia
- Otrzymujesz natywne doświadczenie Filament bez konieczności utrzymywania osobnej bazy kodu

> **Uwaga:** Ten pakiet używa natywnych komponentów Livewire + Blade Filament (tabele, formularze, listy informacyjne, akcje, widżety) zamiast niestandardowego interfejsu Vue 3 + Inertia.js z pakietu frontend [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated). Podstawowa funkcjonalność jest taka sama — te same modele, usługi, baza danych i logika biznesowa — ale wygląd interfejsu podąża za systemem projektowym Filament. Niektóre interakcje mogą się nieznacznie różnić (np. modale Filament vs. formularze inline, filtry tabel Filament vs. niestandardowe komponenty filtrów). Jeśli potrzebujesz dokładnego odwzorowania frontendu Inertia, użyj `escalated-laravel` bezpośrednio ze współdzielonymi komponentami Vue.

## Wymagania

- PHP 8.2+
- Laravel 11 lub 12
- Filament 3.x, 4.x lub 5.x
- escalated-dev/escalated-laravel ^0.5

### Kompatybilność wersji

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## Instalacja

### 1. Zainstaluj pakiety

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

Jeśli `escalated-laravel` jest już zainstalowany, dodaj tylko wtyczkę Filament:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Uruchom instalator Escalated (jeśli jeszcze tego nie zrobiono)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Zdefiniuj bramki autoryzacji

W dostawcy usług (np. `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Zarejestruj wtyczkę w panelu Filament

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

Gotowe. Odwiedź panel Filament — pojawi się grupa nawigacji **Wsparcie** ze wszystkimi zasobami zarządzania zgłoszeniami.

## Funkcje

### Zasoby

- **TicketResource** — Pełne zarządzanie zgłoszeniami ze stronami listy, widoku i tworzenia
  - Filtrowanie po statusie, priorytecie, dziale, agencie, tagach, SLA
  - Zakładki szybkiego filtrowania: Wszystkie, Moje zgłoszenia, Nieprzypisane, Pilne, Naruszenie SLA
  - Akcje masowe: Przypisz, Zmień status, Zmień priorytet, Dodaj tagi, Zamknij, Usuń
  - Strona widoku z wątkiem konwersacji, szczegółami paska bocznego, informacjami SLA, oceną satysfakcji
  - Akcje nagłówka: Odpowiedz, Notatka, Przypisz, Status, Priorytet, Obserwuj, Makro, Rozwiąż, Zamknij, Otwórz ponownie
- **DepartmentResource** — CRUD dla działów wsparcia z przypisaniem agentów
- **TagResource** — CRUD dla tagów zgłoszeń z selektorem kolorów
- **SlaPolicyResource** — Zarządzanie politykami SLA z czasami odpowiedzi/rozwiązania na priorytet
- **EscalationRuleResource** — Kreator warunków/akcji dla automatycznych reguł eskalacji
- **CannedResponseResource** — Wstępnie napisane szablony odpowiedzi z kategoriami
- **MacroResource** — Wieloakcyjne makra automatyzacji z krokami do zmiany kolejności

### Widżety pulpitu

- **TicketStatsOverview** — Kluczowe metryki: Moje otwarte, Nieprzypisane, Łącznie otwarte, Naruszenie SLA, Rozwiązane dziś, CSAT
- **TicketsByStatusChart** — Wykres pierścieniowy dystrybucji zgłoszeń według statusu
- **TicketsByPriorityChart** — Wykres słupkowy otwartych zgłoszeń według priorytetu
- **CsatOverviewWidget** — Metryki satysfakcji klienta: Średnia ocena, Łączne oceny, Wskaźnik satysfakcji
- **RecentTicketsWidget** — Tabela 5 najnowszych zgłoszeń
- **SlaBreachWidget** — Tabela zgłoszeń z naruszonymi celami SLA

### Strony

- **Dashboard** — Pulpit wsparcia ze wszystkimi widżetami
- **Reports** — Analityka zakresu dat ze statystykami, podziałem na działy i osią czasu
- **Settings** — Ustawienia administratora dla prefiksu referencji, zgłoszeń gości, automatycznego zamykania, limitów załączników

### Menedżerowie relacji

- **RepliesRelationManager** — Wątek odpowiedzi z notatkami wewnętrznymi, przypinaniem i wstawianiem gotowych odpowiedzi
- **ActivitiesRelationManager** — Dziennik audytu tylko do odczytu wszystkich aktywności zgłoszeń
- **FollowersRelationManager** — Zarządzanie obserwatorami zgłoszeń

### Akcje wielokrotnego użytku

- `AssignTicketAction` — Przypisz zgłoszenie do agenta
- `ChangeStatusAction` — Zmień status zgłoszenia
- `ChangePriorityAction` — Zmień priorytet zgłoszenia
- `ApplyMacroAction` — Zastosuj makro do zgłoszenia
- `FollowTicketAction` — Przełącz obserwowanie zgłoszenia
- `PinReplyAction` — Przypnij/odepnij notatki wewnętrzne

### Niestandardowe komponenty Livewire

- **TicketConversation** — Pełny wątek konwersacji z edytorem odpowiedzi, wstawianiem gotowych odpowiedzi i przypinaniem notatek
- **SatisfactionRating** — Wyświetlanie oceny satysfakcji klienta z wizualizacją gwiazdkową

## Konfiguracja

Wtyczka jest konfigurowana poprzez łańcuchowanie metod na instancji wtyczki:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Etykieta grupy nawigacji (domyślnie: 'Support')
    ->agentGate('escalated-agent')  // Bramka dostępu agenta (domyślnie: 'escalated-agent')
    ->adminGate('escalated-admin')  // Bramka dostępu admina (domyślnie: 'escalated-admin')
```

Cała pozostała konfiguracja (SLA, tryby hostingu, powiadomienia itp.) jest zarządzana przez pakiet bazowy `escalated-laravel` w `config/escalated.php`. Zobacz [README escalated-laravel](https://github.com/escalated-dev/escalated-laravel) dla pełnej referencji konfiguracji.

## Publikowanie widoków

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Zrzuty ekranu

_Wkrótce._

## Dostępne również dla

- **[Escalated dla Laravel](https://github.com/escalated-dev/escalated-laravel)** — Pakiet Composer Laravel
- **[Escalated dla Rails](https://github.com/escalated-dev/escalated-rails)** — Silnik Ruby on Rails
- **[Escalated dla Django](https://github.com/escalated-dev/escalated-django)** — Wielokrotnego użytku aplikacja Django
- **[Escalated dla AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — Pakiet AdonisJS v6
- **[Escalated dla Filament](https://github.com/escalated-dev/escalated-filament)** — Wtyczka panelu administracyjnego Filament (jesteś tutaj)
- **[Współdzielony frontend](https://github.com/escalated-dev/escalated)** — Komponenty interfejsu Vue 3 + Inertia.js

Ta sama architektura, ten sam system zgłoszeń — natywne doświadczenie Filament dla paneli administracyjnych Laravel.

## Licencja

MIT
