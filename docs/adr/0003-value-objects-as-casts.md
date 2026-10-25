# ADR 0003: Value objects stored through Eloquent casts

- Status: proposed (accepted once the project owner reviews it)
- Date: 2026-10-25

## Context

A user's name, email and phone number each have rules (an email must be valid, a phone number needs a country code). Passing them around as strings spreads those rules through the code.

## Decision

`NameObject`, `EmailObject` and `PhoneObject` are immutable value objects in `Domain\User\ValueObjects`. They validate when constructed. Eloquent casts convert them to and from their columns, so `$user->email` is always an `EmailObject`.

Storage formats are chosen so the database can still do its job:

- email: the plain lowercase address, because the column has a unique index and is searched
- phone: `+<country code> <number>`, also unique and searchable
- name: JSON with `first`, `middle` and `last`, because the parts are read back individually

## Consequences

- Invalid values fail at the edge, with an `InvalidArgumentException`, instead of reaching the database.
- Code that needs a string gets one explicitly (`getEmail()`, `fullName()`).
- A name is stored as JSON, so searching it is a `LIKE` over the JSON text. That is acceptable for the current list sizes; a generated column or search index can replace it if needed.
- Query builder calls such as `where('email', ...)` bypass casts, so values must be normalised by the caller.

## Notes

The value objects were part of the original design. The storage formats above were proposed by Claude Code while fixing Sprint 1 bugs: the first version stored the email as JSON, which broke the unique index and lookups, and the casts wrote a shape the read side could not parse. The round-trip tests in `UserCastsTest` exist to keep that from returning.
