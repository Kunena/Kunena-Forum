import { copyFileSync, mkdirSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { defineConfig } from 'cypress';
import base from './cypress.config.js';

// The configuration of the Cypress run which makes the screenshots of the documentation (docs/7.2). The screenshots are made on the
// Joomla site of the tests, as the administrator and as a user, so every picture shows the real pages of the checkout:
//
//   KUNENA_CMS_PATH=E:/www/apache/htdocs/tests npx cypress run --config-file cypress.docs.config.js
//
// The options of the Aurelia template are kept in a file of the template (config/params.ini, not in git): the task
// kunenaTemplateParams changes some of them for a screenshot, and gives the old values back, so a test can put them back.

const paramsFile = 'src/site/template/aurelia/config/params.ini';

const readParams = () => {
  const params = {};

  readFileSync(paramsFile, 'utf8')
    .split(/\r?\n/)
    .forEach((line) => {
      const match = line.match(/^([^=\s]+)=(.*)$/);

      if (match) {
        params[match[1]] = match[2];
      }
    });

  return params;
};

export default defineConfig({
  ...base,
  screenshotsFolder: 'docs/7.2/images',
  videosFolder: 'tests/System/output/videos',
  video: false,
  viewportWidth: 1200,
  viewportHeight: 2600,
  e2e: {
    ...base.e2e,
    specPattern: ['tests/System/docs/**/*.cy.{js,mjs}'],
    video: false,
    setupNodeEvents(on, config) {
      base.e2e.setupNodeEvents(on, config);

      // A big window, so a tall viewport is shown at its real size and not scaled down to fit (the pictures are cut from it).
      on('before:browser:launch', (browser, launchOptions) => {
        if (browser.family === 'chromium') {
          launchOptions.args.push('--window-size=1400,3000', '--force-device-scale-factor=1', '--hide-scrollbars');
        }

        return launchOptions;
      });

      // The pictures are kept as docs/7.2/images/<name>.png, not in a folder for each spec.
      on('after:screenshot', (details) => {
        if (!details.name || details.testFailure) {
          return undefined;
        }

        const target = join('docs/7.2/images', `${details.name}.png`);

        mkdirSync(dirname(target), { recursive: true });
        copyFileSync(details.path, target);

        if (details.path !== target) {
          rmSync(details.path);
        }

        return { path: target };
      });

      on('task', {
        // updates: { name: 'value' }. Gives the values which the options had.
        kunenaTemplateParams(updates) {
          const old = readParams();
          const lines = readFileSync(paramsFile, 'utf8').split(/\r?\n/);
          const done = new Set();
          const text = lines.map((line) => {
            const match = line.match(/^([^=\s]+)=/);

            if (match && Object.prototype.hasOwnProperty.call(updates, match[1])) {
              done.add(match[1]);

              return `${match[1]}="${updates[match[1]]}"`;
            }

            return line;
          });

          Object.keys(updates).filter((name) => !done.has(name)).forEach((name) => text.push(`${name}="${updates[name]}"`));
          writeFileSync(paramsFile, text.join('\n'));

          return old;
        },
        // Gives the text of the file with the options of the template.
        kunenaTemplateParamsFile() {
          return readFileSync(paramsFile, 'utf8');
        },
        kunenaTemplateParamsRestore(text) {
          writeFileSync(paramsFile, text);

          return null;
        },
      });

      return config;
    },
  },
});
