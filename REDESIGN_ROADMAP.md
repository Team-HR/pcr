# PCR System Redesign Roadmap

## Phase 1 — Data Layer (You Are Here ✅)
These must be done before touching business logic or UI.

- [x] **Normalize `mi_incharge`** → `pms_ipcr_si_assignments` *(done)*
- [ ] **Normalize `mi_quality`, `mi_eff`, `mi_time`** — these use PHP `serialize()` stored as a string in the DB. That's fragile and hard to query. Move them to a proper ratings table (e.g., `pms_ipcr_si_ratings`)
- [ ] **Normalize `corrections`** — also uses `serialize()`, same problem
- [ ] **Standardize table naming** — the codebase mixes `spms_pcr_*`, `pms_rsm_*`, `pms_ipcr_*`. Settle on one convention
- [ ] **Add proper foreign key constraints** — the skipped rows during migration revealed orphaned data with no enforcement

## Phase 2 — Security & Code Quality
- [ ] **Replace string-interpolated SQL with prepared statements** — the entire codebase is vulnerable to SQL injection (`"WHERE id='$someVar'"` pattern everywhere)
- [ ] **Consolidate config.php files** — `SaveMfoSI`, `SaveMfoSIEdit`, etc. are massive if/elseif chains inside included files. Move them to proper controller classes
- [ ] **Remove dead/commented-out code** — `trows()` in `config.php` has a huge commented-out block; `rsm_class.php` has the same

## Phase 3 — Business Logic
- [ ] **Unify the duplicate `start_duplicating()` variants** — there are three almost-identical functions (`start_duplicating`, `start_duplicating_copy_to`, `start_duplicating_to_diff_dept`). Extract the common logic
- [ ] **Review `PmsAppMigrator.php`** — it already has a `pms_rsm_assignments` table migration that overlaps with `pms_ipcr_si_assignments`. Clarify which tables are canonical

## Phase 4 — Frontend
- [ ] Only tackle this after data and backend are stable

---

## Notes
- Started: 2026-05-20
- First completed item: `mi_incharge` normalization into `pms_ipcr_si_assignments`
- Migration script: `tools/migrate_si_assignments.php`
