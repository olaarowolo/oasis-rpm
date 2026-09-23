# Copilot Instructions

This file defines guidance for AI-generated code in this repository.

## Checklist

- [ ] Verify that this file exists and reflects current repository conventions.
- [ ] Clarify project requirements before making changes.
- [ ] Scaffold the project where needed.
- [ ] Customize the project to meet feature and styling requirements.
- [ ] Install required extensions and dependencies.
- [ ] Compile/build the project to ensure it runs.
- [ ] Create and run tasks/tests.
- [ ] Launch the project to verify behavior.
- [ ] Ensure documentation is complete.

## Email view standards

- Follow `resources/views/emails/bootcamp/EMAIL_VIEWS_PLAN.md` for email view standards, naming, data contracts, and testing guidance.
- When adding email views:
  - create a Blade file under `resources/views/emails/daily/` (or appropriate folder).
  - add corresponding `subject` and `preheader` keys to `resources/lang/en/emails.php`.
  - include a plain-text alternative view.
- Use `layouts/bootcamp.blade.php` for common styles.
- Include preheader text via the `$preheader` variable.

## QA steps for email views

1. Render the view with sample data and inspect for missing variables.
2. Send a test message to Gmail, Outlook, and Apple Mail.
3. Verify the plain-text alternative is present and links include proper tracking/UTM params.

## Contact

- For copy or scheduling changes: events@yourdomain.org
