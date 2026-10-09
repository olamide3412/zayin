# MEDIA_VIDEO.md — Images & Video

## 1. Images
- Upload via `spatie/laravel-medialibrary`; generate conversions: `thumb` (400w), `card` (800w),
  `hero` (1600w) in **WebP** (+ AVIF if available), keep original privately.
- Admin must supply **alt text** (required); filename slugified.
- Use `<ResponsiveImage>` with srcset/sizes, width/height, lazy loading.
- Max upload 10MB; accept JPG/PNG/WebP/HEIC (convert HEIC → JPG/WebP).
- Strip EXIF GPS from public images.

## 2. Video — Requirements
Admin uploads videos of completed work (installations, maintenance, before/after). Files from
phones are large (100MB–2GB), connections are unreliable.

**Supported inputs:** MP4, MOV, WebM, MKV (≤ 2GB, ≤ 10 min recommended).
**Delivery:** web-optimised H.264/AAC MP4 (`+faststart`) in 480p and 720p, poster JPG/WebP.
Optional HLS later.

## 3. Upload Flow (resumable)
1. Admin selects file in Vue `VideoUploader` (progress bar, pause/resume, cancel).
2. `POST /admin/uploads/init` → server validates type/size, returns `upload_id`.
3. Client sends 5–10MB chunks (`/chunk`, with index + checksum); retries failed chunks with backoff.
4. `/complete` → server assembles file to temp storage, creates `project_videos` row
   (`status = processing`) and dispatches `ProcessVideoUpload` job.
5. Client polls `/admin/videos/{id}/status` until `ready` or `failed` (error message shown).
Alternative libraries: tus protocol (`tus-php` + `tus-js-client`) or Uppy. Choose one and
document it in ARCHITECTURE.md.

## 4. Processing Job (queue `media`, FFmpeg required)
- Probe with `ffprobe`: duration, resolution, codecs; reject corrupt/unsupported.
- Transcode to 720p and 480p: `-c:v libx264 -preset medium -crf 23 -c:a aac -b:a 128k -movflags +faststart`.
- Generate poster at ~1s (or admin-chosen timestamp) + a 3-frame preview.
- Normalise rotation, strip metadata (GPS).
- Upload renditions to object storage; delete temp files; save `renditions`, `duration_seconds`,
  `file_size`, `poster_path`; set `ready`. On error → `failed` + log + notify admin.
- Timeouts, retries (2), and memory limits configured; use a separate worker with `nice`.

## 5. Storage & Delivery
- Local disk for dev. Production: S3-compatible bucket (Cloudflare R2, DigitalOcean Spaces,
  Backblaze B2, or AWS S3) behind CDN with range requests enabled.
- Public renditions: long cache headers; originals private (signed URLs) or deleted after
  successful processing (admin setting).
- Backup original videos (cold storage) if the client wants masters.

## 6. Playback (`<VideoPlayer>`)
- `<video controls preload="none" playsinline poster=...>` with sources 720p/480p; default to
  480p when `navigator.connection.saveData` or effective type is `2g/3g`.
- Click-to-play; no autoplay with sound; autoplay muted loops only for tiny hero clips (<2MB)
  and never on slow connections.
- Lazy-mount when scrolled into view (IntersectionObserver).
- Captions/transcript text visible on page; show duration badge.
- YouTube/Vimeo option: use a privacy-friendly **facade** (load iframe only on click).
- Track `video_play` and 50%/100% progress.

## 7. SEO for Video
Visible title/description/transcript, `VideoObject` JSON-LD, video sitemap with
`<video:video>` tags (thumbnail, title, description, content_loc, duration, publication_date).

## 8. Moderation & Rights
Admin confirms customer consent before publishing videos showing homes/people/number plates
(NDPA). Provide blur/crop guidance. Add "consent_obtained" checkbox on project form.
