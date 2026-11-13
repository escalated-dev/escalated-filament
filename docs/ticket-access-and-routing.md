# Ticket access and routing

## Optional ticket access and routing

Requires Laravel backend 1.9.1 or later and its additive migrations. Configure
only the options your installation needs in the published `config/escalated.php`:
`visibility = department`, `accounts.enabled`, `accounts.scope_agents`, and
`sla.targeted`. All remain off by default (`visibility` is `all`). Account
resolution uses the backend `TicketAccountResolver` implemented by the host.

With restrictions enabled, custom panel gates also require the backend's staff
seat (or admin seat for administration). If your custom gates define those
roles, set the backend `authorization.agent_gate` and `authorization.admin_gate`
to the matching names. A panel-only gate cannot bypass backend visibility.

Lists, search, reports, widgets and bulk actions inherit the current agent's
ticket boundary. Conversation and relation-manager requests recheck the parent
ticket after membership changes. Assignment choices include only eligible
agents, and creation validates the assignee again. Admins retain access within
their tenant. Department-restricted agents cannot see tickets without a department.

Ticket creation includes a ticket-type field. The escalation-rule editor supports
ticket-type conditions and a complete account type/ID pair. Automation conditions
accept the same fields through their JSON editor. When targeted SLAs are enabled,
the SLA form offers department and type targets. Requester account resolution
and SLA selection use Laravel's ticket lifecycle. Moving departments preserves
the attached SLA and its clock; explicit reselection is a host decision.
