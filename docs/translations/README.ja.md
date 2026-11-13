<p align="center"><sub>
  <a href="README.ar.md">العربية</a> ·
  <a href="README.de.md">Deutsch</a> ·
  <a href="../../README.md">English</a> ·
  <a href="README.es.md">Español</a> ·
  <a href="README.fr.md">Français</a> ·
  <a href="README.it.md">Italiano</a> ·
  <b>日本語</b> ·
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
  既存の Filament 管理パネルの中で Escalated のヘルプデスクを動かせます。<br>
  チケット、部署、SLA ポリシー、エスカレーションルール、マクロを Filament のネイティブリソースとして提供し、中身は Escalated for Laravel が動かします。
</p>

<p align="center">
  <a href="https://escalated.dev"><b>ウェブサイト</b></a> ·
  <a href="https://escalated.dev/docs"><b>ドキュメント</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>変更履歴</b></a>
</p>

## できること

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>いつも使っているパネルにヘルプデスクを</h3>
      フィルター、クイックタブ、一括操作を備えたチケット一覧と、会話、SLA の詳細、ワンクリックの返信・割り当て・マクロ操作を備えたチケットページ。
    </td>
    <td width="50%" valign="top">
      <h3>Escalated for Laravel と同じエンジン</h3>
      SLA、エスカレーションルール、イベント、Webhook はすべて <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a> から提供されるため、重複も食い違いも生じません。
    </td>
  </tr>
</table>

## 機能

<table>
<tr>
<td width="33%" valign="top">

**チケット**
- 一覧、詳細、作成ページ
- ステータス、優先度、エージェント、SLA でのフィルター
- タブ: 自分の担当、未割り当て、緊急、SLA 違反間近
- 割り当て、ステータス、優先度、タグ、クローズの一括操作
- 返信、メモ、割り当て、フォロー、マクロ適用
- 設定で定義する独自のチケットアクション

</td>
<td width="33%" valign="top">

**管理リソース**
- エージェント割り当て付きの部署
- カラーピッカー付きのタグ
- 優先度ごとの SLA ポリシー
- エスカレーションルールの条件/アクションビルダー
- カテゴリー付きの定型返信
- 並べ替え可能なステップを持つマクロ

</td>
<td width="33%" valign="top">

**ダッシュボードウィジェット**
- オープン、未割り当て、SLA 違反、CSAT の統計
- ステータス別・優先度別のチケットチャート
- CSAT の概要
- 最近のチケット
- SLA に違反したチケット

</td>
</tr>
<tr>
<td width="33%" valign="top">

**会話**
- 内部メモ付きの返信スレッド
- ピン留めしたメモと定型返信
- チケットごとのアクティビティ監査ログ
- チケットのフォロワー
- 星による満足度評価

</td>
<td width="33%" valign="top">

**ページ**
- すべてのウィジェットを載せたサポートダッシュボード
- 部署別の期間指定レポート
- プレフィックス、ゲスト、自動クローズ、添付ファイルの設定

</td>
<td width="33%" valign="top">

**ニュースレター（オプション）**
- 予約配信に対応した Markdown ニュースレター
- 静的および動的な受信者リスト
- テーマ付きの再利用可能なテンプレート

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">すべての機能の詳細</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## クイックスタート

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

その後、`escalated-agent` と `escalated-admin` のゲートを定義し、パネルに `EscalatedFilamentPlugin::make()` を追加してください。PHP 8.2 以上、Laravel 11 または 12、Filament 3.x、4.x、5.x のいずれかが必要です。両方の手順は[インストールガイド](../installation.md)で説明しています。

## 詳しく知る

- [すべての機能](../features.md) · [仕組み](../how-it-works.md) · [インストール](../installation.md)
- [設定、ビュー、翻訳](../configuration.md) · [チケットのアクセスとルーティング](../ticket-access-and-routing.md) · [プラグイン](../plugins.md)
- コア設定のリファレンスは [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel) にあります

Escalated は [Laravel](https://github.com/escalated-dev/escalated-laravel)、[Rails](https://github.com/escalated-dev/escalated-rails)、[Django](https://github.com/escalated-dev/escalated-django)、[NestJS](https://github.com/escalated-dev/escalated-nestjs)、[AdonisJS](https://github.com/escalated-dev/escalated-adonis)、[Symfony](https://github.com/escalated-dev/escalated-symfony)、[Phoenix](https://github.com/escalated-dev/escalated-phoenix)、[Go](https://github.com/escalated-dev/escalated-go)、[.NET](https://github.com/escalated-dev/escalated-dotnet)、[Spring](https://github.com/escalated-dev/escalated-spring)、[WordPress](https://github.com/escalated-dev/escalated-wordpress) 向けにも提供しています。共通の Vue UI は [escalated](https://github.com/escalated-dev/escalated) にあります。

## ライセンス

MIT。Copyright (c) 2026 Escalated.dev。詳細は [LICENSE](../../LICENSE) を参照してください。
