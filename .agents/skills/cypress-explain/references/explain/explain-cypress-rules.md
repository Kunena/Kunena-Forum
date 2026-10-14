# Explain Cypress Rules

You MUST read and follow [../documentation/documentation-rules.md](../documentation/documentation-rules.md) when citing or looking up Cypress API and concepts.

1. Start With the Purpose

    State what the API/concept does in one sentence and focus on the problem it solves in Cypress testing.

2. Provide the Core Mental Model

    Explain how it works conceptually, not every detail. Describe where it fits in the Cypress workflow.

3. Show the Smallest Useful Example

    Include a minimal code snippet demonstrating typical usage. Prefer realistic but short examples.

4. Mention Common Use Cases and Pitfalls

    List 2-3 typical scenarios, such as:
    - Mocking API responses
    - Waiting for network requests
    - Observing request payloads

    Identify any commonly-encountered problems such as:
    - Must be defined *before* the request occurs
    - Cannot be chained

5. Avoid Exhaustive Details

    Do not include:
    - Full API signature
    - All options
    - Edge cases

    Those belong in deeper documentation.

6. Use Cypress Terminology Correctly

    Use accurate Cypress terms such as:
    - Command chain
    - Assertions
    - Test runner
    - Network stubbing
    - Subject

    Avoid generic testing terms when Cypress has a specific concept.

7. Prefer Concise Clarity Over Completeness

    If forced to choose, a clear + short explanation is preferred to a comprehensive explanation. Focus on how concepts are applied rather than the theory behind them unless instructed otherwise. The user most likely wants to understand behavior or how to use something rather than exact details on how it works.

    Aim for less than 20 lines total for the overall explanation (not per section).
