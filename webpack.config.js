const path = require('path');

const OwlAdmin = require('@owl-ui/admin');

const adminConfig = OwlAdmin.getWebpackConfig(path.resolve(__dirname));

module.exports = [adminConfig];
