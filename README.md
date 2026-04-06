# Very simple PHP/HTML/JS image gallery (no database needed): Instagram-style grid, album browsing and fullscreen slideshow.

---

## Features

| Feature | Details |
|---|---|
| Album grid | Responsive, Instagram-style square grid |
| Album cover | First image (ASC filename order) |
| Album metadata | `title.txt` and `description.txt` per folder |
| Photo grid | Responsive square grid, ASC filename order |
| Fullscreen slideshow | Opens on photo click |
| Navigation | ← → arrow buttons, keyboard arrows, touch swipe |
| Thumbnail strip | Scrubable strip at the bottom of the lightbox |
| Close | ✕ button, Escape key, or click outside the image |
| Lazy loading | Images load on demand |
| No dependencies | Vanilla JS + PHP, no npm, no framework |

---

## Files

```
gallery/
├── index.html      ← Main page: album grid
├── album.html      ← Album page: photo grid + slideshow
├── api.php         ← Backend API (PHP)
├── title.js        ← Title of the gallery
├── photos/         ← Your photos go here (create this folder)
│   ├── my-trip/
│   │   ├── title.txt
│   │   ├── description.txt
│   │   ├── 001.jpg
│   │   ├── 002.jpg
│   │   └── ...
│   └── another-album/
│       ├── title.txt
│       ├── description.txt
│       └── ...
└── README.md
```

---

## Setup

### 1. Requirements
- A web server with **PHP 7.4+** (Apache, Nginx, Caddy, etc.)
- No database or additional libraries needed

### 2. Deploy
Upload all files to your server's web root (or a subdirectory).

### 3. Create your photo library

Inside the `photos/` directory, create one subdirectory per album:

```
photos/
└── summer-2024/
    ├── title.txt          ← One line: the album title
    ├── description.txt    ← One or more lines: album caption/description
    ├── DSC_001.jpg
    ├── DSC_002.jpg
    └── ...
```

- **Supported formats:** JPG, JPEG, PNG, GIF, WebP, AVIF
- **Album order:** sorted by directory name DESC - newest first, assumes date-prefixed names (e.g. `2025-01 - Summer`, `2024-09 - Album Title`)
- **Image order:** sorted by image name ASC — name files accordingly (e.g. `001.jpg`, `002.jpg`)
- **Cover image:** the first image (alphabetically) is used as the album cover

### 4. Customization

- **Change the gallery name:** Edit `title.js` file
- **Adjust grid columns:** Change `--col: 320px` (albums) or `minmax(220px, 1fr)` (photos) in the CSS
- **Square vs rectangular thumbnails:** Change `aspect-ratio` on `.album-card__thumb` / `.photo-item`
- **Custom photos path:** Edit `PHOTOS_DIR` and `PHOTOS_URL` in `api.php`

---

## API Reference

The `api.php` file exposes a simple JSON API used by the frontend:

### `GET api.php`
Returns all albums.

```json
{
  "albums": [
    {
      "slug": "summer-2024",
      "title": "Summer 2024",
      "description": "A wonderful trip to the coast.",
      "cover": "photos/summer-2024/001.jpg",
      "count": 42
    }
  ]
}
```

### `GET api.php?album=summer-2024`
Returns images for a single album.

```json
{
  "album": "summer-2024",
  "title": "Summer 2024",
  "description": "A wonderful trip to the coast.",
  "images": ["photos/summer-2024/001.jpg", "photos/summer-2024/002.jpg"],
  "filenames": ["001.jpg", "002.jpg"]
}
```

