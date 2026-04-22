# AGENTS

## API Architecture

- All customer-facing and admin-facing APIs must use the service + repository pattern.
- Controllers should stay thin and only handle HTTP concerns such as request validation, response formatting, and delegation.
- Business logic belongs in service classes.
- Data access and query orchestration belong in repository classes.
- New API work should not place business rules directly inside controllers.
