# THE NEWS APPLICATION

A Dockerized Laravel application that syncs news articles from **The Guardian** and **NewsAPI**, provides RESTful APIs for news access, and supports user authentication with personalized feeds.

---

## PREREQUISITES

- **Docker** (v24.0+)
- **Docker Compose** (v2.20+)
- **Available Ports**: 
  - `8000` (application) 
  - `3306` (MySQL)

---

## VERSIONS

- Laravel Framework 11.39.1
- PHP 8.3.16
- PHPUnit 10.5.9
- Docker version 27.4.0
- Docker Compose version v2.31.0-desktop.2

---

## API Keys Setup

1. Sign up for [News API](https://newsapi.org/register) and [The Guardian API](https://open-platform.theguardian.com/access/).
2. Add your API keys to the `.env` file:
```
GUARDIAN_API_KEY="api_key"
NEWSAPI_API_KEY="api_key"
```

---

## QUICK START

1. **Clone the repository**:
   ```bash
   git clone https://github.com/muhammadjazirahly/news-application
   cd news-application
   ```

2. **Environment setup**:
   ```bash
   cp .env.example .env
   ```
   *Note*: Update the `.env` file with your API keys and database credentials.

3. **Build and run containers**:
   ```bash
   docker compose up --build -d
   ```

4. **Verify services**:
   ```bash
   docker compose ps
   ```

---

## API DOCUMENTATION

Postman collection available at:

`/news-application/News Application Collection.postman_collection.json`

### Endpoints

#### Authentication:
- **POST** `/api/register` - User registration
- **POST** `/api/login` - User login
- **POST** `/api/logout` - User logout

#### Articles (Guest endpoint):
- **GET** `/api/articles` - List articles with pagination (filter with: `author`, `category`, `published_at`)

#### Favorites (Requires authentication):
- **POST** `/api/favorites/authors/{author}` - Toggle favorite author
- **GET** `/api/favorites/authors` - Get favorite authors
- **POST** `/api/favorites/categories/{category}` - Toggle favorite category
- **GET** `/api/favorites/categories` - Get favorite categories
- **GET** `/api/favorites/articles` - Get personalized feed

---

## NEWS SYNC OPERATION

The application automatically syncs news every hour through:

1. **Scheduler -> Commands -> Jobs Pipeline**:
   - Laravel scheduler triggers `sync:news` command hourly.
   - Dispatches sync jobs to a dedicated queue (`news-sync`).

2. **Data Processing**:
   - **Normalization**: Converts API-specific formats to a unified structure.
   - **Deduplication**: Uses `source + source_id` combination.
   - **Relationship Management**: Links articles with authors/categories.

3. **Error Handling**:
   - Automatic retries (5 attempts with backoff delays).
   - Rate limit detection (429 errors).
   - Detailed logging in `storage/logs/sync-*.log`.

4. **Personalization**:
   - Combines articles from:
     * Favorite authors' latest articles.
     * Favorite categories' latest articles.
   - Automatic updates with new syncs.
   - Deduplication across sources.

---

*Note*: This is a technical assessment prototype. Production deployment requires additional security measures and error handling.
