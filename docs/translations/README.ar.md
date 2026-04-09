<p align="center">
  <b>العربية</b> •
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
  <a href="README.ru.md">Русский</a> •
  <a href="README.tr.md">Türkçe</a> •
  <a href="README.zh-CN.md">简体中文</a>
</p>

# Escalated لـ Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

إضافة للوحة تحكم [Filament](https://filamentphp.com) لنظام تذاكر الدعم [Escalated](https://github.com/escalated-dev/escalated-laravel). إدارة التذاكر والأقسام وسياسات SLA وقواعد التصعيد والماكرو والمزيد — كل ذلك من داخل لوحة تحكم Filament الحالية.

> **[escalated.dev](https://escalated.dev)** — تعرف على المزيد، شاهد العروض التوضيحية، وقارن بين خيارات السحابة والاستضافة الذاتية.

## كيف يعمل

Escalated لـ Filament هو **غلاف إضافة Filament** حول [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). لا يكرر أي منطق أعمال. بدلاً من ذلك، يوفر موارد وصفحات وعناصر واجهة وإجراءات Filament التي تستدعي نفس الخدمات والنماذج والأحداث من حزمة Laravel الأساسية. هذا يعني:

- جميع منطق دورة حياة التذاكر وحسابات SLA وقواعد التصعيد تأتي من `escalated-laravel`
- جداول قاعدة البيانات والهجرات والإعدادات تُدار بواسطة الحزمة الأساسية
- الأحداث والإشعارات وخطافات الويب تعمل تماماً كما في واجهة Inertia
- تحصل على تجربة Filament أصلية دون الحاجة لصيانة قاعدة كود منفصلة

> **ملاحظة:** تستخدم هذه الحزمة مكونات Livewire + Blade الأصلية من Filament (الجداول، النماذج، قوائم المعلومات، الإجراءات، عناصر الواجهة) بدلاً من واجهة Vue 3 + Inertia.js المخصصة من حزمة [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated) الأمامية. الوظائف الأساسية هي نفسها — نفس النماذج والخدمات وقاعدة البيانات ومنطق الأعمال — لكن شكل وأسلوب الواجهة يتبع نظام تصميم Filament. قد تختلف بعض التفاعلات قليلاً (مثل نوافذ Filament المنبثقة مقابل النماذج المضمنة، مرشحات جداول Filament مقابل مكونات المرشح المخصصة). إذا كنت بحاجة إلى تطابق دقيق مع واجهة Inertia الأمامية، استخدم `escalated-laravel` مباشرة مع مكونات Vue المشتركة بدلاً من ذلك.

## المتطلبات

- PHP 8.2+
- Laravel 11 أو 12
- Filament 3.x أو 4.x أو 5.x
- escalated-dev/escalated-laravel ^0.5

### توافق الإصدارات

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## التثبيت

### 1. تثبيت الحزم

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

إذا كان `escalated-laravel` مثبتاً لديك بالفعل، أضف إضافة Filament فقط:

```bash
composer require escalated-dev/escalated-filament
```

### 2. تشغيل مُثبّت Escalated (إذا لم يتم ذلك بالفعل)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. تعريف بوابات التفويض

في مزود خدمة (مثل `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. تسجيل الإضافة في لوحة Filament

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

أنت جاهز الآن. قم بزيارة لوحة Filament — ستظهر مجموعة تنقل **الدعم** مع جميع موارد إدارة التذاكر.

## الميزات

### الموارد

- **TicketResource** — إدارة كاملة للتذاكر مع صفحات القائمة والعرض والإنشاء
  - قابلة للتصفية حسب الحالة والأولوية والقسم والوكيل والعلامات وSLA
  - علامات تبويب تصفية سريعة: الكل، تذاكري، غير مُعيّنة، عاجلة، انتهاك SLA
  - إجراءات جماعية: تعيين، تغيير الحالة، تغيير الأولوية، إضافة علامات، إغلاق، حذف
  - صفحة عرض مع سلسلة المحادثة وتفاصيل الشريط الجانبي ومعلومات SLA وتقييم الرضا
  - إجراءات الرأس: رد، ملاحظة، تعيين، حالة، أولوية، متابعة، ماكرو، حل، إغلاق، إعادة فتح
- **DepartmentResource** — عمليات CRUD لأقسام الدعم مع تعيين الوكلاء
- **TagResource** — عمليات CRUD لعلامات التذاكر مع منتقي الألوان
- **SlaPolicyResource** — إدارة سياسات SLA مع أوقات الاستجابة/الحل لكل أولوية
- **EscalationRuleResource** — منشئ الشروط/الإجراءات لقواعد التصعيد التلقائي
- **CannedResponseResource** — قوالب ردود مكتوبة مسبقاً مع تصنيفات
- **MacroResource** — ماكرو أتمتة متعدد الإجراءات مع خطوات قابلة لإعادة الترتيب

### عناصر واجهة لوحة المعلومات

- **TicketStatsOverview** — مقاييس رئيسية: المفتوحة لدي، غير مُعيّنة، إجمالي المفتوحة، انتهاك SLA، المحلولة اليوم، CSAT
- **TicketsByStatusChart** — مخطط دائري لتوزيع التذاكر حسب الحالة
- **TicketsByPriorityChart** — مخطط شريطي للتذاكر المفتوحة حسب الأولوية
- **CsatOverviewWidget** — مقاييس رضا العملاء: متوسط التقييم، إجمالي التقييمات، معدل الرضا
- **RecentTicketsWidget** — جدول لأحدث 5 تذاكر
- **SlaBreachWidget** — جدول التذاكر التي انتهكت أهداف SLA

### الصفحات

- **Dashboard** — لوحة معلومات الدعم مع جميع العناصر
- **Reports** — تحليلات بنطاق زمني مع إحصائيات وتفصيل الأقسام والجدول الزمني
- **Settings** — إعدادات المسؤول لبادئة المرجع وتذاكر الضيوف والإغلاق التلقائي وحدود المرفقات

### مديرو العلاقات

- **RepliesRelationManager** — سلسلة الردود مع الملاحظات الداخلية والتثبيت وإدراج الردود الجاهزة
- **ActivitiesRelationManager** — سجل تدقيق للقراءة فقط لجميع أنشطة التذاكر
- **FollowersRelationManager** — إدارة متابعي التذاكر

### إجراءات قابلة لإعادة الاستخدام

- `AssignTicketAction` — تعيين تذكرة لوكيل
- `ChangeStatusAction` — تغيير حالة التذكرة
- `ChangePriorityAction` — تغيير أولوية التذكرة
- `ApplyMacroAction` — تطبيق ماكرو على تذكرة
- `FollowTicketAction` — تبديل متابعة تذكرة
- `PinReplyAction` — تثبيت/إلغاء تثبيت الملاحظات الداخلية

### مكونات Livewire المخصصة

- **TicketConversation** — سلسلة محادثة كاملة مع مؤلف الردود وإدراج الردود الجاهزة وتثبيت الملاحظات
- **SatisfactionRating** — عرض تقييم رضا العملاء مع تصور النجوم

## الإعدادات

يتم تكوين الإضافة من خلال تسلسل الطرق على نسخة الإضافة:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // تسمية مجموعة التنقل (الافتراضي: 'Support')
    ->agentGate('escalated-agent')  // بوابة وصول الوكيل (الافتراضي: 'escalated-agent')
    ->adminGate('escalated-admin')  // بوابة وصول المسؤول (الافتراضي: 'escalated-admin')
```

جميع الإعدادات الأخرى (SLA، أوضاع الاستضافة، الإشعارات، إلخ) تُدار بواسطة حزمة `escalated-laravel` الأساسية في `config/escalated.php`. راجع [ملف README الخاص بـ escalated-laravel](https://github.com/escalated-dev/escalated-laravel) للاطلاع على مرجع الإعدادات الكامل.

## نشر العروض

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## لقطات الشاشة

_قريباً._

## متوفر أيضاً لـ

- **[Escalated لـ Laravel](https://github.com/escalated-dev/escalated-laravel)** — حزمة Laravel عبر Composer
- **[Escalated لـ Rails](https://github.com/escalated-dev/escalated-rails)** — محرك Ruby on Rails
- **[Escalated لـ Django](https://github.com/escalated-dev/escalated-django)** — تطبيق Django قابل لإعادة الاستخدام
- **[Escalated لـ AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — حزمة AdonisJS v6
- **[Escalated لـ Filament](https://github.com/escalated-dev/escalated-filament)** — إضافة لوحة تحكم Filament (أنت هنا)
- **[الواجهة الأمامية المشتركة](https://github.com/escalated-dev/escalated)** — مكونات واجهة Vue 3 + Inertia.js

نفس البنية، نفس نظام التذاكر — تجربة Filament أصلية للوحات تحكم Laravel الإدارية.

## الرخصة

MIT
