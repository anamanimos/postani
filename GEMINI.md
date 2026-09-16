# Project Guidelines & Rules

## UI & Dialog Standards
- **Standard Modal & Notification**: Seluruh konfirmasi hapus data, peringatan, dan alert di aplikasi web (semua Blade views) **WAJIB** menggunakan SweetAlert2.
- **Dilarang**: Menggunakan `confirm(...)` atau `alert(...)` bawaan browser.
- **Form Delete Standard**: Gunakan `class="confirm-delete"` dan `data-confirm="Pesan konfirmasi..."` pada elemen `<form>` agar ditangani oleh listener SweetAlert2 global.

<!-- antislop:start -->
## antislop
For UI, copy, accessibility, mobile layout, or code comments work, refer to the antislop skills in `.agents/skills/`:
- Core rules: `.agents/skills/antislop/SKILL.md`
- UI / visual: `.agents/skills/antislop-ui/SKILL.md`
- Copy & text: `.agents/skills/antislop-copywriting/SKILL.md`
- Accessibility / People: `.agents/skills/antislop-human/SKILL.md`
- Mobile / responsive: `.agents/skills/antislop-layoutmobile/SKILL.md`
- Code comments: `.agents/skills/antislop-code/SKILL.md`
<!-- antislop:end -->
