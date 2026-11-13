<p align="center"><sub>
  <a href="README.ar.md">العربية</a> ·
  <a href="README.de.md">Deutsch</a> ·
  <a href="../../README.md">English</a> ·
  <b>Español</b> ·
  <a href="README.fr.md">Français</a> ·
  <a href="README.it.md">Italiano</a> ·
  <a href="README.ja.md">日本語</a> ·
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
  Ejecuta el helpdesk de Escalated dentro de tu panel de administración de Filament.<br>
  Tickets, departamentos, políticas de SLA, reglas de escalado y macros como recursos nativos de Filament, sobre Escalated for Laravel.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Sitio web</b></a> ·
  <a href="https://escalated.dev/docs"><b>Documentación</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>Registro de cambios</b></a>
</p>

## Qué hace

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Tu helpdesk en el panel que ya usas</h3>
      Listas de tickets con filtros, pestañas rápidas y acciones masivas, y una página de ticket con la conversación, los detalles del SLA y acciones de un clic para responder, asignar y aplicar macros.
    </td>
    <td width="50%" valign="top">
      <h3>El mismo motor que Escalated for Laravel</h3>
      Los SLA, las reglas de escalado, los eventos y los webhooks vienen de <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>, así que nada se duplica y nada se desincroniza.
    </td>
  </tr>
</table>

## Funciones

<table>
<tr>
<td width="33%" valign="top">

**Tickets**
- Páginas de lista, detalle y creación
- Filtros por estado, prioridad, agente, SLA
- Pestañas: míos, sin asignar, urgentes, a punto de incumplir el SLA
- Asignación, estado, prioridad, etiquetas y cierre en bloque
- Responder, nota, asignar, seguir, aplicar macro
- Acciones de ticket propias desde tu configuración

</td>
<td width="33%" valign="top">

**Recursos de administración**
- Departamentos con asignación de agentes
- Etiquetas con selector de color
- Políticas de SLA por prioridad
- Constructor de condiciones y acciones para reglas de escalado
- Respuestas predefinidas con categorías
- Macros con pasos reordenables

</td>
<td width="33%" valign="top">

**Widgets del panel**
- Estadísticas de abiertos, sin asignar, incumplidos y CSAT
- Gráficos de tickets por estado y prioridad
- Resumen de CSAT
- Tickets recientes
- Tickets con el SLA incumplido

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Conversación**
- Hilo de respuestas con notas internas
- Notas fijadas y respuestas predefinidas
- Registro de actividad por ticket
- Seguidores del ticket
- Valoración de satisfacción con estrellas

</td>
<td width="33%" valign="top">

**Páginas**
- Panel de soporte con todos los widgets
- Informes por departamento en un rango de fechas
- Ajustes de prefijo, invitados, cierre automático, adjuntos

</td>
<td width="33%" valign="top">

**Boletines (opcional)**
- Boletines en Markdown con envío programado
- Listas de destinatarios estáticas y dinámicas
- Plantillas reutilizables con temas

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">Todas las funciones en detalle</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Inicio rápido

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Después define los gates `escalated-agent` y `escalated-admin` y añade `EscalatedFilamentPlugin::make()` a tu panel. Requiere PHP 8.2+, Laravel 11 o 12 y Filament 3.x, 4.x o 5.x. La [guía de instalación](../installation.md) explica ambos pasos.

## Más información

- [Todas las funciones](../features.md) · [Cómo funciona](../how-it-works.md) · [Instalación](../installation.md)
- [Configuración, vistas y traducciones](../configuration.md) · [Acceso a tickets y enrutamiento](../ticket-access-and-routing.md) · [Plugins](../plugins.md)
- [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel) para la referencia de configuración del núcleo

Escalated también está disponible para [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) y [WordPress](https://github.com/escalated-dev/escalated-wordpress). La interfaz Vue compartida está en [escalated](https://github.com/escalated-dev/escalated).

## Licencia

MIT. Copyright (c) 2026 Escalated.dev. Consulta [LICENSE](../../LICENSE).
