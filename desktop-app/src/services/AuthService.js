const axios = require('axios');
const fs = require('fs');
const path = require('path');
const { remote } = require('electron');
const config = require('./../../../config/app.js');

document.getElementById('submit_button').addEventListener('click', async () => {
    const email = document.getElementById('email_or_mobile_number').value;
    const password = document.getElementById('password').value;

    if (!email || !password) {
        alert('Email and Password are required!');
        return;
    }

    try {
        const response = await axios.post(`${config.api_base_url}/login`, {
            email,
            password,
        });

        const token = response.data.token;

        if (token) {
            // Write the token to the .env file
            const envPath = path.join(__dirname, './../../.env');
            const envContent = `APP_NAME=${config.app_name}\nAPP_ENV=${config.app_env}\nAPP_DEBUG=${config.app_debug}\nAPI_BASE_URL=${config.api_base_url}\nAUTH_TOKEN=${token}\n`;
            fs.writeFileSync(envPath, envContent);

            // Redirect to the dashboard
            const win = remote.getCurrentWindow();
            win.loadFile(path.join(__dirname, '../dashboard/home/index.html'));
        } else {
            alert('Failed to retrieve token. Please try again.');
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('Login failed. Please check your credentials and try again.');
    }
});
