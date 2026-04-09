<p align="center">
  <a href="README.ar.md">العربية</a> •
  <a href="README.de.md">Deutsch</a> •
  <a href="../../README.md">English</a> •
  <a href="README.es.md">Español</a> •
  <a href="README.fr.md">Français</a> •
  <a href="README.it.md">Italiano</a> •
  <b>日本語</b> •
  <a href="README.ko.md">한국어</a> •
  <a href="README.nl.md">Nederlands</a> •
  <a href="README.pl.md">Polski</a> •
  <a href="README.pt-BR.md">Português (BR)</a> •
  <a href="README.ru.md">Русский</a> •
  <a href="README.tr.md">Türkçe</a> •
  <a href="README.zh-CN.md">简体中文</a>
</p>

# Escalated for Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

[Escalated](https://github.com/escalated-dev/escalated-laravel) サポートチケットシステム用の [Filament](https://filamentphp.com) 管理パネルプラグインです。チケット、部門、SLAポリシー、エスカレーションルール、マクロなどを、既存のFilament管理パネルから管理できます。

> **[escalated.dev](https://escalated.dev)** — 詳細の確認、デモの閲覧、クラウドとセルフホストの比較ができます。

## 仕組み

Escalated for Filament は [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel) の **Filament プラグインラッパー** です。ビジネスロジックの重複はありません。代わりに、コアLaravelパッケージの同じサービス、モデル、イベントを呼び出すFilament Resources、Pages、Widgets、Actionsを提供します。つまり：

- チケットのライフサイクルロジック、SLA計算、エスカレーションルールはすべて `escalated-laravel` から提供されます
- データベーステーブル、マイグレーション、設定はコアパッケージが管理します
- イベント、通知、WebhookはInertia UIと全く同じように動作します
- 別のコードベースを保守することなく、ネイティブなFilament体験が得られます

> **注意:** このパッケージは、[`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated) フロントエンドパッケージのカスタム Vue 3 + Inertia.js UI ではなく、Filament ネイティブの Livewire + Blade コンポーネント（テーブル、フォーム、情報リスト、アクション、ウィジェット）を使用します。コア機能は同じです — 同じモデル、サービス、データベース、ビジネスロジック — しかしUIの外観はFilamentのデザインシステムに従います。一部のインタラクションは若干異なる場合があります（例：Filamentのモーダル vs. インラインフォーム、Filamentのテーブルフィルター vs. カスタムフィルターコンポーネント）。Inertiaフロントエンドとの完全な一致が必要な場合は、`escalated-laravel` を共有Vueコンポーネントと直接使用してください。

## 要件

- PHP 8.2+
- Laravel 11 または 12
- Filament 3.x、4.x、または 5.x
- escalated-dev/escalated-laravel ^0.5

### バージョン互換性

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## インストール

### 1. パッケージのインストール

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

`escalated-laravel` が既にインストール済みの場合、Filament プラグインのみ追加してください：

```bash
composer require escalated-dev/escalated-filament
```

### 2. Escalated インストーラーの実行（未実行の場合）

```bash
php artisan escalated:install
php artisan migrate
```

### 3. 認可ゲートの定義

サービスプロバイダー（例：`AppServiceProvider`）内で：

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Filament パネルにプラグインを登録

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

準備完了です。Filament パネルにアクセスすると、すべてのチケット管理リソースを含む **Support** ナビゲーショングループが表示されます。

## 機能

### リソース

- **TicketResource** — リスト、表示、作成ページを備えた完全なチケット管理
  - ステータス、優先度、部門、エージェント、タグ、SLAでフィルタリング可能
  - クイックフィルタータブ：すべて、マイチケット、未割り当て、緊急、SLA違反
  - 一括操作：割り当て、ステータス変更、優先度変更、タグ追加、クローズ、削除
  - 会話スレッド、サイドバー詳細、SLA情報、満足度評価付きの表示ページ
  - ヘッダーアクション：返信、ノート、割り当て、ステータス、優先度、フォロー、マクロ、解決、クローズ、再開
- **DepartmentResource** — エージェント割り当て付きのサポート部門CRUD
- **TagResource** — カラーピッカー付きのチケットタグCRUD
- **SlaPolicyResource** — 優先度別の応答/解決時間を含むSLAポリシー管理
- **EscalationRuleResource** — 自動エスカレーションルールの条件/アクションビルダー
- **CannedResponseResource** — カテゴリー付きの定型応答テンプレート
- **MacroResource** — 並べ替え可能なステップを含むマルチアクション自動化マクロ

### ダッシュボードウィジェット

- **TicketStatsOverview** — 主要メトリクス：自分のオープン、未割り当て、合計オープン、SLA違反、本日解決、CSAT
- **TicketsByStatusChart** — ステータス別チケット分布のドーナツチャート
- **TicketsByPriorityChart** — 優先度別オープンチケットの棒グラフ
- **CsatOverviewWidget** — 顧客満足度メトリクス：平均評価、総評価数、満足率
- **RecentTicketsWidget** — 最新5件のチケットテーブル
- **SlaBreachWidget** — SLA目標違反チケットのテーブル

### ページ

- **Dashboard** — すべてのウィジェット付きサポートダッシュボード
- **Reports** — 統計、部門別内訳、タイムラインを含む日付範囲分析
- **Settings** — 参照プレフィックス、ゲストチケット、自動クローズ、添付ファイル制限の管理設定

### リレーションマネージャー

- **RepliesRelationManager** — 内部ノート、ピン留め、定型応答挿入付きの返信スレッド
- **ActivitiesRelationManager** — すべてのチケットアクティビティの読み取り専用監査ログ
- **FollowersRelationManager** — チケットのフォロワー管理

### 再利用可能なアクション

- `AssignTicketAction` — チケットをエージェントに割り当て
- `ChangeStatusAction` — チケットステータスの変更
- `ChangePriorityAction` — チケット優先度の変更
- `ApplyMacroAction` — チケットにマクロを適用
- `FollowTicketAction` — チケットフォローの切り替え
- `PinReplyAction` — 内部ノートのピン留め/解除

### カスタムLivewireコンポーネント

- **TicketConversation** — 返信エディター、定型応答挿入、ノートピン留め付きの完全な会話スレッド
- **SatisfactionRating** — 星の視覚化による顧客満足度評価の表示

## 設定

プラグインはプラグインインスタンスのメソッドチェーンで設定します：

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // ナビゲーショングループラベル（デフォルト: 'Support'）
    ->agentGate('escalated-agent')  // エージェントアクセスのゲート（デフォルト: 'escalated-agent'）
    ->adminGate('escalated-admin')  // 管理者アクセスのゲート（デフォルト: 'escalated-admin'）
```

その他の設定（SLA、ホスティングモード、通知など）はすべてコアの `escalated-laravel` パッケージの `config/escalated.php` で管理されます。完全な設定リファレンスは [escalated-laravel README](https://github.com/escalated-dev/escalated-laravel) を参照してください。

## ビューの公開

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## スクリーンショット

_近日公開。_

## 他のフレームワーク向け

- **[Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel)** — Laravel Composerパッケージ
- **[Escalated for Rails](https://github.com/escalated-dev/escalated-rails)** — Ruby on Railsエンジン
- **[Escalated for Django](https://github.com/escalated-dev/escalated-django)** — Django再利用可能アプリ
- **[Escalated for AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — AdonisJS v6パッケージ
- **[Escalated for Filament](https://github.com/escalated-dev/escalated-filament)** — Filament管理パネルプラグイン（現在表示中）
- **[共有フロントエンド](https://github.com/escalated-dev/escalated)** — Vue 3 + Inertia.js UIコンポーネント

同じアーキテクチャ、同じチケットシステム — Laravel管理パネルのためのネイティブFilament体験。

## ライセンス

MIT
