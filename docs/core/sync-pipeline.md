# Sync pipeline

## Overview

Sync is the process of aligning WordPress `trvlr_attraction` posts with the remote trvlr catalog. Two entry paths exist:

1. **Full (catalog) sync** — Fetches a list of attractions from the API, then processes each item in **batches** so PHP time and memory limits are not exceeded on large catalogs.
2. **Single-attraction sync** — Fetches one record by trvlr ID and updates the matching post (or creates it), using the same update rules as the full sync.

## Remote API

HTTP calls are implemented inside the sync class. Requests use JSON bodies and headers that identify the site’s trvlr organization (via stored options such as organisation ID, which affects the `Origin` header). The list endpoint returns summary rows; each attraction is then loaded in full with a detail endpoint. Image URLs may come from the list payload when the detail response does not include a full gallery, so list thumbnails are not lost.

If the organisation or connection settings are wrong, fetches fail and errors are logged (and optionally emailed).

## Full sync lifecycle

1. **Start** — A new sync session ID is generated. The catalog list is fetched. If empty or invalid, sync aborts with a logged error.
2. **State** — Progress and counters (created, updated, skipped, errors) are stored in a WordPress option so batch steps can resume. A transient exposes **progress** (counts and message) for the admin UI.
3. **Batches** — Each batch processes a small number of attractions sequentially. After each item, state is saved; if more items remain, a **single-event** cron is scheduled to continue soon after. This avoids long-running HTTP requests for the initial “start sync” call.
4. **Per-item processing** — For each API row, the engine loads full attraction data, then runs the same **update** routine used elsewhere: create or update post and meta, handle images and taxonomy, apply skip/partial logic for custom edits, and refresh **sync hashes** for fields that were actually written (see [data model and change detection](data-model-and-change-detection.md)).
5. **Completion** — When every row is processed, the run is marked complete, progress transients are cleared, a summary is logged, and completion email may be sent.

## Concurrency and stale runs

If a sync is already marked in progress and a recent heartbeat exists, a new full sync may be refused to prevent overlapping runs. A timeout allows recovery if a previous run died mid-batch.

## Outcomes per attraction

The engine distinguishes several outcomes:

- **Created** — New WordPress post for a trvlr ID that did not exist locally.
- **Updated** — At least one field was written from API data.
- **Partial** — Some fields updated, others intentionally skipped because of local edits or hash rules.
- **Skipped** — Nothing could be applied because all relevant fields were protected or unchanged in a way that requires no write.
- **No changes** — Data matched what was already synced; no meaningful update.
- **Error** — Post save failed or critical data was missing; may trigger error notification.

## Draft vs publish for new posts

New attractions without usable images may be created as **draft** until imagery exists, so the public site does not show empty listings; existing posts retain their current status when updated.

## Images

Images are downloaded and attached according to rules that respect the same skip/force semantics as text fields where applicable. Featured image and gallery meta participate in the field map and hashing.

## Single-attraction sync

Used for manual per-post refresh. It reuses detail fetch and `update` logic. If the list endpoint had a thumbnail but detail lacks gallery data, a cached list image can be applied so the post is not left without visuals.
