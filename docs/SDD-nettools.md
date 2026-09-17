# Nettools Module — SDD

> **Module:** `tgbot-module-nettools` (`BAGArt\TelegramBotNettools`)
> **Status:** 100% complete (205 tests)

---

## What Was Done

Network auditor/admin toolkit module. 19 user commands for network reconnaissance and diagnostics.

### Command Surface

**User commands**: `/ip`, `/whois`, `/dns`, `/asn`, `/http`, `/subs`, `/ping`, `/trace`, `/port`, `/os`, `/ssl`, `/sec`, `/mail`, `/reco`, `/report`, `/my`, `/r`
**Admin-gated**: `/portscan`, `/dnsbl`, `/nt` (network tools)
**MCP probe**: External binary probe integration

### Key Features

- Target memory (remembers previous scan targets per user)
- Recommendation engine (suggests next scans based on previous results)
- Report generation (formatted scan results)
- 5-language i18n (RU, EN, FR, ES, ZH)
- Binary dependencies (nmap, etc.) + mmdb files for GeoIP

### Key Decisions

- Admin commands gated by role check (prevents abuse)
- Target memory is user-scoped (privacy)
- MCP probe allows external tool integration without PHP binary dependencies

### Files

- `src/` — Commands, handlers, probe integration
- `Readme.md` — Module overview (root, 120 lines)
