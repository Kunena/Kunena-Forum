---
name: cypress-docs
description: Search and extract Cypress information from official documentation (docs.cypress.io, cypress.io); prefer LLM markdown under /llm/* and refuse unverified API or behavior claims.
model: inherit
background: false
metadata:
  version: 1.0.0
---

# Cypress Documentation

## Purpose
Enable the agent to retrieve accurate, up-to-date, and verifiable information about the Cypress testing framework by prioritizing official documentation and structured sources.

## When to use

Apply this skill whenever the task depends on **finding, reading, or quoting Cypress documentation** rather than general testing intuition:

- **Look up facts**: commands, APIs, assertions, lifecycle hooks, configuration options, environment variables, CLI flags, plugins, or TypeScript types as documented by Cypress.
- **Confirm behavior**: how something works in a given Cypress version, E2E vs component testing differences, browser support, or networking/cy.intercept semantics.
- **Before asserting “Cypress can/cannot…”**: search docs first; do not rely on memory for exact signatures, defaults, or deprecated APIs.
- **Extract structured content**: follow the LLM-optimized docs strategy below (`llms.txt`, `/llm/*`) when fetching or summarizing doc pages.
- **Ground answers for others**: when explaining Cypress to a user, writing examples, or reviewing code where correctness must match official docs.

If the user only needs **writing or fixing tests** without a documentation lookup, prefer `cypress-author`; if they only need **test explanation** without fetching docs, prefer `cypress-explain`. Use **this** skill when official documentation is the source of truth.

## Source Prioritization

### Primary Sources (ALWAYS search first)
- https://docs.cypress.io
- https://www.cypress.io

## 🤖 LLM-Optimized Docs Strategy

When accessing `docs.cypress.io`:

1. Fetch `/llms.txt`

2. Parse it to discover:
   - LLM-friendly documentation paths
   - Structured content endpoints

3. Prefer content under `/llm/*`. Every path on the site has an optimized version hosted under `/llm` - for example, `https://docs.cypress.io/app/faq` is available at `https://docs.cypress.io/llm/markdown/app/faq.md`.

4. Why:
   - Markdown / JSON format
   - Cleaner structure
   - Less noise than HTML

5. Fallback:
   - If `/llm/*` is incomplete, use standard docs pages

## Critical Rules

### Never Assume Missing Features
- NEVER assume Cypress does not support a feature
- ALWAYS search before concluding
- Retry with alternate terminology if needed

### Anti-Hallucination Guard

If documentation cannot verify a claim:

- Say: "I could not verify this in Cypress docs"
- Provide closest supported alternative (if available)
- DO NOT invent APIs or behavior

## Search Strategy

### 1. Classify the Query

| Query Type        | Search Location              |
|------------------|------------------------------|
| How do I...      | /guides/, /core-concepts/    |
| What is...       | /core-concepts/              |
| API / Commands   | /api/commands/               |
| Assertions       | /api/assertions/             |
| Config issues    | /configuration/              |
| CI/CD            | /guides/ci-cd/               |
| Errors           | /references/error-messages/  |

### 2. Search Flow

1. `/llm/*` (via `/llms.txt`)
2. Standard docs pages
3. `/changelog/`
4. `cypress.io` (blog, updates)

### 3. Error-Aware Routing

If the query includes:
- Error messages
- Stack traces

Then:
1. Search `/references/error-messages`
2. Expand to guides and API docs

## Structured Extraction Rules

### Commands
- Syntax
- Required arguments
- Optional options
- Return behavior
- Example usage

### Concepts
- Definition
- Key rules
- Common pitfalls
- Example

### Configuration
- Option name
- Type
- Default value
- Example

## Version Awareness

- Detect Cypress version if provided
- If NOT provided: assume latest stable version
- If behavior differs by version:
  - Explicitly call it out

## Response Style Guidelines

- Prefer official examples
- Provide working code snippets
- Keep answers concise but complete
- Avoid speculation

## Caching Strategy (Optional)

Cache frequently used topics:
- cy.visit
- cy.get
- cy.intercept
- authentication patterns
- common configuration

## Confidence Annotation

Internally assess confidence:

- High → Direct match in official docs
- Medium → Inferred from multiple sources
- Low → Unclear or edge case

If LOW:
- Clearly communicate uncertainty

## LLM Path Auto-Discovery

- Always parse `/llms.txt`
- Dynamically adapt to:
  - New `/llm/*` paths
  - Updated documentation formats

## Safety Rules

- NEVER invent Cypress APIs
- NEVER guess syntax
- ALWAYS verify behavior
- Prefer "unknown" over incorrect

## Example Behavior

User: "How do I mock API requests in Cypress?"

Agent should:
1. Classify → API / network
2. Search `/llm/markdown/api/` and `/llm/markdown/guides/`
3. Identify `cy.intercept`
4. Extract structured details
5. Return:
   - Explanation
   - Syntax
   - Example
   - Notes

## Summary

This skill ensures:
- Accurate answers from official sources
- Reduced hallucination
- Structured, high-quality outputs
- Adaptability to evolving Cypress docs
