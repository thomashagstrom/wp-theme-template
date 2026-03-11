module.exports = {
  parserOpts: {
    // Accept both conventional headers (feat:, fix:) and legacy commit subjects.
    headerPattern: /^(\w*)(?:\((.*)\))?:?\s*(.*)$/,
    headerCorrespondence: ['type', 'scope', 'subject'],
  },
};
