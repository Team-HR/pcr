# PCR System Redesign Roadmap

## Phase 1 — Data Layer
These must be done before touching business logic or UI.

- [x] **Normalize `mi_incharge`** → `pms_ipcr_si_assignments` *(done)*
- [x] **Normalize `mi_quality`, `mi_eff`, `mi_time`** → `pms_si_qet_descriptors` *(done — branch `feature/normalize-si-qet-descriptors`)*
- [x] **Drop old columns** — `mi_quality`, `mi_eff`, `mi_time` dropped from `spms_pcr_indicators` *(done)*
- [x] **Drop `mi_incharge` column** — dropped from `spms_pcr_indicators` *(done)*
- [x] **Normalize `corrections`** — swapped serialize/unserialize → json_encode/json_decode; migration executed: 2368 rows migrated *(done)*
- [x] **Standardize table naming** — renamed pms_ipcr_si_assignments → spms_pcr_si_assignments and pms_si_qet_descriptors → spms_pcr_si_qet_descriptors; migration executed *(done)*
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
- `mi_incharge` → `pms_ipcr_si_assignments`: `tools/migrate_si_assignments.php`
- `mi_quality/eff/time` → `pms_si_qet_descriptors`: `tools/migrate_si_qet_descriptors.php` (dual-write active; old columns not yet dropped)
