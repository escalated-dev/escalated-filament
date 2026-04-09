<p align="center">
  <a href="README.ar.md">العربية</a> •
  <a href="README.de.md">Deutsch</a> •
  <a href="../../README.md">English</a> •
  <a href="README.es.md">Español</a> •
  <a href="README.fr.md">Français</a> •
  <a href="README.it.md">Italiano</a> •
  <a href="README.ja.md">日本語</a> •
  <b>한국어</b> •
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

[Escalated](https://github.com/escalated-dev/escalated-laravel) 지원 티켓 시스템을 위한 [Filament](https://filamentphp.com) 관리 패널 플러그인입니다. 기존 Filament 관리 패널에서 티켓, 부서, SLA 정책, 에스컬레이션 규칙, 매크로 등을 관리할 수 있습니다.

> **[escalated.dev](https://escalated.dev)** — 자세히 알아보기, 데모 보기, 클라우드와 셀프호스팅 옵션 비교.

## 작동 방식

Escalated for Filament은 [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel)의 **Filament 플러그인 래퍼**입니다. 비즈니스 로직을 복제하지 않습니다. 대신 코어 Laravel 패키지의 동일한 서비스, 모델, 이벤트를 호출하는 Filament Resources, Pages, Widgets, Actions를 제공합니다. 이것은 다음을 의미합니다:

- 모든 티켓 라이프사이클 로직, SLA 계산, 에스컬레이션 규칙은 `escalated-laravel`에서 제공됩니다
- 데이터베이스 테이블, 마이그레이션, 설정은 코어 패키지가 관리합니다
- 이벤트, 알림, 웹훅은 Inertia UI에서와 정확히 동일하게 작동합니다
- 별도의 코드베이스를 유지하지 않고 네이티브 Filament 경험을 얻을 수 있습니다

> **참고:** 이 패키지는 [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated) 프론트엔드 패키지의 커스텀 Vue 3 + Inertia.js UI 대신 Filament 네이티브 Livewire + Blade 컴포넌트(테이블, 폼, 정보 목록, 액션, 위젯)를 사용합니다. 핵심 기능은 동일합니다 — 같은 모델, 서비스, 데이터베이스, 비즈니스 로직 — 하지만 UI 디자인은 Filament의 디자인 시스템을 따릅니다. 일부 상호작용은 약간 다를 수 있습니다(예: Filament 모달 vs. 인라인 폼, Filament 테이블 필터 vs. 커스텀 필터 컴포넌트). Inertia 프론트엔드와의 정확한 일치가 필요하면 `escalated-laravel`을 공유 Vue 컴포넌트와 직접 사용하세요.

## 요구 사항

- PHP 8.2+
- Laravel 11 또는 12
- Filament 3.x, 4.x 또는 5.x
- escalated-dev/escalated-laravel ^0.5

### 버전 호환성

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## 설치

### 1. 패키지 설치

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

`escalated-laravel`이 이미 설치되어 있다면 Filament 플러그인만 추가하세요:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Escalated 설치 프로그램 실행 (아직 하지 않은 경우)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. 인가 게이트 정의

서비스 프로바이더(예: `AppServiceProvider`)에서:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Filament 패널에 플러그인 등록

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

준비 완료입니다. Filament 패널을 방문하면 모든 티켓 관리 리소스가 포함된 **Support** 내비게이션 그룹이 나타납니다.

## 기능

### 리소스

- **TicketResource** — 목록, 보기, 생성 페이지를 갖춘 완전한 티켓 관리
  - 상태, 우선순위, 부서, 에이전트, 태그, SLA로 필터링 가능
  - 빠른 필터 탭: 전체, 내 티켓, 미할당, 긴급, SLA 위반
  - 대량 작업: 할당, 상태 변경, 우선순위 변경, 태그 추가, 닫기, 삭제
  - 대화 스레드, 사이드바 상세정보, SLA 정보, 만족도 평가가 포함된 보기 페이지
  - 헤더 액션: 답장, 메모, 할당, 상태, 우선순위, 팔로우, 매크로, 해결, 닫기, 재오픈
- **DepartmentResource** — 에이전트 할당이 가능한 지원 부서 CRUD
- **TagResource** — 색상 선택기가 포함된 티켓 태그 CRUD
- **SlaPolicyResource** — 우선순위별 응답/해결 시간이 포함된 SLA 정책 관리
- **EscalationRuleResource** — 자동 에스컬레이션 규칙을 위한 조건/액션 빌더
- **CannedResponseResource** — 카테고리가 포함된 사전 작성 응답 템플릿
- **MacroResource** — 재정렬 가능한 단계가 포함된 다중 액션 자동화 매크로

### 대시보드 위젯

- **TicketStatsOverview** — 핵심 메트릭: 내 오픈, 미할당, 전체 오픈, SLA 위반, 오늘 해결, CSAT
- **TicketsByStatusChart** — 상태별 티켓 분포 도넛 차트
- **TicketsByPriorityChart** — 우선순위별 오픈 티켓 바 차트
- **CsatOverviewWidget** — 고객 만족도 메트릭: 평균 평점, 총 평점, 만족률
- **RecentTicketsWidget** — 최근 5개 티켓 테이블
- **SlaBreachWidget** — SLA 목표 위반 티켓 테이블

### 페이지

- **Dashboard** — 모든 위젯이 포함된 지원 대시보드
- **Reports** — 통계, 부서별 분석, 타임라인이 포함된 날짜 범위 분석
- **Settings** — 참조 접두사, 게스트 티켓, 자동 닫기, 첨부파일 제한을 위한 관리자 설정

### 관계 관리자

- **RepliesRelationManager** — 내부 메모, 고정, 정형 응답 삽입이 포함된 답장 스레드
- **ActivitiesRelationManager** — 모든 티켓 활동의 읽기 전용 감사 로그
- **FollowersRelationManager** — 티켓 팔로워 관리

### 재사용 가능한 액션

- `AssignTicketAction` — 에이전트에게 티켓 할당
- `ChangeStatusAction` — 티켓 상태 변경
- `ChangePriorityAction` — 티켓 우선순위 변경
- `ApplyMacroAction` — 티켓에 매크로 적용
- `FollowTicketAction` — 티켓 팔로우 전환
- `PinReplyAction` — 내부 메모 고정/해제

### 커스텀 Livewire 컴포넌트

- **TicketConversation** — 답장 에디터, 정형 응답 삽입, 메모 고정이 포함된 전체 대화 스레드
- **SatisfactionRating** — 별 시각화를 통한 고객 만족도 평가 표시

## 설정

플러그인은 플러그인 인스턴스의 메서드 체이닝으로 설정합니다:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // 내비게이션 그룹 레이블 (기본값: 'Support')
    ->agentGate('escalated-agent')  // 에이전트 접근 게이트 (기본값: 'escalated-agent')
    ->adminGate('escalated-admin')  // 관리자 접근 게이트 (기본값: 'escalated-admin')
```

기타 모든 설정(SLA, 호스팅 모드, 알림 등)은 코어 `escalated-laravel` 패키지의 `config/escalated.php`에서 관리됩니다. 전체 설정 참조는 [escalated-laravel README](https://github.com/escalated-dev/escalated-laravel)를 확인하세요.

## 뷰 퍼블리싱

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## 스크린샷

_곧 제공 예정._

## 다른 프레임워크에서도 사용 가능

- **[Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel)** — Laravel Composer 패키지
- **[Escalated for Rails](https://github.com/escalated-dev/escalated-rails)** — Ruby on Rails 엔진
- **[Escalated for Django](https://github.com/escalated-dev/escalated-django)** — Django 재사용 가능 앱
- **[Escalated for AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — AdonisJS v6 패키지
- **[Escalated for Filament](https://github.com/escalated-dev/escalated-filament)** — Filament 관리 패널 플러그인 (현재 보고 있는 페이지)
- **[공유 프론트엔드](https://github.com/escalated-dev/escalated)** — Vue 3 + Inertia.js UI 컴포넌트

같은 아키텍처, 같은 티켓 시스템 — Laravel 관리 패널을 위한 네이티브 Filament 경험.

## 라이선스

MIT
