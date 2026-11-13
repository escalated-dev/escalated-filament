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
  <a href="README.ru.md">Русский</a> ·
  <a href="README.tr.md">Türkçe</a> ·
  <b>简体中文</b>
</sub></p>

<p align="center">
  <a href="https://escalated.dev"><img src="https://raw.githubusercontent.com/escalated-dev/escalated/main/docs/assets/readme/logo.png" width="72" height="72" alt="Escalated"></a>
</p>

<h1 align="center">Escalated for Filament</h1>

<p align="center">
  在你现有的 Filament 管理面板中运行 Escalated 帮助台。<br>
  工单、部门、SLA 策略、升级规则和宏都以原生 Filament 资源的形式提供，底层由 Escalated for Laravel 驱动。
</p>

<p align="center">
  <a href="https://escalated.dev"><b>官网</b></a> ·
  <a href="https://escalated.dev/docs"><b>文档</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>更新日志</b></a>
</p>

## 功能

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>帮助台就在你已经在用的面板里</h3>
      带筛选、快捷标签页和批量操作的工单列表，以及包含对话、SLA 详情和一键回复、分配、执行宏的工单页面。
    </td>
    <td width="50%" valign="top">
      <h3>与 Escalated for Laravel 相同的引擎</h3>
      SLA、升级规则、事件和 webhook 都来自 <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>，因此没有重复实现，也不会出现不一致。
    </td>
  </tr>
</table>

## 功能列表

<table>
<tr>
<td width="33%" valign="top">

**工单**
- 列表、查看和创建页面
- 按状态、优先级、客服、SLA 筛选
- 标签页：我的、未分配、紧急、即将违反 SLA
- 批量分配、状态、优先级、标签、关闭
- 回复、备注、分配、关注、执行宏
- 通过配置添加自定义工单操作

</td>
<td width="33%" valign="top">

**管理资源**
- 支持分配客服的部门
- 带颜色选择器的标签
- 按优先级设置的 SLA 策略
- 升级规则的条件/动作构建器
- 带分类的预设回复
- 步骤可重新排序的宏

</td>
<td width="33%" valign="top">

**仪表盘小部件**
- 待处理、未分配、已违反 SLA 和 CSAT 统计
- 按状态和优先级的工单图表
- CSAT 概览
- 最近工单
- 已违反 SLA 的工单

</td>
</tr>
<tr>
<td width="33%" valign="top">

**对话**
- 带内部备注的回复线程
- 置顶备注和预设回复
- 每个工单的活动审计日志
- 工单关注者
- 星级满意度评分

</td>
<td width="33%" valign="top">

**页面**
- 包含全部小部件的支持仪表盘
- 按部门的日期范围报表
- 前缀、访客、自动关闭、附件设置

</td>
<td width="33%" valign="top">

**邮件通讯（可选）**
- 支持定时发送的 Markdown 邮件通讯
- 静态和动态收件人列表
- 带主题的可复用模板

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">查看每项功能的详细说明</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## 快速开始

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

然后定义 `escalated-agent` 和 `escalated-admin` 两个 gate，并把 `EscalatedFilamentPlugin::make()` 添加到你的面板。需要 PHP 8.2+、Laravel 11 或 12，以及 Filament 3.x、4.x 或 5.x。[安装指南](../installation.md)介绍了这两个步骤。

## 了解更多

- [全部功能](../features.md) · [工作原理](../how-it-works.md) · [安装](../installation.md)
- [配置、视图和翻译](../configuration.md) · [工单访问与路由](../ticket-access-and-routing.md) · [插件](../plugins.md)
- 核心配置参考见 [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel)

Escalated 也提供 [Laravel](https://github.com/escalated-dev/escalated-laravel)、[Rails](https://github.com/escalated-dev/escalated-rails)、[Django](https://github.com/escalated-dev/escalated-django)、[NestJS](https://github.com/escalated-dev/escalated-nestjs)、[AdonisJS](https://github.com/escalated-dev/escalated-adonis)、[Symfony](https://github.com/escalated-dev/escalated-symfony)、[Phoenix](https://github.com/escalated-dev/escalated-phoenix)、[Go](https://github.com/escalated-dev/escalated-go)、[.NET](https://github.com/escalated-dev/escalated-dotnet)、[Spring](https://github.com/escalated-dev/escalated-spring) 和 [WordPress](https://github.com/escalated-dev/escalated-wordpress) 版本。共享的 Vue 界面位于 [escalated](https://github.com/escalated-dev/escalated)。

## 许可证

MIT。Copyright (c) 2026 Escalated.dev。详见 [LICENSE](../../LICENSE)。
