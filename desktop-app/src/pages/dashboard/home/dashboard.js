const screenshot = require('screenshot-desktop');
const sharp = require('sharp');
const fs = require('fs');
const path = require('path');
const ps = require('ps-node');
const { exec } = require('child_process');

let startTime, timerInterval, screenshotInterval, activeWindowInterval;
let activeApps = {};

const timerElement = document.getElementById('timer');
const startButton = document.getElementById('start');
const stopButton = document.getElementById('stop');

function formatTime(ms) {
	const seconds = Math.floor(ms / 1000) % 60;
	const minutes = Math.floor(ms / (1000 * 60)) % 60;
	const hours = Math.floor(ms / (1000 * 60 * 60));
	return `${hours.toString().padStart(2, '0')}:${minutes
    .toString()
    .padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

function updateTimer() {
	const elapsed = Date.now() - startTime;
	timerElement.textContent = formatTime(elapsed);
}

async function takeScreenshot() {
	const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
	const outputScreenshotPath = path.join(__dirname, `screenshot-${timestamp}.jpg`);

	try {
		const displays = await screenshot.listDisplays();

		const screenshots = await Promise.all(
			displays.map((display) =>
				screenshot({
					format: 'jpg',
					screen: display.id
				}).then((img) => ({
					img,
					width: display.width,
					height: display.height,
				}))
			)
		);

		const totalWidth = screenshots.reduce((sum, screen) => sum + screen.width, 0);
		const maxHeight = Math.max(...screenshots.map((screen) => screen.height));

		const compositeImages = [];
		let offsetX = 0;
		for (const { img, width, height } of screenshots) {
			const tempPath = path.join(__dirname, `temp-${timestamp}-${offsetX}.jpg`);

			fs.writeFileSync(tempPath, img);
			
            compositeImages.push({
				input: tempPath,
				left: offsetX,
				top: 0
			});

			offsetX += width;
		}

		await sharp({
				create: {
					width: totalWidth,
					height: maxHeight,
					channels: 3,
					background: { r: 0, g: 0, b: 0 },
				},
			})
			.composite(compositeImages)
			.jpeg({
				quality: 10
			})
			.toFile(outputScreenshotPath);

		compositeImages.forEach(({ input }) => fs.unlinkSync(input));
	}
    catch (err) {
		console.error('Failed to take combined screenshot:', err);
	}
}

function trackActiveApplications() {
	if (process.platform === 'win32') {
		trackWindowsActiveApp();
	}
    else if (process.platform === 'darwin' || process.platform === 'linux') {
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
			const appName = process.command.split(/[\\/]/).pop().toLowerCase();

			if (!activeApps[appName]) {
				activeApps[appName] = {
					start: currentTime,
					duration: 0
				};
			} 
            else {
				activeApps[appName].duration += currentTime - activeApps[appName].start;
				activeApps[appName].start = currentTime;
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
				const appName = match[0].toLowerCase();

				if (!activeApps[appName]) {
					activeApps[appName] = {
						start: currentTime,
						duration: 0
					};
				} 
                else {
					activeApps[appName].duration += currentTime - activeApps[appName].start;
					activeApps[appName].start = currentTime;
				}
			}
		});
	});
}

function saveAppUsageReport() {
	const reportPath = path.join(__dirname, 'app-usage-report.json');
	const reportData = {};

	for (const [appName, {
			duration
		}] of Object.entries(activeApps)) {
		reportData[appName] = formatTime(duration);
	}

	fs.writeFileSync(reportPath, JSON.stringify(reportData, null, 2), 'utf-8');
	console.log(`App usage report saved to ${reportPath}`);
}

startButton.addEventListener('click', () => {
	startTime = Date.now();
	startButton.disabled = true;
	stopButton.disabled = false;

	timerInterval = setInterval(updateTimer, 1000);

	screenshotInterval = setInterval(() => {
		takeScreenshot();
	}, 10000);

	activeWindowInterval = setInterval(trackActiveApplications, 1000);
});

stopButton.addEventListener('click', () => {
	clearInterval(timerInterval);
	clearInterval(screenshotInterval);
	clearInterval(activeWindowInterval);

	startButton.disabled = false;
	stopButton.disabled = true;

	saveAppUsageReport();
});
