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
  <b>Português (BR)</b> ·
  <a href="README.ru.md">Русский</a> ·
  <a href="README.tr.md">Türkçe</a> ·
  <a href="README.zh-CN.md">简体中文</a>
</sub></p>

<p align="center">
  <a href="https://escalated.dev"><img src="https://raw.githubusercontent.com/escalated-dev/escalated/main/docs/assets/readme/logo.png" width="72" height="72" alt="Escalated"></a>
</p>

<h1 align="center">Escalated for Filament</h1>

<p align="center">
  Rode o helpdesk do Escalated dentro do seu painel admin do Filament.<br>
  Tickets, departamentos, políticas de SLA, regras de escalonamento e macros como recursos nativos do Filament, com o Escalated for Laravel por trás.
</p>

<p align="center">
  <a href="https://escalated.dev"><b>Site</b></a> ·
  <a href="https://escalated.dev/docs"><b>Documentação</b></a> ·
  <a href="https://escalated.dev/cloud"><b>Cloud</b></a> ·
  <a href="../../CHANGELOG.md"><b>Histórico de mudanças</b></a>
</p>

## O que ele faz

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>Seu helpdesk no painel que você já usa</h3>
      Listas de tickets com filtros, abas rápidas e ações em massa, e uma página de ticket com a conversa, os detalhes de SLA e ações de um clique para responder, atribuir e aplicar macros.
    </td>
    <td width="50%" valign="top">
      <h3>O mesmo motor do Escalated for Laravel</h3>
      SLAs, regras de escalonamento, eventos e webhooks vêm todos do <a href="https://github.com/escalated-dev/escalated-laravel">escalated-laravel</a>, então nada é duplicado e nada fica dessincronizado.
    </td>
  </tr>
</table>

## Recursos

<table>
<tr>
<td width="33%" valign="top">

**Tickets**
- Páginas de lista, visualização e criação
- Filtros por status, prioridade, agente, SLA
- Abas: meus, não atribuídos, urgentes, prestes a estourar o SLA
- Atribuição, status, prioridade, tags e fechamento em massa
- Responder, nota, atribuir, seguir, aplicar macro
- Ações de ticket personalizadas a partir da sua configuração

</td>
<td width="33%" valign="top">

**Recursos de administração**
- Departamentos com atribuição de agentes
- Tags com seletor de cor
- Políticas de SLA por prioridade
- Construtor de condições e ações para regras de escalonamento
- Respostas prontas com categorias
- Macros com etapas reordenáveis

</td>
<td width="33%" valign="top">

**Widgets do painel**
- Estatísticas de abertos, não atribuídos, com SLA estourado e CSAT
- Gráficos de tickets por status e prioridade
- Visão geral de CSAT
- Tickets recentes
- Tickets com SLA estourado

</td>
</tr>
<tr>
<td width="33%" valign="top">

**Conversa**
- Thread de respostas com notas internas
- Notas fixadas e respostas prontas
- Log de atividades por ticket
- Seguidores do ticket
- Avaliação de satisfação com estrelas

</td>
<td width="33%" valign="top">

**Páginas**
- Painel de suporte com todos os widgets
- Relatórios por departamento em um intervalo de datas
- Configurações de prefixo, convidados, fechamento automático, anexos

</td>
<td width="33%" valign="top">

**Newsletters (opcional)**
- Newsletters em Markdown com envio agendado
- Listas de destinatários estáticas e dinâmicas
- Templates reutilizáveis com temas

</td>
</tr>
</table>

<p align="center"><sub><a href="../features.md">Todos os recursos em detalhe</a></sub></p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://filamentphp.com"><img src="https://img.shields.io/badge/Filament-FDAE4B?style=for-the-badge&logoColor=white" alt="Filament"></a>
  <a href="https://livewire.laravel.com"><img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
</p>

## Início rápido

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
php artisan escalated:install
php artisan migrate
```

Depois defina os gates `escalated-agent` e `escalated-admin` e adicione `EscalatedFilamentPlugin::make()` ao seu painel. Requer PHP 8.2+, Laravel 11 ou 12 e Filament 3.x, 4.x ou 5.x. O [guia de instalação](../installation.md) explica as duas etapas.

## Saiba mais

- [Todos os recursos](../features.md) · [Como funciona](../how-it-works.md) · [Instalação](../installation.md)
- [Configuração, views e traduções](../configuration.md) · [Acesso a tickets e roteamento](../ticket-access-and-routing.md) · [Plugins](../plugins.md)
- [Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel) para a referência de configuração do núcleo

O Escalated também está disponível para [Laravel](https://github.com/escalated-dev/escalated-laravel), [Rails](https://github.com/escalated-dev/escalated-rails), [Django](https://github.com/escalated-dev/escalated-django), [NestJS](https://github.com/escalated-dev/escalated-nestjs), [AdonisJS](https://github.com/escalated-dev/escalated-adonis), [Symfony](https://github.com/escalated-dev/escalated-symfony), [Phoenix](https://github.com/escalated-dev/escalated-phoenix), [Go](https://github.com/escalated-dev/escalated-go), [.NET](https://github.com/escalated-dev/escalated-dotnet), [Spring](https://github.com/escalated-dev/escalated-spring) e [WordPress](https://github.com/escalated-dev/escalated-wordpress). A interface Vue compartilhada fica em [escalated](https://github.com/escalated-dev/escalated).

## Licença

MIT. Copyright (c) 2026 Escalated.dev. Veja [LICENSE](../../LICENSE).
