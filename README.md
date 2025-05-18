Weather Social Poster
A WordPress plugin that posts weather updates from clientraw.txt to Bluesky and Mastodon, with Bluesky interaction display.
Features

Posts weather updates (temperature, humidity, wind) to Bluesky and Mastodon.
Displays Bluesky post interactions via shortcode [weather_bluesky_feed].
Configurable posting schedules: Bluesky (specific times or interval), Mastodon (hourly, 2-hourly, 6-hourly).
Unified admin interface for settings and testing.
Caches Bluesky interactions for 15 minutes.

Requirements

WordPress 5.0+
PHP 7.4+
Bluesky account credentials (username, app-specific password)
Mastodon app credentials (instance URL, client key, client secret, access token)
Accessible clientraw.txt URL

Installation

Clone or download the repository.
Upload the weather-social-poster folder to /wp-content/plugins/.
Activate the plugin in WordPress admin.
Configure settings under Settings > Weather Social.
Use [weather_bluesky_feed limit="5"] to display Bluesky interactions.

Configuration

General: Set clientraw.txt URL, location, website URL, and live weather URL.
Bluesky: Enter username, password, post times (e.g., 08:00,12:00) or interval (minutes, min 30), and feed limit (1-20).
Mastodon: Set instance URL, client key, client secret, access token, post title, and interval (hourly, 2-hourly, 6-hourly).
Test posts via the settings page.
Schedule posts via cron settings.

Usage

Add [weather_bluesky_feed limit="5"] to display Bluesky interactions (1-20).
Use the Test Post button to verify settings for Bluesky, Mastodon, or both.
Run cron manually for immediate posts.

Contributing

Fork the repository.
Create a feature branch (git checkout -b feature/your-feature).
Commit changes (git commit -m 'Add your feature').
Push to the branch (git push origin feature/your-feature).
Open a pull request.

Report bugs or suggest features via GitHub Issues.
License
GPL-3.0
Credits
Developed by Marcus Hazel-McGown (MM0ZIF).
