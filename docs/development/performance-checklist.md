# Kunena Performance Checklist

Use this checklist to compare before/after speed changes in a consistent way.

## 1) Prepare a stable test window

- Use the same URL, logged-in state, and dataset for each run.
- Disable unrelated background jobs during measurements.
- Run at least one warmup pass before collecting metrics.

## 2) Capture baseline timings

Run from workspace root:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\perf-smoke.ps1 -Url "http://localhost/index.php?option=com_kunena&view=home" -Warmup 3 -Iterations 20
```

Record:

- Avg ms
- P95 ms
- Min/Max ms
- Query count (if debug SQL markers are enabled)

## 3) Compare after changes

Repeat the same command after each optimization and compare outputs.

Recommended threshold for a meaningful win:

- Avg ms improves by at least 10%
- P95 ms does not regress
- Query count is flat or reduced

## 4) Verify cache behavior

- First request may be slower (cache fill).
- Subsequent requests should be stable and faster.
- After editing template XML/icon config, verify caches invalidate automatically.

## 5) Regression safety checks

- Browse category lists, topic lists, and topic pages.
- Confirm topic icons, category icons, and labels still render correctly.
- Clear cache once and re-check:

```powershell
# Run in Joomla admin if needed, or use your existing cache-clear flow
```

