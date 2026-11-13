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
  <b>Türkçe</b> ·
  <a href="README.zh-CN.md">简体中文</a>
</sub></p>

<p align="center">
  <a href="https://escalated.dev"><img src="https://raw.githubusercontent.com/escalated-dev/escalated/main/docs/assets/readme/logo.png" width="72" height="72" alt="Escalated"></a>
</p>

<h1 align="center">Escalated for Filament</h1>

<p align="center">
  Escalated yardım masasını mevcut Filament yönetim panelinizin içinde çalıştırın.<br>
  Talepler, departmanlar, SLA politikaları, eskalasyon kuralları ve makrolar yerel Filament kaynakları olarak gelir ve Escalated for Laravel üzerinde çalışır.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Web sitesi</b></a> ·
  <a href="https://escalated.dev/docs"><b>Dokümantasyon</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>Değişiklik günlüğü</b></a>
</p>

## Neler yapar

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Zaten kullandığınız panelde yardım masanız</h3>
      Filtreler, hızlı sekmeler ve toplu işlemlerle talep listeleri; konuşmayı, SLA ayrıntılarını ve tek tıkla yanıtlama, atama ve makro işlemlerini içeren bir talep sayfası.
    </td>
    <td width="50%" valign="top">
      <h3>Escalated for Laravel ile aynı motor</h3>
      SLA'lar, eskalasyon kuralları, olaylar ve webhook'ların hepsi <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a> paketinden gelir; böylece hiçbir şey kopyalanmaz ve hiçbir şey ayrışmaz.
    </td>
  </tr>
</table>

## Özellikler

<table>
<tr>
<td width="33%" valign="top">

**Talepler**
- Liste, görüntüleme ve oluşturma sayfaları
- Durum, öncelik, temsilci, SLA filtreleri
- Sekmeler: benim, atanmamış, acil, SLA ihlaline yakın
- Toplu atama, durum, öncelik, etiket, kapatma
- Yanıtla, not, ata, takip et, makro uygula
- Yapılandırmanızdan özel talep işlemleri

</td>
<td width="33%" valign="top">

**Yönetim kaynakları**
- Temsilci atamalı departmanlar
- Renk seçicili etiketler
- Önceliğe göre SLA politikaları
- Eskalasyon kuralı koşul/işlem oluşturucusu
- Kategorili hazır yanıtlar
- Sırası değiştirilebilir adımlara sahip makrolar

</td>
<td width="33%" valign="top">

**Panel widget'ları**
- Açık, atanmamış, SLA ihlalli ve CSAT istatistikleri
- Duruma ve önceliğe göre talep grafikleri
- CSAT özeti
- Son talepler
- SLA'sı ihlal edilmiş talepler

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Konuşma**
- İç notlarla yanıt dizisi
- Sabitlenmiş notlar ve hazır yanıtlar
- Talep başına etkinlik denetim kaydı
- Talep takipçileri
- Yıldızlı memnuniyet puanı

</td>
<td width="33%" valign="top">

**Sayfalar**
- Tüm widget'ları içeren destek paneli
- Departmana göre tarih aralığı raporları
- Önek, misafirler, otomatik kapatma, ekler için ayarlar

</td>
<td width="33%" valign="top">

**Bültenler (isteğe bağlı)**
- Zamanlanmış gönderimli Markdown bültenleri
- Statik ve dinamik alıcı listeleri
- Temalı, yeniden kullanılabilir şablonlar

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">Tüm özelliklerin ayrıntıları</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Hızlı başlangıç

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Ardından `escalated-agent` ve `escalated-admin` gate'lerini tanımlayın ve panelinize `EscalatedFilamentPlugin::make()` ekleyin. PHP 8.2+, Laravel 11 veya 12 ve Filament 3.x, 4.x veya 5.x gerekir. [Kurulum kılavuzu](../installation.md) iki adımı da anlatır.

## Daha fazla bilgi

- [Tüm özellikler](../features.md) · [Nasıl çalışır](../how-it-works.md) · [Kurulum](../installation.md)
- [Yapılandırma, görünümler ve çeviriler](../configuration.md) · [Talep erişimi ve yönlendirme](../ticket-access-and-routing.md) · [Eklentiler](../plugins.md)
- Çekirdek yapılandırma başvurusu için [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel)

Escalated, [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) ve [WordPress](https://github.com/escalated-dev/escalated-wordpress) için de kullanılabilir. Paylaşılan Vue arayüzü [escalated](https://github.com/escalated-dev/escalated) deposunda bulunur.

## Lisans

MIT. Copyright (c) 2026 Escalated.dev. Ayrıntılar için [LICENSE](../../LICENSE) dosyasına bakın.
