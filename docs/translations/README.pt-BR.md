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
  <b>Português (BR)</b> •
  <a href="README.ru.md">Русский</a> •
  <a href="README.tr.md">Türkçe</a> •
  <a href="README.zh-CN.md">简体中文</a>
</p>

# Escalated para Filament

[![Tests](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/escalated-dev/escalated-filament/actions/workflows/run-tests.yml)
[![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Filament](https://img.shields.io/badge/filament-v3-FDAE4B?logo=data:image/svg+xml;base64,&logoColor=white)](https://filamentphp.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Um plugin de painel administrativo [Filament](https://filamentphp.com) para o sistema de tickets de suporte [Escalated](https://github.com/escalated-dev/escalated-laravel). Gerencie tickets, departamentos, políticas de SLA, regras de escalonamento, macros e mais — tudo de dentro do seu painel administrativo Filament existente.

> **[escalated.dev](https://escalated.dev)** — Saiba mais, veja demos e compare as opções Cloud vs Auto-hospedado.

## Como funciona

O Escalated para Filament é um **wrapper de plugin Filament** em torno do [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). Ele não duplica nenhuma lógica de negócio. Em vez disso, fornece Resources, Pages, Widgets e Actions do Filament que chamam os mesmos serviços, modelos e eventos do pacote Laravel principal. Isso significa:

- Toda a lógica do ciclo de vida dos tickets, cálculos de SLA e regras de escalonamento vêm do `escalated-laravel`
- Tabelas do banco de dados, migrations e configuração são gerenciadas pelo pacote principal
- Eventos, notificações e webhooks disparam exatamente como na interface Inertia
- Você obtém uma experiência nativa do Filament sem manter uma base de código separada

> **Nota:** Este pacote usa os componentes nativos Livewire + Blade do Filament (tabelas, formulários, listas de informações, ações, widgets) em vez da interface personalizada Vue 3 + Inertia.js do pacote frontend [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated). A funcionalidade principal é a mesma — mesmos modelos, serviços, banco de dados e lógica de negócio — mas a aparência da interface segue o sistema de design do Filament. Algumas interações podem diferir levemente (ex.: modais do Filament vs. formulários inline, filtros de tabela do Filament vs. componentes de filtro personalizados). Se você precisa de correspondência exata com o frontend Inertia, use o `escalated-laravel` diretamente com os componentes Vue compartilhados.

## Requisitos

- PHP 8.2+
- Laravel 11 ou 12
- Filament 3.x, 4.x ou 5.x
- escalated-dev/escalated-laravel ^0.5

### Compatibilidade de versões

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

## Instalação

### 1. Instalar os pacotes

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

Se o `escalated-laravel` já estiver instalado, apenas adicione o plugin do Filament:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Executar o instalador do Escalated (se ainda não foi feito)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Definir os gates de autorização

Em um service provider (ex.: `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Registrar o plugin no seu painel Filament

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

Pronto. Visite seu painel Filament — um grupo de navegação **Suporte** aparecerá com todos os recursos de gerenciamento de tickets.

## Recursos

### Resources

- **TicketResource** — Gerenciamento completo de tickets com páginas de lista, visualização e criação
  - Filtrável por status, prioridade, departamento, agente, tags, SLA
  - Abas de filtro rápido: Todos, Meus Tickets, Não atribuídos, Urgente, Violação de SLA
  - Ações em massa: Atribuir, Alterar status, Alterar prioridade, Adicionar tags, Fechar, Excluir
  - Página de visualização com thread de conversação, detalhes da barra lateral, info de SLA, avaliação de satisfação
  - Ações do cabeçalho: Responder, Nota, Atribuir, Status, Prioridade, Seguir, Macro, Resolver, Fechar, Reabrir
- **DepartmentResource** — CRUD para departamentos de suporte com atribuição de agentes
- **TagResource** — CRUD para tags de tickets com seletor de cores
- **SlaPolicyResource** — Gerenciamento de políticas de SLA com tempos de resposta/resolução por prioridade
- **EscalationRuleResource** — Construtor de condições/ações para regras de escalonamento automático
- **CannedResponseResource** — Templates de respostas pré-escritas com categorias
- **MacroResource** — Macros de automação multi-ação com etapas reordenáveis

### Widgets do painel

- **TicketStatsOverview** — Métricas principais: Meus abertos, Não atribuídos, Total abertos, SLA violado, Resolvidos hoje, CSAT
- **TicketsByStatusChart** — Gráfico de rosca da distribuição de tickets por status
- **TicketsByPriorityChart** — Gráfico de barras de tickets abertos por prioridade
- **CsatOverviewWidget** — Métricas de satisfação do cliente: Avaliação média, Total de avaliações, Taxa de satisfação
- **RecentTicketsWidget** — Tabela dos 5 tickets mais recentes
- **SlaBreachWidget** — Tabela de tickets com metas de SLA violadas

### Páginas

- **Dashboard** — Painel de suporte com todos os widgets
- **Reports** — Análises por período com estatísticas, detalhamento por departamento e linha do tempo
- **Settings** — Configurações de admin para prefixo de referência, tickets de convidados, fechamento automático, limites de anexos

### Gerenciadores de relações

- **RepliesRelationManager** — Thread de respostas com notas internas, fixação e inserção de respostas prontas
- **ActivitiesRelationManager** — Log de auditoria somente leitura de todas as atividades dos tickets
- **FollowersRelationManager** — Gerenciar seguidores dos tickets

### Ações reutilizáveis

- `AssignTicketAction` — Atribuir um ticket a um agente
- `ChangeStatusAction` — Alterar o status do ticket
- `ChangePriorityAction` — Alterar a prioridade do ticket
- `ApplyMacroAction` — Aplicar uma macro a um ticket
- `FollowTicketAction` — Alternar o acompanhamento de um ticket
- `PinReplyAction` — Fixar/desfixar notas internas

### Componentes Livewire personalizados

- **TicketConversation** — Thread de conversação completo com editor de respostas, inserção de respostas prontas e fixação de notas
- **SatisfactionRating** — Exibição da avaliação de satisfação do cliente com visualização em estrelas

## Configuração

O plugin é configurado através de encadeamento de métodos na instância do plugin:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Rótulo do grupo de navegação (padrão: 'Support')
    ->agentGate('escalated-agent')  // Gate de acesso do agente (padrão: 'escalated-agent')
    ->adminGate('escalated-admin')  // Gate de acesso do admin (padrão: 'escalated-admin')
```

Todas as demais configurações (SLA, modos de hospedagem, notificações, etc.) são gerenciadas pelo pacote principal `escalated-laravel` em `config/escalated.php`. Consulte o [README do escalated-laravel](https://github.com/escalated-dev/escalated-laravel) para a referência completa de configuração.

## Publicação de views

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Capturas de tela

_Em breve._

## Também disponível para

- **[Escalated para Laravel](https://github.com/escalated-dev/escalated-laravel)** — Pacote Composer do Laravel
- **[Escalated para Rails](https://github.com/escalated-dev/escalated-rails)** — Engine do Ruby on Rails
- **[Escalated para Django](https://github.com/escalated-dev/escalated-django)** — Aplicação Django reutilizável
- **[Escalated para AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — Pacote AdonisJS v6
- **[Escalated para Filament](https://github.com/escalated-dev/escalated-filament)** — Plugin de painel administrativo Filament (você está aqui)
- **[Frontend compartilhado](https://github.com/escalated-dev/escalated)** — Componentes de interface Vue 3 + Inertia.js

Mesma arquitetura, mesmo sistema de tickets — experiência nativa do Filament para painéis administrativos Laravel.

## Licença

MIT
