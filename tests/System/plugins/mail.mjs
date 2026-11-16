import { simpleParser } from 'mailparser';
import { SMTPServer } from 'smtp-server';

// The mail server instance
let mailServer = null;

// The cached mails
let cachedMails = [];

/**
 * The mail as the tests read it: the envelope, the headers (an address header as its text) and the parsed body.
 *
 * @param envelope  the envelope of the SMTP session
 * @param parsed    the mail parsed by mailparser
 *
 * @returns the mail
 */
function buildMail(envelope, parsed) {
  const headers = {};

  parsed.headers.forEach((value, name) => {
    headers[name] = value && value.text !== undefined ? value.text : value;
  });

  const receivers = {};

  envelope.rcptTo.forEach((recipient) => {
    receivers[recipient.address] = true;
  });

  return {
    sender: envelope.mailFrom ? envelope.mailFrom.address : '',
    receivers,
    headers,
    body: parsed.text,
    html: parsed.html,
    attachments: parsed.attachments,
  };
}

/**
 * Returns all cached mails. It waits for maximum 3 seconds till a mail arrives.
 *
 * @returns a promise which resolves the cached mails
 */
async function getMails() {
  // Waiting here maximum 3 seconds to get a mail
  for (let i = 0; i < 3; i += 1) {
    if (cachedMails.length !== 0) {
      break;
    }

    // Sleep for a second
    /* eslint-disable no-await-in-loop */
    await new Promise((r) => { setTimeout(r, 1000); });
    /* eslint-enable no-await-in-loop */
  }

  return cachedMails;
}

/**
 * Clears the cached mails.
 *
 * @returns null
 */
function clearEmails() {
  cachedMails = [];

  return null;
}

/**
 * Starts the mail server: an SMTP server without authentication and without TLS, which keeps every mail it receives.
 *
 * @returns null
 */
function startMailServer(config) {
  // Check if the mail server is already started
  if (mailServer !== null) {
    return null;
  }

  mailServer = new SMTPServer({
    disabledCommands: ['AUTH', 'STARTTLS'],
    logger: false,
    onData(stream, session, callback) {
      simpleParser(stream)
        .then((parsed) => {
          // Uncomment the next line when you want to see the incoming mails while writing the tests
          // console.log(parsed.subject, parsed.text);

          cachedMails.push(buildMail(session.envelope, parsed));
          callback(null, 'OK');
        })
        .catch(callback);
    },
  });

  mailServer.on('error', (error) => console.error('Mail server:', error.message));

  // Start the mail server on the configured port
  mailServer.listen(config.env.smtp_port);

  // Reset the cached mails
  cachedMails = [];

  return null;
}

export { getMails, clearEmails, startMailServer };
