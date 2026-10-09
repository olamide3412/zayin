# Solar Load Calculator — Feature README

> **Feature:** Energy / Solar Sizing Calculator
> **Route:** `/solar-calculator` (public, indexable, SSR)
> **Replaces:** `FR-018` in `REQUIREMENTS.md` (upgraded from *Could* to *Should*)
> **Read first:** `AGENTS.md` → then this file.

A simple, mobile-first tool where a visitor lists the appliances in their home or shop
(with wattage), says how long they want the system to power them, and instantly gets a
recommended **solar panel array, inverter size and battery bank**, plus matching packages
from the shop and a one-tap **quote / WhatsApp** call to action.

Why it matters: it answers the #1 question Nigerian buyers have ("what size solar do I
need?"), builds trust, captures high-intent leads with their exact load profile, ranks for
searches like *"solar calculator Nigeria"*, and feeds directly into package sales.

---

## 1. User Experience

### 1.1 Three-step flow (single page, no reload)

| Step | What the user does | Notes |
|---|---|---|
| **1. Appliances** | Search/pick from presets (tap to add), or add a custom item. Edit **watts**, **quantity**, **hours/day**. | Presets prefilled with typical wattage; always editable. Running total shown in a sticky bar. |
| **2. Preferences** | Choose **backup duration** (hours), **goal**, **battery type**, **location zone**. | 4 simple inputs, sensible defaults. "Advanced" collapsed. |
| **3. Results** | See daily energy, inverter, battery, solar panels, charge controller, and matching packages. | CTA: Get Quote · WhatsApp · Save/Share · Download PDF. |

### 1.2 Wireframe (mobile)

```
┌────────────────────────────┐
│ Solar Calculator           │
│ ① Appliances ② Setup ③ Result│
├────────────────────────────┤
│ 🔎 Search: "fan", "fridge"  │
│ [LED Bulb][Fan][TV][Fridge]│  ← preset chips
│                            │
│ Ceiling Fan                │
│  Watts [75] Qty [3] Hrs [8]│  ⌫
│ Fridge (medium)            │
│  Watts [150] Qty [1] Hrs[10]│ ⌫
│ + Add custom appliance     │
├────────────────────────────┤
│ Total: 632 W · 4.98 kWh/day│  ← sticky
│        [ Next → ]          │
└────────────────────────────┘
```

### 1.3 Inputs (Step 2)

| Input | Type | Default | Meaning |
|---|---|---|---|
| Backup duration | Slider/number (1–24 h) | 12 | How many hours the battery must power the loads without grid/sun (e.g. 12 = overnight). |
| Goal | Radio | `backup` | `backup` = inverter + battery for outages (solar recharges). `full_solar` = solar covers all daily usage (off-grid / hybrid). |
| Battery type | Radio | `lithium` | `lithium` (LiFePO4) or `tubular` (lead-acid tubular/gel). Different usable depth of discharge. |
| Location zone | Select | `south_west` | Sets peak sun hours (see §4.3). |
| Advanced (collapsed) | | | Days of autonomy (1–3), preferred panel wattage, surge safety margin. |

### 1.4 Results (Step 3)

Show, in plain English with a one-line explanation under each:

1. **Energy use:** daily Wh/kWh, monthly kWh (×30), peak load (W).
2. **Inverter:** recommended kVA, system voltage (12/24/48V), surge check status.
3. **Battery bank:** usable energy needed, total bank size (kWh and Ah at system voltage),
   number of units of a catalogue battery.
4. **Solar panels:** total array watts, number of panels at chosen panel size, estimated daily
   generation.
5. **Charge controller:** MPPT amps (rounded up to a standard rating).
6. **Recommended packages:** up to 3 matching products from the shop (Good / Better / Best).
7. **Warnings:** heavy resistive appliances, AC load, motor start-up surge, oversize notice.
8. **Disclaimer:** estimate only; a technician site survey confirms the final design.

---

## 2. Requirements

| ID | Requirement | Pri |
|---|---|---|
| FR-090 | Appliance picker with searchable presets + custom appliances (name, watts, qty, hours/day) | M |
| FR-091 | Instant (client-side) calculation as inputs change; no page reload | M |
| FR-092 | Compute daily energy (Wh), peak load (W), and surge load | M |
| FR-093 | Recommend inverter size (kVA) with power-factor, safety margin and surge check | M |
| FR-094 | Recommend battery bank from **backup duration**, depth of discharge and efficiency | M |
| FR-095 | Recommend solar array from daily energy, peak sun hours and system losses | M |
| FR-096 | Recommend system voltage and charge controller (MPPT) rating | S |
| FR-097 | Match results to shop packages (`specs`: `capacity_kva`, `battery_kwh`, `panels_w`) with fallback "Request custom quote" | M |
| FR-098 | Prefill the quote form / WhatsApp message with the load profile and result summary | M |
| FR-099 | Save & share results via unguessable URL (`/solar-calculator/r/{uuid}`), noindex | S |
| FR-100 | Download result as PDF | C |
| FR-101 | Admin can manage appliance presets and calculation constants without code changes | M |
| FR-102 | Server recomputes every saved/submitted calculation (never trust client numbers) | M |
| FR-103 | Warnings for heavy loads (iron, kettle, heater, cooker) and AC/motor loads | M |
| FR-104 | SEO-optimised landing content, FAQs and JSON-LD around the tool | M |
| FR-105 | Analytics events: `calculator_started`, `calculator_completed`, `calculator_quote_click`, `calculator_whatsapp_click` | S |

**Acceptance criteria (summary)**
- A first-time user completes a calculation on a 360px phone in under 2 minutes.
- Adding/removing an appliance updates totals in < 100 ms.
- Server and client produce **identical** results for all golden test fixtures.
- Page is fully rendered in HTML source (intro, how-it-works, FAQ, preset list) for SEO.
- Zero layout shift (CLS < 0.1) while results update.

---

## 3. Calculation Engine

**Single source of truth:** `App\Services\Solar\SolarSizingCalculator` (pure PHP, no I/O).
A mirrored TypeScript composable `useSolarCalculator()` gives instant UI results. Both load
constants from the same settings snapshot and are verified against shared **golden fixtures**
(`tests/Fixtures/solar_calculator_cases.json`). Saved calculations store the settings snapshot
used, so old results stay reproducible.

### 3.1 Definitions
- `W` = rated running power of one unit; `qty` = number of units; `h` = hours/day of use.
- `m` = start-up surge multiplier of an appliance (1 for lights/electronics; 3–7 for motors).

### 3.2 Step-by-step formulas

**1. Daily energy**
```
dailyWh        = Σ ( W × qty × h )
monthlyKWh     = dailyWh × 30 / 1000
```

**2. Peak load and surge**
```
peakW          = Σ ( W × qty )            // all appliances assumed on together (conservative)
surgeExtraW    = max( W × qty × (m − 1) ) // largest single motor start-up on top of running load
surgePeakW     = peakW + surgeExtraW
```
> Optional diversity factor (default 1.0, advanced setting) lets an admin or advanced user
> reduce `peakW` if not everything runs at once.

**3. Inverter**
```
requiredVA     = peakW × safetyFactor / powerFactor       // 1.25 and 0.8 by default
inverterKVA    = smallest standard size ≥ requiredVA/1000
surgeCheck     : inverterKVA × 1000 × powerFactor × surgeCapacityFactor ≥ surgePeakW   // 2.0 default
                 if it fails → move up one standard size until it passes
```

**4. Battery bank**
```
windowWh       = Σ ( W × qty × min(h, backupHours) )       // energy used during the backup window
usableWhNeeded = windowWh × autonomyDays / inverterEfficiency   // 0.90 default
bankWh         = usableWhNeeded / depthOfDischarge          // lithium 0.80 · tubular 0.50
```
> For `full_solar`/off-grid users, set **backupHours = 24** (or the night duration) so the bank
> covers everything that must run without sun.

**5. System voltage** (by bank size, then reconcile with inverter)
```
bankWh ≤ 2,400  → 12 V
bankWh ≤ 7,200  → 24 V
otherwise       → 48 V
```
If the inverter's native voltage is lower than the voltage required by the bank, **upsize the
inverter** to the smallest standard model that supports the higher voltage and show the note
*"Upsized to support the battery bank."*

Standard inverter catalogue (editable in admin):

| kVA | 1 | 1.5 | 2.5 | 3.5 | 5 | 7.5 | 10 | >10 |
|---|---|---|---|---|---|---|---|---|
| System V | 12 | 12 | 24 | 24 | 48 | 48 | 48 | custom quote |

**6. Battery sizing outputs**
```
bankAh         = bankWh / systemVoltage
units          = ceil( bankWh / unitWh )          // unit from catalogue, e.g. 48V 100Ah lithium ≈ 5,120 Wh
```
Series/parallel wiring is verified by a technician; show it as *"Configuration confirmed at site survey"*.

**7. Solar array**
```
targetPVWh     = goal == full_solar ? dailyWh : (windowWh × autonomyDays / inverterEfficiency)
requiredPVW    = targetPVWh / ( peakSunHours × systemDerate )     // derate 0.75 default
panels         = ceil( requiredPVW / panelWatts )                 // panelWatts default 550
arrayW         = panels × panelWatts
estDailyGenWh  = arrayW × peakSunHours × systemDerate
```

**8. Charge controller (MPPT)**
```
mpptAmps       = arrayW / systemVoltage × 1.25
controller     = smallest standard rating ≥ mpptAmps    // 30, 40, 50, 60, 80, 100, 120 A
```
Note for installers: confirm PV string voltage (Voc) against the controller's max input.
For all-in-one hybrid inverters this step is informational.

### 3.3 Default constants (all admin-editable, versioned)

| Constant | Default | Note |
|---|---|---|
| `safety_factor` | 1.25 | headroom on inverter sizing |
| `power_factor` | 0.8 | W ↔ VA conversion |
| `inverter_efficiency` | 0.90 | DC→AC losses |
| `system_derate` | 0.75 | panel/cable/controller/temperature/dust losses |
| `dod_lithium` | 0.80 | LiFePO4 usable depth of discharge |
| `dod_tubular` | 0.50 | lead-acid tubular/gel |
| `surge_capacity_factor` | 2.0 | inverter short-term surge vs. rating |
| `panel_watts_options` | 400 / 450 / 550 | default 550 |
| `diversity_factor` | 1.0 | advanced |
| `days_of_autonomy` | 1 | advanced, 1–3 |

> These are sensible starting engineering defaults, not guarantees. The client's technical
> lead must review and approve them before launch.

### 3.4 Worked example (use as a golden fixture)

| Appliance | W | Qty | h/day | Wh/day |
|---|---|---|---|---|
| LED bulb | 10 | 8 | 5 | 400 |
| Ceiling fan | 75 | 3 | 8 | 1,800 |
| TV (LED) | 80 | 1 | 6 | 480 |
| Decoder | 20 | 1 | 6 | 120 |
| Fridge (medium, duty-cycled) | 150 | 1 | 10 | 1,500 |
| Wi-Fi router | 12 | 1 | 24 | 288 |
| Laptop | 65 | 1 | 6 | 390 |
| **Total** | **632 W** | | | **4,978 Wh (≈ 4.98 kWh/day, ≈ 149 kWh/month)** |

Settings: backup 12 h · lithium · south-west (PSH 4.5) · goal `full_solar` · 550 W panels.

- Surge: fridge `m = 3` → extra 300 W → `surgePeakW = 932 W`
- Inverter: `632 × 1.25 / 0.8 = 987.5 VA` → **1 kVA** (surge check: 1,000 × 0.8 × 2 = 1,600 W ≥ 932 ✔)
- Battery: `windowWh = 400 + 1,800 + 480 + 120 + 1,500 + 144 + 390 = 4,834 Wh` →
  usable `4,834 / 0.90 = 5,371 Wh` → bank `5,371 / 0.80 = 6,714 Wh` (≈ 6.7 kWh)
- Voltage: 6,714 Wh → **24 V** → inverter upsized to **2.5 kVA / 24 V**; bank ≈ **280 Ah @ 24 V**
- Solar: `4,978 / (4.5 × 0.75) = 1,475 W` → `ceil(1,475 / 550) = 3` panels → **1,650 W array**
- Controller: `1,650 / 24 × 1.25 = 85.9 A` → **100 A MPPT**

---

## 4. Data & Configuration

### 4.1 Tables (add to `docs/DATABASE.md`)

```
appliance_presets
  id, name, slug, category, watts, surge_multiplier (default 1.0),
  default_hours (effective hours/day, duty-cycle aware), icon, is_resistive bool,
  is_active, sort_order, notes
appliance_categories      id, name, slug, sort_order        (Lighting, Cooling, Entertainment…)
solar_calc_settings       id, version, constants json, inverter_catalog json,
                          controller_ratings json, psh_by_zone json, is_active, created_by
solar_calculations        id, uuid (unique), user_id nullable, session_id,
                          inputs json, results json, settings_version, zone,
                          quote_request_id nullable, ip_hash, created_at
```
Add nullable `calculation_id` to `quote_requests`. Add `specs` keys on products:
`capacity_kva`, `system_voltage`, `battery_kwh`, `battery_type`, `panels_w`, `panel_count`.

### 4.2 Starter appliance presets (seed; typical values, user-editable)

| Category | Appliance | W | Surge × | Default h/day |
|---|---|---|---|---|
| Lighting | LED bulb | 10 | 1 | 5 |
| Lighting | Security/flood light (LED) | 30 | 1 | 10 |
| Cooling | Ceiling fan | 75 | 2 | 8 |
| Cooling | Standing/table fan | 60 | 2 | 8 |
| Cooling | Air conditioner 1 HP (inverter) | 750 | 3 | 6 |
| Cooling | Air conditioner 1.5 HP | 1,200 | 3 | 6 |
| Cooling | Air conditioner 2 HP | 1,700 | 3 | 6 |
| Kitchen | Refrigerator (medium) | 150 | 3 | 10 |
| Kitchen | Chest freezer | 200 | 4 | 10 |
| Kitchen | Blender | 400 | 2 | 0.5 |
| Kitchen | Microwave | 1,200 | 1 | 0.5 |
| Kitchen ⚠ | Electric kettle | 1,500 | 1 | 0.5 |
| Home | Electric iron ⚠ | 1,000 | 1 | 0.5 |
| Home | Washing machine | 500 | 2 | 1 |
| Water | Water pump 0.5 HP | 370 | 5 | 2 |
| Water | Water pump 1 HP | 750 | 5 | 2 |
| Entertainment | TV LED 32" | 50 | 1 | 6 |
| Entertainment | TV LED 43"–55" | 80 | 1 | 6 |
| Entertainment | DSTV/GOtv decoder | 20 | 1 | 6 |
| Entertainment | Sound system | 100 | 1 | 4 |
| Office/IT | Laptop | 65 | 1 | 6 |
| Office/IT | Desktop PC + monitor | 200 | 1 | 8 |
| Office/IT | Wi-Fi router / ONT | 12 | 1 | 24 |
| Office/IT | Starlink kit | 75 | 1 | 24 |
| Office/IT | Printer | 50 | 1 | 1 |
| Security | CCTV (DVR + 4 cameras) | 50 | 1 | 24 |
| Other | Phone charger | 10 | 1 | 3 |
| Other | Sewing machine | 100 | 2 | 4 |

⚠ = *resistive/heating* load (`is_resistive = true`) → show a warning that these drain
batteries fast and recommend gas/alternatives or a larger system. Wattages are **typical
approximations**; always label them "typical — check your appliance label" and let users edit.
Appliances with compressors (fridge, freezer, AC) use a **duty-cycled effective hours** value.

### 4.3 Peak sun hours by zone (configurable defaults — verify)

| Zone | Default PSH (h/day) |
|---|---|
| North-West / North-East | 5.5 |
| North-Central (Middle Belt) | 5.0 |
| South-West | 4.5 |
| South-East / South-South (incl. coastal) | 4.2 |

Approximate planning values. Validate with PVGIS / NASA POWER data for the target
locations and prefer conservative numbers. Stored in `solar_calc_settings.psh_by_zone`.

---

## 5. Architecture & Files

```
app/
  Services/Solar/
    SolarSizingCalculator.php        # pure function: (inputs, settings) → results DTO
    SolarSettings.php                # loads active settings snapshot (cached)
    PackageMatcher.php               # finds products matching results
  DataTransferObjects/Solar/{CalculatorInput,CalculatorResult}.php
  Http/Controllers/Public/SolarCalculatorController.php
  Http/Controllers/Admin/{AppliancePresetController,SolarSettingsController}.php
  Http/Requests/Solar/{CalculateRequest,SaveCalculationRequest}.php
  Models/{AppliancePreset,SolarCalculation}.php
resources/js/
  Pages/Public/SolarCalculator/Index.vue
  Pages/Public/SolarCalculator/Shared.vue          # /solar-calculator/r/{uuid}
  Components/solar/
    ApplianceSearch.vue  ApplianceRow.vue  PresetChips.vue
    SetupForm.vue  ResultSummary.vue  ResultCard.vue
    PackageSuggestions.vue  CalculatorWarnings.vue  StickyTotals.vue
  Composables/useSolarCalculator.ts                 # mirrors PHP engine
  lib/solar/{constants.ts,types.ts}
tests/
  Unit/SolarSizingCalculatorTest.php
  Feature/SolarCalculatorTest.php
  Fixtures/solar_calculator_cases.json              # shared golden cases (PHP + Vitest)
resources/js/__tests__/useSolarCalculator.spec.ts
```

### 5.1 Routes

| Method | URL | Name | Purpose |
|---|---|---|---|
| GET | `/solar-calculator` | solar.index | Tool + SEO content (SSR) |
| POST | `/solar-calculator/calculate` | solar.calculate | Server-verified calculation (JSON), throttled |
| POST | `/solar-calculator/save` | solar.save | Persist + return share URL |
| GET | `/solar-calculator/r/{uuid}` | solar.shared | Shared result page (`noindex`) |
| GET | `/solar-calculator/r/{uuid}/pdf` | solar.pdf | PDF download (Could) |
| POST | `/request-quote` | quotes.store | Accepts optional `calculation_uuid` |
| — | `/admin/solar/presets` · `/admin/solar/settings` | admin.solar.* | Manage presets & constants |

Use Ziggy `route()` helpers; throttle `calculate` and `save` (e.g. 60/min per IP).

### 5.2 Request validation (`CalculateRequest`)

```
appliances                 required|array|min:1|max:40
appliances.*.name          required|string|max:80
appliances.*.watts         required|numeric|min:1|max:10000
appliances.*.qty           required|integer|min:1|max:99
appliances.*.hours         required|numeric|min:0.1|max:24
appliances.*.surge         nullable|numeric|min:1|max:10
appliances.*.preset_id     nullable|exists:appliance_presets,id
backup_hours               required|numeric|min:1|max:24
goal                       required|in:backup,full_solar
battery_type               required|in:lithium,tubular
zone                       required|in:north_west,north_east,north_central,south_west,south_east,south_south
days_of_autonomy           nullable|integer|min:1|max:3
panel_watts                nullable|in:400,450,550
```
Preset values are re-read from the DB when `preset_id` is given; only user-edited values are
taken from the request (still range-checked).

### 5.3 Result DTO (shape returned to Vue)

```json
{
  "energy":   { "dailyWh": 4978, "monthlyKWh": 149.3, "peakW": 632, "surgePeakW": 932 },
  "inverter": { "kva": 2.5, "voltage": 24, "upsizedForBattery": true, "surgeOk": true },
  "battery":  { "usableWhNeeded": 5371, "bankWh": 6714, "bankAh": 280, "type": "lithium",
                "dod": 0.8, "unitsNeeded": 3, "unitLabel": "24V 100Ah lithium" },
  "solar":    { "requiredW": 1475, "panelW": 550, "panels": 3, "arrayW": 1650,
                "estDailyGenWh": 5569, "peakSunHours": 4.5 },
  "controller": { "mpptAmps": 85.9, "recommendedA": 100 },
  "warnings": ["resistive_load", "ac_load", "motor_surge"],
  "packages": [ { "id": 12, "name": "…", "slug": "…", "price": 0, "tier": "good" } ],
  "settingsVersion": 3
}
```
Money fields stay in **kobo** (project rule). Never expose internal IDs beyond what the UI needs.

### 5.4 Package matching (`PackageMatcher`)
1. Candidates: published solar packages whose `capacity_kva ≥ result.inverter.kva`,
   `battery_kwh ≥ result.battery.bankWh/1000`, and `panels_w ≥ result.solar.arrayW`.
2. Sort by price; return the cheapest as **Good**, the next tier up (more battery/panel
   headroom) as **Better**, and a lithium/top-spec option as **Best** when available.
3. If nothing fits → show **"Your system needs a custom design — Request a custom quote"**
   with the full profile prefilled. Never show a price the catalogue doesn't have.

### 5.5 Frontend rules
- Calculation runs in `useSolarCalculator()` (computed/reactive); debounce only text inputs.
- Persist the draft to `localStorage` inside `try/catch`, only after mount (SSR-safe), with an
  expiry (e.g. 7 days) and a "Clear" button.
- Number inputs: `inputmode="decimal"`, min/max, clear error messages, large tap targets.
- Show a skeleton instead of jumping layout when packages load (CLS).
- Results ↔ server: on **Save/Quote**, POST inputs; server recomputes and returns the
  authoritative result; if it differs from the client, display the server result.
- Accessibility: labelled inputs, `aria-live="polite"` on totals, keyboard-operable preset chips,
  contrast AA, no information by colour alone.
- Copy: plain English, tooltips explaining *watts, kVA, Ah, DoD, MPPT* in one line each.

---

## 6. SEO for the Calculator Page

The tool must not be a JS-only island. Server-render real content around it.

- **URL:** `/solar-calculator` · **Title:** `Solar Calculator Nigeria – Size Your Inverter, Battery & Panels | {{COMPANY_NAME}}`
- **Meta description (≈150):** "Free solar load calculator for Nigerian homes and businesses. Add your appliances and get the inverter, battery and solar panel size you need."
- **H1:** "Solar Calculator: How Much Solar Do You Need in Nigeria?"
- **On-page content (server-rendered, 500–800 words):** how it works, what kVA/battery/panels mean,
  example household sizing, common appliance wattage table (the preset list), tips to cut load,
  FAQs (visible text), internal links to Solar service, solar packages, projects, blog posts and
  the quote page.
- **JSON-LD:** `WebApplication` (name, applicationCategory `UtilitiesApplication`, operatingSystem
  `Web`, offers free), `BreadcrumbList`, `FAQPage` (visible FAQs), plus sitewide Organization.
- **Indexing:** `/solar-calculator` indexable; `/solar-calculator/r/{uuid}` and PDF are `noindex`
  and excluded from the sitemap; query strings never create indexable URLs.
- **Supporting content:** blog posts such as "How to calculate the solar system size for your
  house in Nigeria", "5kVA vs 3.5kVA inverter: which do you need?", "Lithium vs tubular
  batteries" linking back to the calculator.
- **Performance:** calculator JS lazy hydrated; preset data delivered in the page props
  (no extra request); no heavy chart libraries. Targets as in `docs/SEO.md` §7.
- **Events:** push the analytics events from FR-105 (consent-aware).

---

## 7. Lead Capture Integration

- "Get a quote for this system" opens the quote form prefilled with: name/phone fields empty,
  service = Solar, plus `calculation_uuid`. The admin quote view shows the appliance list,
  daily energy, and recommended system.
- WhatsApp message template (URL-encoded, via `wa.me`):
  `Hello, I used your solar calculator. Daily use: 4.98 kWh. Suggested: 2.5kVA inverter, 6.7kWh lithium battery, 1.65kW solar. Result: {share-url}. Please advise.`
- Optional soft gate: ask for phone/email **only** to *save/send* results, never to view them.
- Rate-limit and honeypot/Turnstile on save/quote endpoints.

---

## 8. Warnings & Messaging Rules

| Condition | Message |
|---|---|
| Any `is_resistive` appliance | "Heating appliances (iron, kettle, cooker) use a lot of power and drain batteries fast. Consider gas or running them only when the sun is out." |
| AC selected | "Air conditioners need a larger system. Inverter-type ACs use less power." |
| Motor load (`m ≥ 3`) | "Fridges, freezers, ACs and pumps draw extra power when starting; we've sized the inverter for that." |
| Result > 10 kVA | "This is a large system. Request a custom design." |
| `hours` > `backup_hours` for an item | Info: only the backup window is counted for battery sizing. |
| Total daily energy < 300 Wh | "That's very small — double-check your quantities and hours." |

Always include: *"This is an estimate. Final sizing depends on a site survey, roof/shading,
appliance ratings and brand specifications."*

---

## 9. Testing

| Level | What |
|---|---|
| Unit (Pest) | Each formula, rounding to standard sizes, voltage reconciliation, surge upsizing, edge values (min/max), lithium vs tubular, backup vs full_solar |
| Golden fixtures | ≥ 12 cases in `solar_calculator_cases.json` (small flat, family home, home + AC, shop with freezer, pump-heavy, >10 kVA) run in **both** Pest and Vitest and must match exactly |
| Feature | Validation rules, rate limiting, tamper attempt (client sends wrong watts/preset), save/share/noindex, package matching with and without matches, quote prefill |
| Component | Add/edit/remove appliance, totals update, warnings appear, localStorage failure doesn't break the page |
| E2E (Playwright) | Pick presets → set options → view results → request quote; mobile viewport |
| SEO | SSR output contains H1, intro copy, FAQs and JSON-LD; shared result pages return `noindex` |
| Performance | Lighthouse budget on `/solar-calculator` (mobile) per `docs/TESTING.md` |

Rounding rule: round **up** for capacity (inverter, panels, battery units, controller), display
energy to 2 decimals, amps to 1 decimal.

---

## 10. Implementation Plan (tick as you go)

- [ ] **S1** Migrations, models, seeders (presets, settings, PSH) + admin CRUD for presets/settings
- [ ] **S2** `SolarSizingCalculator` + DTOs + golden fixtures + unit tests
- [ ] **S3** TypeScript mirror `useSolarCalculator` + parity tests against fixtures
- [ ] **S4** Calculator UI (steps 1–3), warnings, sticky totals, draft persistence
- [ ] **S5** `/calculate` endpoint, saving, shared result page, rate limiting
- [ ] **S6** `PackageMatcher` + product `specs` fields + package suggestions UI
- [ ] **S7** Quote/WhatsApp integration with `calculation_uuid`; admin quote view shows profile
- [ ] **S8** SEO content, FAQs, JSON-LD, sitemap entry, internal links, analytics events
- [ ] **S9** PDF export (optional), polish, accessibility pass, Lighthouse + E2E green
- [ ] **S10** Technical review of constants/presets by the client's solar engineer; sign-off

**Definition of done:** all of S1–S8 complete, golden fixtures pass in PHP and TS, SEO checks
green, mobile QA on a low-end Android phone, and the client's engineer has approved the default
constants and preset wattages.

---

## 11. Future Ideas (not in scope now)
- Grid-hours input ("NEPA/light supply hours per day") to reduce battery and solar size
- Monthly electricity/fuel savings and payback estimate (generator petrol/diesel comparison)
- Roof-area check ("will 8 panels fit?") and tilt/shading questions
- Appliance upload by photo of label (OCR) · Yoruba/Hausa/Igbo/Pidgin labels
- Instalment/financing estimate · email/SMS results · compare 2–3 scenarios side by side

## 12. Notes For The AI Agent
- Keep the **engine pure and deterministic**; no DB calls or randomness inside it.
- Change constants only through `solar_calc_settings`; never hard-code magic numbers in Vue or PHP.
- Do not invent product prices or specs; match against real catalogue data only.
- All numbers shown to users must be traceable to the formulas in §3; update this file and the
  golden fixtures whenever a formula changes.
- Follow `AGENTS.md` for SSR, SEO, money-in-kobo, validation and testing rules.
