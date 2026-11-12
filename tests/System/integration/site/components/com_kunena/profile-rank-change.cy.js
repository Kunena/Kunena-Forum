// The rank of a user changed from his profile page (task user.changerank): an administrator gives a special rank or no rank,
// and a wrong token, a rank which is not special, a user who does not exist and a user without the right change nothing.
describe('Kunena profile: change the rank of a user', () => {
  const base = '/index.php?option=com_kunena';
  const target = 798;
  const admin = 797;
  const stamp = Date.now();
  const old = { ranks: {}, options: {} };
  let rankId;

  const rankOf = (id) => cy.task('queryDB', `SELECT \`rank\` FROM #__kunena_users WHERE userid = ${id}`).then((rows) => Number(rows[0].rank));

  const tokenOf = (userid) => cy.request(`${base}&view=user&userid=${userid}`).then((page) => /name="([0-9a-f]{32})" value="1"/.exec(page.body)?.[1] ?? 'x');

  const send = (fields, token) => cy.request({
    method: 'POST',
    url: `${base}&view=user&task=changerank`,
    form: true,
    body: { ...fields, [token]: '1' },
    failOnStatusCode: false,
    followRedirect: false,
  });

  beforeEach(() => {
    [target, admin].forEach((id) => rankOf(id).then((rank) => {
      old.ranks[id] = rank;
    }));

    cy.task('queryDB', 'SELECT rankId FROM #__kunena_ranks WHERE rankSpecial = 1 LIMIT 1').then((rows) => {
      if (rows.length) {
        rankId = Number(rows[0].rankId);

        return;
      }

      cy.task('queryDB', `INSERT INTO #__kunena_ranks (rankTitle, rankMin, rankSpecial, rankImage) VALUES ('Cypress rank ${stamp}', 0, 1, 'rank1.gif')`).then((result) => {
        rankId = Number(result.insertId);
      });
    });

    // The rank is shown on the profile only with the statistics and the ranking.
    ['showUserStats', 'showRanking'].forEach((name) => cy.kunena_setOption(name, '1').then((previous) => {
      if (!(name in old.options)) {
        old.options[name] = previous;
      }
    }));

    cy.doFrontendLogin(Cypress.expose('username'), Cypress.expose('password'), false);
  });

  afterEach(() => {
    cy.then(() => {
      Object.entries(old.ranks).forEach(([id, rank]) => cy.task('queryDB', `UPDATE #__kunena_users SET \`rank\` = ${Number(rank)} WHERE userid = ${id}`));
      Object.entries(old.options).forEach(([name, value]) => cy.kunena_setOption(name, value ?? ''));
    });
    cy.task('queryDB', `DELETE FROM #__kunena_ranks WHERE rankTitle = 'Cypress rank ${stamp}'`);
    cy.then(() => {
      old.ranks = {};
      old.options = {};
    });
  });

  it('shows the rank selector to an administrator on the profile of a user', () => {
    cy.visit(`${base}&view=user&userid=${target}`);
    cy.get('form select[name="rank"]').should('exist').closest('form').should('have.attr', 'action').and('match', /task[=-]changerank/);
  });

  it('gives a special rank, and no rank', () => {
    tokenOf(target).then((token) => {
      send({ userid: target, rank: rankId }, token).its('status').should('be.lessThan', 500);
      rankOf(target).then((rank) => expect(rank, 'the special rank is stored').to.eq(rankId));

      send({ userid: target, rank: 0 }, token).its('status').should('be.lessThan', 500);
      rankOf(target).then((rank) => expect(rank, 'no rank given').to.eq(0));
    });
  });

  it('changes nothing with a wrong token, a rank which is not special or a user who does not exist', () => {
    cy.task('queryDB', `UPDATE #__kunena_users SET \`rank\` = 0 WHERE userid = ${target}`);

    send({ userid: target, rank: rankId }, 'deadbeefdeadbeefdeadbeefdeadbeef').its('status').should('be.lessThan', 500);
    rankOf(target).then((rank) => expect(rank, 'a wrong token').to.eq(0));

    tokenOf(target).then((token) => {
      send({ userid: target, rank: 999999 }, token).its('status').should('be.lessThan', 500);
      rankOf(target).then((rank) => expect(rank, 'a rank which is not special').to.eq(0));

      cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_users WHERE userid = 999999').then((before) => {
        send({ userid: 999999, rank: rankId }, token).its('status').should('be.lessThan', 500);
        cy.task('queryDB', 'SELECT COUNT(*) AS n FROM #__kunena_users WHERE userid = 999999').then((rows) => expect(Number(rows[0].n), 'no user is made').to.eq(Number(before[0].n)));
      });
      rankOf(target).then((rank) => expect(rank).to.eq(0));
    });
  });

  it('does not let a user without the right change a rank', () => {
    cy.clearAllCookies();
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);

    cy.visit(`${base}&view=user&userid=${admin}`);
    cy.get('select[name="rank"]').should('not.exist');

    rankOf(admin).then((before) => {
      tokenOf(target).then((token) => {
        send({ userid: admin, rank: before === rankId ? 0 : rankId }, token).its('status').should('be.lessThan', 500);
        rankOf(admin).then((rank) => expect(rank, 'the rank of the administrator').to.eq(before));
      });
    });
  });
});
