---
name: project-review
description: Review Med-Rep Booking System project status, check for common issues, and verify critical functionality
tags: [review, testing, debugging]
---

# Project Review Skill

This skill helps review the Med-Rep Booking System for common issues and verify critical functionality.

## What This Skill Does

1. **Checks Git Status** - Current branch, uncommitted changes
2. **Verifies Critical Files** - Ensures key files exist and have correct structure
3. **Checks for Common Issues** - Looks for known anti-patterns
4. **Reviews Statistics Implementation** - Validates statistics service methods
5. **Checks 2FA Implementation** - Verifies 2FA flow integrity
6. **Summarizes Project State** - Provides actionable recommendations

## When to Use

- Starting work on a new session
- After user reports issues
- Before making major changes
- After merging/pulling updates
- Regular health checks

## Usage

```
/project-review
```

Or with specific focus:

```
/project-review --focus=statistics
/project-review --focus=2fa
/project-review --focus=git
```

## What It Checks

### Git Status
- Current branch name
- Uncommitted changes
- Recent commits
- Push/pull status

### File Structure
- Critical controllers exist
- Service files present
- View files intact
- Export classes available

### Common Issues
- ❌ pharmacy_id checks (should not exist)
- ❌ Array/object access mismatches
- ❌ Status name errors (confirmed vs approved)
- ❌ Auth::logout() in 2FA flow
- ❌ Missing month/year parameters
- ❌ Field name mismatches

### Statistics Health
- Service methods have month/year params
- Controllers pass selected month/year
- Views handle array data correctly
- Exports include month/year filtering

### 2FA Health
- Session keys use `2fa:auth:*` prefix
- No logout during challenge
- Trusted device logic intact
- Recovery codes encrypted

## Output Format

The skill will provide:

```
📊 PROJECT REVIEW SUMMARY
========================

✅ PASSED CHECKS:
- Git branch: claude/statistics-and-2fa-mt8lz
- All critical files present
- Statistics methods have month/year params
- 2FA session keys correct

⚠️ WARNINGS:
- 3 uncommitted changes detected
- Consider updating documentation

❌ ISSUES FOUND:
- Line 42 in StatisticsService.php: Missing month parameter
- Line 128 in super-admin.blade.php: Using object notation on array

💡 RECOMMENDATIONS:
1. Commit uncommitted changes
2. Fix array access in super-admin.blade.php
3. Add month parameter to getNewMetric()

📈 STATISTICS HEALTH: 95% ✅
🔐 2FA HEALTH: 100% ✅
📦 GIT HEALTH: 85% ⚠️

Overall Status: HEALTHY WITH MINOR ISSUES
```

## Behind the Scenes

The skill performs these checks:

```bash
# 1. Git checks
git status
git log --oneline -5
git branch

# 2. File existence checks
test -f src/app/Services/StatisticsService.php
test -f src/app/Http/Controllers/StatisticsController.php
# ... more files

# 3. Content checks (grep patterns)
grep -r "pharmacy_id" src/app/Http/Controllers/StatisticsController.php
grep -r "Auth::logout()" src/app/Http/Controllers/TwoFactorController.php
grep -r "->name" src/resources/views/admin/statistics/

# 4. Structure validation
# Check method signatures
grep "public static function.*(\$month.*\$year" src/app/Services/StatisticsService.php
```

## Integration with Agents

This skill can be used alongside specialized agents:

```bash
# Use this skill first to identify issues
/project-review

# Then use appropriate agent to fix
/explore src/app/Services  # Explore agent to understand structure
/plan "Fix array access issues"  # Plan agent for strategy
```

## Configuration

No configuration needed - works out of the box for Med-Rep Booking System.

## Examples

### Example 1: Quick Health Check
```
User: /project-review
Claude: Running comprehensive project review...

📊 PROJECT REVIEW SUMMARY
========================
✅ All critical systems operational
⚠️ 2 uncommitted changes
💡 Ready to commit and push

Overall Status: HEALTHY
```

### Example 2: Issue Detection
```
User: /project-review --focus=statistics
Claude: Reviewing statistics implementation...

❌ ISSUES FOUND:
- StatisticsController.php:95 - Missing month parameter in export
- super-admin.blade.php:156 - Array accessed as object

💡 RECOMMENDATIONS:
1. Add month/year to exportExcel() method
2. Change $dept->name to $dept['department']
```

### Example 3: After User Reports Bug
```
User: Statistics page is broken
Claude: Let me run a project review to diagnose...
/project-review --focus=statistics

Found 3 critical issues:
1. Array/object mismatch in view
2. Missing approval rate calculation
3. Export not using selected month

Shall I fix these issues?
```

## Maintenance

Update this skill when:
- New critical files are added
- New anti-patterns are discovered
- New features are implemented
- Common issues change

---

**Skill Version:** 1.0
**Last Updated:** January 15, 2026
**Compatibility:** Med-Rep Booking System v1.0+
