const dotenv = require('dotenv');
const assert = require('assert');

dotenv.config();

const { APP_NAME, APP_ENV, APP_DEBUG, API_BASE_URL, AUTH_TOKEN } = process.env;

assert(APP_NAME, 'APP_NAME is required');
assert(APP_ENV, 'APP_ENV is required');
assert(APP_DEBUG, 'APP_DEBUG is required');
assert(API_BASE_URL, 'API_BASE_URL is required');

module.exports = {
    app_name: APP_NAME,
    app_env: APP_ENV,
    app_debug: APP_DEBUG,
    api_base_url: API_BASE_URL,
    auth_token: AUTH_TOKEN,
};
