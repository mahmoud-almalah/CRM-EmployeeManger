# ADR 0001: Layered architecture with the domain at the centre

- Status: proposed (accepted once the project owner reviews it)
- Date: 2026-10-25

## Context

The project started as an experiment in applying Clean Architecture and DDD to a Laravel application, away from the usual `app/Models` + fat controller layout. The experiment needs rules that can be checked, otherwise the folders are just names.

## Decision

Code lives in four layers under `src/`: Domain, Application, Infrastructure and Presentation, plus a Shared kernel. Dependencies point inward (see [architecture](../architecture.md)). The Application layer owns the contracts; Infrastructure implements them.

Eloquent is allowed inside the Domain as the persistence mechanism for entities. A strictly framework-free domain would mean a second mapping layer between entities and tables, which is not worth it for this system.

## Consequences

- Controllers have no query logic and can be read as a list of what the API does.
- Swapping a repository means changing one binding.
- Eloquent in the domain is a deliberate trade-off. Entities can be tested only with the framework booted.
- More files per feature than a conventional Laravel app. The module template in the architecture guide keeps that predictable.

## Notes

Drafted with Claude Code. The layering idea is the project owner's; the review that found two violations of the rule above in Sprint 1 was done by Claude. The value objects lived in Application while Domain casts imported them, and `UserService` imported the concrete `UserRepository`. Both were fixed by moving the value objects to Domain and adding a `UserRepositoryContract`.
