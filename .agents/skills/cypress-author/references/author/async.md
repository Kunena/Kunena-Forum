# Asynchronous Logic

Cypress uses a command queue and is not Promise-based. Using Promises or features like `async`/`await` can introduce flakiness or instability into tests.

Improper asynchronous logic can cause errors like:
* "returned a promise while also invoking cy commands"
* "commands still in queue after test finished"

**Avoid**

* Mixing `cy` commands with manual Promises
* Returning values other than Cypress chains
* Forgetting to return a Promise
* Using async code (`setTimeout`) without telling Mocha

**Prefer**

* `return` Promises if used
* Or avoid Promises entirely and stay in Cypress chains

Never mix Cypress chains with external async control unless you fully understand the lifecycle.
