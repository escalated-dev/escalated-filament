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
  <a href="README.pl.md">Polski</a> •
  <a href="README.pt-BR.md">Português (BR)</a> •
  <b>Русский</b> •
  <a href="README.tr.md">Türkçe</a> •
  <a href="README.zh-CN.md">简体中文</a>
</p>

# Escalated для Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Плагин панели администрирования [Filament](https://filamentphp.com) для системы тикетов поддержки [Escalated](https://github.com/escalated-dev/escalated-laravel). Управляйте тикетами, отделами, политиками SLA, правилами эскалации, макросами и многим другим — всё из вашей существующей панели администрирования Filament.

> **[escalated.dev](https://escalated.dev)** — Узнайте больше, посмотрите демо и сравните варианты Cloud и Self-Hosted.

## Как это работает

Escalated для Filament — это **обёртка плагина Filament** вокруг [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). Он не дублирует бизнес-логику. Вместо этого предоставляет ресурсы, страницы, виджеты и действия Filament, которые вызывают те же сервисы, модели и события из основного пакета Laravel. Это означает:

- Вся логика жизненного цикла тикетов, расчёты SLA и правила эскалации поступают из `escalated-laravel`
- Таблицы базы данных, миграции и конфигурация управляются основным пакетом
- События, уведомления и вебхуки срабатывают точно так же, как в интерфейсе Inertia
- Вы получаете нативный опыт Filament без необходимости поддерживать отдельную кодовую базу

> **Примечание:** Этот пакет использует нативные компоненты Livewire + Blade от Filament (таблицы, формы, информационные списки, действия, виджеты) вместо пользовательского интерфейса Vue 3 + Inertia.js из пакета [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated). Базовая функциональность одинакова — те же модели, сервисы, база данных и бизнес-логика — но внешний вид интерфейса следует дизайн-системе Filament. Некоторые взаимодействия могут незначительно отличаться (например, модальные окна Filament vs. встроенные формы, фильтры таблиц Filament vs. пользовательские компоненты фильтров). Если вам нужно точное соответствие с фронтендом Inertia, используйте `escalated-laravel` напрямую с общими Vue-компонентами.

## Требования

- PHP 8.2+
- Laravel 11 или 12
- Filament 3.x, 4.x или 5.x
- escalated-dev/escalated-laravel ^0.5

### Совместимость версий

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## Установка

### 1. Установка пакетов

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

Если `escalated-laravel` уже установлен, просто добавьте плагин Filament:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Запуск установщика Escalated (если ещё не сделано)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Определение шлюзов авторизации

В сервис-провайдере (например, `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Регистрация плагина в панели Filament

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

Готово. Посетите вашу панель Filament — появится навигационная группа **Поддержка** со всеми ресурсами управления тикетами.

## Возможности

### Ресурсы

- **TicketResource** — Полное управление тикетами со страницами списка, просмотра и создания
  - Фильтрация по статусу, приоритету, отделу, агенту, тегам, SLA
  - Вкладки быстрого фильтра: Все, Мои тикеты, Неназначенные, Срочные, Нарушение SLA
  - Массовые действия: Назначить, Изменить статус, Изменить приоритет, Добавить теги, Закрыть, Удалить
  - Страница просмотра с цепочкой переписки, деталями боковой панели, информацией SLA, оценкой удовлетворённости
  - Действия заголовка: Ответить, Заметка, Назначить, Статус, Приоритет, Следить, Макрос, Решить, Закрыть, Переоткрыть
- **DepartmentResource** — CRUD для отделов поддержки с назначением агентов
- **TagResource** — CRUD для тегов тикетов с выбором цвета
- **SlaPolicyResource** — Управление политиками SLA с временем ответа/решения по приоритетам
- **EscalationRuleResource** — Конструктор условий/действий для автоматических правил эскалации
- **CannedResponseResource** — Готовые шаблоны ответов с категориями
- **MacroResource** — Многодействийные макросы автоматизации с перетасовываемыми шагами

### Виджеты панели

- **TicketStatsOverview** — Ключевые метрики: Мои открытые, Неназначенные, Всего открытых, Нарушение SLA, Решено сегодня, CSAT
- **TicketsByStatusChart** — Кольцевая диаграмма распределения тикетов по статусу
- **TicketsByPriorityChart** — Столбчатая диаграмма открытых тикетов по приоритету
- **CsatOverviewWidget** — Метрики удовлетворённости клиентов: Средняя оценка, Всего оценок, Коэффициент удовлетворённости
- **RecentTicketsWidget** — Таблица 5 последних тикетов
- **SlaBreachWidget** — Таблица тикетов с нарушенными целями SLA

### Страницы

- **Dashboard** — Панель поддержки со всеми виджетами
- **Reports** — Аналитика по диапазону дат со статистикой, разбивкой по отделам и временной шкалой
- **Settings** — Настройки администратора для префикса ссылки, гостевых тикетов, автозакрытия, лимитов вложений

### Менеджеры связей

- **RepliesRelationManager** — Цепочка ответов с внутренними заметками, закреплением и вставкой готовых ответов
- **ActivitiesRelationManager** — Журнал аудита только для чтения всех действий по тикетам
- **FollowersRelationManager** — Управление подписчиками тикетов

### Повторно используемые действия

- `AssignTicketAction` — Назначить тикет агенту
- `ChangeStatusAction` — Изменить статус тикета
- `ChangePriorityAction` — Изменить приоритет тикета
- `ApplyMacroAction` — Применить макрос к тикету
- `FollowTicketAction` — Переключить отслеживание тикета
- `PinReplyAction` — Закрепить/открепить внутренние заметки

### Пользовательские компоненты Livewire

- **TicketConversation** — Полная цепочка переписки с редактором ответов, вставкой готовых ответов и закреплением заметок
- **SatisfactionRating** — Отображение оценки удовлетворённости клиента со звёздной визуализацией

## Конфигурация

Плагин настраивается через цепочку методов на экземпляре плагина:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Метка навигационной группы (по умолчанию: 'Support')
    ->agentGate('escalated-agent')  // Шлюз доступа агента (по умолчанию: 'escalated-agent')
    ->adminGate('escalated-admin')  // Шлюз доступа администратора (по умолчанию: 'escalated-admin')
```

Вся остальная конфигурация (SLA, режимы хостинга, уведомления и т.д.) управляется основным пакетом `escalated-laravel` в `config/escalated.php`. Смотрите [README escalated-laravel](https://github.com/escalated-dev/escalated-laravel) для полного справочника конфигурации.

## Публикация представлений

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Скриншоты

_Скоро._

## Также доступно для

- **[Escalated для Laravel](https://github.com/escalated-dev/escalated-laravel)** — Composer-пакет Laravel
- **[Escalated для Rails](https://github.com/escalated-dev/escalated-rails)** — Движок Ruby on Rails
- **[Escalated для Django](https://github.com/escalated-dev/escalated-django)** — Повторно используемое приложение Django
- **[Escalated для AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — Пакет AdonisJS v6
- **[Escalated для Filament](https://github.com/escalated-dev/escalated-filament)** — Плагин панели администрирования Filament (вы здесь)
- **[Общий фронтенд](https://github.com/escalated-dev/escalated)** — Компоненты интерфейса Vue 3 + Inertia.js

Та же архитектура, та же система тикетов — нативный опыт Filament для панелей администрирования Laravel.

## Лицензия

MIT
