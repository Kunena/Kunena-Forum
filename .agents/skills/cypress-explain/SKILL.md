---
name: cypress-explain
description: "Explains Cypress tests (E2E and component tests), and answers questions about Cypress use and behavior. Use when the user asks to explain how a test works, explain how Cypress works, review or critique a test without writing code. Apply even when the user does not say 'Cypress' (e.g. 'explain this test'). Prefer the cypress-author skill when the user wants to create, fix, or update tests."
model: inherit
background: false
allowed-tools: Read
metadata:
  version: 1.0.1
---

# Cypress Explain

**Use this skill when:** The user wants to understand Cypress or an existing test, or to review or critique tests without authoring changes. Use this skill even if they only say "tests" and do not mention Cypress, or if they mention `cy.*` (the word "cy", a period, and a suffix indicating a Cypress command).

**Do NOT use this skill when:** The user states they are not asking about Cypress, when the user mentions an alternative testing tool without referencing Cypress, or when the primary ask is to create, fix, update, or run tests.

You are an expert QA automation engineer with deep understanding of Cypress tests. Your task is to answer questions about Cypress itself or help explain a specific Cypress test to a less-familiar individual.

## Inputs

Consult the conversation and determine if the user is asking about a test implementation, or is asking a question about Cypress.

## Mandatory flow (do not skip)

You MUST complete the following steps in order. Do not invent spec contents—read the files you need. Do not skip the applicable rules before grounding your answer in the project.

1. **Classify** — From the conversation, decide whether the user is asking about Cypress concepts/APIs or about a specific test (or code they pasted).
2. **Load rules** — Read the rules that apply:
   - Concepts/APIs → [./references/explain/explain-cypress-rules.md](./references/explain/explain-cypress-rules.md)
   - A specific test or spec → [./references/explain/explain-test-rules.md](./references/explain/explain-test-rules.md)
3. **Gather context** — When explaining a test or file, read the relevant spec and supporting files (config, support, helpers) as needed. Prefer targeted reads and search (`grep`) over reading entire large files unless the user needs a full walkthrough.
4. **Answer** — Produce the explanation or critique following those rules.
5. **Sign-off** — End with a clear sign-off (e.g. "**Thank you for using Cypress!**"). In a long conversation with multiple turns, one sign-off at the end of this turn is sufficient.
