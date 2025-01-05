const screenshot = require('screenshot-desktop');
const sharp = require('sharp');
const fs = require('fs');
const path = require('path');
const ps = require('ps-node');
const {
	exec
} = require('child_process');

let startTime, timerInterval, screenshotInterval, activeAppInterval;
let appActivity = {};

const timerElement = document.getElementById('timer');
const startButton = document.getElementById('start');
const stopButton = document.getElementById('stop');

function formatTime(ms) {
	const date = new Date(ms);
	return date.toTimeString().split(' ')[0];
}

function updateTimer() {
	const elapsed = Date.now() - startTime;
	const date = new Date(elapsed);

	timerElement.textContent = date.toISOString().split('T')[1].split('.')[0];
}

async function takeScreenshot() {
	const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
	const outputScreenshotPath = path.join(__dirname, `screenshot-${timestamp}.jpg`);

	try {
		const img = await screenshot({
			format: 'jpg'
		});
		fs.writeFileSync(outputScreenshotPath, img);
		console.log(`Screenshot saved: ${outputScreenshotPath}`);
	} catch (err) {
		console.error('Failed to take screenshot:', err);
	}
}

function trackActiveApplications() {
	if (process.platform === 'win32') {
		trackWindowsActiveApp();
	} else if (process.platform === 'darwin' || process.platform === 'linux') {
		trackUnixActiveApp();
	}
}

function trackWindowsActiveApp() {
	ps.lookup({}, (err, resultList) => {
		if (err) {
			console.error('Error tracking active applications:', err);
			return;
		}

		const currentTime = Date.now();

		resultList.forEach((process) => {
			const appName = process.command.split(/[\\/]/).pop();

			if (!appActivity[appName]) {
				appActivity[appName] = {
					opening_time: formatTime(currentTime),
					closing_time: null,
					tabs: []
				};
			} else {
				appActivity[appName].closing_time = formatTime(currentTime);
			}
		});
	});
}

function trackUnixActiveApp() {
	exec('ps aux', (error, stdout, stderr) => {
		if (error || stderr) {
			console.error('Error tracking active applications:', error || stderr);
			return;
		}

		const currentTime = Date.now();

		const lines = stdout.split('\n');
		lines.forEach((line) => {
			const match = line.match(/\b\S+$/);
			if (match) {
				const appName = match[0];

				if (!appActivity[appName]) {
					appActivity[appName] = {
						opening_time: formatTime(currentTime),
						closing_time: null,
						tabs: []
					};
				} else {
					appActivity[appName].closing_time = formatTime(currentTime);
				}
			}
		});
	});
}

function saveAppUsageReport() {
	const reportPath = path.join(__dirname, 'app-usage-report.json');
	const filteredApps = Object.entries(appActivity).map(([appName, data]) => ({
		app_name: appName,
		opening_time: data.opening_time,
		closing_time: data.closing_time || formatTime(Date.now()),
		tabs: data.tabs
	}));

	fs.writeFileSync(reportPath, JSON.stringify(filteredApps, null, 2), 'utf-8');
	console.log(`App usage report saved to ${reportPath}`);
}

startButton.addEventListener('click', () => {
	startTime = Date.now();
	startButton.disabled = true;
	stopButton.disabled = false;

	timerInterval = setInterval(updateTimer, 1000);
	screenshotInterval = setInterval(takeScreenshot, 10000);
	activeAppInterval = setInterval(() => {
		trackActiveApplications();
	}, 5000);
});

stopButton.addEventListener('click', () => {
	clearInterval(timerInterval);
	clearInterval(screenshotInterval);
	clearInterval(activeAppInterval);

	startButton.disabled = false;
	stopButton.disabled = true;

	saveAppUsageReport();
});
