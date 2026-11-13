<p align="center"><sub>
  <a href="README.ar.md">العربية</a> ·
  <a href="README.de.md">Deutsch</a> ·
  <a href="../../README.md">English</a> ·
  <a href="README.es.md">Español</a> ·
  <a href="README.fr.md">Français</a> ·
  <a href="README.it.md">Italiano</a> ·
  <a href="README.ja.md">日本語</a> ·
  <b>한국어</b> ·
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
  기존 Filament 관리자 패널 안에서 Escalated 헬프데스크를 운영하세요.<br>
  티켓, 부서, SLA 정책, 에스컬레이션 규칙, 매크로를 Filament 기본 리소스로 제공하며, Escalated for Laravel 위에서 동작합니다.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>웹사이트</b></a> ·
  <a href="https://escalated.dev/docs"><b>문서</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>변경 내역</b></a>
</p>

## 주요 기능

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>이미 쓰고 있는 패널 안의 헬프데스크</h3>
      필터, 빠른 탭, 일괄 작업이 있는 티켓 목록과, 대화, SLA 세부 정보, 원클릭 답장·배정·매크로 작업이 있는 티켓 페이지를 제공합니다.
    </td>
    <td width="50%" valign="top">
      <h3>Escalated for Laravel과 같은 엔진</h3>
      SLA, 에스컬레이션 규칙, 이벤트, 웹훅은 모두 <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>에서 제공되므로 중복되거나 어긋나는 부분이 없습니다.
    </td>
  </tr>
</table>

## 기능 목록

<table>
<tr>
<td width="33%" valign="top">

**티켓**
- 목록, 보기, 생성 페이지
- 상태, 우선순위, 상담원, SLA 필터
- 탭: 내 티켓, 미배정, 긴급, SLA 위반 임박
- 일괄 배정, 상태, 우선순위, 태그, 종료
- 답장, 메모, 배정, 팔로우, 매크로 적용
- 설정에서 정의하는 사용자 지정 티켓 작업

</td>
<td width="33%" valign="top">

**관리 리소스**
- 상담원 배정이 포함된 부서
- 색상 선택기가 있는 태그
- 우선순위별 SLA 정책
- 에스컬레이션 규칙 조건/작업 빌더
- 카테고리별 정형 답변
- 순서를 바꿀 수 있는 단계로 구성된 매크로

</td>
<td width="33%" valign="top">

**대시보드 위젯**
- 열린 티켓, 미배정, SLA 위반, CSAT 통계
- 상태별·우선순위별 티켓 차트
- CSAT 개요
- 최근 티켓
- SLA를 위반한 티켓

</td>
</tr>
<tr>
<td width="33%" valign="top">

**대화**
- 내부 메모가 포함된 답장 스레드
- 고정 메모와 정형 답변
- 티켓별 활동 감사 로그
- 티켓 팔로워
- 별점 만족도 평가

</td>
<td width="33%" valign="top">

**페이지**
- 모든 위젯을 담은 지원 대시보드
- 부서별 기간 지정 보고서
- 접두사, 게스트, 자동 종료, 첨부 파일 설정

</td>
<td width="33%" valign="top">

**뉴스레터(선택 사항)**
- 예약 발송을 지원하는 Markdown 뉴스레터
- 정적·동적 수신자 목록
- 테마가 있는 재사용 가능한 템플릿

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">모든 기능 자세히 보기</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## 빠른 시작

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

그런 다음 `escalated-agent`와 `escalated-admin` 게이트를 정의하고 패널에 `EscalatedFilamentPlugin::make()`를 추가하세요. PHP 8.2 이상, Laravel 11 또는 12, Filament 3.x, 4.x 또는 5.x가 필요합니다. 두 단계 모두 [설치 가이드](../installation.md)에서 다룹니다.

## 더 알아보기

- [전체 기능](../features.md) · [동작 방식](../how-it-works.md) · [설치](../installation.md)
- [설정, 뷰, 번역](../configuration.md) · [티켓 접근과 라우팅](../ticket-access-and-routing.md) · [플러그인](../plugins.md)
- 핵심 설정 레퍼런스는 [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel)을 참고하세요

Escalated는 [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring), [WordPress](https://github.com/escalated-dev/escalated-wordpress)용으로도 제공됩니다. 공용 Vue UI는 [escalated](https://github.com/escalated-dev/escalated) 저장소에 있습니다.

## 라이선스

MIT. Copyright (c) 2026 Escalated.dev. [LICENSE](../../LICENSE)를 참고하세요.
