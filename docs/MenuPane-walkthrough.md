# MenuPane walkthrough notes

Handoff from a Cursor chat so discussion can continue on another machine.
Main file: `resources/js/Components/Dashbord/MenuPane.jsx`
Related: `resources/js/Pages/Dashboard.jsx`, `resources/less/_menu_pane.less`
Controller props: `app/Http/Controllers/DashbordController.php` → `Inertia::render('Dashboard', …)`

If this chat is still available in Cursor history (same account), resume there. Otherwise continue from these notes.

---

## Data flow (agreed mental model)

| Source | Role |
|--------|------|
| `usePage().props` (`settings`, `menu_masks`, `factors`, `menu_items`, `eating`) | Server snapshot from Inertia. Treat as read-only. |
| `useForm` → `data` / `setData` | Local editable working copy, seeded from props. UI edits and optimistic updates go here. |

Same initial content, different purpose. After user edits they diverge until a sync/reload.

Controller sends e.g. `'eating' => auth()->user()->eating`, `'menu_items' => $menus`, plus settings/masks/factors.

---

## Sync `useEffect`s

1. **`[eating]`** — when Inertia brings a new `eating` prop (e.g. after `dashboard.updatefactors`), copy into `data.eating`. Needed because `useForm` only uses initial values on mount.
2. **`[menu_items]`** — same for the menu list.

Kept as **two effects** on purpose: each runs only when its dependency changes. Can merge into one with `[eating, menu_items]`, but then either change re-applies both.

**Polling caveat:** blind `setData` from props will overwrite mid-edit. If polling later: skip sync while dirty / pause on focus / don’t overwrite the active field (`activeField` already tracks focus).

Multiple hooks: negligible cost; cost is work inside effects, not the number of effects.

---

## `factors` vs `eating` vs `factor`

- **`factors`**: 24h schedule; `now: true` marks the row for the current hour.
- **`eating` / `data.eating`**: coefficients **actually applied** to this meal.
- **`current_factor`** (`factors.find(f => f.now)`): leftover from early design. Almost unused for real calc.
- **`factor` = `new Factor(data.eating…)`**: correct object used for menu/dose calculations.

**Planned later:** selectbox to pick a row from `factors` → copy into `eating` → persist. Until then, TODO in MenuPane documents this.

**Inconsistency today:** k2 input still binds `current_factor.k2` instead of `data.eating.k2`.

---

## Glucose UI state (lines ~99–102)

Separate `useState`s: `glucose1`, `glucose2`, `ouv` (+ WIP `k1`).

**Advice from chat:**
- Separate states are fine for three fields.
- An object `{ glucose1, glucose2, ouv }` is clearer if unifying `updateGlucose` / `formatGlucose` (generic `[field]` updater).
- Don’t put `k1` in the glucose object (different type).
- Bigger clarity win later: reduce duplication with `data.eating` — eating as source of truth, `Glucose` as view/parse helper (mmol/plasma).

---

## What was cleaned (prior task)

- Removed Dashboard test `CalculableInput` and dead chrome.
- Removed MenuPane dead helpers / unused imports / debug strip / empty `InfoPieces.jsx`.
- **Kept** `useTrans` / `__` (needed soon).
- Left alone: actions bar, list, six inputs, mock nutrition/doses panels, Scale.

---

## Still unfinished (next steps for later)

- Wire k1, k2, BE inputs properly (to `eating`).
- Factor select → copy to `eating`.
- Real nutrition panel (right) instead of mocks.
- Real doses/`grand-total` panel instead of mocks.
- Scale still static (user said leave for now).

---

## Resume prompt (paste into a new chat)

> Continue MenuPane walkthrough from `docs/MenuPane-walkthrough.md`. We already covered: props vs useForm, eating/menu_items sync effects, factors vs eating vs Factor, glucose state vs object. Next explain from after the glucose state / list building / updateGlucose / formatGlucose / JSX layout.

---

*Saved so the same Cursor account on another computer can pick this up via the project repo.*
