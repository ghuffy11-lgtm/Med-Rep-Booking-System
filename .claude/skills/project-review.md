---
name: project-review
description: Review Med-Rep Booking System project status, check for common issues, and verify critical functionality
tags: [review, testing, debugging]
---

# Project Review Skill

This skill helps review the Med-Rep Booking System for common issues and verify critical functionality.

## What This Skill Does

1. **Checks Git Status** - Current branch, uncommitted changes
2. **Analyzes Recent Changes** - Reviews what changed in last 5 commits
3. **Verifies Critical Files** - Ensures key files exist and have correct structure
4. **Checks for Common Issues** - Looks for known anti-patterns
5. **Reviews Statistics Implementation** - Validates statistics service methods
6. **Checks 2FA Implementation** - Verifies 2FA flow integrity
7. **Summarizes Project State** - Provides actionable recommendations

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

### Recent Changes Analysis
- Last 5 commits with messages
- Files modified in recent commits
- New features added
- Bugs fixed
- Pattern changes (e.g., array access fixes)
- Impact on complete system vs specific modules

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

🔄 RECENT CHANGES (Last 5 Commits):
1. ✅ [3799441] docs: Add Claude Code guide and project review skill
   - Impact: Complete system (documentation)
   - Files: 2 new docs added
   - Status: Documentation enhancement

2. ✅ [2ec99cf] docs: Add comprehensive documentation for Statistics and 2FA
   - Impact: Complete system (documentation)
   - Files: STATISTICS_AND_2FA_DOCUMENTATION.md
   - Status: 850-line reference guide created

3. ✅ [dc002bd] fix: Show all booking statuses in distribution chart
   - Impact: Statistics module only
   - Files: StatisticsService.php, StatisticsController.php
   - Status: Bug fix (status distribution now shows all 4 statuses)

4. ✅ [8af3bb2] fix: Fix pharmacy admin export functionality
   - Impact: Statistics module only
   - Files: StatisticsController.php, StatisticsExport.php, 5 views
   - Status: Bug fix (pharmacy admin can now export)

5. ✅ [27a9d16] fix: Fix approval rate calculation
   - Impact: Statistics module only
   - Files: StatisticsService.php, StatisticsController.php
   - Status: Bug fix (status changed from "confirmed" to "approved")

📊 CHANGE SUMMARY:
- 🐛 3 bug fixes (all in Statistics module)
- 📚 2 documentation additions (complete system)
- 🎯 Feature scope: Statistics Dashboard & 2FA (already implemented)
- ⚡ System impact: 40% module-specific, 60% system-wide docs

✅ PASSED CHECKS:
- Git branch: claude/statistics-and-2fa-mt8lz
- All critical files present
- Statistics methods have month/year params
- 2FA session keys correct
- All recent bug fixes validated ✅

⚠️ WARNINGS:
- None detected

❌ ISSUES FOUND:
- None detected

💡 RECOMMENDATIONS:
1. All recent changes look good ✅
2. System is stable and well-documented
3. Ready for production deployment

📈 STATISTICS HEALTH: 100% ✅
🔐 2FA HEALTH: 100% ✅
📦 GIT HEALTH: 100% ✅
📚 DOCUMENTATION: 100% ✅

Overall Status: EXCELLENT ✅
```

## Behind the Scenes

The skill performs these checks:

```bash
# 1. Git checks
git status
git log --oneline -5
git branch

# 2. Recent changes analysis
git log --oneline -5 --name-only  # Get commits with file names
git show --stat HEAD~0            # Latest commit details
git show --stat HEAD~1            # Second latest commit details
# Analyze commit types: feat, fix, docs, refactor, etc.
# Categorize by impact: complete system vs specific module
# Count changes by type

# 3. File existence checks
test -f src/app/Services/StatisticsService.php
test -f src/app/Http/Controllers/StatisticsController.php
# ... more files

# 4. Content checks (grep patterns)
grep -r "pharmacy_id" src/app/Http/Controllers/StatisticsController.php
grep -r "Auth::logout()" src/app/Http/Controllers/TwoFactorController.php
grep -r "->name" src/resources/views/admin/statistics/

# 5. Structure validation
# Check method signatures
grep "public static function.*(\$month.*\$year" src/app/Services/StatisticsService.php

# 6. Validate recent fixes
# For each recent bug fix commit, verify the fix is still in place
# Check that fixed patterns are correct (e.g., array access, status names)
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

### Example 4: Understanding Recent Work
```
User: /project-review --focus=recent
Claude: Analyzing recent changes...

🔄 RECENT CHANGES ANALYSIS:
Last 5 commits focus on:
- 60% Bug fixes (Statistics module)
- 40% Documentation (Complete system)

Key improvements:
✅ Status distribution now complete (all 4 statuses)
✅ Pharmacy admin can export (was broken)
✅ Approval rate fixed (wrong status name)
✅ Comprehensive documentation added

Impact assessment:
- Statistics module: 100% functional ✅
- 2FA system: Unchanged, still working ✅
- Documentation: Significantly improved ✅

Conclusion: Recent work focused on fixing Statistics bugs
and documenting the complete system. All fixes validated.
```

## Maintenance

Update this skill when:
- New critical files are added
- New anti-patterns are discovered
- New features are implemented
- Common issues change

---

## Key Features

### Complete System Analysis
The skill checks the **ENTIRE Med-Rep Booking System**, not just recent changes:
- All critical files across the project
- Complete Statistics implementation (8 service methods)
- Complete 2FA system (login, challenge, verification)
- All export functions (Excel & PDF)
- System-wide anti-patterns

### Recent Changes Analysis
Additionally provides **focused analysis of recent work**:
- Last 5 commits with categorization (feat/fix/docs)
- Impact assessment (complete system vs specific module)
- Change type distribution (bugs vs features vs docs)
- Validation that fixes are still in place

### Intelligent Categorization
- **Module-specific changes**: Statistics, 2FA, Bookings, etc.
- **System-wide changes**: Documentation, architecture, security
- **Change types**: Bug fixes, features, refactoring, documentation
- **Impact level**: Critical, moderate, minor

---

**Skill Version:** 1.1
**Last Updated:** January 16, 2026
**Compatibility:** Med-Rep Booking System v1.0+
**New in 1.1:** Recent Changes Analysis with impact categorization
