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
  <a href="README.ru.md">Русский</a> •
  <a href="README.tr.md">Türkçe</a> •
  <b>简体中文</b>
</p>

# Escalated Filament 版

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

为 [Escalated](https://github.com/escalated-dev/escalated-laravel) 支持工单系统打造的 [Filament](https://filamentphp.com) 管理面板插件。在现有的 Filament 管理面板中管理工单、部门、SLA 策略、升级规则、宏等。

> **[escalated.dev](https://escalated.dev)** — 了解更多、查看演示、比较云端与自托管方案。

## 工作原理

Escalated Filament 版是围绕 [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel) 的 **Filament 插件包装器**。它不复制任何业务逻辑。相反，它提供 Filament 的 Resources、Pages、Widgets 和 Actions，调用核心 Laravel 包中的相同服务、模型和事件。这意味着：

- 所有工单生命周期逻辑、SLA 计算和升级规则均来自 `escalated-laravel`
- 数据库表、迁移和配置由核心包管理
- 事件、通知和 Webhook 的触发方式与 Inertia UI 完全相同
- 无需维护单独的代码库即可获得原生 Filament 体验

> **注意：** 此包使用 Filament 原生的 Livewire + Blade 组件（表格、表单、信息列表、操作、小部件），而非 [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated) 前端包中的自定义 Vue 3 + Inertia.js UI。核心功能相同——相同的模型、服务、数据库和业务逻辑——但界面外观遵循 Filament 的设计系统。某些交互可能略有不同（例如 Filament 模态框 vs. 内联表单，Filament 表格筛选器 vs. 自定义筛选组件）。如需与 Inertia 前端完全一致，请直接使用 `escalated-laravel` 配合共享的 Vue 组件。

## 系统要求

- PHP 8.2+
- Laravel 11 或 12
- Filament 3.x、4.x 或 5.x
- escalated-dev/escalated-laravel ^0.5

### 版本兼容性

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## 安装

### 1. 安装包

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

如果已安装 `escalated-laravel`，只需添加 Filament 插件：

```bash
composer require escalated-dev/escalated-filament
```

### 2. 运行 Escalated 安装程序（如尚未执行）

```bash
php artisan escalated:install
php artisan migrate
```

### 3. 定义授权门

在服务提供者（如 `AppServiceProvider`）中：

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. 在 Filament 面板中注册插件

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

设置完成。访问您的 Filament 面板——将出现包含所有工单管理资源的 **支持** 导航组。

## 功能特性

### 资源

- **TicketResource** — 完整的工单管理，包含列表、查看和创建页面
  - 可按状态、优先级、部门、客服、标签、SLA 筛选
  - 快速筛选标签页：全部、我的工单、未分配、紧急、SLA 违规
  - 批量操作：分配、更改状态、更改优先级、添加标签、关闭、删除
  - 查看页面包含对话线程、侧边栏详情、SLA 信息、满意度评分
  - 头部操作：回复、备注、分配、状态、优先级、关注、宏、解决、关闭、重新打开
- **DepartmentResource** — 支持部门的 CRUD，带客服分配
- **TagResource** — 工单标签的 CRUD，带颜色选择器
- **SlaPolicyResource** — SLA 策略管理，按优先级设置响应/解决时间
- **EscalationRuleResource** — 自动升级规则的条件/操作构建器
- **CannedResponseResource** — 带分类的预设回复模板
- **MacroResource** — 可重排步骤的多操作自动化宏

### 仪表板小部件

- **TicketStatsOverview** — 关键指标：我的待处理、未分配、总待处理、SLA 违规、今日已解决、CSAT
- **TicketsByStatusChart** — 按状态分布的工单环形图
- **TicketsByPriorityChart** — 按优先级的待处理工单柱状图
- **CsatOverviewWidget** — 客户满意度指标：平均评分、总评分数、满意率
- **RecentTicketsWidget** — 最近 5 张工单表格
- **SlaBreachWidget** — SLA 目标违规工单表格

### 页面

- **Dashboard** — 包含所有小部件的支持仪表板
- **Reports** — 带统计、部门分析和时间线的日期范围分析
- **Settings** — 参考前缀、访客工单、自动关闭、附件限制的管理设置

### 关系管理器

- **RepliesRelationManager** — 带内部备注、置顶和预设回复插入的回复线程
- **ActivitiesRelationManager** — 所有工单活动的只读审计日志
- **FollowersRelationManager** — 管理工单关注者

### 可复用操作

- `AssignTicketAction` — 将工单分配给客服
- `ChangeStatusAction` — 更改工单状态
- `ChangePriorityAction` — 更改工单优先级
- `ApplyMacroAction` — 对工单应用宏
- `FollowTicketAction` — 切换工单关注
- `PinReplyAction` — 置顶/取消置顶内部备注

### 自定义 Livewire 组件

- **TicketConversation** — 完整的对话线程，带回复编辑器、预设回复插入和备注置顶
- **SatisfactionRating** — 带星级可视化的客户满意度评分展示

## 配置

插件通过插件实例的方法链进行配置：

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // 导航组标签（默认：'Support'）
    ->agentGate('escalated-agent')  // 客服访问门（默认：'escalated-agent'）
    ->adminGate('escalated-admin')  // 管理员访问门（默认：'escalated-admin'）
```

其他所有配置（SLA、托管模式、通知等）由核心 `escalated-laravel` 包在 `config/escalated.php` 中管理。完整配置参考请查看 [escalated-laravel README](https://github.com/escalated-dev/escalated-laravel)。

## 发布视图

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## 截图

_即将推出。_

## 其他框架版本

- **[Escalated Laravel 版](https://github.com/escalated-dev/escalated-laravel)** — Laravel Composer 包
- **[Escalated Rails 版](https://github.com/escalated-dev/escalated-rails)** — Ruby on Rails 引擎
- **[Escalated Django 版](https://github.com/escalated-dev/escalated-django)** — Django 可复用应用
- **[Escalated AdonisJS 版](https://github.com/escalated-dev/escalated-adonis)** — AdonisJS v6 包
- **[Escalated Filament 版](https://github.com/escalated-dev/escalated-filament)** — Filament 管理面板插件（当前页面）
- **[共享前端](https://github.com/escalated-dev/escalated)** — Vue 3 + Inertia.js UI 组件

相同的架构，相同的工单系统——为 Laravel 管理面板打造的原生 Filament 体验。

## 许可证

MIT
