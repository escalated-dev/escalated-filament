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
  <b>Türkçe</b> •
  <a href="README.zh-CN.md">简体中文</a>
</p>

# Escalated Filament için

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

[Escalated](https://github.com/escalated-dev/escalated-laravel) destek bilet sistemi için [Filament](https://filamentphp.com) yönetim paneli eklentisi. Biletleri, departmanları, SLA politikalarını, eskalasyon kurallarını, makroları ve daha fazlasını mevcut Filament yönetim panelinizden yönetin.

> **[escalated.dev](https://escalated.dev)** — Daha fazla bilgi edinin, demoları izleyin ve Bulut ile Kendi Sunucunuz seçeneklerini karşılaştırın.

## Nasıl Çalışır

Escalated Filament için, [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel) etrafında bir **Filament eklenti sarmalayıcısıdır**. Hiçbir iş mantığını çoğaltmaz. Bunun yerine, çekirdek Laravel paketindeki aynı servisleri, modelleri ve olayları çağıran Filament Resources, Pages, Widgets ve Actions sağlar. Bu şu anlama gelir:

- Tüm bilet yaşam döngüsü mantığı, SLA hesaplamaları ve eskalasyon kuralları `escalated-laravel`dan gelir
- Veritabanı tabloları, migration'lar ve yapılandırma çekirdek paket tarafından yönetilir
- Olaylar, bildirimler ve webhook'lar Inertia arayüzündeki gibi tam olarak çalışır
- Ayrı bir kod tabanı sürdürmeden yerel Filament deneyimi elde edersiniz

> **Not:** Bu paket, [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated) frontend paketindeki özel Vue 3 + Inertia.js arayüzü yerine Filament'in yerel Livewire + Blade bileşenlerini (tablolar, formlar, bilgi listeleri, eylemler, widget'lar) kullanır. Çekirdek işlevsellik aynıdır — aynı modeller, servisler, veritabanı ve iş mantığı — ancak arayüz görünümü Filament'in tasarım sistemini takip eder. Bazı etkileşimler hafifçe farklılık gösterebilir (örn. Filament modal'ları vs. satır içi formlar, Filament tablo filtreleri vs. özel filtre bileşenleri). Inertia frontend ile piksel düzeyinde eşleşme gerekiyorsa, paylaşılan Vue bileşenleri ile doğrudan `escalated-laravel` kullanın.

## Gereksinimler

- PHP 8.2+
- Laravel 11 veya 12
- Filament 3.x, 4.x veya 5.x
- escalated-dev/escalated-laravel ^0.5

### Sürüm Uyumluluğu

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## Kurulum

### 1. Paketleri yükleyin

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

`escalated-laravel` zaten yüklüyse, sadece Filament eklentisini ekleyin:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Escalated yükleyicisini çalıştırın (henüz yapılmadıysa)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Yetkilendirme kapılarını tanımlayın

Bir servis sağlayıcıda (örn. `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Eklentiyi Filament panelinize kaydedin

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

Hazırsınız. Filament panelinizi ziyaret edin — tüm bilet yönetim kaynaklarını içeren bir **Destek** navigasyon grubu görünecektir.

## Özellikler

### Kaynaklar

- **TicketResource** — Liste, görüntüleme ve oluşturma sayfalarıyla tam bilet yönetimi
  - Durum, öncelik, departman, temsilci, etiketler, SLA'ya göre filtrelenebilir
  - Hızlı filtre sekmeleri: Tümü, Biletlerim, Atanmamış, Acil, SLA İhlali
  - Toplu işlemler: Ata, Durum Değiştir, Öncelik Değiştir, Etiket Ekle, Kapat, Sil
  - Konuşma dizisi, kenar çubuğu detayları, SLA bilgisi, memnuniyet değerlendirmesi içeren görüntüleme sayfası
  - Başlık eylemleri: Yanıtla, Not, Ata, Durum, Öncelik, Takip Et, Makro, Çöz, Kapat, Yeniden Aç
- **DepartmentResource** — Temsilci atamalı destek departmanları için CRUD
- **TagResource** — Renk seçicili bilet etiketleri için CRUD
- **SlaPolicyResource** — Önceliğe göre yanıt/çözüm süreleriyle SLA politika yönetimi
- **EscalationRuleResource** — Otomatik eskalasyon kuralları için koşul/eylem oluşturucu
- **CannedResponseResource** — Kategorili hazır yanıt şablonları
- **MacroResource** — Yeniden sıralanabilir adımlı çoklu eylem otomasyon makroları

### Pano Widget'ları

- **TicketStatsOverview** — Temel metrikler: Açıklarım, Atanmamış, Toplam Açık, SLA İhlali, Bugün Çözülen, CSAT
- **TicketsByStatusChart** — Duruma göre bilet dağılımı halka grafiği
- **TicketsByPriorityChart** — Önceliğe göre açık biletler çubuk grafiği
- **CsatOverviewWidget** — Müşteri memnuniyeti metrikleri: Ortalama Puan, Toplam Puanlar, Memnuniyet Oranı
- **RecentTicketsWidget** — En son 5 bilet tablosu
- **SlaBreachWidget** — SLA hedefleri ihlal edilen bilet tablosu

### Sayfalar

- **Dashboard** — Tüm widget'lı destek panosu
- **Reports** — İstatistikler, departman dağılımı ve zaman çizelgesiyle tarih aralığı analizleri
- **Settings** — Referans ön eki, misafir biletleri, otomatik kapatma, ek dosya sınırları için yönetici ayarları

### İlişki Yöneticileri

- **RepliesRelationManager** — Dahili notlar, sabitleme ve hazır yanıt ekleme içeren yanıt dizisi
- **ActivitiesRelationManager** — Tüm bilet etkinliklerinin salt okunur denetim günlüğü
- **FollowersRelationManager** — Bilet takipçilerini yönet

### Yeniden Kullanılabilir Eylemler

- `AssignTicketAction` — Bileti bir temsilciye ata
- `ChangeStatusAction` — Bilet durumunu değiştir
- `ChangePriorityAction` — Bilet önceliğini değiştir
- `ApplyMacroAction` — Bilete makro uygula
- `FollowTicketAction` — Bilet takibini aç/kapat
- `PinReplyAction` — Dahili notları sabitle/kaldır

### Özel Livewire Bileşenleri

- **TicketConversation** — Yanıt editörü, hazır yanıt ekleme ve not sabitleme içeren tam konuşma dizisi
- **SatisfactionRating** — Yıldız görselleştirmeli müşteri memnuniyeti değerlendirmesi gösterimi

## Yapılandırma

Eklenti, eklenti örneği üzerinde metot zincirleme ile yapılandırılır:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Navigasyon grubu etiketi (varsayılan: 'Support')
    ->agentGate('escalated-agent')  // Temsilci erişim kapısı (varsayılan: 'escalated-agent')
    ->adminGate('escalated-admin')  // Yönetici erişim kapısı (varsayılan: 'escalated-admin')
```

Diğer tüm yapılandırmalar (SLA, barındırma modları, bildirimler vb.) çekirdek `escalated-laravel` paketi tarafından `config/escalated.php` dosyasında yönetilir. Tam yapılandırma referansı için [escalated-laravel README](https://github.com/escalated-dev/escalated-laravel) dosyasına bakın.

## View'ları Yayınlama

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Ekran Görüntüleri

_Yakında._

## Şunlar için de mevcut

- **[Escalated Laravel için](https://github.com/escalated-dev/escalated-laravel)** — Laravel Composer paketi
- **[Escalated Rails için](https://github.com/escalated-dev/escalated-rails)** — Ruby on Rails motoru
- **[Escalated Django için](https://github.com/escalated-dev/escalated-django)** — Yeniden kullanılabilir Django uygulaması
- **[Escalated AdonisJS için](https://github.com/escalated-dev/escalated-adonis)** — AdonisJS v6 paketi
- **[Escalated Filament için](https://github.com/escalated-dev/escalated-filament)** — Filament yönetim paneli eklentisi (buradasınız)
- **[Paylaşılan Frontend](https://github.com/escalated-dev/escalated)** — Vue 3 + Inertia.js arayüz bileşenleri

Aynı mimari, aynı bilet sistemi — Laravel yönetim panelleri için yerel Filament deneyimi.

## Lisans

MIT
