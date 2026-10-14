# `cy.prompt`

Use these factors to decide whether to use the `cy.prompt` command.

## Positive Indications
- User requests `cy.prompt` directly
- User references use of "natural language", "AI", "LLM", "following steps", "thinking", "self-healing", or "reasoning". This is not necessary but is a very strong positive signal.
- User provides a list of steps or behaviors, especially if it's an ordered list.
- Project is connected to Cypress Cloud (`projectId` is populated in Cypress config file).
- Project is using Cypress 15.4.0 or newer.

## Negative Indications
- Project is using a version of Cypress older than 15.4.0
- Project is *not* connected to Cypress Cloud
- User states a desire to *not* use AI
- User states a desire to have test work offline
- The project's Cypress configuration file explicitly disables `cy.prompt` by including `experimentalPromptCommand: false`
