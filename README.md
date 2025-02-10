# 📺 yt-duration-filter

Filter YouTube videos by duration – because sometimes, you just want to find a video that fits the time you have!

## 🚀 What is this project about?

YouTube provides an endless stream of content, but there’s one missing feature: filtering videos by duration across your subscriptions.

Imagine these scenarios:  
✔ You have 45 minutes for lunch – find videos that match your break!  
✔ You're on a 2-hour train ride – pick the right content for your journey.  
✔ Your kids want to watch YouTube, but not too long – set a duration limit.  

This application makes it simple:  
✅ Choose the YouTube channels you want to follow  
✅ Automatically fetch videos from those channels  
✅ Filter by duration and watch what fits your schedule  

## 💡 Why build this when extensions like PocketTube exist?

This project is just the starting point for a larger idea:

🚀 A content aggregator for Videos, Podcasts, and Articles, where filtering by duration remains the core feature.

At this stage, it's a single-user application to focus on functionality. User management will be implemented later, once the project expands beyond YouTube.

## 🛠 Tech Stack & Development Approach

* Built with PHP & Symfony
* Applies SOLID & KISS principles
* Uses the Factory pattern for maintainability
* Basic test coverage (more to come!)
* No hexagonal architecture yet, but it's under consideration

## 📌 Getting Started
### 1️⃣ Create a YouTube API Key

To fetch video data, you'll need a free YouTube API key.

➡ Get your key here: [YouTube API Guide](https://developers.google.com/youtube/v3/getting-started)

Add your API key to your `.env.local` file:

```
YOUTUBE_API_KEY=your_api_key_here
```

### 2️⃣ Apply Database Migrations

Run the following command to set up the database schema:

```
php bin/console doctrine:migrations:migrate
```

### 3️⃣ (Optional) Load Project Fixtures

If you want to prepopulate the database with test data, run:

```
php bin/console doctrine:fixtures:load
```

### 4️⃣ Start Fetching YouTube Videos

Once everything is set up, fetch your first batch of videos by running:

```
php bin/console app:channel:refresh
```

🎉 You're now ready to filter and browse videos by duration!

## 📝 Contributions & Feedback

This project is a work in progress – suggestions and contributions are welcome!
Feel free to open an issue or submit a pull request.

Let me know what you think! 🚀
