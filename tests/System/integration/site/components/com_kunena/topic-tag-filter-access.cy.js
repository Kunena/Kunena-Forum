// The filter of the topic lists by a tag (ftag) took any tag: a user filtered by a tag which he can not see (a tag of the staff,
// with an access level which he does not have) and saw which topics have that tag.
describe('Kunena topic lists: the filter by a tag which the user can not see', () => {
  const base = '/index.php?option=com_kunena';
  const stamp = Date.now();
  const tagged = { id: 2, subject: 'How do I change my avatar?' };
  const untagged = 'Welcome to the forum';
  let old;
  let tagId;

  before(() => {
    cy.kunena_setOption('enableTags', '1').then((value) => {
      old = value ?? null;
    });
    // A published tag of the access level Special (3): the registered users do not see it.
    cy.task('queryDB', `INSERT INTO #__tags (parent_id, level, path, title, alias, published, access, created_time, modified_time, language, description, images, metadata, metadesc, params, urls)
      VALUES (1, 1, 'staff-${stamp}', 'Staff ${stamp}', 'staff-${stamp}', 1, 3, NOW(), NOW(), '*', '', '', '', '', '', '')`).then((result) => {
      tagId = result.insertId;
      cy.task('queryDB', `INSERT INTO #__contentitem_tag_map (type_alias, core_content_id, content_item_id, tag_id, tag_date, type_id)
        VALUES ('com_kunena.topic', 0, ${tagged.id}, ${tagId}, NOW(), 0)`);
    });
  });

  after(() => {
    cy.kunena_setOption('enableTags', old);
    if (!tagId) {
      return;
    }
    cy.task('queryDB', `DELETE FROM #__contentitem_tag_map WHERE tag_id = ${tagId}`);
    cy.task('queryDB', `DELETE FROM #__tags WHERE id = ${tagId}`);
  });

  it('does not filter the list by that tag', () => {
    cy.doFrontendLogin('ci-user', 'ci-user-17082005-x', false);
    // All the topics (sel=-1), not only the recent ones.
    cy.request(`${base}&view=topics&mode=topics&sel=-1&ftag=${tagId}`).then((page) => {
      // Filtered by the tag, the list would have only the tagged topic.
      expect(page.body, 'the topic with the tag').to.contain(tagged.subject);
      expect(page.body, 'a topic without the tag').to.contain(untagged);
      expect(page.body, 'the name of the tag').not.to.contain(`Staff ${stamp}`);
    });
  });
});
