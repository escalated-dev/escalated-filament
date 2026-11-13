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
  <a href="README.pl.md">Polski</a> ·
  <a href="README.pt-BR.md">Português (BR)</a> ·
  <b>Русский</b> ·
  <a href="README.tr.md">Türkçe</a> ·
  <a href="README.zh-CN.md">简体中文</a>
</sub></p>

<p align="center">
  <a href="https://escalated.dev"><img src="https://raw.githubusercontent.com/escalated-dev/escalated/main/docs/assets/readme/logo.png" width="72" height="72" alt="Escalated"></a>
</p>

<h1 align="center">Escalated for Filament</h1>

<p align="center">
  Запустите хелпдеск Escalated прямо в вашей админ-панели Filament.<br>
  Тикеты, отделы, политики SLA, правила эскалации и макросы в виде нативных ресурсов Filament на основе Escalated for Laravel.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Сайт</b></a> ·
  <a href="https://escalated.dev/docs"><b>Документация</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>Список изменений</b></a>
</p>

## Что он делает

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Хелпдеск в панели, которой вы уже пользуетесь</h3>
      Списки тикетов с фильтрами, быстрыми вкладками и массовыми действиями, а также страница тикета с перепиской, деталями SLA и действиями в один клик: ответ, назначение и макрос.
    </td>
    <td width="50%" valign="top">
      <h3>Тот же движок, что и в Escalated for Laravel</h3>
      SLA, правила эскалации, события и вебхуки берутся из <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>, поэтому ничего не дублируется и ничего не расходится.
    </td>
  </tr>
</table>

## Возможности

<table>
<tr>
<td width="33%" valign="top">

**Тикеты**
- Страницы списка, просмотра и создания
- Фильтры по статусу, приоритету, агенту, SLA
- Вкладки: мои, неназначенные, срочные, близкие к нарушению SLA
- Массовое назначение, статус, приоритет, теги, закрытие
- Ответ, заметка, назначение, подписка, применение макроса
- Собственные действия с тикетами из вашей конфигурации

</td>
<td width="33%" valign="top">

**Ресурсы администрирования**
- Отделы с назначением агентов
- Теги с выбором цвета
- Политики SLA по приоритетам
- Конструктор условий и действий для правил эскалации
- Шаблоны ответов с категориями
- Макросы с шагами, которые можно переставлять

</td>
<td width="33%" valign="top">

**Виджеты панели**
- Статистика открытых, неназначенных, просроченных тикетов и CSAT
- Диаграммы тикетов по статусу и приоритету
- Обзор CSAT
- Последние тикеты
- Тикеты с нарушенным SLA

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Переписка**
- Ветка ответов с внутренними заметками
- Закреплённые заметки и шаблоны ответов
- Журнал действий по каждому тикету
- Подписчики тикета
- Оценка удовлетворённости звёздами

</td>
<td width="33%" valign="top">

**Страницы**
- Панель поддержки со всеми виджетами
- Отчёты по отделам за выбранный период
- Настройки префикса, гостей, автозакрытия, вложений

</td>
<td width="33%" valign="top">

**Рассылки (необязательно)**
- Рассылки в Markdown с отложенной отправкой
- Статические и динамические списки получателей
- Многоразовые шаблоны с темами

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">Все возможности подробно</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Быстрый старт

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Затем определите гейты `escalated-agent` и `escalated-admin` и добавьте `EscalatedFilamentPlugin::make()` в вашу панель. Требуются PHP 8.2+, Laravel 11 или 12 и Filament 3.x, 4.x или 5.x. Оба шага описаны в [руководстве по установке](../installation.md).

## Подробнее

- [Все возможности](../features.md) · [Как это работает](../how-it-works.md) · [Установка](../installation.md)
- [Конфигурация, представления и переводы](../configuration.md) · [Доступ к тикетам и маршрутизация](../ticket-access-and-routing.md) · [Плагины](../plugins.md)
- [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel): справочник по базовой конфигурации

Escalated также доступен для [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) и [WordPress](https://github.com/escalated-dev/escalated-wordpress). Общий интерфейс на Vue находится в [escalated](https://github.com/escalated-dev/escalated).

## Лицензия

MIT. Copyright (c) 2026 Escalated.dev. См. [LICENSE](../../LICENSE).
