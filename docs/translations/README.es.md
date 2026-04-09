<p align="center">
  <a href="README.ar.md">العربية</a> •
  <a href="README.de.md">Deutsch</a> •
  <a href="../../README.md">English</a> •
  <b>Español</b> •
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

# Escalated para Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Un plugin de panel de administración [Filament](https://filamentphp.com) para el sistema de tickets de soporte [Escalated](https://github.com/escalated-dev/escalated-laravel). Gestione tickets, departamentos, políticas SLA, reglas de escalamiento, macros y más — todo desde su panel de administración Filament existente.

> **[escalated.dev](https://escalated.dev)** — Conozca más, vea demos y compare opciones Cloud vs Auto-hospedado.

## Cómo funciona

Escalated para Filament es un **wrapper de plugin Filament** alrededor de [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). No duplica ninguna lógica de negocio. En su lugar, proporciona Resources, Pages, Widgets y Actions de Filament que llaman a los mismos servicios, modelos y eventos del paquete Laravel base. Esto significa:

- Toda la lógica del ciclo de vida de tickets, cálculos SLA y reglas de escalamiento provienen de `escalated-laravel`
- Las tablas de base de datos, migraciones y configuración son gestionadas por el paquete base
- Los eventos, notificaciones y webhooks se disparan exactamente como en la interfaz Inertia
- Obtiene una experiencia nativa de Filament sin mantener una base de código separada

> **Nota:** Este paquete utiliza los componentes nativos de Livewire + Blade de Filament (tablas, formularios, listas informativas, acciones, widgets) en lugar de la interfaz personalizada Vue 3 + Inertia.js del paquete frontend [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated). La funcionalidad básica es la misma — mismos modelos, servicios, base de datos y lógica de negocio — pero la apariencia de la interfaz sigue el sistema de diseño de Filament. Algunas interacciones pueden diferir ligeramente (ej. modales de Filament vs. formularios en línea, filtros de tabla de Filament vs. componentes de filtro personalizados). Si necesita paridad exacta con el frontend Inertia, use `escalated-laravel` directamente con los componentes Vue compartidos.

## Requisitos

- PHP 8.2+
- Laravel 11 o 12
- Filament 3.x, 4.x o 5.x
- escalated-dev/escalated-laravel ^0.5

### Compatibilidad de versiones

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## Instalación

### 1. Instalar los paquetes

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

Si ya tiene `escalated-laravel` instalado, solo agregue el plugin de Filament:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Ejecutar el instalador de Escalated (si aún no se ha hecho)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Definir las puertas de autorización

En un proveedor de servicios (ej. `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Registrar el plugin en su panel Filament

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

Listo. Visite su panel Filament — aparecerá un grupo de navegación **Soporte** con todos los recursos de gestión de tickets.

## Características

### Recursos

- **TicketResource** — Gestión completa de tickets con páginas de lista, vista y creación
  - Filtrable por estado, prioridad, departamento, agente, etiquetas, SLA
  - Pestañas de filtro rápido: Todos, Mis Tickets, Sin asignar, Urgente, Violación SLA
  - Acciones masivas: Asignar, Cambiar estado, Cambiar prioridad, Agregar etiquetas, Cerrar, Eliminar
  - Página de vista con hilo de conversación, detalles de barra lateral, info SLA, calificación de satisfacción
  - Acciones de encabezado: Responder, Nota, Asignar, Estado, Prioridad, Seguir, Macro, Resolver, Cerrar, Reabrir
- **DepartmentResource** — CRUD para departamentos de soporte con asignación de agentes
- **TagResource** — CRUD para etiquetas de tickets con selector de color
- **SlaPolicyResource** — Gestión de políticas SLA con tiempos de respuesta/resolución por prioridad
- **EscalationRuleResource** — Constructor de condiciones/acciones para reglas de escalamiento automático
- **CannedResponseResource** — Plantillas de respuestas predefinidas con categorías
- **MacroResource** — Macros de automatización multi-acción con pasos reordenables

### Widgets del panel

- **TicketStatsOverview** — Métricas clave: Mis abiertos, Sin asignar, Total abiertos, SLA violado, Resueltos hoy, CSAT
- **TicketsByStatusChart** — Gráfico circular de distribución de tickets por estado
- **TicketsByPriorityChart** — Gráfico de barras de tickets abiertos por prioridad
- **CsatOverviewWidget** — Métricas de satisfacción del cliente: Calificación promedio, Total de calificaciones, Tasa de satisfacción
- **RecentTicketsWidget** — Tabla de los 5 tickets más recientes
- **SlaBreachWidget** — Tabla de tickets con objetivos SLA incumplidos

### Páginas

- **Dashboard** — Panel de soporte con todos los widgets
- **Reports** — Análisis por rango de fechas con estadísticas, desglose por departamento y línea de tiempo
- **Settings** — Configuración de admin para prefijo de referencia, tickets de invitados, cierre automático, límites de adjuntos

### Gestores de relaciones

- **RepliesRelationManager** — Hilo de respuestas con notas internas, fijado e inserción de respuestas predefinidas
- **ActivitiesRelationManager** — Registro de auditoría de solo lectura de todas las actividades de tickets
- **FollowersRelationManager** — Gestionar seguidores de tickets

### Acciones reutilizables

- `AssignTicketAction` — Asignar un ticket a un agente
- `ChangeStatusAction` — Cambiar el estado del ticket
- `ChangePriorityAction` — Cambiar la prioridad del ticket
- `ApplyMacroAction` — Aplicar un macro a un ticket
- `FollowTicketAction` — Alternar el seguimiento de un ticket
- `PinReplyAction` — Fijar/desfijar notas internas

### Componentes Livewire personalizados

- **TicketConversation** — Hilo de conversación completo con editor de respuestas, inserción de respuestas predefinidas y fijado de notas
- **SatisfactionRating** — Visualización de calificación de satisfacción del cliente con estrellas

## Configuración

El plugin se configura mediante encadenamiento de métodos en la instancia del plugin:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Etiqueta del grupo de navegación (por defecto: 'Support')
    ->agentGate('escalated-agent')  // Puerta de acceso de agente (por defecto: 'escalated-agent')
    ->adminGate('escalated-admin')  // Puerta de acceso de admin (por defecto: 'escalated-admin')
```

Toda la demás configuración (SLA, modos de hospedaje, notificaciones, etc.) es gestionada por el paquete base `escalated-laravel` en `config/escalated.php`. Consulte el [README de escalated-laravel](https://github.com/escalated-dev/escalated-laravel) para la referencia completa de configuración.

## Publicar vistas

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Capturas de pantalla

_Próximamente._

## También disponible para

- **[Escalated para Laravel](https://github.com/escalated-dev/escalated-laravel)** — Paquete Composer de Laravel
- **[Escalated para Rails](https://github.com/escalated-dev/escalated-rails)** — Motor de Ruby on Rails
- **[Escalated para Django](https://github.com/escalated-dev/escalated-django)** — Aplicación reutilizable de Django
- **[Escalated para AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — Paquete AdonisJS v6
- **[Escalated para Filament](https://github.com/escalated-dev/escalated-filament)** — Plugin de panel de administración Filament (está aquí)
- **[Frontend compartido](https://github.com/escalated-dev/escalated)** — Componentes de interfaz Vue 3 + Inertia.js

Misma arquitectura, mismo sistema de tickets — experiencia nativa de Filament para paneles de administración Laravel.

## Licencia

MIT
