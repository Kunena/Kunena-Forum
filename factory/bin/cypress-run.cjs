// Run Cypress in the current folder and write the tests which failed to a file.
// factory/bin/site-cypress starts this: node cypress-run.cjs <folder of the cypress package> <result.json> <specs>
//
// The result: {"ran": <number of tests>, "specs": ["<spec which ran>", ...],
//              "failed": ["<spec> :: <describe> > <test>", ...]}.
// Exit 0 when Cypress ran (also with failed tests), 2 when it could not run.

const [packageDir, resultFile, specs] = process.argv.slice(2);
const fs = require('fs');
const cypress = require(packageDir);

cypress.run({
  project: process.cwd(),
  configFile: 'cypress.config.js',
  // No videos: nobody watches them, and they slow a long run down.
  config: { e2e: { video: false } },
  spec: specs,
}).then((result) => {
  if (result.status === 'failed') {
    console.error(`cypress-run: ${result.message}`);
    process.exit(2);
  }
  const failed = [];
  const specs = [];
  for (const run of result.runs) {
    const spec = run.spec.relative.replace(/\\/g, '/');
    const tests = (run.tests || []).filter((test) => test.state === 'failed');
    if ((run.tests || []).length > 0) {
      specs.push(spec);
    }
    for (const test of tests) {
      failed.push(`${spec} :: ${test.title.join(' > ')}`);
    }
    // A spec which broke before or outside its tests (it does not compile, a hook failed).
    if (run.error && tests.length === 0) {
      failed.push(`${spec} :: (the spec did not run)`);
    }
  }
  fs.writeFileSync(resultFile, JSON.stringify({ ran: result.totalTests, specs, failed: failed.sort() }, null, 1));
  process.exit(0);
}).catch((error) => {
  console.error(`cypress-run: ${error.message}`);
  process.exit(2);
});
