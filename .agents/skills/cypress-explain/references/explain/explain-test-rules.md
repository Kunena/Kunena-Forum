# Explain Test Rules

You MUST read and follow [../documentation/documentation-rules.md](../documentation/documentation-rules.md) when citing or looking up Cypress API and concepts.

Keep explanations scannable: use bullets and short paragraphs unless the user asks for deeper detail.

When the user asks why a test is flaky or why it failed, emphasize dependencies, assumptions, and risks over structure.

1. Start With the Test's Intent

    Explain what the test is verifying and why it exists. Focus on the user behavior or system behavior, not the implementation. Allow the user to ask for a more comprehensive explanation of a specific area.

    Key questions to answer:

    - What feature is being tested?
    - What outcome should happen?

2. Outline the Test Structure

    Briefly describe the phases and layout of the test.

3. Explain Important Cypress Behavior

    Highlight how Cypress mechanics affect the test. Attempt to identify how these behaviors may contribute to test performance or stability/instability.

4. Identify Critical Dependencies or Assumptions

    Call out things the test relies on that may not be obvious. Identify what could break the test or cause it to behave unpredictably or be flaky.

5. Point Out Risks or Potential Problems

    Always mention weak points or maintainability issues.

    Common Cypress test risks:
    - brittle selectors
    - mixing async and sync code
    - timing assumptions
    - unnecessary `cy.wait()`
    - UI coupling
    - missing assertions
    - overly long tests

6. Best Practices

    In one short pass, note where the test diverges from what sections 1-5 already imply and from authoritative Cypress docs.

7. Corrections

    - Attempt to associate the title for the test and parent blocks to the identified test behavior. If misleading or incorrect titles are found then mention them in the output and suggest corrections.
    - Do not assume comments are always correct. If misleading or incorrect comments are found then mention them in the output and suggest corrections.
