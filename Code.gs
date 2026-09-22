/**
 * Student Research Supervision Portal - Google Apps Script Backend
 * Supervisor: Dr. Olasunkanmi Arowolo, PhD
 * Lecturer, Department of Journalism and Media Studies
 * Lagos State University (LASU)
 */

// STRICT SUPERVISOR AUTHORIZATION EMAIL(S)
// Primary display address kept for backwards compatibility / UI text.
var AUTHORIZED_SUPERVISOR_EMAIL = "olasunkanmi.arowolo@lasu.edu.ng";

// All accounts allowed to act as the supervisor. Add the Google account that OWNS
// the Apps Script project here too, because a web app deployed with
// "Execute as: Me" reports the OWNER's email, not the visitor's.
var AUTHORIZED_SUPERVISOR_EMAILS = [
  "olasunkanmi.arowolo@lasu.edu.ng",
  "olaarowolo.ng@gmail.com"
];

// Returns true if the given email is an authorized supervisor (case-insensitive).
function isAuthorizedSupervisor(email) {
  var e = String(email || "").trim().toLowerCase();
  if (!e) return false;
  return AUTHORIZED_SUPERVISOR_EMAILS.some(function(a) {
    return a.trim().toLowerCase() === e;
  });
}

/**
 * Reusable notification engine for meeting-log lifecycle events.
 * A single place defines the subject/body for every stage, so adding or changing
 * a stage is a one-line edit. Safe to call anywhere: it never throws (email
 * failures are logged, not fatal) and skips silently when no recipient exists.
 *
 * @param {string} event   One of: "SUBMITTED", "PENDING", "UNDER_REVIEW", "APPROVED", "REJECTED"
 * @param {Object} ctx     { studentName, studentEmail, meetingNumber, logId, feedback }
 */
function notifyMeetingLogEvent(event, ctx) {
  try {
    ctx = ctx || {};
    // SUBMITTED notifies the supervisor; all other stages notify the student.
    var to = (event === "SUBMITTED")
      ? AUTHORIZED_SUPERVISOR_EMAIL
      : String(ctx.studentEmail || "").trim();
    if (!to) return false; // no recipient -> skip gracefully

    return sendEmailView(to, "meetingStatus", {
      event: event,
      studentName: ctx.studentName || "Student",
      studentEmail: ctx.studentEmail || "",
      meetingNumber: ctx.meetingNumber || "",
      logId: ctx.logId || "",
      feedback: ctx.feedback || "",
      url: getWebAppUrl()
    });
  } catch (err) {
    Logger.log("notifyMeetingLogEvent failed (" + event + "): " + err);
    return false; // never let email failure break the main operation
  }
}

/**
 * Single, tested email path used everywhere in this project.
 * Tries MailApp first, falls back to GmailApp, validates the recipient,
 * logs failures, and never throws. Returns true on a successful send.
 */
function sendEmailSafe(to, subject, body, htmlBody) {
  try {
    to = String(to || "").trim();
    // Basic email sanity check; skip silently if missing/invalid.
    if (!to || to.indexOf("@") === -1) {
      Logger.log("sendEmailSafe: skipped, invalid recipient '" + to + "'");
      return false;
    }
    var text = body + "\n\n— TheOAsis (for LASU) UG-Research Supervision Portal";
    var options = { name: "TheOAsis (for LASU) UG-Research Supervision Portal" };
    if (htmlBody) options.htmlBody = htmlBody;
    try {
      MailApp.sendEmail(to, subject, text, options);
      return true;
    } catch (e1) {
      Logger.log("sendEmailSafe: MailApp failed (" + e1 + "), trying GmailApp");
      GmailApp.sendEmail(to, subject, text, options);
      return true;
    }
  } catch (err) {
    Logger.log("sendEmailSafe failed to " + to + ": " + err);
    return false;
  }
}

/**
 * Sends a test email to the supervisor. Run this once from the editor or the
 * "LASU Portal" menu to trigger the mail-authorization prompt and confirm
 * that email delivery works in your deployment.
 */
function sendTestEmail() {
  var ok = sendEmailView(AUTHORIZED_SUPERVISOR_EMAIL, "test", {});
  var msg = ok ? "Test email sent to " + AUTHORIZED_SUPERVISOR_EMAIL + "."
               : "Test email FAILED — check the execution log and mail authorization.";
  try { SpreadsheetApp.getUi().alert(msg); } catch (e) {}
  return msg;
}

/* =========================================================================
 * REUSABLE EMAIL VIEWS
 * A small, dependency-free templating layer so every email shares one branded
 * HTML shell and a set of reusable components. Add or change a view in one
 * place (EMAIL_VIEWS) and it applies everywhere.
 * ========================================================================= */

// Brand palette (kept in one place).
var EMAIL_BRAND = {
  navy: "#002744", blue: "#035388", gold: "#f59e0b",
  ink: "#1f2937", muted: "#6b7280", line: "#e5e7eb", bg: "#f0f4f8"
};

// Escapes text for safe inclusion in HTML.
function emailEscape(s) {
  return String(s == null ? "" : s)
    .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
}

// Reusable HTML components (inline styles for email-client compatibility).
var EMAIL_UI = {
  paragraph: function (text) {
    return '<p style="margin:0 0 14px;color:' + EMAIL_BRAND.ink +
           ';font-size:14px;line-height:1.6;">' + emailEscape(text) + '</p>';
  },
  heading: function (text) {
    return '<h2 style="margin:0 0 12px;color:' + EMAIL_BRAND.navy +
           ';font-size:18px;">' + emailEscape(text) + '</h2>';
  },
  badge: function (text, color) {
    color = color || EMAIL_BRAND.blue;
    return '<span style="display:inline-block;padding:4px 10px;border-radius:999px;' +
           'background:' + color + ';color:#fff;font-size:11px;font-weight:700;' +
           'letter-spacing:.3px;">' + emailEscape(text) + '</span>';
  },
  quote: function (text) {
    return '<blockquote style="margin:0 0 14px;padding:12px 16px;background:' + EMAIL_BRAND.bg +
           ';border-left:4px solid ' + EMAIL_BRAND.gold + ';border-radius:8px;color:' +
           EMAIL_BRAND.ink + ';font-size:14px;">' + emailEscape(text) + '</blockquote>';
  },
  // key/value rows table
  infoRows: function (rows) {
    var body = (rows || []).filter(function (r) { return r && r[1]; }).map(function (r) {
      return '<tr>' +
        '<td style="padding:6px 12px 6px 0;color:' + EMAIL_BRAND.muted + ';font-size:12px;white-space:nowrap;vertical-align:top;">' +
          emailEscape(r[0]) + '</td>' +
        '<td style="padding:6px 0;color:' + EMAIL_BRAND.ink + ';font-size:13px;">' +
          emailEscape(r[1]) + '</td></tr>';
    }).join("");
    return body ? '<table style="width:100%;border-collapse:collapse;margin:0 0 14px;">' + body + '</table>' : '';
  },
  button: function (label, url) {
    if (!url) return '';
    return '<p style="margin:6px 0 14px;"><a href="' + emailEscape(url) + '" ' +
           'style="display:inline-block;padding:10px 18px;background:' + EMAIL_BRAND.navy +
           ';color:#fff;text-decoration:none;border-radius:10px;font-size:13px;font-weight:700;">' +
           emailEscape(label) + '</a></p>';
  }
};

// Branded HTML shell wrapping any inner content (reusable layout).
function emailShell(title, innerHtml) {
  return '' +
  '<div style="margin:0;padding:24px 0;background:' + EMAIL_BRAND.bg + ';font-family:Inter,Arial,Helvetica,sans-serif;">' +
    '<div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid ' + EMAIL_BRAND.line + ';border-radius:16px;overflow:hidden;">' +
      // header
      '<div style="background:linear-gradient(135deg,' + EMAIL_BRAND.navy + ',' + EMAIL_BRAND.blue + ');padding:20px 24px;color:#fff;">' +
        '<div style="font-size:16px;font-weight:800;">Research Supervision Portal ' +
          '<span style="font-size:10px;background:' + EMAIL_BRAND.gold + ';color:#1a1a1a;padding:2px 6px;border-radius:6px;vertical-align:middle;">LASU</span></div>' +
        '<div style="font-size:11px;opacity:.85;margin-top:2px;">Dr. Olasunkanmi Arowolo • Journalism &amp; Media Studies</div>' +
      '</div>' +
      // body
      '<div style="padding:24px;">' +
        (title ? EMAIL_UI.heading(title) : '') +
        innerHtml +
      '</div>' +
      // footer
      '<div style="padding:14px 24px;border-top:1px solid ' + EMAIL_BRAND.line + ';color:' + EMAIL_BRAND.muted + ';font-size:11px;">' +
        'This is an automated message from the TheOAsis (for LASU) UG-Research Supervision Portal. Please do not reply.' +
      '</div>' +
    '</div>' +
  '</div>';
}

// Named views: each returns { subject, title, intro, rows?, quote?, badge?, cta? }.
// Single source of truth for all portal emails.
var EMAIL_VIEWS = {
  test: function (d) {
    return {
      subject: "LASU Portal — test email",
      title: "Test email",
      body: EMAIL_UI.paragraph("This confirms the Research Supervision Portal can send email successfully.")
    };
  },
  topicSubmitted: function (d) {
    return {
      subject: "New topic proposal from " + (d.studentName || "a student"),
      title: "New topic proposal",
      body:
        EMAIL_UI.paragraph((d.studentName || "A student") + " submitted a topic proposal for your review.") +
        EMAIL_UI.quote('"' + (d.topic || "") + '"') +
        EMAIL_UI.infoRows([
          ["Student", d.studentName], ["Matric", d.matric], ["Proposal", d.proposalId]
        ]) +
        (d.abstract ? EMAIL_UI.paragraph("Abstract: " + d.abstract) : "") +
        EMAIL_UI.button("Open the portal", d.url)
    };
  },
  topicApproved: function (d) {
    return {
      subject: "Your research topic has been APPROVED",
      title: "Topic approved",
      body:
        EMAIL_UI.paragraph("Dear " + (d.studentName || "Student") + ",") +
        '<p style="margin:0 0 14px;">' + EMAIL_UI.badge("APPROVED", "#059669") + '</p>' +
        EMAIL_UI.paragraph("Your research topic has been approved and is now your active project topic.") +
        EMAIL_UI.quote('"' + (d.topic || "") + '"') +
        (d.comment ? EMAIL_UI.quote("Supervisor comment: " + d.comment) : "") +
        EMAIL_UI.infoRows([["Approved on", d.approvedDate]]) +
        EMAIL_UI.button("View your portal", d.url)
    };
  },
  topicRevision: function (d) {
    return {
      subject: "Revision requested on your topic proposal",
      title: "Revision requested",
      body:
        EMAIL_UI.paragraph("Dear " + (d.studentName || "Student") + ",") +
        '<p style="margin:0 0 14px;">' + EMAIL_UI.badge("REVISION REQUESTED", "#e11d48") + '</p>' +
        EMAIL_UI.paragraph("Your supervisor has requested a revision of your proposed topic. Please revise and resubmit.") +
        EMAIL_UI.quote('"' + (d.topic || "") + '"') +
        (d.comment ? EMAIL_UI.quote("Supervisor comment: " + d.comment) : "") +
        EMAIL_UI.button("Revise in the portal", d.url)
    };
  },
  topicConditionallyApproved: function (d) {
    return {
      subject: "Your topic proposal has been CONDITIONALLY APPROVED",
      title: "Conditionally approved",
      body:
        EMAIL_UI.paragraph("Dear " + (d.studentName || "Student") + ",") +
        '<p style="margin:0 0 14px;">' + EMAIL_UI.badge("CONDITIONALLY APPROVED", "#d97706") + '</p>' +
        EMAIL_UI.paragraph("Your research topic has been <strong>conditionally approved</strong>. You may proceed with the next stage, but you <strong>must satisfy the conditions below</strong> before your work can be fully accepted.") +
        EMAIL_UI.quote('"' + (d.topic || "") + '"') +
        (d.conditions ? '<div style="margin:0 0 14px;padding:12px;background:#fffbeb;border-left:4px solid #f59e0b;border-radius:8px;"><strong style="color:#92400e;">Conditions:</strong><ul style="margin:6px 0 0 18px;padding:0;color:#92400e;font-size:13px;line-height:1.6;">' + d.conditions.split('\n').map(function(line){ return line.trim() ? '<li>' + line.trim() + '</li>' : ''; }).join('') + '</ul></div>' : '') +
        (d.comment ? EMAIL_UI.quote("Supervisor comment: " + d.comment) : "") +
        EMAIL_UI.infoRows([["Approved on", d.approvedDate]]) +
        EMAIL_UI.paragraph("Please ensure you meet all listed conditions. If you have questions, contact your supervisor before proceeding.") +
        EMAIL_UI.button("View your portal", d.url)
    };
  },
  meetingStatus: function (d) {
    // Reusable across all meeting-log statuses.
    var map = {
      SUBMITTED:    { label: "SUBMITTED",    color: EMAIL_BRAND.blue, line: "A new meeting log was submitted and is pending review." },
      PENDING:      { label: "PENDING",      color: EMAIL_BRAND.gold, line: "Your meeting log is pending supervisor review." },
      UNDER_REVIEW: { label: "UNDER REVIEW", color: "#2563eb",        line: "Your meeting log has been selected for review." },
      APPROVED:     { label: "APPROVED",     color: "#059669",        line: "Your meeting log has been approved." },
      REJECTED:     { label: "REWRITE REQUESTED", color: "#e11d48",   line: "A rewrite of your meeting log has been requested. Please revise and resubmit." }
    };
    var s = map[d.event] || map.PENDING;
    var isSupervisorNote = d.event === "SUBMITTED";
    return {
      subject: (isSupervisorNote ? "New meeting log submitted by " + (d.studentName || "a student")
                                 : "Meeting log " + (d.meetingNumber || "") + " — " + s.label),
      title: "Meeting log update",
      body:
        EMAIL_UI.paragraph("Dear " + (isSupervisorNote ? "Dr. Arowolo" : (d.studentName || "Student")) + ",") +
        '<p style="margin:0 0 14px;">' + EMAIL_UI.badge(s.label, s.color) + '</p>' +
        EMAIL_UI.paragraph(s.line) +
        EMAIL_UI.infoRows([
          ["Meeting", d.meetingNumber], ["Log ID", d.logId], ["Student", d.studentName]
        ]) +
        (d.feedback ? EMAIL_UI.quote("Supervisor note: " + d.feedback) : "") +
        EMAIL_UI.button("Open the portal", d.url)
    };
  },
  stageAdvanced: function (d) {
    return {
      subject: "Research stage updated: " + (d.stageName || ""),
      title: "Research stage updated",
      body:
        EMAIL_UI.paragraph("Dear " + (d.studentName || "Student") + ",") +
        '<p style="margin:0 0 14px;">' + EMAIL_UI.badge("STAGE " + (d.stage || ""), EMAIL_BRAND.navy) + '</p>' +
        EMAIL_UI.paragraph("Your research has advanced to a new stage:") +
        EMAIL_UI.quote((d.stageName || "")) +
        EMAIL_UI.infoRows([["Stage", (d.stage || "") + " of " + (d.total || "")]]) +
        (d.note ? EMAIL_UI.quote("Supervisor note: " + d.note) : "") +
        (parseInt(d.stage, 10) === 3
          ? EMAIL_UI.paragraph("Now that you have passed Stage 2 and advanced to Stage 3, please complete the UG/PG Defense Readiness Tracker to help us prepare you for your defense.") +
            EMAIL_UI.button("Fill the Defense Readiness Tracker", "https://olaarowolo.com/apps/ug-pg-dr.html")
          : "") +
        EMAIL_UI.button("View your roadmap", d.url)
    };
  },
  digest: function (d) {
    return {
      subject: "Daily supervision digest — " + (d.pendingProposals || 0) + " proposal(s), " + (d.pendingLogs || 0) + " log(s) pending" + (d.pendingResources > 0 ? (", " + d.pendingResources + " resource(s) awaiting review") : ""),
      title: "Daily supervision digest",
      body:
        EMAIL_UI.paragraph("Good morning, Dr. Arowolo. Here is your pending-items summary:") +
        EMAIL_UI.infoRows([
          ["Proposals pending approval", String(d.pendingProposals || 0)],
          ["Meeting logs pending / under review", String(d.pendingLogs || 0)],
          ["Resources awaiting review", String(d.pendingResources || 0)],
          ["Total supervised students", String(d.totalStudents || 0)]
        ]) +
        EMAIL_UI.button("Open the portal", d.url)
    };
  },
  resourceSubmitted: function (d) {
    return {
      subject: "Resource completion submitted by " + (d.studentName || "a student"),
      title: "Resource completion submitted",
      body:
        EMAIL_UI.paragraph((d.studentName || "A student") + " has marked a learning resource as complete and is awaiting your review.") +
        EMAIL_UI.quote('"' + (d.resourceTitle || "") + '"') +
        EMAIL_UI.infoRows([
          ["Student", d.studentName], ["Matric", d.matric], ["Points", String(d.points || 0)], ["Submitted", d.submittedDate || ""]
        ]) +
        EMAIL_UI.button("Review in the portal", d.url)
    };
  },
  resourceApproved: function (d) {
    return {
      subject: "Your resource completion has been APPROVED",
      title: "Resource approved",
      body:
        EMAIL_UI.paragraph("Dear " + (d.studentName || "Student") + ",") +
        '<p style="margin:0 0 14px;">' + EMAIL_UI.badge("APPROVED", "#059669") + '</p>' +
        EMAIL_UI.paragraph("Your supervisor has approved this learning resource. You have earned " + (d.points || 0) + " point(s).") +
        EMAIL_UI.quote('"' + (d.resourceTitle || "") + '"') +
        (d.feedback ? EMAIL_UI.quote("Supervisor comment: " + d.feedback) : "") +
        EMAIL_UI.infoRows([["Points awarded", String(d.points || 0)], ["Approved on", d.reviewedDate || ""]]) +
        EMAIL_UI.button("View your portal", d.url)
    };
  },
  resourceRejected: function (d) {
    return {
      subject: "Revision requested on your resource completion",
      title: "Resource revision requested",
      body:
        EMAIL_UI.paragraph("Dear " + (d.studentName || "Student") + ",") +
        '<p style="margin:0 0 14px;">' + EMAIL_UI.badge("REWRITE REQUESTED", "#e11d48") + '</p>' +
        EMAIL_UI.paragraph("Your supervisor has reviewed your resource completion and requested a revision. Please revisit the resource and resubmit.") +
        EMAIL_UI.quote('"' + (d.resourceTitle || "") + '"') +
        (d.feedback ? EMAIL_UI.quote("Supervisor comment: " + d.feedback) : "") +
        EMAIL_UI.button("Revisit in the portal", d.url)
    };
  }
};

/**
 * Renders a named view to { subject, html, text }.
 * Falls back gracefully if the view name is unknown.
 */
function renderEmail(viewName, data) {
  data = data || {};
  var fn = EMAIL_VIEWS[viewName];
  if (!fn) {
    return { subject: "LASU Portal notification", html: emailShell("Notification", EMAIL_UI.paragraph(String(data.message || ""))), text: String(data.message || "") };
  }
  var v = fn(data);
  var html = emailShell(v.title || "", v.body || "");
  // Build a plain-text version by stripping tags from the body.
  var text = String(v.body || "").replace(/<[^>]+>/g, " ").replace(/\s+/g, " ").trim();
  return { subject: v.subject || "LASU Portal notification", html: html, text: text };
}

/**
 * Renders a named view and sends it (HTML + plain-text). Reusable everywhere.
 */
function sendEmailView(to, viewName, data) {
  var e = renderEmail(viewName, data);
  return sendEmailSafe(to, e.subject, e.text, e.html);
}

/**
 * Best-effort web-app URL for CTA buttons in emails.
 */
function getWebAppUrl() {
  try { return ScriptApp.getService().getUrl() || ""; } catch (e) { return ""; }
}

/* =========================================================================
 * GEMINI AI ASSISTANT (server-side; API key never reaches the client)
 * ========================================================================= */

// The Gemini model to use. Prefer the floating "-latest" alias so the assistant
// keeps working when Google retires a specific dated model (e.g. gemini-1.5-flash
// was removed from the public API). Change the first entry to pin a model.
var GEMINI_MODEL = "gemini-flash-latest";

// Ordered fallback list — FREE-TIER ONLY. askGemini() tries these in turn and
// uses the first that responds, so a single deprecated/unavailable/overloaded
// model never breaks the chat. GEMINI_MODEL is always tried first (de-duped).
//
// The Gemini free tier is Flash-class only (Pro models are paid-only), so every
// entry here is a free Flash/Flash-Lite model. The order is deliberate:
//   - Flash first for answer quality.
//   - Flash-Lite last: it has the most generous free daily quota and is the
//     least likely to be rate-limited (429) or overloaded (503) during a spike,
//     so it's the best "always answers something" free fallback.
// Different aliases/versions map to different backends, which helps during a
// transient overload even within the Flash family.
var GEMINI_MODEL_CANDIDATES = [
  "gemini-flash-latest",      // floating Flash alias (primary)
  "gemini-2.5-flash",         // pinned current Flash
  "gemini-2.0-flash",         // older Flash (different backend)
  "gemini-flash-lite-latest", // floating Flash-Lite alias
  "gemini-2.5-flash-lite"     // pinned Flash-Lite — highest free quota, final fallback
];

// System context that shapes every assistant reply for this portal.
var GEMINI_SYSTEM_CONTEXT =
  "You are the research assistant for Dr. Olasunkanmi Arowolo, a Journalism and " +
  "Media Studies supervisor at Lagos State University (LASU). Give concise, practical, " +
  "academically sound guidance for undergraduate media/journalism research: framing " +
  "analysis, content analysis codebooks, methodology (Chapter 3), APA 7th citations, " +
  "research objectives/hypotheses, and Nigerian/Lagos media context. Keep answers focused.";

/**
 * One-time setup: store your Gemini API key in Script Properties.
 * Run from the editor:  setGeminiApiKey("YOUR_KEY_HERE")
 * The key is stored server-side and is never exposed to the browser.
 */
function setGeminiApiKey(key) {
  var k = String(key || "").trim();
  if (!k) return "No key provided. Usage: setGeminiApiKey(\"YOUR_KEY\").";
  PropertiesService.getScriptProperties().setProperty("GEMINI_API_KEY", k);
  return "Gemini API key saved (" + k.length + " chars).";
}

function getGeminiApiKey_() {
  return PropertiesService.getScriptProperties().getProperty("GEMINI_API_KEY") || "";
}

/**
 * Stores a Gemini API key AND immediately runs a live test call, so you know
 * right away whether it works. No key is embedded in this file — pass it in
 * from the editor, e.g.:
 *   setGeminiApiKeyAndTest("AQ.Ab8...your-key")
 * (For a no-typing UI flow, use the "Set Gemini API key" menu item, which
 * prompts for the key; then run "Check Gemini setup".)
 */
function setGeminiApiKeyAndTest(key) {
  var KEY = String(key || "").trim();
  if (!KEY) return "No key provided. Usage: setGeminiApiKeyAndTest(\"YOUR_KEY\").";
  var saved = setGeminiApiKey(KEY);
  var res = askGemini("Reply with the single word: OK");
  var msg = saved + "\nTest call: " +
    (res && res.success ? "SUCCESS — " + res.reply + " [model: " + (res.model || "?") + "]"
                        : "FAILED — " + (res && res.message));
  try { SpreadsheetApp.getUi().alert(msg); } catch (e) {}
  Logger.log(msg);
  return msg;
}

/**
 * Menu-driven key setup: prompts for the key so you don't have to edit code.
 * Get the key from Google AI Studio (aistudio.google.com -> Get API key).
 */
function promptSetGeminiApiKey() {
  try {
    var ui = SpreadsheetApp.getUi();
    var resp = ui.prompt("Set Gemini API key",
      "Paste your Gemini API key (from Google AI Studio):", ui.ButtonSet.OK_CANCEL);
    if (resp.getSelectedButton() !== ui.Button.OK) return;
    var msg = setGeminiApiKey(resp.getResponseText());
    ui.alert(msg);
  } catch (e) {
    Logger.log("promptSetGeminiApiKey: " + e);
  }
}

/**
 * Reports whether a key is stored (without revealing it) and runs a live test call.
 * Shows the key's length and 4-char prefix so you can spot a wrong key (a valid
 * Gemini API key from Google AI Studio starts with "AIza") without exposing it.
 */
function checkGeminiSetup() {
  var key = getGeminiApiKey_();
  var msg;
  if (!key) {
    msg = "No Gemini API key stored. Use 'Set Gemini API key' first.";
  } else {
    var prefix = key.slice(0, 4);
    // Valid Gemini keys: new authorization keys start with "AQ." ; legacy
    // standard keys start with "AIza" (being retired by Google in Sept 2026).
    var looksValid = (prefix === "AIza") || (key.indexOf("AQ.") === 0);
    var res = askGemini("Reply with the single word: OK");
    msg = "Key stored (" + key.length + " chars, prefix '" + prefix + "'" +
          (looksValid ? "" : " — WARNING: expected 'AQ.' or 'AIza'; this may not be a valid Gemini API key") + ").\n" +
          "Test call: " +
          (res && res.success ? "SUCCESS — " + res.reply + " [model: " + (res.model || "?") + "]"
                              : "FAILED — " + (res && res.message));
  }
  try { SpreadsheetApp.getUi().alert(msg); } catch (e) {}
  Logger.log(msg);
  return msg;
}

/**
 * Diagnostic: logs the stored key's length and 4-char prefix WITHOUT revealing
 * the key. A valid Gemini API key starts with "AIza"; anything else (e.g. an
 * OAuth/auth token) will fail with a 403 "project denied access" error.
 * Run from the editor and check View -> Logs.
 */
function debugGeminiKey() {
  var k = getGeminiApiKey_();
  if (!k) { Logger.log("No Gemini API key stored."); return "No key stored."; }
  var prefix = k.slice(0, 4);
  var isAuthKey = k.indexOf("AQ.") === 0;   // new authorization key
  var isStdKey = prefix === "AIza";          // legacy standard key
  var verdict = isAuthKey
    ? "Prefix 'AQ.' — new-style authorization key (current format). OK."
    : (isStdKey
        ? "Prefix 'AIza' — legacy standard key (works until Google retires it ~Sept 2026)."
        : "Prefix is neither 'AQ.' nor 'AIza' — likely the wrong value (e.g. an OAuth token, not an API key).");
  var msg = "GEMINI_API_KEY: length=" + k.length + ", prefix='" + prefix + "'. " + verdict;
  Logger.log(msg);
  return prefix;
}

/**
 * Sends a prompt to the Gemini API and returns { success, reply } or
 * { success:false, message }. Called from the client via google.script.run.
 */
function askGemini(prompt) {
  try {
    var text = String(prompt || "").trim();
    if (!text) return { success: false, message: "Empty prompt." };

    var key = getGeminiApiKey_();
    if (!key) {
      return { success: false, message: "Gemini API key not set. Run setGeminiApiKey(\"YOUR_KEY\") once in the Apps Script editor." };
    }

    var payload = {
      // Prepend the system context to the user's question.
      contents: [{ parts: [{ text: GEMINI_SYSTEM_CONTEXT + "\n\nQuestion:\n" + text }] }],
      generationConfig: { temperature: 0.4, maxOutputTokens: 800 }
    };

    // Build the ordered, de-duplicated model list (GEMINI_MODEL first).
    var models = [GEMINI_MODEL].concat(GEMINI_MODEL_CANDIDATES).filter(function (m, i, arr) {
      return m && arr.indexOf(m) === i;
    });

    var lastErr = "No models attempted.";
    // On a transient overload (503/429) we prefer to move to a DIFFERENT model
    // immediately rather than hammer the same busy one — a different model/tier
    // is far more likely to be free during a demand spike. We keep one short
    // retry per model as a light cushion for brief blips.
    var MAX_TRANSIENT_RETRIES = 1;

    for (var mi = 0; mi < models.length; mi++) {
      var model = models[mi];
      // Send the key via the x-goog-api-key header (works for both the new
      // "AQ." authorization keys and legacy "AIza" standard keys). The old
      // "?key=" query-string form only reliably works for legacy keys.
      var url = "https://generativelanguage.googleapis.com/v1beta/models/" +
                model + ":generateContent";

      var attempt = 0;
      var moveToNextModel = false;

      while (true) {
        var res = UrlFetchApp.fetch(url, {
          method: "post",
          contentType: "application/json",
          headers: { "x-goog-api-key": key },
          payload: JSON.stringify(payload),
          muteHttpExceptions: true
        });

        var code = res.getResponseCode();
        var data = {};
        try { data = JSON.parse(res.getContentText()); } catch (e) {}

        if (code === 200) {
          var reply = "";
          try { reply = data.candidates[0].content.parts[0].text; } catch (e) { reply = ""; }
          if (reply) return { success: true, reply: reply, model: model };
          lastErr = "No response generated by " + model + ".";
          moveToNextModel = true;
          break; // try next model
        }

        var apiMsg = (data && data.error && data.error.message) ? data.error.message : ("HTTP " + code);
        lastErr = apiMsg;
        Logger.log("askGemini: model '" + model + "' failed (" + code + "): " + apiMsg);

        // Transient: overloaded (503) or rate-limited (429). Retry the SAME model
        // a couple of times with a short backoff before giving up on it.
        var isTransient = (code === 503) || (code === 429) ||
          /high demand|overloaded|temporar|try again/i.test(apiMsg);
        if (isTransient && attempt < MAX_TRANSIENT_RETRIES) {
          attempt++;
          Utilities.sleep(600); // brief pause, then one quick retry of this model
          continue; // retry same model
        }
        if (isTransient) { moveToNextModel = true; break; } // exhausted -> next model (different backend)

        // Model unavailable (404 / not found) -> try the next model.
        var isModelUnavailable = (code === 404) ||
          /not\s*found|not\s*supported|unsupported|does not exist/i.test(apiMsg);
        if (isModelUnavailable) { moveToNextModel = true; break; }

        // Non-recoverable (auth, bad request, project denied) -> stop now.
        return { success: false, message: "Gemini error: " + apiMsg };
      }

      if (moveToNextModel) continue;
    }

    return { success: false, message: "Gemini is busy right now (all models overloaded). Please try again in a moment. Last error: " + lastErr };
  } catch (err) {
    Logger.log("askGemini failed: " + err);
    return { success: false, message: err.toString() };
  }
}

/**
 * Diagnostic: lists the models your API key can actually call generateContent on.
 * Run from the editor, then check View -> Logs. Use any listed name as GEMINI_MODEL.
 */
function listGeminiModels() {
  try {
    var key = getGeminiApiKey_();
    if (!key) { Logger.log("No Gemini API key stored."); return "No key."; }
    var res = UrlFetchApp.fetch(
      "https://generativelanguage.googleapis.com/v1beta/models",
      { headers: { "x-goog-api-key": key }, muteHttpExceptions: true });
    var data = {};
    try { data = JSON.parse(res.getContentText()); } catch (e) {}
    var names = (data.models || [])
      .filter(function (m) { return (m.supportedGenerationMethods || []).indexOf("generateContent") !== -1; })
      .map(function (m) { return m.name; });
    var msg = names.length ? ("Models supporting generateContent:\n" + names.join("\n"))
                           : ("No usable models found. Raw: " + res.getContentText().slice(0, 500));
    Logger.log(msg);
    try { SpreadsheetApp.getUi().alert(msg); } catch (e) {}
    return msg;
  } catch (err) {
    Logger.log("listGeminiModels failed: " + err);
    return err.toString();
  }
}

// SUPERVISOR PASSPHRASE (deployment-independent login).
// Change this to a strong secret before sharing the web app. The passphrase is
// only ever checked on the server; it is never sent to the client.
var SUPERVISOR_PASSPHRASE = "LASU-Arowolo-2026";

/**
 * Verifies the supervisor passphrase server-side. Works regardless of how the
 * web app is deployed or which Google account is signed in.
 * Returns { success: true, supervisor: {...} } or { success: false, message }.
 */
function verifySupervisorLogin(passphrase) {
  try {
    var input = String(passphrase || "").trim();
    if (!input) {
      return { success: false, message: "Passphrase is required." };
    }
    if (input !== SUPERVISOR_PASSPHRASE) {
      return { success: false, message: "Incorrect supervisor passphrase." };
    }
    return {
      success: true,
      supervisor: {
        name: "Olasunkanmi Arowolo, PhD",
        email: AUTHORIZED_SUPERVISOR_EMAIL,
        department: "Journalism and Media Studies",
        institution: "Lagos State University (LASU)"
      }
    };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

// The required database tabs.
var REQUIRED_SHEETS = ["Students", "Meetings", "Proposals", "StudentMeetingLogs", "TopicHistory", "StageHistory", "Resources", "ResourceProgress"];

// Audit-trail columns for topic proposal lifecycle events.
var TOPIC_HISTORY_HEADERS = [
  "Timestamp", "Proposal ID", "Student Name", "Matric No", "Topic Title", "Action", "Note"
];

// The research lifecycle stages (single source of truth). 1-indexed by position.
// Adding/renaming/reordering a stage is a one-line change here (mirror in the HTML).
var STAGES = [
  "Topic / Subject / Interest Area, Ideation & Approval",
  "Identification of Research Gap",
  "Chapter 1 — Introduction",
  "Chapter 2 — Literature Review",
  "Chapter 3 — Methodology",
  "Chapter 4 — Data Analysis",
  "Chapter 5 — Conclusion & Recommendations",
  "Revision",
  "Formatting & Export as PDF",
  "Reflection, Review & Future Prospects (Career & Next Steps)",
  "Sign-off"
];
function stageName(n) {
  var i = parseInt(n, 10);
  return (i >= 1 && i <= STAGES.length) ? STAGES[i - 1] : STAGES[0];
}

// Audit-trail columns for research stage progression.
var STAGE_HISTORY_HEADERS = [
  "Timestamp", "Matric No", "Student Name", "Stage #", "Stage Name", "Action", "Note"
];

// Column headers for the detailed supervisory meeting log.
// Columns 0-20 are the formal log fields (student-owned + supervisor-owned);
// the last three (21-23) are evidence-tracking columns.
var STUDENT_LOG_HEADERS = [
  "Timestamp",                     // 0  (auto)
  "Student Name & Matric Number",  // 1  (auto)
  "Meeting Number",                // 2  student
  "Date",                          // 3  student
  "Mode",                          // 4  student
  "Duration",                      // 5  student
  "Previous Actions",              // 6  student
  "Progress Since Last Meeting",   // 7  student
  "Discussion Points",             // 8  student
  "Work Reviewed",                 // 9  student
  "Chapter / Section",             // 10 student
  "Feedback Summary",              // 11 supervisor
  "Areas Requiring Revision",      // 12 supervisor
  "Agreed Actions",                // 13 supervisor
  "Risks / Concerns Identified",   // 14 student
  "Support Required",              // 15 student
  "Next Meeting Proposed Date",    // 16 student
  "Next Meeting Focus Area",       // 17 student
  "Supervisor Signature",          // 18 supervisor
  "Student Signature",             // 19 student
  "Sign-off Date",                 // 20 supervisor
  "Log ID",                        // 21 tracking
  "Status",                        // 22 tracking
  "Supervisor Feedback"            // 23 tracking (short status note)
];

// Audit-trail columns for the learning-resource completion tracker.
// One row per (student, resource) interaction. A resource is APPROVED only
// after a supervisor has reviewed and accepted the student's submission.
var RESOURCE_PROGRESS_HEADERS = [
  "Timestamp",               // 0  (auto)
  "Matric No",               // 1  indexed
  "Student Name",            // 2
  "Resource Row Index",      // 3  1-based ref into the Resources sheet
  "Resource Title",          // 4  snapshot for readability
  "Status",                  // 5  INCOMPLETE / PENDING_APPROVAL / APPROVED
  "Points",                  // 6  snapshot of Resources.Points at approval
  "Submitted Date",          // 7  when the student marked it complete
  "Reviewed Date",           // 8  when the supervisor acted
  "Supervisor Comment"       // 9  feedback on approve/reject
];

// Valid resource-completion workflow statuses.
var RESOURCE_STATUS = {
  INCOMPLETE: "INCOMPLETE",
  PENDING_APPROVAL: "PENDING_APPROVAL",
  APPROVED: "APPROVED"
};

// Column index constants for ResourceProgress (0-based).
var RP_COL = {
  TIMESTAMP: 0, MATRIC: 1, STUDENT_NAME: 2, RESOURCE_ROW: 3,
  TITLE: 4, STATUS: 5, POINTS: 6, SUBMITTED: 7, REVIEWED: 8, COMMENT: 9
};

// Column index constants for the Resources sheet (0-based).
var RS_COL = {
  SECTION: 0, TYPE: 1, TITLE: 2, URL: 3, DESCRIPTION: 4,
  SORT: 5, MANDATORY: 6, STAGE: 7, POINTS: 8
};

// 1-based column index of the new Points column on the Students sheet.
// Students header: [ID, Name, Matric, Lastname, Topic, Phase, Progress,
// Last Meeting, Status, Email, Topic Approved Date, Stage, Stage Updated,
// Drive Folder URL, Points]  → Points is column 15.
var STUDENT_POINTS_COL = 15;

// Column index constants for StudentMeetingLogs (0-based).
var LOG_COL = {
  TIMESTAMP: 0, NAME_MATRIC: 1, MEETING_NUMBER: 2,
  FEEDBACK_SUMMARY: 11, AREAS_REVISION: 12, AGREED_ACTIONS: 13,
  SUPERVISOR_SIGNATURE: 18, SIGNOFF_DATE: 20,
  LOG_ID: 21, STATUS: 22, FEEDBACK: 23
};

var PROPOSAL_COMMENT_COL = 10;
var PROPOSAL_CONDITIONS_COL = 11;

// Learning Resources sheet — the single source of truth for the content shown
// on the "Learning Resources" student tab. Each row is one resource item.
//
// Added columns (additive, non-breaking):
//   Stage  — the 1-indexed research stage (1–11) this resource belongs to.
//            Resources for the student's current stage must be APPROVED before
//            the student can advance. (Stage 1 = topic ideation/approval, Stage 2
//            = research-gap prerequisites, etc. — mirrors STAGES[].)
//   Points — points awarded to the student when the supervisor APPROVES the
//            resource. Default 10 for mandatory, 5 for optional, 0 otherwise.
var RESOURCES_HEADERS = [
  "Section",        // 0  grouping: Current Assignment / Required Worksheets / Prerequisite Videos
  "Item Type",      // 1  video / worksheet / link
  "Title",          // 2  human-readable label
  "URL",            // 3  clickable target
  "Description",    // 4  short blurb
  "Sort Order",     // 5  numeric ordering within the section
  "Mandatory",      // 6  Yes / No / Optional
  "Stage",          // 7  1..STAGES.length — stage this resource gates / belongs to
  "Points"          // 8  integer points earned on supervisor APPROVAL
];

// Seed data mirrors the current hardcoded Learning Resources UI so an existing
// database keeps the same content after running setupPortalDatabase().
// Two trailing columns were added for the progress tracker:
//   [Stage] — a 1-indexed stage (1–11) that gates stage advancement.
//   [Points] — integer points awarded on supervisor APPROVAL.
// Stage mapping: Current Assignment → Stage 1 (topic ideation/approval work);
//                Worksheets + Prerequisite Videos → Stage 2 (research-gap work).
var RESOURCES_SEED_DATA = [
  ["Current Assignment", "video", "BSc Project Guide for Nigerian Students — Research Expectations Explained", "https://youtu.be/v26gZYpOvmQ", "Current assignment video 3.", 1, "Yes", 1, 10],
  ["Required Worksheets", "worksheet", "rg-worksheet.pdf", "http://olaarowolo.com/OAsis-AA", "Research gap worksheet (RG).", 1, "Yes", 2, 10],
  ["Required Worksheets", "worksheet", "rge-worksheet.pdf", "http://olaarowolo.com/OAsis-AA", "Research gap worksheet extended (RGE).", 2, "Yes", 2, 10],
  ["Prerequisite Videos", "video", "How to Email Your Project Supervisor Correctly — Student Guide for Academic Emails", "https://youtu.be/yZ5murFFSJs", "Video 1 — must be completed.", 1, "Yes", 2, 10],
  ["Prerequisite Videos", "video", "Undergraduate Dissertation Blueprint — 12 Week Roadmap, Supervision Strategy and AI Ethics", "https://youtu.be/9LfcXS4wVzQ", "Video 2 — must be completed.", 2, "Yes", 2, 10],
  ["Prerequisite Videos", "video", "How to Email Your Project Supervisor Correctly — Student Guide for Academic Emails (Lesson 1)", "https://youtu.be/yZ5murFFSJs?is=pOJ99TyootbwwcN6", "Lesson 1 — supplementary.", 3, "Optional", 2, 5]
];

// Valid meeting-log workflow statuses. A log is immutable EXCEPT when REJECTED
// (which lets the student rewrite/resubmit).
var LOG_STATUS = {
  PENDING: "PENDING",
  UNDER_REVIEW: "UNDER_REVIEW",
  APPROVED: "APPROVED",
  REJECTED: "REJECTED"
};

/**
 * Adds a "LASU Portal" menu to the spreadsheet UI so the supervisor can set up
 * or reset the database without opening the Apps Script editor.
 * Runs automatically when the bound spreadsheet is opened.
 */
function onOpen() {
  try {
    SpreadsheetApp.getUi()
      .createMenu("LASU Portal")
      .addItem("Set up EVERYTHING (db + triggers)", "setupEverything")
      .addSeparator()
      .addItem("Set up / repair database", "setupPortalDatabase")
      .addItem("Install / reset triggers", "setupTriggers")
      .addItem("List triggers", "listTriggers")
       .addItem("Reseed students only", "reseedStudents")
        .addItem("Reset all students to Stage 1", "resetAllStagesToOne")
        .addItem("Reset all resource progress", "resetAllResourceProgress")
        .addItem("Fix column number formats", "fixAllColumnFormats")
        .addItem("Create student Drive folders", "createStudentFolders")
      .addSeparator()
      .addItem("Send daily digest now", "dailySupervisorDigest")
      .addItem("Send test email", "sendTestEmail")
      .addItem("Set Gemini API key", "promptSetGeminiApiKey")
      .addItem("Check Gemini setup", "checkGeminiSetup")
      .addItem("Debug Gemini key (prefix only)", "debugGeminiKey")
      .addItem("List Gemini models", "listGeminiModels")
      .addSeparator()
      .addItem("Set Google Chat webhook", "promptSetChatWebhookUrl")
      .addItem("Post features announcement to Chat", "postAnnouncementToChat")
      .addToUi();
  } catch (err) {
    // getUi() is unavailable when not bound to a spreadsheet UI; ignore.
  }
}

/**
 * Populates (or fully rebuilds) the portal database.
 * - Ensures the database spreadsheet exists.
 * - Rewrites all three tabs (Students, Meetings, Proposals) with the correct
 *   column layout and the real seed data.
 *
 * Safe to run multiple times. This CLEARS existing tab contents and re-writes them,
 * so it resets any progress/status edits. Run it once after deploying, or whenever
 * the schema changes (e.g. the added "Lastname" column).
 *
 * Returns a short summary string (shown in an alert when run from the menu).
 */
function setupPortalDatabase() {
  var ss = getDbSpreadsheet();

  REQUIRED_SHEETS.forEach(function(sheetName) {
    var sheet = ss.getSheetByName(sheetName);
    if (!sheet) {
      sheet = ss.insertSheet(sheetName);
    } else {
      sheet.clear();
    }
    initSheetHeader(sheet, sheetName);
    // Bold + freeze the header row for readability.
    sheet.getRange(1, 1, 1, sheet.getLastColumn()).setFontWeight("bold");
    sheet.setFrozenRows(1);
    sheet.autoResizeColumns(1, sheet.getLastColumn());
  });

  var summary = "Database ready: " + getSeedStudents().length +
    " students seeded across " + REQUIRED_SHEETS.length + " sheets.";

  // Show a confirmation dialog if invoked from the Sheet UI.
  try {
    SpreadsheetApp.getUi().alert(summary);
  } catch (err) {
    // No UI available (e.g. run from the editor); ignore.
  }

  Logger.log(summary);
  return summary;
}

/* =========================================================================
 * TRIGGERS + SCHEDULED JOBS
 * ========================================================================= */

/**
 * Time-driven job: emails the supervisor a summary of items awaiting action.
 * Runs daily (installed by setupTriggers). Skips sending if nothing is pending.
 */
function dailySupervisorDigest() {
  try {
    var pendingProposals = getProposalsList().length; // getProposalsList returns only PENDING
    var logs = getAllMeetingLogs();
    var pendingLogs = logs.filter(function (l) {
      return l.status === "PENDING" || l.status === "UNDER_REVIEW";
    }).length;
    var totalStudents = getStudentsList().length;

    // Resources awaiting supervisor review (PENDING_APPROVAL).
    var pendingResources = getPendingResourceApprovalsCount();

    // Nothing to nag about -> don't send.
    if (pendingProposals === 0 && pendingLogs === 0 && pendingResources === 0) {
      Logger.log("dailySupervisorDigest: nothing pending; no email sent.");
      return "Nothing pending.";
    }

    sendEmailView(AUTHORIZED_SUPERVISOR_EMAIL, "digest", {
      pendingProposals: pendingProposals,
      pendingLogs: pendingLogs,
      pendingResources: pendingResources,
      totalStudents: totalStudents,
      url: getWebAppUrl()
    });
    return "Digest sent: " + pendingProposals + " proposals, " + pendingLogs + " logs, " + pendingResources + " resources.";
  } catch (err) {
    Logger.log("dailySupervisorDigest failed: " + err);
    return "Digest failed: " + err;
  }
}

/**
 * Installs the project's triggers idempotently. Removes existing project
 * triggers first so re-running never creates duplicates, then installs:
 *   - onOpen (installable) so the "LASU Portal" menu always appears.
 *   - dailySupervisorDigest: time-based, once daily around 7am.
 * Requires the spreadsheet to be the bound container for onOpen to be useful.
 */
function setupTriggers() {
  var removed = 0;
  var existing = ScriptApp.getProjectTriggers();
  for (var i = 0; i < existing.length; i++) {
    ScriptApp.deleteTrigger(existing[i]);
    removed++;
  }

  var installed = [];

  // Menu on open (installable trigger, bound to the spreadsheet).
  try {
    var ss = getDbSpreadsheet();
    ScriptApp.newTrigger("onOpen").forSpreadsheet(ss).onOpen().create();
    installed.push("onOpen");
  } catch (e) {
    Logger.log("setupTriggers: onOpen trigger not created (" + e + ")");
  }

  // Daily digest around 07:00 in the script's timezone.
  ScriptApp.newTrigger("dailySupervisorDigest")
    .timeBased().everyDays(1).atHour(7).create();
  installed.push("dailySupervisorDigest (daily ~07:00)");

  var msg = "Triggers reset. Removed " + removed + ", installed: " + installed.join(", ") + ".";
  try { SpreadsheetApp.getUi().alert(msg); } catch (e) {}
  Logger.log(msg);
  return msg;
}

/**
 * One-shot setup: builds/repairs the database AND installs all triggers.
 * Run this once from the "LASU Portal" menu (or the editor) after deploying.
 */
function setupEverything() {
  var dbMsg = setupPortalDatabase();
  var trigMsg = setupTriggers();
  var msg = dbMsg + "\n" + trigMsg;
  try { SpreadsheetApp.getUi().alert("Setup complete:\n\n" + msg); } catch (e) {}
  return msg;
}

/**
 * Lists currently installed project triggers (handler + type) for verification.
 */
function listTriggers() {
  var trigs = ScriptApp.getProjectTriggers();
  var lines = trigs.map(function (t) {
    return t.getHandlerFunction() + " — " + t.getEventType();
  });
  var msg = trigs.length ? ("Installed triggers:\n" + lines.join("\n")) : "No triggers installed.";
  try { SpreadsheetApp.getUi().alert(msg); } catch (e) {}
  Logger.log(msg);
  return msg;
}

// Web App Request Handler
function doGet(e) {
  return HtmlService.createHtmlOutputFromFile('Index.html')
    .setTitle('Research Supervision Portal | Dr. Olasunkanmi Arowolo (LASU)')
    .setXFrameOptionsMode(HtmlService.XFrameOptionsMode.ALLOWALL)
    .addMetaTag('viewport', 'width=device-width, initial-scale=1.0');
}

/**
 * Checks active user authentication and supervisor privileges.
 * Enforces `isSupervisor: false` for all non-matching or empty email sessions.
 */
function getInitialData() {
  try {
    var ss = getDbSpreadsheet();
    var email = getCurrentUserEmail();
    var isSupervisor = isAuthorizedSupervisor(email);

    return {
      status: "connected",
      userEmail: email || "",
      isSupervisor: isSupervisor,
      supervisorName: "Olasunkanmi Arowolo, PhD",
      department: "Journalism and Media Studies",
      institution: "Lagos State University (LASU)",
      cohortDriveUrl: COHORT_DRIVE_URL,
      spreadsheetUrl: ss.getUrl()
    };
  } catch (err) {
    return { status: "error", message: err.toString() };
  }
}

/**
 * Resolves the current user's email as reliably as possible.
 * getActiveUser() can return "" depending on deployment/domain; fall back to
 * getEffectiveUser() (the identity the script runs as).
 */
function getCurrentUserEmail() {
  var email = "";
  try {
    email = Session.getActiveUser().getEmail() || "";
  } catch (e1) {
    email = "";
  }
  if (!email) {
    try {
      email = Session.getEffectiveUser().getEmail() || "";
    } catch (e2) {
      email = "";
    }
  }
  return email.trim().toLowerCase();
}

/**
 * Helper to ensure user is strictly authorized as Dr. Olasunkanmi Arowolo.
 * Throws an authorization error if the email does not match or is blank.
 */
function checkSupervisorAuthorization(passphrase) {
  // Authorized either by a recognized Google account OR the supervisor passphrase.
  // (The passphrase path is needed because a web app deployed with "Execute as: Me"
  //  cannot reliably identify individual visitors.)
  var email = getCurrentUserEmail();
  if (isAuthorizedSupervisor(email)) return;

  if (String(passphrase || "").trim() === SUPERVISOR_PASSPHRASE) return;

  throw new Error("Unauthorized: valid supervisor passphrase or authorized account required.");
}

// The canonical database spreadsheet ID (extracted from the supervisor's URL).
// This is the single source of truth: the portal always binds to THIS file, so
// running setup or the web app repeatedly never spawns duplicate spreadsheets.
// To point at a different file, paste its ID here (the long token between
// "/d/" and "/edit" in the Google Sheets URL).
var DB_SPREADSHEET_ID = "1SjroodZJ-LeYOYqjH0OEFKvMjByq95JU5ELFmNw_Juk";

// Script Property key that remembers a spreadsheet the script created itself,
// so a fallback-created DB is reused on every subsequent run (never recreated).
var DB_ID_PROP = "DB_SPREADSHEET_ID";

// Base name used when the script must create its own database spreadsheet.
var DB_NAME_BASE = "Dr. OA's LASU Supervision Portal DB";

/**
 * Gets (or, only as a last resort, creates) the ONE database spreadsheet and
 * ensures its required sheets exist.
 *
 * Lookup order — the first that works wins, and the resolved ID is cached in
 * Script Properties so it is reused on every future run:
 *   1. The bound/active spreadsheet (if the script is container-bound).
 *   2. The pinned DB_SPREADSHEET_ID constant.
 *   3. A previously created ID remembered in Script Properties.
 *   4. Create a NEW spreadsheet, timestamped in the name, and remember its ID.
 *
 * Steps 2–4 guarantee we never create a fresh spreadsheet on every run.
 */
function getDbSpreadsheet() {
  var ss = null;

  // 1. Container-bound spreadsheet, if any.
  try { ss = SpreadsheetApp.getActiveSpreadsheet(); } catch (e) { ss = null; }

  // 2. Pinned, known spreadsheet ID (preferred for the standalone web app).
  if (!ss && DB_SPREADSHEET_ID) {
    try {
      ss = SpreadsheetApp.openById(DB_SPREADSHEET_ID);
    } catch (e) {
      Logger.log("getDbSpreadsheet: pinned DB_SPREADSHEET_ID could not be opened (" + e + ").");
    }
  }

  var props = PropertiesService.getScriptProperties();

  // 3. An ID the script created and remembered on a previous run.
  if (!ss) {
    var savedId = props.getProperty(DB_ID_PROP);
    if (savedId) {
      try {
        ss = SpreadsheetApp.openById(savedId);
      } catch (e) {
        Logger.log("getDbSpreadsheet: saved DB id no longer opens (" + e + "); will recreate.");
        props.deleteProperty(DB_ID_PROP);
      }
    }
  }

  // 4. Last resort: create ONE spreadsheet, stamp its name with the academic
  //    session and a timestamp, and remember it so this branch never runs again.
  //    Wrapped in try/catch so a creation failure can never crash a live request.
  if (!ss) {
    try {
      var tz = Session.getScriptTimeZone();
      var stamp = Utilities.formatDate(new Date(), tz, "yyyy-MM-dd HH:mm");
      var session = getAcademicSession();
      ss = SpreadsheetApp.create(DB_NAME_BASE + " " + session + " (" + stamp + ")");
      props.setProperty(DB_ID_PROP, ss.getId());
      Logger.log("getDbSpreadsheet: created new DB '" + ss.getName() + "' (" + ss.getId() + ") and cached its ID.");
    } catch (e) {
      Logger.log("getDbSpreadsheet: could not create fallback DB (" + e + ").");
      throw new Error("Database spreadsheet is unavailable. Check DB_SPREADSHEET_ID access. Details: " + e);
    }
  }

  ensureSheetsExist(ss);
  return ss;
}

/**
 * Returns the current academic session as "YYYY/YYYY+1" (e.g. "2026/2027").
 * An academic session is assumed to start in September, so from September to
 * December the session is thisYear/nextYear, and from January to August it is
 * lastYear/thisYear. Change SESSION_START_MONTH if your session begins later.
 */
var SESSION_START_MONTH = 9; // September (1-based)
function getAcademicSession() {
  var now = new Date();
  var tz = Session.getScriptTimeZone();
  var year = parseInt(Utilities.formatDate(now, tz, "yyyy"), 10);
  var month = parseInt(Utilities.formatDate(now, tz, "M"), 10);
  var startYear = (month >= SESSION_START_MONTH) ? year : year - 1;
  return startYear + "/" + (startYear + 1);
}

/**
 * Ensures required tabs exist with Journalism/Media seed data, and non-
 * destructively repairs any missing header columns on sheets that already exist
 * (e.g. an older database created before "Supervisor Comment" was introduced).
 * This only ADDS missing header cells; it never clears, reorders, or deletes
 * existing data, so it is safe to run against a live spreadsheet.
 */
function ensureSheetsExist(ss) {
  REQUIRED_SHEETS.forEach(function(sheetName) {
    var sheet = ss.getSheetByName(sheetName);
    if (!sheet) {
      sheet = ss.insertSheet(sheetName);
      initSheetHeader(sheet, sheetName);
    } else {
      ensureSheetColumns(sheet, sheetName);
    }
  });
}

// The authoritative header row for each sheet. Used to detect and repair a
// sheet that is missing trailing columns (added in a later version). Kept in
// sync with initSheetHeader().
function expectedHeadersFor(sheetName) {
  switch (sheetName) {
    case "Proposals":
      return ["Timestamp", "Proposal ID", "Student Name", "Matric No",
              "Proposed Topic Title", "Location", "Date Submitted", "Abstract",
              "Status", "Supervisor Comment"];
    case "TopicHistory":
      return TOPIC_HISTORY_HEADERS;
    case "StageHistory":
      return STAGE_HISTORY_HEADERS;
    case "StudentMeetingLogs":
      return STUDENT_LOG_HEADERS;
    case "Resources":
      return RESOURCES_HEADERS;
    case "ResourceProgress":
      return RESOURCE_PROGRESS_HEADERS;
    default:
      return null; // no repair defined for this sheet
  }
}

/**
 * Non-destructively adds any missing trailing header cells to an existing sheet.
 * Only writes a header cell when the expected column header is missing/blank in
 * row 1; never touches data rows or existing headers.
 */
function ensureSheetColumns(sheet, sheetName) {
  try {
    var expected = expectedHeadersFor(sheetName);
    if (!expected || !expected.length) return;

    var lastCol = Math.max(sheet.getLastColumn(), 1);
    var current = sheet.getRange(1, 1, 1, Math.max(lastCol, expected.length)).getValues()[0];

    var changed = false;
    for (var c = 0; c < expected.length; c++) {
      var existing = String(current[c] == null ? "" : current[c]).trim();
      if (!existing) {
        sheet.getRange(1, c + 1).setValue(expected[c]);
        changed = true;
      }
    }
    if (changed) {
      sheet.getRange(1, 1, 1, expected.length).setFontWeight("bold");
      Logger.log("ensureSheetColumns: repaired header on '" + sheetName + "'.");
    }
  } catch (err) {
    Logger.log("ensureSheetColumns failed for " + sheetName + ": " + err);
  }
}

// Single source of truth for the student roster.
// Each row:
// [Student ID, Full Name, Matric No, Lastname, Email, Google Drive Folder URL]
//
// Student IDs start at STU-015.
// Email is populated where provided.
// Drive URL is populated where provided.
// Blank email = notification skipped gracefully.
// Blank Drive URL = no personal Drive folder configured yet.

function getSeedStudents() {
  return [
    ["STU-015", "ASOGBA AYOMIDE ITUNUOLUWA", "230910036", "ASOGBA", "ayomide.asogba230910036@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1k5lbtITLXHqot0_9WK7FZPegJ7-h2QQu?usp=drive_link"],

    ["STU-016", "AYANNIYI AYOOLA ABDULMALIK", "230910203", "AYANNIYI", "abdulmalik.ayanniyi230910203@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1PR8H4kVpVQcq-utr3cTa7MDrrv02ZkQm?usp=drive_link"],

    ["STU-017", "FASHANU TITILAYO DEBORAH", "230910064", "FASHANU", "titilayo.fashanu230910064@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1w_CP9l_m5ze3v2VeNyltpFH6rn_H5Dap?usp=drive_link"],

    ["STU-018", "OWOKONI MARVELOUS", "230910060", "OWOKONI", "", "https://drive.google.com/drive/folders/1xiMAD6sD3cciUwf6057wdZg9mKJ1oUkB?usp=drive_link"],

    ["STU-019", "OLALEYE FAITH AYOMIDE", "230910357", "OLALEYE", "faith.olaleye230910357@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1T1df91FiuvPdlm7uF9phdQl9G2crT_4a?usp=drive_link"],

    ["STU-020", "BENJAMIN JENNIFER OLUCHUKWU", "230910047", "BENJAMIN", "", "https://drive.google.com/drive/folders/1DECs2n7VS_4ZrurfWh9Qa2mDFkuE6Fgx?usp=drive_link"],

    ["STU-021", "OJIKUTU FEYISHAYO PATRICIA", "230910399", "OJIKUTU", "", "https://drive.google.com/drive/folders/1arGE267-DNiQSfHNBqGkG_rbc71RbUIg?usp=drive_link"],

    ["STU-022", "OTOMA ANWULIKA HAPPINESS", "24091049", "OTOMA", "anwulika.otoma240910459@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1b99KBpxpb9Cj9edb1wd5FiFpE14UFOgo?usp=drive_link"],

    ["STU-023", "AWOBAJO SOLIAT MORINSOLA", "230910366", "AWOBAJO", "soliat.awobajo230910366@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1anF3KZV-FA6h1KfuAKZMviGdCQR72sAP?usp=drive_link"],

    ["STU-024", "AJANI ESTHER TITILAYO", "230910020", "AJANI", "esther.ajani230910020@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1VIQfaTPzuhBAbf-h-FPXPYmfEBGsIK5L?usp=drive_link"],

    ["STU-025", "TAPERE OLUWAFERANMI AJOKE", "230910166", "TAPERE", "", "https://drive.google.com/drive/folders/16MwkwC_Jk0F-00--tj-yiCPVZokGebkh?usp=drive_link"],

    ["STU-026", "HASSAN HAFSAH OYINKANSOLA", "230910073", "HASSAN", "hafsah.hassan230910073@st.lasu.edu.ng", "https://drive.google.com/drive/folders/13BCPEflZSYuFnFAx322QY8V3Inzk6wA6?usp=drive_link"],

    ["STU-027", "ADENUGA ABDUL- WAHAB ADEDAYO", "230910008", "ADENUGA", "abdul-wahab.adenuga230910008@st.lasu.edu.ng", "https://drive.google.com/drive/folders/14SiKglrOh21hueCNdWqnaVAFV_pke3zb?usp=drive_link"],

    ["STU-028", "LAWAL IFEOLUWA VICTOR", "230910390", "LAWAL", "ifeoluwa.lawal230910223@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1CtFwoop12fCXFosn7OnjiFz2O5Vgscvz?usp=drive_link"],

    ["STU-029", "OLADEJO EMMANUELLA OLUWATOFUNMI", "230910239", "OLADEJO", "emmanuella.oladejo230910239@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1PvK5lc6ZbkPgWdGH8q0CS7jbDx6ndqgM?usp=drive_link"],

    ["STU-030", "ADETUNJI AMINAT OLAONIPEKUN", "230910362", "ADETUNJI", "aminat.adetunji230910362@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1WKiHJcBVgkUu5TCQnwIuLgobvpnznNn8?usp=drive_link"],

    ["STU-031", "FATAI OLUWATOBILOBA IYIOLUWA", "230910066", "FATAI", "oluwatobiloba.fatai230910066@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1PDJ74KJMu8B_jg6O3ixZ4Phm9pvd2wXw?usp=drive_link"],

    ["STU-032", "ADEKUNLE YEWANDE AMANDA", "240910471", "ADEKUNLE", "yewande.adekunle240910471@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1bFOEXGqO0X7h0azHJTXWKjZhzCblJOTF?usp=drive_link"],

    ["STU-033", "OLAMUYIWA GIDEON TEMITOPE", "230910283", "OLAMUYIWA", "gideon.olamuyiwa230910283@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1OuDyAJjaFTzMiF1xbZaG4K917tEoG0Is?usp=drive_link"],

    ["STU-034", "AGBAJE EBUNOLUWA ABIGEAL", "230910016", "AGBAJE", "ebunoluwa.agbaje230910016@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1njeXWH0w_R33ML483HRl4vlGccUnyhsf?usp=drive_link"],

    ["STU-035", "SALIU HALIMA OPEMIPO", "230910152", "SALIU", "halima.saliu230910152@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1iZAvEDq5OEWe83rsPc_BUjvaRUpwOyyy?usp=drive_link"],

    ["STU-036", "OLABISI FOLASHADE VICTORIA", "230910124", "OLABISI", "Folashade.olabisi230910124@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1SrQioGrGVb2Mrnx2_i80Cy3A5JzefPrP?usp=drive_link"],

    ["STU-037", "KAPPO OLAMILEKAN SEJORO", "230910093", "KAPPO", "olamilekan.kappo230910093@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1pZ24I807lM80ZpkYHxPtofBlUgaaVszo?usp=drive_link"],

    ["STU-038", "ODETAYO DAVID DOMINION", "230910232", "ODETAYO", "david.odetayo230910232@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1vFVXKDcNTC-gf4OLi19d4CzyoOuyb876?usp=drive_link"],

    ["STU-039", "ONAARA OLUWAFAYOKEMI OLUWAFERANMI", "230910135", "ONAARA", "oluwafayokemi.onaara230910135@st.lasu.edu.ng", "https://drive.google.com/drive/folders/1KJMABww5p49W8bIrVO0rU5dZd3DVZ7Es?usp=drive_link"],

    // Test account:
    // Login with matric 100910031 + surname AROWOLO.
    // 6th slot is the student's personal Google Drive folder URL.
    [
      "STU-TEST",
      "AROWOLO OLASUNKANMI TEST",
      "100910031",
      "AROWOLO",
      "olasunkanmiarowolo@gmail.com",
      "https://drive.google.com/drive/folders/1Gaker2qc5qV3ILsekhybECqrKiMaJ3yr?usp=drive_link"
    ]
  ];
}


// Shared cohort Google Drive folder (same for all students). This is the PARENT
// under which each student's personal folder is created.
var COHORT_DRIVE_URL = "https://drive.google.com/drive/folders/1ItRG74m4U82e8b_mkXJ2X4WVWrsxWyFH?usp=drive_link";

// Template folder whose sub-folder structure is replicated into every student's
// personal folder (this is the test student's folder: "100910031 - AROWOLO ...").
var TEMPLATE_DRIVE_URL = "https://drive.google.com/drive/folders/1T5Jn_CSB7crx1s9lZBXQBH4oDfuAr9yq?usp=drive_link";

/**
 * Extracts a Drive folder ID from a full folder URL (or returns the input if it
 * already looks like a bare ID).
 */
function extractDriveFolderId(urlOrId) {
  var s = String(urlOrId || "").trim();
  if (!s) return "";
  var m = s.match(/\/folders\/([a-zA-Z0-9_-]+)/);
  if (m) return m[1];
  m = s.match(/[?&]id=([a-zA-Z0-9_-]+)/);
  if (m) return m[1];
  return s; // assume it's already an ID
}

/**
 * Returns an existing child folder by name, or creates it if missing.
 * Keeps the operation idempotent so re-runs never duplicate folders.
 */
function getOrCreateChildFolder(parentFolder, name) {
  var existing = parentFolder.getFoldersByName(name);
  if (existing.hasNext()) return existing.next();
  return parentFolder.createFolder(name);
}

/**
 * Recursively replicates the sub-folder tree of `templateFolder` inside
 * `destFolder`. Only folders are mirrored (not files), and existing folders are
 * reused. This preserves nested sub-directories ("keep all sub dir").
 */
function replicateFolderTree(templateFolder, destFolder) {
  var subs = templateFolder.getFolders();
  while (subs.hasNext()) {
    var tSub = subs.next();
    var dSub = getOrCreateChildFolder(destFolder, tSub.getName());
    replicateFolderTree(tSub, dSub); // recurse to keep nested sub-dirs
  }
}

/**
 * Creates a personal Google Drive folder for every student under the cohort
 * parent folder, named "<Matric> - <Full Name>" (e.g. "100910031 - AROWOLO
 * OLASUNKANMI TEST"), and mirrors the template folder's sub-folder structure
 * into each. The resulting folder URL is written back to the Students sheet
 * (Drive Folder URL column). Safe to re-run: existing folders are reused.
 *
 * Run from: LASU Portal menu -> "Create student Drive folders".
 */
function createStudentFolders() {
  var parentId = extractDriveFolderId(COHORT_DRIVE_URL);
  var templateId = extractDriveFolderId(TEMPLATE_DRIVE_URL);

  if (!parentId) {
    throw new Error('COHORT_DRIVE_URL is empty or unparseable. Set it to the cohort folder link at the top of Code.gs.');
  }

  // Access the cohort (parent) folder. The most common failure here is a
  // permission/ID problem, which Drive reports with a cryptic
  // "No item with the given ID could be found" message. Wrap it so the user
  // gets an actionable explanation instead.
  var parent;
  try {
    parent = DriveApp.getFolderById(parentId);
  } catch (e) {
    var who = "";
    try { who = Session.getActiveUser().getEmail(); } catch (ignore) {}
    throw new Error(
      'Cannot open the cohort Drive folder (ID: ' + parentId + ').\n\n' +
      'This usually means one of the following:\n' +
      '  1. The folder is not shared with the account running this script' +
      (who ? ' (' + who + ')' : '') + '. Share it (Editor access) with that account.\n' +
      '  2. The folder ID in COHORT_DRIVE_URL is wrong, or the folder was moved to Trash / deleted.\n' +
      '  3. You authorized the script under a different Google account than the one that owns the folder.\n\n' +
      'Fix the sharing/ID, then re-run "Create student Drive folders".\n\n' +
      'Original Drive error: ' + e.message
    );
  }

  var template = null;
  if (templateId) {
    try {
      template = DriveApp.getFolderById(templateId);
    } catch (e) {
      template = null; // template unreadable -> create student folders without sub-tree
      Logger.log('Template folder unreadable (ID: ' + templateId + '): ' + e.message +
        '. Proceeding without mirroring the sub-folder tree.');
    }
  }

  var ss = getDbSpreadsheet();
  var sheet = ss.getSheetByName("Students");
  if (!sheet) throw new Error('Students sheet not found. Run "Set up / repair database" first.');

  var values = sheet.getDataRange().getValues();
  var DRIVE_COL = 14; // 1-based column N ("Drive Folder URL"), index 13 in the row array
  var created = 0, reused = 0;

  for (var i = 1; i < values.length; i++) {
    var row = values[i];
    var matric = String(row[2] || "").trim();
    var fullName = String(row[1] || "").trim();
    if (!row[0] || !matric) continue;

    var folderName = matric + " - " + fullName;

    // Reuse an existing student folder with this name if present.
    var existing = parent.getFoldersByName(folderName);
    var studentFolder;
    if (existing.hasNext()) {
      studentFolder = existing.next();
      reused++;
    } else {
      studentFolder = parent.createFolder(folderName);
      created++;
    }

    // Mirror the template sub-folder tree (keeps all nested sub-dirs).
    if (template) {
      replicateFolderTree(template, studentFolder);
    }

    // Write the folder URL back to the sheet.
    sheet.getRange(i + 1, DRIVE_COL).setValue(studentFolder.getUrl());
  }

  var msg = "Student Drive folders ready: " + created + " created, " + reused +
    " reused. URLs written to the Students sheet.";
  try {
    SpreadsheetApp.getUi().alert(msg);
  } catch (e) { /* no UI context */ }
  Logger.log(msg);
  return msg;
}

/**
 * Rebuilds the Students sheet with the current 9-column layout and the real roster.
 * Run this ONCE from the Apps Script editor if the sheet was created before the
 * "Lastname" column was added (fixes "No student found" login errors).
 * NOTE: this clears the Students sheet and re-writes it. Progress/topic/status
 * columns are reset to defaults.
 */
function reseedStudents() {
  var ss = getDbSpreadsheet();
  var sheet = ss.getSheetByName("Students");
  if (!sheet) {
    sheet = ss.insertSheet("Students");
  } else {
    sheet.clear();
  }
  initSheetHeader(sheet, "Students");
  return "Students sheet reseeded with " + getSeedStudents().length + " students.";
}

function initSheetHeader(sheet, sheetName) {
  if (sheetName === "Students") {
     sheet.appendRow(["Student ID", "Full Name", "Matric No", "Lastname", "Research Topic", "Phase", "Progress (%)", "Last Meeting", "Status", "Email", "Topic Approved Date", "Stage", "Stage Updated Date", "Drive Folder URL", "Points"]);
     getSeedStudents().forEach(function(s) {
       // s[4] (email) and s[5] (Drive folder URL) are optional.
       // Everyone starts at Stage 1 (topic ideation & approval) with 0 points.
       sheet.appendRow([s[0], s[1], s[2], s[3], "Topic pending approval", "Chapter 1 (Introduction)", 0, "N/A", "Active", s[4] || "", "", 1, "", s[5] || "", 0]);
     });
    applyStudentColumnFormats(sheet);
  } else if (sheetName === "Meetings") {
    sheet.appendRow(["Timestamp", "Meeting ID", "Student ID", "Meeting #", "Date", "Mode", "Work Reviewed", "Notes", "Status"]);
    sheet.appendRow([new Date(), "MTG-005", "STU-101", "#5", "27 Mar 2026", "Google Meet", "Chapter 1-3 Preliminary Draft", "Methodology approved. Ensure content analysis codebook is finalized.", "Approved / Signed"]);
    sheet.appendRow([new Date(), "MTG-006", "STU-101", "#6", "04 Jun 2026", "Email Review", "Media Coding Instrument", "Include framing variables for election news reporting.", "Approved / Signed"]);
  } else if (sheetName === "Proposals") {
    sheet.appendRow(["Timestamp", "Proposal ID", "Student Name", "Matric No", "Proposed Topic Title", "Location", "Date Submitted", "Abstract", "Status", "Supervisor Comment"]);
    sheet.appendRow([new Date(), "PROP-01", "OGUNLEYE OLUMIDE", "2021/48980", "AI-Generated Content and Media Literacy among LASU Undergraduates", "Lagos State University Main Campus", "02 Sep 2026", "Study evaluates student capacity to discern deepfakes and AI news content.", "PENDING", ""]);
    sheet.appendRow([new Date(), "PROP-02", "SULAIMON FATIMA", "2021/48991", "Gender Representation in Commercial Radio Advertising", "Ikeja Media Hubs", "05 Sep 2026", "A 6-month content analysis of prime-time radio commercials in Lagos.", "PENDING", ""]);
  } else if (sheetName === "StudentMeetingLogs") {
    sheet.appendRow(STUDENT_LOG_HEADERS);
  } else if (sheetName === "TopicHistory") {
    sheet.appendRow(TOPIC_HISTORY_HEADERS);
  } else if (sheetName === "StageHistory") {
    sheet.appendRow(STAGE_HISTORY_HEADERS);
  } else if (sheetName === "Resources") {
    sheet.appendRow(RESOURCES_HEADERS);
    RESOURCES_SEED_DATA.forEach(function(r) { sheet.appendRow(r); });
  } else if (sheetName === "ResourceProgress") {
    sheet.appendRow(RESOURCE_PROGRESS_HEADERS);
  }
}

/**
 * One-click fix: applies number formats to all existing columns in the Students
 * sheet. Run from the "LASU Portal" menu → "Fix column number formats" to repair
 * the "30 Dec 1899" display issue on databases that were seeded before the
 * format fix was added.
 */
function fixAllColumnFormats() {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Students");
    if (!sheet) {
      SpreadsheetApp.getUi().alert("No 'Students' sheet found.");
      return;
    }
    applyStudentColumnFormats(sheet);
    SpreadsheetApp.getUi().alert("Column number formats applied to the Students sheet. Refresh the sheet to see corrected values.");
  } catch (err) {
    SpreadsheetApp.getUi().alert("Fix failed: " + err);
  }
}

/**
 * Applies explicit number formats to the Students sheet columns to prevent
 * Google Sheets from interpreting numeric 0 as the date "30 Dec 1899" (date
 * serial 0).  Call this after creating or reseeding the Students sheet, and
 * whenever column values are updated programmatically.
 *
 * Column map (1-indexed):
 *   6  Progress (%)      → Number  (prevents 0 → 30 Dec 1899)
 *   7  Last Meeting      → Text    (values are pre-formatted strings like "N/A" or "15 Mar 2025")
 *   10 Topic Approved Date → Text
 *   11 Stage             → Number
 *   12 Stage Updated Date → Text
 *   14 Points            → Number
 */
function applyStudentColumnFormats(sheet) {
  if (!sheet) return;
  var lastRow = Math.max(sheet.getLastRow(), 2);
  var dataRows = lastRow - 1;
  if (dataRows < 1) return;

  // Column map (1-indexed).  Numeric 0 in a Date-formatted cell renders as
  // "30 Dec 1899" (date serial 0) — these setNumberFormats calls prevent that.
  sheet.getRange(2, 6,  dataRows, 1).setNumberFormat('0');      // Progress (%) → Number
  sheet.getRange(2, 7,  dataRows, 1).setNumberFormat('@');      // Last Meeting → Text
  sheet.getRange(2, 10, dataRows, 1).setNumberFormat('@');      // Topic Approved Date → Text
  sheet.getRange(2, 11, dataRows, 1).setNumberFormat('0');      // Stage → Number
  sheet.getRange(2, 12, dataRows, 1).setNumberFormat('@');      // Stage Updated Date → Text
  sheet.getRange(2, 14, dataRows, 1).setNumberFormat('0');      // Points → Number
}

/**
 * Creates the sheet if missing.
 */
function recordTopicHistory(propId, studentName, matric, topicTitle, action, note) {
  try {
    var ss = getDbSpreadsheet();
    var sheet = ss.getSheetByName("TopicHistory");
    if (!sheet) {
      sheet = ss.insertSheet("TopicHistory");
      sheet.appendRow(TOPIC_HISTORY_HEADERS);
    }
    sheet.appendRow([
      new Date(),
      propId || "",
      studentName || "",
      matric || "",
      topicTitle || "",
      action || "",
      note || ""
    ]);
  } catch (err) {
    Logger.log("recordTopicHistory failed: " + err);
  }
}

/**
 * Returns the topic history rows for a given matric (newest first), or all rows
 * if no matric is provided.
 */
function getTopicHistory(matric) {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("TopicHistory");
    if (!sheet) return [];
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];
    var wanted = String(matric || "").trim().toLowerCase();
    var out = [];
    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      if (wanted && String(row[3]).trim().toLowerCase() !== wanted) continue;
      out.push({
        timestamp: row[0] ? new Date(row[0]).toISOString() : "",
        proposalId: String(row[1] || ""),
        studentName: String(row[2] || ""),
        matric: String(row[3] || ""),
        topic: String(row[4] || ""),
        action: String(row[5] || ""),
        note: String(row[6] || "")
      });
    }
    return out.reverse();
  } catch (err) {
    Logger.log("getTopicHistory failed: " + err);
    return [];
  }
}

/**
 * Saves a detailed meeting log submitted by a student.
 * The caller must supply matric + lastname (surname) which are re-verified
 * server-side, so only a real student can write to their own record.
 * Timestamp and "Student Name & Matric Number" are set from the verified record.
 */
function submitStudentMeetingLog(data) {
  try {
    data = data || {};

    // Re-verify the student identity server-side.
    var auth = verifyStudentLogin(data.matric, data.lastname);
    if (!auth.success) {
      return { success: false, message: auth.message || "Student verification failed." };
    }
    var student = auth.student;

    var sheet = getDbSpreadsheet().getSheetByName("StudentMeetingLogs");
    if (!sheet) {
      sheet = getDbSpreadsheet().insertSheet("StudentMeetingLogs");
      sheet.appendRow(STUDENT_LOG_HEADERS);
    }

    var nameAndMatric = student.name + " (" + student.matric + ")";

    // The Meeting Number is assigned by the server (not user-entered):
    //  - If this is a rewrite of a REJECTED log (client passes its meeting number),
    //    reuse that same number.
    //  - Otherwise auto-assign the next sequential number for this student.
    var meetingNumber;
    var requested = String(data.meetingNumber || "").trim();
    if (requested) {
      var existing = findLatestLogForStudentMeeting(sheet, student.matric, requested);
      if (existing && existing.status !== LOG_STATUS.REJECTED) {
        return {
          success: false,
          message: "Meeting log " + requested + " already exists (" + existing.status +
                   ") and cannot be edited. Only a supervisor rewrite request (REJECTED) " +
                   "allows resubmission."
        };
      }
      // Valid rewrite of a rejected log: keep its number.
      meetingNumber = existing ? requested : nextMeetingNumberForStudent(sheet, student.matric);
    } else {
      meetingNumber = nextMeetingNumberForStudent(sheet, student.matric);
    }

    var logId = "LOG-" + Date.now();

    // Student-owned fields are written now; supervisor-owned fields
    // (Feedback Summary, Areas Requiring Revision, Agreed Actions, Supervisor
    // Signature, Sign-off Date) are left blank until the supervisor reviews.
    sheet.appendRow([
      new Date(),                    // 0  Timestamp
      nameAndMatric,                 // 1  Student Name & Matric
      meetingNumber,                 // 2  Meeting Number (server-assigned)
      data.meetingDate || "",        // 3  Date
      data.meetingMode || "",        // 4  Mode
      data.duration || "",           // 5  Duration
      data.previousActions || "",    // 6  Previous Actions
      data.progressSince || "",      // 7  Progress Since Last Meeting
      data.discussionPoints || "",   // 8  Discussion Points
      data.workReviewed || "",       // 9  Work Reviewed
      data.chapterFocus || "",       // 10 Chapter / Section
      "",                            // 11 Feedback Summary (supervisor)
      "",                            // 12 Areas Requiring Revision (supervisor)
      "",                            // 13 Agreed Actions (supervisor)
      data.risks || "",              // 14 Risks / Concerns
      data.supportRequired || "",    // 15 Support Required
      data.nextMeetingDate || "",    // 16 Next Meeting Proposed Date
      data.nextMeetingFocus || "",   // 17 Next Meeting Focus Area
      "",                            // 18 Supervisor Signature (supervisor)
      data.studentSignature || "",   // 19 Student Signature
      "",                            // 20 Sign-off Date (supervisor)
      logId,                         // 21 Log ID
      LOG_STATUS.PENDING,            // 22 Status
      ""                             // 23 Supervisor Feedback (status note)
    ]);

    // Keep the roster's "Last Meeting" column current for this student.
    if (data.meetingDate) {
      updateStudentLastMeeting(student.id, data.meetingDate);
    }

    // Notify the supervisor that a new log awaits review (reusable engine).
    notifyMeetingLogEvent("SUBMITTED", {
      studentName: student.name,
      studentEmail: student.email,
      meetingNumber: meetingNumber,
      logId: logId
    });

    return {
      success: true,
      message: "Meeting log submitted successfully!",
      log: {
        logId: logId,
        status: LOG_STATUS.PENDING,
        feedback: "",
        timestamp: new Date().toISOString(),
        studentNameMatric: nameAndMatric,
        meetingNumber: meetingNumber,
        meetingDate: data.meetingDate || "",
        meetingMode: data.meetingMode || "",
        duration: data.duration || "",
        previousActions: data.previousActions || "",
        progressSince: data.progressSince || "",
        discussionPoints: data.discussionPoints || "",
        workReviewed: data.workReviewed || "",
        chapterFocus: data.chapterFocus || "",
        feedbackSummary: "",
        areasRevision: "",
        agreedActions: "",
        risks: data.risks || "",
        supportRequired: data.supportRequired || "",
        nextMeetingDate: data.nextMeetingDate || "",
        nextMeetingFocus: data.nextMeetingFocus || "",
        supervisorSignature: "",
        studentSignature: data.studentSignature || "",
        signoffDate: ""
      }
    };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

/**
 * Computes the next sequential meeting number for a student.
 * Counts DISTINCT numeric meeting numbers already recorded for the student
 * (so a rejected+rewritten log doesn't inflate the count) and returns count + 1.
 */
function nextMeetingNumberForStudent(sheet, matric) {
  var values = sheet.getDataRange().getValues();
  var wantMatric = String(matric || "").trim().toLowerCase();
  var seen = {};
  for (var i = 1; i < values.length; i++) {
    var nameMatric = String(values[i][LOG_COL.NAME_MATRIC] || "").toLowerCase();
    if (nameMatric.indexOf(wantMatric) === -1) continue;
    var n = parseInt(String(values[i][LOG_COL.MEETING_NUMBER] || "").replace(/[^0-9]/g, ""), 10);
    if (!isNaN(n)) seen[n] = true;
  }
  var max = 0;
  Object.keys(seen).forEach(function(k) { var v = parseInt(k, 10); if (v > max) max = v; });
  return String(max + 1);
}

/**
 * Finds the most recent log row for a given student matric + meeting number.
 * Returns { rowIndex (1-based), status } or null.
 */
function findLatestLogForStudentMeeting(sheet, matric, meetingNumber) {
  var values = sheet.getDataRange().getValues();
  var wantMatric = String(matric || "").trim().toLowerCase();
  var wantMtg = String(meetingNumber || "").trim().toLowerCase();
  var found = null;
  for (var i = 1; i < values.length; i++) {
    var row = values[i];
    var nameMatric = String(row[LOG_COL.NAME_MATRIC] || "").toLowerCase();
    var mtg = String(row[2] || "").trim().toLowerCase();
    if (nameMatric.indexOf(wantMatric) !== -1 && mtg === wantMtg) {
      found = { rowIndex: i + 1, status: String(row[LOG_COL.STATUS] || LOG_STATUS.PENDING) };
    }
  }
  return found;
}

/**
 * Returns the detailed meeting logs for one student, matched by matric number
 * embedded in the "Student Name & Matric Number" column. Newest first.
 */
function getStudentMeetingLogs(matric) {
  try {
    var wanted = String(matric || "").trim().toLowerCase();
    if (!wanted) return [];

    var sheet = getDbSpreadsheet().getSheetByName("StudentMeetingLogs");
    if (!sheet) return [];

    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];

    var logs = [];
    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      var nameMatric = String(row[1] || "").toLowerCase();
      if (nameMatric.indexOf(wanted) === -1) continue;

      logs.push(rowToLogObject(row));
    }
    return logs.reverse();
  } catch (err) {
    Logger.log("Error getting student meeting logs: " + err);
    return [];
  }
}

// Maps a StudentMeetingLogs row array to a log object (single source of mapping).
function rowToLogObject(row) {
  return {
    timestamp: row[0] ? new Date(row[0]).toISOString() : "",
    studentNameMatric: String(row[1] || ""),
    meetingNumber: String(row[2] || ""),
    meetingDate: String(row[3] || ""),
    meetingMode: String(row[4] || ""),
    duration: String(row[5] || ""),
    previousActions: String(row[6] || ""),
    progressSince: String(row[7] || ""),
    discussionPoints: String(row[8] || ""),
    workReviewed: String(row[9] || ""),
    chapterFocus: String(row[10] || ""),
    feedbackSummary: String(row[11] || ""),
    areasRevision: String(row[12] || ""),
    agreedActions: String(row[13] || ""),
    risks: String(row[14] || ""),
    supportRequired: String(row[15] || ""),
    nextMeetingDate: String(row[16] || ""),
    nextMeetingFocus: String(row[17] || ""),
    supervisorSignature: String(row[18] || ""),
    studentSignature: String(row[19] || ""),
    signoffDate: String(row[20] || ""),
    logId: String(row[LOG_COL.LOG_ID] || ""),
    status: String(row[LOG_COL.STATUS] || LOG_STATUS.PENDING),
    feedback: String(row[LOG_COL.FEEDBACK] || "")
  };
}

/**
 * Returns ALL student meeting logs (supervisor view). Newest first.
 */
function getAllMeetingLogs() {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("StudentMeetingLogs");
    if (!sheet) return [];
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];
    var logs = [];
    for (var i = 1; i < values.length; i++) {
      if (values[i][LOG_COL.LOG_ID]) logs.push(rowToLogObject(values[i]));
    }
    return logs.reverse();
  } catch (err) {
    Logger.log("Error getting all meeting logs: " + err);
    return [];
  }
}

/**
 * Supervisor-only: sets the status of a meeting log by Log ID and records
 * optional feedback, then emails the student for the new stage.
 * Requires a valid supervisor account OR passphrase (checkSupervisorAuthorization).
 *
 * @param {string} logId
 * @param {string} status  One of LOG_STATUS values
 * @param {string} feedback
 * @param {string} passphrase  (optional) supervisor passphrase
 */
function updateMeetingLogStatus(logId, status, feedback, passphrase, fields) {
  try {
    checkSupervisorAuthorization(passphrase); // account OR passphrase

    // Validate the requested status against the allowed set (dynamic/reusable).
    var allowed = [LOG_STATUS.PENDING, LOG_STATUS.UNDER_REVIEW, LOG_STATUS.APPROVED, LOG_STATUS.REJECTED];
    if (allowed.indexOf(status) === -1) {
      return { success: false, message: "Invalid status: " + status };
    }

    var sheet = getDbSpreadsheet().getSheetByName("StudentMeetingLogs");
    if (!sheet) return { success: false, message: "No meeting logs found." };

    fields = fields || {};

    var values = sheet.getDataRange().getValues();
    for (var i = 1; i < values.length; i++) {
      if (String(values[i][LOG_COL.LOG_ID]) === String(logId)) {
        var r = i + 1;
        sheet.getRange(r, LOG_COL.STATUS + 1).setValue(status);
        sheet.getRange(r, LOG_COL.FEEDBACK + 1).setValue(feedback || "");

        // Write supervisor-owned fields when provided (leave blank ones untouched).
        if (fields.feedbackSummary !== undefined) sheet.getRange(r, LOG_COL.FEEDBACK_SUMMARY + 1).setValue(fields.feedbackSummary || "");
        if (fields.areasRevision !== undefined) sheet.getRange(r, LOG_COL.AREAS_REVISION + 1).setValue(fields.areasRevision || "");
        if (fields.agreedActions !== undefined) sheet.getRange(r, LOG_COL.AGREED_ACTIONS + 1).setValue(fields.agreedActions || "");
        if (fields.supervisorSignature !== undefined) sheet.getRange(r, LOG_COL.SUPERVISOR_SIGNATURE + 1).setValue(fields.supervisorSignature || "");
        if (fields.signoffDate !== undefined) sheet.getRange(r, LOG_COL.SIGNOFF_DATE + 1).setValue(fields.signoffDate || "");

        // Resolve the student email for the notification.
        var nameMatric = String(values[i][LOG_COL.NAME_MATRIC] || "");
        var studentEmail = lookupStudentEmailByNameMatric(nameMatric);
        var studentName = nameMatric.replace(/\s*\(.*\)\s*$/, "");

        notifyMeetingLogEvent(status, {
          studentName: studentName,
          studentEmail: studentEmail,
          meetingNumber: String(values[i][LOG_COL.MEETING_NUMBER] || ""),
          logId: logId,
          feedback: feedback || ""
        });

        return { success: true, message: "Log " + logId + " set to " + status + "." };
      }
    }
    return { success: false, message: "Log " + logId + " not found." };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

// Looks up a student's email from the Students sheet by matching the matric
// embedded in the "Name (Matric)" string. Returns "" if not found.
function lookupStudentEmailByNameMatric(nameMatric) {
  try {
    var m = String(nameMatric).match(/\(([^)]+)\)\s*$/);
    if (!m) return "";
    var matric = m[1].trim().toLowerCase();
    var sheet = getDbSpreadsheet().getSheetByName("Students");
    var values = sheet.getDataRange().getValues();
    for (var i = 1; i < values.length; i++) {
      if (String(values[i][2]).trim().toLowerCase() === matric) {
        return String(values[i][9] || "");
      }
    }
    return "";
  } catch (err) {
    return "";
  }
}

/**
 * Returns a single student's current record by matric (fresh from the sheet),
 * or null if not found. Used by the student portal to refresh after a supervisor
 * action (topic approval, stage advance) without requiring re-login.
 */
function getStudentByMatric(matric) {
  var want = String(matric || "").trim().toLowerCase();
  if (!want) return null;
  var list = getStudentsList();
  for (var i = 0; i < list.length; i++) {
    if (String(list[i].matric).trim().toLowerCase() === want) return list[i];
  }
  return null;
}

function getStudentsList() {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Students");
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];

    var students = [];
    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      if (row[0]) {
        students.push({
          id: String(row[0]),
          name: String(row[1]),
          matric: String(row[2]),
          lastname: String(row[3]),
          topic: String(row[4]),
          phase: String(row[5]),
          progress: Number(row[6]) || 0,
          lastMeeting: String(row[7]),
          status: String(row[8]),
          email: String(row[9] || ""),
          topicApprovedDate: String(row[10] || ""),
          stage: Number(row[11]) || 1,
           stageUpdated: String(row[12] || ""),
           driveFolder: String(row[13] || ""),
           points: Number(row[14]) || 0
         });
       }
     }
     return students;
  } catch (err) {
    Logger.log("Error getting students: " + err);
    return [];
  }
}

/**
 * Authenticates a student using Matric Number + Lastname (surname).
 * Lastname match is case-insensitive; matric is compared as a trimmed string.
 * Returns the matching student object on success, or { success: false } otherwise.
 */
function verifyStudentLogin(matric, lastname) {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Students");
    var values = sheet.getDataRange().getValues();
    var inMatric = String(matric || "").trim().toLowerCase();
    var inLastname = String(lastname || "").trim().toLowerCase();

    if (!inMatric || !inLastname) {
      return { success: false, message: "Matric number and surname are required." };
    }

    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      var rowMatric = String(row[2]).trim().toLowerCase();

      // Prefer the dedicated Lastname column; if it's blank (older sheets that
      // predate the Lastname column), fall back to the first word of Full Name.
      var storedLastname = String(row[3] || "").trim().toLowerCase();
      var derivedLastname = String(row[1] || "").trim().toLowerCase().split(/\s+/)[0] || "";
      var matchesLastname = (storedLastname === inLastname) || (derivedLastname === inLastname);

      if (rowMatric === inMatric && matchesLastname) {
        return {
          success: true,
          student: {
            id: String(row[0]),
            name: String(row[1]),
            matric: String(row[2]),
            lastname: storedLastname ? String(row[3]) : (String(row[1]).trim().split(/\s+/)[0] || ""),
            topic: String(row[4]),
            phase: String(row[5]),
            progress: Number(row[6]) || 0,
            lastMeeting: String(row[7]),
            status: String(row[8]),
            email: String(row[9] || ""),
            topicApprovedDate: String(row[10] || ""),
            stage: Number(row[11]) || 1,
             stageUpdated: String(row[12] || ""),
             driveFolder: String(row[13] || ""),
             points: Number(row[14]) || 0
           }
         };
       }
     }

    return { success: false, message: "No student found with that matric number and surname." };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

function getMeetingsList() {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Meetings");
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];

    var meetings = [];
    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      if (row[1]) {
        meetings.push({
          id: String(row[1]),
          studentId: String(row[2]),
          meetingNum: String(row[3]),
          date: String(row[4]),
          mode: String(row[5]),
          workReviewed: String(row[6]),
          notes: String(row[7]),
          status: String(row[8])
        });
      }
    }
    return meetings.reverse();
  } catch (err) {
    Logger.log("Error getting meetings: " + err);
    return [];
  }
}

/**
 * Formats a "Date Submitted" cell for display.
 * - Real Date objects (e.g. Google Form timestamps) -> "dd MMM yyyy HH:mm".
 *   If the time is exactly midnight (a date-only value) the time is omitted.
 * - Anything else is returned as a trimmed string unchanged.
 */
function formatSubmittedDate(value) {
  if (value instanceof Date && !isNaN(value.getTime())) {
    var tz = Session.getScriptTimeZone();
    var hasTime = value.getHours() !== 0 || value.getMinutes() !== 0 || value.getSeconds() !== 0;
    return Utilities.formatDate(value, tz, hasTime ? "dd MMM yyyy HH:mm" : "dd MMM yyyy");
  }
  return String(value == null ? "" : value).trim();
}

function getProposalsList() {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Proposals");
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];

    var proposals = [];
    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      if (row[1] && (row[8] === "PENDING" || row[8] === "CONDITIONALLY_APPROVED")) {
        proposals.push({
          id: String(row[1]),
          studentName: String(row[2]),
          matric: String(row[3]),
          topic: String(row[4]),
          location: String(row[5]),
          // "Date Submitted" may be a real Date (e.g. from a Google Form) or a
          // pre-formatted string. Format Dates so the UI shows the real
          // date + time instead of a raw "... 00:00:00 GMT" string.
          date: formatSubmittedDate(row[6]),
          abstract: String(row[7]),
          status: String(row[8]),
          comment: String(row[9] || ""),
          conditions: String(row[10] || "")
        });
      }
    }

    return proposals;
  } catch (err) {
    Logger.log("Error getting proposals: " + err);
    return [];
  }
}

/**
 * Reads the Learning Resources sheet and returns a structured array for the UI.
 * Rows are grouped by Section and ordered by Sort Order, so the student portal
 * can render each section (Current Assignment, Worksheets, Prerequisites) in
 * the same order they appear in the spreadsheet.
 */
function getResources() {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Resources");
    if (!sheet) return { sections: [], count: 0 };
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return { sections: [], count: 0 };

    var grouped = {};
    var order = [];
    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      if (!row[2] && !row[3]) continue; // skip fully-blank rows
      var section = String(row[0] || "General").trim();
      if (!grouped[section]) { grouped[section] = []; order.push(section); }
      grouped[section].push({
        section: section,
        type: String(row[1] || "link").trim(),
        title: String(row[2] || "").trim(),
        url: String(row[3] || "").trim(),
        description: String(row[4] || "").trim(),
        sortOrder: parseInt(row[5], 10) || 0,
        mandatory: String(row[6] || "Optional").trim(),
        stage: parseInt(row[7], 10) || 1,
        points: parseInt(row[8], 10) || 0,
        // 1-based sheet row, so the supervisor editor can edit/delete it.
        rowIndex: i + 1
      });
    }

    order.forEach(function (s) {
      grouped[s].sort(function (a, b) { return a.sortOrder - b.sortOrder; });
    });

    return { sections: order, items: grouped, count: values.length - 1 };
  } catch (err) {
    Logger.log("Error getResources: " + err);
    return { sections: [], items: {}, count: 0 };
  }
}

/**
 * Supervisor-only: upserts a single resource row (matched by Title + Section).
 * Pass rowIndex=null to append a new row. Used by the Learning Resources editor.
 */
function updateResourceRow(data) {
  try {
    checkSupervisorAuthorization(data && data.passphrase);
    var sheet = getDbSpreadsheet().getSheetByName("Resources");
    if (!sheet) return { success: false, message: "Resources sheet not found. Run setup." };

    var section = String(data.section || "").trim();
    var type = String(data.type || "link").trim();
    var title = String(data.title || "").trim();
    var url = String(data.url || "").trim();
    var description = String(data.description || "").trim();
    var sortOrder = parseInt(data.sortOrder, 10) || 0;
    var mandatory = String(data.mandatory || "Optional").trim();
    var stage = parseInt(data.stage, 10) || 1;
    var points = parseInt(data.points, 10) || 0;

    if (stage < 1 || stage > STAGES.length) stage = 1;

    if (!title) return { success: false, message: "Title is required." };

    var values = sheet.getDataRange().getValues();
    var rowIndex = data.rowIndex ? parseInt(data.rowIndex, 10) : -1;
    if (rowIndex < 1) {
      // Match existing row by Section + Title (case-insensitive) for upsert.
      for (var i = 1; i < values.length; i++) {
        if (String(values[i][0]).toLowerCase() === section.toLowerCase() &&
            String(values[i][2]).toLowerCase() === title.toLowerCase()) {
          rowIndex = i + 1; // 1-based sheet row
          break;
        }
      }
    }

    var row = [section, type, title, url, description, sortOrder, mandatory, stage, points];
    if (rowIndex > 0) {
      sheet.getRange(rowIndex, 1, 1, row.length).setValues([row]);
    } else {
      sheet.appendRow(row);
    }

    return { success: true, rowIndex: rowIndex || sheet.getLastRow(), row: row };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

/**
 * Supervisor-only: deletes a resource row by 1-based sheet row index.
 */
function deleteResourceRow(data) {
  try {
    checkSupervisorAuthorization(data && data.passphrase);
    var sheet = getDbSpreadsheet().getSheetByName("Resources");
    if (!sheet) return { success: false, message: "Resources sheet not found." };
    var rowIndex = parseInt(data.rowIndex, 10);
    if (rowIndex < 2) return { success: false, message: "Cannot delete the header row." };
    sheet.deleteRow(rowIndex);
    return { success: true };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

/* =========================================================================
 * LEARNING-RESOURCE COMPLETION TRACKER
 * Students mark resources complete, the supervisor approves/rejects them, and
 * points are awarded. Stage advancement is gated by prerequisite completion.
 *
 * Data lives in the ResourceProgress sheet (one row per student+resource).
 * The Resources sheet carries the Stage + Points metadata.
 * ========================================================================= */

/**
 * Ensures the ResourceProgress sheet exists and returns it.
 */
function getResourceProgressSheet() {
  var ss = getDbSpreadsheet();
  var sheet = ss.getSheetByName("ResourceProgress");
  if (!sheet) {
    sheet = ss.insertSheet("ResourceProgress");
    sheet.appendRow(RESOURCE_PROGRESS_HEADERS);
  }
  return sheet;
}

/**
 * Returns the column-9 (matric index) lookup row for a resource's metadata,
 * or null. Internal helper shared by progress + gating logic.
 */
function getResourceRowMetadata(resourceRowIndex) {
  var sheet = getDbSpreadsheet().getSheetByName("Resources");
  if (!sheet) return null;
  var values = sheet.getDataRange().getValues();
  var r = parseInt(resourceRowIndex, 10);
  if (isNaN(r) || r < 2 || r > values.length) return null;
  var row = values[r - 1];
  return {
    section: String(row[0] || ""),
    type: String(row[1] || ""),
    title: String(row[2] || ""),
    url: String(row[3] || ""),
    stage: parseInt(row[7], 10) || 1,
    points: parseInt(row[8], 10) || 0
  };
}

/**
 * Student-submission: marks a resource as complete (status → PENDING_APPROVAL).
 * Re-verifies student identity server-side (matric + lastname) so only the real
 * student can submit for their own record. Notifies the supervisor by email.
 *
 * @param {Object} data  { matric, lastname, resourceRow }
 * @return {Object}       { success, message, status }
 */
function submitResourceCompletion(data) {
  try {
    data = data || {};

    // Re-verify the student identity server-side.
    var auth = verifyStudentLogin(data.matric, data.lastname);
    if (!auth.success) {
      return { success: false, message: auth.message || "Student verification failed." };
    }
    var student = auth.student;

    var resourceRow = parseInt(data.resourceRow, 10);
    var meta = getResourceRowMetadata(resourceRow);
    if (!meta) return { success: false, message: "Resource not found." };

    var sheet = getResourceProgressSheet();
    var values = sheet.getDataRange().getValues();
    var wantMatric = String(student.matric).trim().toLowerCase();

    // Upsert: find an existing progress row for this (matric, resourceRow).
    for (var i = 1; i < values.length; i++) {
      var rowMatric = String(values[i][1] || "").trim().toLowerCase();
      var rowRes = parseInt(values[i][3], 10);
      if (rowMatric === wantMatric && rowRes === resourceRow) {
        var existingStatus = String(values[i][5] || "").toUpperCase();
        // If already approved, allow re-submit only if rejected/incomplete.
        if (existingStatus === RESOURCE_STATUS.APPROVED) {
          return { success: false, message: "This resource has already been approved and cannot be resubmitted.", status: RESOURCE_STATUS.APPROVED };
        }
         var dateStr = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), "dd MMM yyyy HH:mm");
         // 1-based columns: 1=Timestamp, 6=Status, 7=Points, 8=Submitted Date
         sheet.getRange(i + 1, 1).setValue(new Date());
         sheet.getRange(i + 1, 6).setValue(RESOURCE_STATUS.PENDING_APPROVAL);
         sheet.getRange(i + 1, 7).setValue(meta.points);
         sheet.getRange(i + 1, 8).setValue(dateStr);

        notifyResourceEvent("SUBMITTED", {
          studentName: student.name, matric: student.matric,
          resourceTitle: meta.title, points: meta.points,
          submittedDate: dateStr
        });
        return { success: true, message: "Resource submitted for review.", status: RESOURCE_STATUS.PENDING_APPROVAL };
      }
    }

    // No existing row: create one.
    var submitDate = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), "dd MMM yyyy HH:mm");
    sheet.appendRow([
      new Date(),                       // Timestamp
      student.matric,                   // Matric No
      student.name,                     // Student Name
      resourceRow,                      // Resource Row Index
      meta.title,                       // Resource Title
      RESOURCE_STATUS.PENDING_APPROVAL, // Status
      meta.points,                      // Points (snapshot)
      submitDate,                       // Submitted Date
      "",                               // Reviewed Date
      ""                                // Supervisor Comment
    ]);

    notifyResourceEvent("SUBMITTED", {
      studentName: student.name, matric: student.matric,
      resourceTitle: meta.title, points: meta.points,
      submittedDate: submitDate
    });

    return { success: true, message: "Resource submitted for review.", status: RESOURCE_STATUS.PENDING_APPROVAL };
  } catch (err) {
    Logger.log("submitResourceCompletion failed: " + err);
    return { success: false, message: err.toString() };
  }
}

/**
 * Supervisor-only: approves or rejects a resource completion submission.
 * Approve → status APPROVED, student points incremented, student emailed.
 * Reject → status INCOMPLETE, student emailed with feedback.
 *
 * @param {Object} data  { rowId (1-based sheet row of ResourceProgress),
 *                         action: "approve"|"reject", feedback, passphrase }
 * @return {Object}      { success, message, status, pointsAwarded }
 */
function reviewResourceSubmission(data) {
  try {
    checkSupervisorAuthorization(data && data.passphrase);
    data = data || {};

    var sheet = getResourceProgressSheet();
    var values = sheet.getDataRange().getValues();
    var rowNum = parseInt(data.rowId, 10);
    if (isNaN(rowNum) || rowNum < 2) return { success: false, message: "Invalid submission reference." };

    if (rowNum > values.length) return { success: false, message: "Submission not found." };
    var row = rowNum - 1; // 0-based in values

    var currentStatus = String(values[row][RP_COL.STATUS] || "").toUpperCase();
    if (currentStatus !== RESOURCE_STATUS.PENDING_APPROVAL) {
      return { success: false, message: "This submission is no longer pending review (current: " + currentStatus + ").", status: currentStatus };
    }

    var matric = String(values[row][RP_COL.MATRIC] || "");
    var studentName = String(values[row][RP_COL.STUDENT_NAME] || "");
    var resourceTitle = String(values[row][RP_COL.TITLE] || "");
    var resourceRow = values[row][RP_COL.RESOURCE_ROW];
    var points = parseInt(values[row][RP_COL.POINTS] || 0, 10);
    // Fallback: if the stored points are 0, look up the resource metadata.
    // This handles rows created before the Points column was populated.
    if (!points) {
      var meta = getResourceRowMetadata(resourceRow);
      if (meta && meta.points) points = meta.points;
    }
    var feedback = String(data.feedback || "").trim();
    var reviewedDate = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), "dd MMM yyyy HH:mm");
    var studentEmail = lookupStudentEmailByMatric(matric);

    // 1-based columns: 6=Status, 9=Reviewed Date, 10=Supervisor Comment
    if (data.action === "approve") {
      sheet.getRange(rowNum, 6).setValue(RESOURCE_STATUS.APPROVED);
      sheet.getRange(rowNum, 9).setValue(reviewedDate);
      sheet.getRange(rowNum, 10).setValue(feedback || "");

      // Award points to the student.
      var awarded = addToStudentPoints(matric, points);

      // Notify the student (approved).
      if (studentEmail) {
        sendEmailView(studentEmail, "resourceApproved", {
          studentName: studentName, matric: matric,
          resourceTitle: resourceTitle, points: points,
          feedback: feedback, reviewedDate: reviewedDate,
          url: getWebAppUrl()
        });
      }
      return { success: true, message: "Resource approved. " + awarded + " point(s) awarded.", status: RESOURCE_STATUS.APPROVED, pointsAwarded: awarded };
    } else {
      // Reject → back to INCOMPLETE so the student can resubmit.
      sheet.getRange(rowNum, 6).setValue(RESOURCE_STATUS.INCOMPLETE);
      sheet.getRange(rowNum, 9).setValue(reviewedDate);
      sheet.getRange(rowNum, 10).setValue(feedback || "");

      if (studentEmail) {
        sendEmailView(studentEmail, "resourceRejected", {
          studentName: studentName, matric: matric,
          resourceTitle: resourceTitle, feedback: feedback,
          url: getWebAppUrl()
        });
      }
      return { success: true, message: "Resource rejected. Student notified to revise and resubmit.", status: RESOURCE_STATUS.INCOMPLETE, pointsAwarded: 0 };
    }
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

/**
 * Adds points to a student's running total in the Students sheet.
 * Returns the number of points actually added (0 if student not found).
 */
function addToStudentPoints(matric, points) {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Students");
    var values = sheet.getDataRange().getValues();
    var want = String(matric || "").trim().toLowerCase();
    var pts = Number(points) || 0;
    for (var i = 1; i < values.length; i++) {
      if (String(values[i][2]).trim().toLowerCase() === want) {
        var current = Number(values[i][14]) || 0; // Points is column 15 (index 14)
        var total = current + pts;
        sheet.getRange(i + 1, STUDENT_POINTS_COL).setValue(total);
        sheet.getRange(i + 1, STUDENT_POINTS_COL).setNumberFormat('0');
        return pts;
      }
    }
    return 0;
  } catch (err) {
    Logger.log("addToStudentPoints failed: " + err);
    return 0;
  }
}

/**
 * Returns a student's resource-progress rows as objects, newest first.
 */
function getResourceProgress(matric) {
  try {
    var want = String(matric || "").trim().toLowerCase();
    if (!want) return [];
    var sheet = getResourceProgressSheet();
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];

    var out = [];
    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      if (String(row[1] || "").trim().toLowerCase() !== want) continue;
      out.push(rowToProgressObject(row, i + 1));
    }
    return out.reverse();
  } catch (err) {
    Logger.log("getResourceProgress failed: " + err);
    return [];
  }
}

/**
 * Returns ALL resource-progress rows (supervisor view for the approval panel).
 * Newest first.
 */
function getResourceProgressAll() {
  try {
    var sheet = getResourceProgressSheet();
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];
    var out = [];
    for (var i = 1; i < values.length; i++) {
      if (values[i][RP_COL.MATRIC] || values[i][RP_COL.STATUS]) {
        out.push(rowToProgressObject(values[i], i + 1));
      }
    }
    return out.reverse();
  } catch (err) {
    Logger.log("getResourceProgressAll failed: " + err);
    return [];
  }
}

/**
 * Returns only the PENDING_APPROVAL resource submissions (supervisor review list).
 */
function getPendingResourceApprovals() {
  try {
    var sheet = getResourceProgressSheet();
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];
    var out = [];
    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      if (String(row[RP_COL.STATUS] || "").toUpperCase() === RESOURCE_STATUS.PENDING_APPROVAL) {
        out.push(rowToProgressObject(row, i + 1));
      }
    }
    return out.reverse();
  } catch (err) {
    Logger.log("getPendingResourceApprovals failed: " + err);
    return [];
  }
}

/**
 * Returns the count of PENDING_APPROVAL submissions (for the digest).
 */
function getPendingResourceApprovalsCount() {
  var list = getPendingResourceApprovals();
  return list.length;
}

/**
 * Maps a ResourceProgress row array to an object.
 * rowId is the 1-based sheet row (for supervisor review/upsert).
 */
function rowToProgressObject(row, rowId) {
  var resourceMeta = getResourceRowMetadata(row[RP_COL.RESOURCE_ROW]);
  return {
    rowId: rowId,
    matric: String(row[RP_COL.MATRIC] || ""),
    studentName: String(row[RP_COL.STUDENT_NAME] || ""),
    resourceRow: parseInt(row[RP_COL.RESOURCE_ROW] || 0, 10),
    resourceTitle: String(row[RP_COL.TITLE] || ""),
    resourceType: resourceMeta ? resourceMeta.type : "",
    resourceStage: resourceMeta ? resourceMeta.stage : 0,
    status: String(row[RP_COL.STATUS] || RESOURCE_STATUS.INCOMPLETE),
    points: Number(row[RP_COL.POINTS]) || 0,
    submittedDate: String(row[RP_COL.SUBMITTED] || ""),
    reviewedDate: String(row[RP_COL.REVIEWED] || ""),
    feedback: String(row[RP_COL.COMMENT] || ""),
    timestamp: row[RP_COL.TIMESTAMP] ? new Date(row[RP_COL.TIMESTAMP]).toISOString() : ""
  };
}

/**
 * Returns true if every resource belonging to the given stage has been APPROVED
 * for the student with the given matric. Used by the stage-advancement gate.
 */
function areStageResourcesComplete(matric, stage) {
  try {
    var want = String(matric || "").trim().toLowerCase();
    if (!want) return false;
    var resources = getResourcesByStage(stage);
    if (!resources.length) return true; // no resources → no prerequisites → OK

    var progress = getResourceProgress(matric);
    var approvedRows = {};
    progress.forEach(function (p) {
      if (p.status === RESOURCE_STATUS.APPROVED) approvedRows[p.resourceRow] = true;
    });

    return resources.every(function (r) {
      return !!approvedRows[r.rowIndex]; // every stage resource must be approved
    });
  } catch (err) {
    Logger.log("areStageResourcesComplete failed: " + err);
    return false;
  }
}

/**
 * Returns all Resources rows belonging to a given stage (1-indexed),
 * each as an object with the sheet rowIndex (1-based) for matching.
 */
function getResourcesByStage(stage) {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Resources");
    if (!sheet) return [];
    var values = sheet.getDataRange().getValues();
    var want = parseInt(stage, 10);
    if (isNaN(want) || want < 1) return [];
    var out = [];
    for (var i = 1; i < values.length; i++) {
      if (!values[i][2] && !values[i][3]) continue;
      if ((parseInt(values[i][7], 10) || 1) === want) {
        out.push({
          rowIndex: i + 1,
          stage: want,
          points: parseInt(values[i][8], 10) || 0,
          title: String(values[i][2] || "")
        });
      }
    }
    return out;
  } catch (err) {
    Logger.log("getResourcesByStage failed: " + err);
    return [];
  }
}

/**
 * Supervisor-facing helper: returns a stage's resources with per-student
 * completion status for a specific student. Useful for the approval panel UI.
 */
function getStageResourcesForStudent(matric, stage) {
  var resources = getResourcesByStage(stage);
  var progress = getResourceProgress(matric);
  var progressMap = {};
  progress.forEach(function (p) {
    progressMap[p.resourceRow] = p;
  });
  return resources.map(function (r) {
    var p = progressMap[r.rowIndex] || null;
    return {
      resourceRow: r.rowIndex,
      title: r.title,
      stage: r.stage,
      points: r.points,
      status: p ? p.status : RESOURCE_STATUS.INCOMPLETE,
      pointsAwarded: p && p.status === RESOURCE_STATUS.APPROVED ? r.points : 0,
      submittedDate: p ? p.submittedDate : "",
      feedback: p ? p.feedback : ""
    };
  });
}

/**
 * Resets ALL resource-progress rows for ALL students to empty.
 * One-time admin action (mirrors resetAllStagesToOne). Run from the menu.
 */
function resetAllResourceProgress() {
  try {
    var sheet = getResourceProgressSheet();
    var values = sheet.getDataRange().getValues();
    var count = Math.max(0, values.length - 1);
    if (count > 0) {
      // Keep the header; delete all data rows from bottom up so indices stay valid.
      for (var i = values.length; i >= 2; i--) {
        sheet.deleteRow(i);
      }
    }

    // Also reset student points to 0.
    var stuSheet = getDbSpreadsheet().getSheetByName("Students");
    if (stuSheet) {
      var stuValues = stuSheet.getDataRange().getValues();
      for (var j = 1; j < stuValues.length; j++) {
        if (stuValues[j][0]) stuSheet.getRange(j + 1, STUDENT_POINTS_COL).setValue(0);
      }
    }

    var msg = "Reset " + count + " resource-progress row(s) and student points.";
    try { SpreadsheetApp.getUi().alert(msg); } catch (e) {}
    Logger.log(msg);
    return msg;
  } catch (err) {
    return "resetAllResourceProgress failed: " + err;
  }
}

/**
 * Notifies the supervisor when a student submits a resource for review.
 * Reuses the branded email view layer (single source of truth).
 */
function notifyResourceEvent(event, ctx) {
  try {
    ctx = ctx || {};
    var to = AUTHORIZED_SUPERVISOR_EMAIL;

    return sendEmailView(to, "resourceSubmitted", {
      studentName: ctx.studentName || "Student",
      matric: ctx.matric || "",
      resourceTitle: ctx.resourceTitle || "",
      points: ctx.points || 0,
      submittedDate: ctx.submittedDate || "",
      url: getWebAppUrl()
    });
   } catch (err) {
    Logger.log("notifyResourceEvent failed (" + event + "): " + err);
    return false;
  }
}

function submitTopicProposal(data) {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Proposals");
    var propId = "PROP-" + Date.now();
    // Include the time-of-day so the portal shows the real submission moment,
    // not midnight. (A date-only string parses back to 00:00:00.)
    var dateStr = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), "dd MMM yyyy HH:mm");

    var studentName = data.studentName || "";
    var matric = data.matric || "";

    sheet.appendRow([
      new Date(),
      propId,
      studentName,
      matric,
      data.title,
      data.location || "LASU Campus",
      dateStr,
      data.abstract,
      "PENDING",
      ""
    ]);

    // Audit trail + notify the supervisor that a proposal awaits review.
    recordTopicHistory(propId, studentName, matric, data.title, "SUBMITTED", data.abstract || "");
    sendEmailView(AUTHORIZED_SUPERVISOR_EMAIL, "topicSubmitted", {
      studentName: studentName, matric: matric, topic: data.title,
      abstract: data.abstract, proposalId: propId, url: getWebAppUrl()
    });

    return {
      success: true,
      message: "Topic proposal logged successfully!",
      proposal: {
        id: propId,
        studentName: studentName,
        matric: matric,
        topic: data.title,
        location: data.location,
        date: dateStr,
        abstract: data.abstract,
        status: "PENDING"
      }
    };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

function logSupervisionMeeting(data) {
  try {
    checkSupervisorAuthorization(data && data.passphrase); // account OR passphrase
    var sheet = getDbSpreadsheet().getSheetByName("Meetings");
    var mtgId = "MTG-" + Date.now();
    var meetingNum = "#" + (sheet.getLastRow());

    sheet.appendRow([
      new Date(),
      mtgId,
      data.studentId,
      meetingNum,
      data.date,
      data.mode,
      data.workReviewed,
      data.notes || "",
      data.status
    ]);

    updateStudentLastMeeting(data.studentId, data.date);

    return {
      success: true,
      message: "Meeting log saved successfully!",
      meeting: {
        id: mtgId,
        meetingNum: meetingNum,
        date: data.date,
        mode: data.mode,
        workReviewed: data.workReviewed,
        notes: data.notes || "No extra notes.",
        status: data.status
      }
    };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

function updateProposalStatus(proposalId, action, passphrase, comment, conditions) {
  try {
    checkSupervisorAuthorization(passphrase); // account OR passphrase
    var sheet = getDbSpreadsheet().getSheetByName("Proposals");
    var values = sheet.getDataRange().getValues();
    var newStatus = action === 'approve' ? 'APPROVED' : (action === 'conditional_approve' ? 'CONDITIONALLY_APPROVED' : 'REVISION_REQUESTED');
    comment = String(comment || "").trim();
    conditions = String(conditions || "").trim();

    for (var i = 1; i < values.length; i++) {
      if (String(values[i][1]) === String(proposalId)) {
        var studentName = values[i][2];
        var matric = values[i][3];
        var topicTitle = values[i][4];

        sheet.getRange(i + 1, 9).setValue(newStatus);
        sheet.getRange(i + 1, PROPOSAL_COMMENT_COL).setValue(comment);
        sheet.getRange(i + 1, PROPOSAL_CONDITIONS_COL).setValue(conditions);

        var approvedDate = "";
        if (action === 'approve' || action === 'conditional_approve') {
          // Reflect the approved topic on the student's record + timestamp it.
          approvedDate = updateStudentTopic(studentName, topicTitle, matric);
          // Stage 1 (topic ideation & approval) is complete -> advance to Stage 2.
          setStudentStageDirect(matric, 2, "Auto-advanced on topic approval");
        }

        // Audit trail.
        var reviewNote = action === 'approve'
          ? ("Approved on " + approvedDate)
          : (action === 'conditional_approve'
            ? ("Conditionally approved on " + approvedDate)
            : "Revision requested");
        if (comment) reviewNote += " Comment: " + comment;
        if (conditions) reviewNote += " Conditions: " + conditions;
        recordTopicHistory(
          proposalId, studentName, matric, topicTitle,
          newStatus,
          reviewNote
        );

        // Notify the student for every review decision.
        // Resolve by matric first, then fall back to name (matric mismatches
        // are a common cause of "missing" emails).
        var lookup = resolveStudentEmail(matric, studentName);
        var studentEmail = lookup.email;
        var emailReason = lookup.reason;
        var emailSent = false;

        if (studentEmail && !emailReason) {
          if (action === 'approve') {
            emailSent = sendEmailView(studentEmail, "topicApproved", {
              studentName: studentName, topic: topicTitle,
              approvedDate: approvedDate, comment: comment, url: getWebAppUrl()
            });
          } else if (action === 'conditional_approve') {
            emailSent = sendEmailView(studentEmail, "topicConditionallyApproved", {
              studentName: studentName, topic: topicTitle,
              approvedDate: approvedDate, conditions: conditions, comment: comment, url: getWebAppUrl()
            });
          } else {
            emailSent = sendEmailView(studentEmail, "topicRevision", {
              studentName: studentName, topic: topicTitle,
              comment: comment, url: getWebAppUrl()
            });
          }
          // Email address was valid but the send still failed (auth/quota).
          if (!emailSent) emailReason = "send_failed";
        }

        // Make the real cause visible in the execution log for every decision.
        Logger.log(
          "updateProposalStatus: " + action + " for '" + studentName + "' (matric '" + matric + "') -> " +
          "email='" + studentEmail + "', sent=" + emailSent + ", reason='" + (emailReason || "ok") + "'"
        );

        return {
          success: true,
          message: "Proposal " + newStatus.toLowerCase() + " successfully!",
          approvedDate: approvedDate,
          emailSent: emailSent,
          emailReason: emailReason,
          emailMessage: describeEmailOutcome(emailReason, emailSent),
          studentEmail: studentEmail
        };
      }
    }

    return { success: false, message: "Proposal " + proposalId + " not found." };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

/**
 * Appends a stage-progression audit row. Creates the sheet if missing.
 */
function recordStageHistory(matric, studentName, stageNum, action, note) {
  try {
    var ss = getDbSpreadsheet();
    var sheet = ss.getSheetByName("StageHistory");
    if (!sheet) {
      sheet = ss.insertSheet("StageHistory");
      sheet.appendRow(STAGE_HISTORY_HEADERS);
    }
    sheet.appendRow([
      new Date(), matric || "", studentName || "",
      stageNum, stageName(stageNum), action || "", note || ""
    ]);
  } catch (err) {
    Logger.log("recordStageHistory failed: " + err);
  }
}

/**
 * Returns stage history for a matric (newest first), or all rows if no matric.
 */
function getStageHistory(matric) {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("StageHistory");
    if (!sheet) return [];
    var values = sheet.getDataRange().getValues();
    if (values.length <= 1) return [];
    var wanted = String(matric || "").trim().toLowerCase();
    var out = [];
    for (var i = 1; i < values.length; i++) {
      var row = values[i];
      if (wanted && String(row[1]).trim().toLowerCase() !== wanted) continue;
      out.push({
        timestamp: row[0] ? new Date(row[0]).toISOString() : "",
        matric: String(row[1] || ""),
        studentName: String(row[2] || ""),
        stage: Number(row[3]) || 0,
        stageName: String(row[4] || ""),
        action: String(row[5] || ""),
        note: String(row[6] || "")
      });
    }
    return out.reverse();
  } catch (err) {
    Logger.log("getStageHistory failed: " + err);
    return [];
  }
}

/**
 * Sets a student's research stage (supervisor-only). Updates the Stage +
 * Stage Updated Date columns, records history, and emails the student.
 * Also mirrors a readable "Phase" for backward-compatible displays.
 *
 * @param {string} matric
 * @param {number} stage        1..STAGES.length
 * @param {string} note         optional note included in the email/history
 * @param {string} passphrase   supervisor passphrase (or authorized account)
 */
function advanceStudentStage(matric, stage, note, passphrase) {
  try {
    checkSupervisorAuthorization(passphrase); // account OR passphrase

    var target = parseInt(stage, 10);
    if (isNaN(target) || target < 1 || target > STAGES.length) {
      return { success: false, message: "Invalid stage: " + stage };
    }

    var set = setStudentStageDirect(matric, target, note);
    if (!set) return { success: false, message: "Student " + matric + " not found." };
    if (set === -1) {
      return { success: false, message: "Cannot advance past Stage " + (target - 1) +
              ": not all resources for that stage have been approved yet. Complete the prerequisites first." };
    }

    return {
      success: true,
      message: "Stage set to " + target + " (" + stageName(target) + ").",
      stage: target,
      stageName: stageName(target)
    };
  } catch (err) {
    return { success: false, message: err.toString() };
  }
}

/**
 * Resets EVERY student to Stage 1 (topic ideation & approval). One-time admin
 * action, runnable from the "LASU Portal" menu or the editor. Records a history
 * row per student. Does not send emails (bulk reset would spam inboxes).
 */
function resetAllStagesToOne() {
  try {
    var sheet = getDbSpreadsheet().getSheetByName("Students");
    if (!sheet) return "No Students sheet found.";
    var values = sheet.getDataRange().getValues();
    var updatedOn = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), "dd MMM yyyy HH:mm");
    var count = 0;

    for (var i = 1; i < values.length; i++) {
      if (!values[i][0]) continue; // skip blank rows
      // 1-indexed: 12 = Stage, 13 = Stage Updated Date, 6 = Phase (mirror)
      sheet.getRange(i + 1, 12).setValue(1);
      sheet.getRange(i + 1, 13).setValue(updatedOn);
      sheet.getRange(i + 1, 6).setValue(stageName(1));
      recordStageHistory(values[i][2], values[i][1], 1, "RESET", "Bulk reset to Stage 1");
      count++;
    }

    var msg = "Reset " + count + " student(s) to Stage 1.";
    try { SpreadsheetApp.getUi().alert(msg); } catch (e) {}
    Logger.log(msg);
    return msg;
  } catch (err) {
    return "resetAllStagesToOne failed: " + err;
  }
}

/**
 * Internal stage setter (no auth check) for use by already-authorized flows
 * such as topic approval. Updates Stage + timestamp + Phase mirror, records
 * history, and emails the student. Returns the stage or 0 on failure.
 */
function setStudentStageDirect(matric, stage, note) {
  try {
    var target = parseInt(stage, 10);
    if (isNaN(target) || target < 1 || target > STAGES.length) return 0;

    var sheet = getDbSpreadsheet().getSheetByName("Students");
    var values = sheet.getDataRange().getValues();
    var want = String(matric || "").trim().toLowerCase();
     var updatedOn = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), "dd MMM yyyy HH:mm");

     // Prerequisite gate: a student may not leave their current stage until every
     // resource belonging to that stage has been APPROVED. (Stage 1 = topic
     // ideation/approval; the topic-approval auto-advance into Stage 2 is the
     // only exception, since Stage 1 resources are typically submitted during
     // Topic History, not resource-completion.)
     var priorStage = (target > 1) ? target - 1 : 0;
     if (priorStage >= 1 && !areStageResourcesComplete(matric, priorStage)) {
       Logger.log("setStudentStageDirect: prerequisite gate blocked — stage " + priorStage +
                  " resources not all approved for " + matric);
       return -1; // signal: blocked by prerequisites
     }

     for (var i = 1; i < values.length; i++) {
       if (String(values[i][2]).trim().toLowerCase() === want) {
         var studentName = String(values[i][1] || "");
         var studentEmail = String(values[i][9] || "");
         sheet.getRange(i + 1, 12).setValue(target);
        sheet.getRange(i + 1, 13).setValue(updatedOn);
        sheet.getRange(i + 1, 6).setValue(stageName(target));
        sheet.getRange(i + 1, 7).setNumberFormat('0');
        sheet.getRange(i + 1, 8).setNumberFormat('@');
        sheet.getRange(i + 1, 12).setNumberFormat('0');
        sheet.getRange(i + 1, 13).setNumberFormat('@');
        recordStageHistory(matric, studentName, target, "ADVANCED", note || "");
        sendEmailView(studentEmail, "stageAdvanced", {
          studentName: studentName, stage: target, stageName: stageName(target),
          total: STAGES.length, note: note || "", url: getWebAppUrl()
        });
        return target;
      }
    }
    return 0;
  } catch (err) {
    Logger.log("setStudentStageDirect failed: " + err);
    return 0;
  }
}

// Resolve a student's email directly by matric from the Students sheet.
function lookupStudentEmailByMatric(matric) {
  return resolveStudentEmail(matric, "").email;
}

/**
 * Resolves a student's email from the Students sheet.
 * Matches by matric (column C / index 2) first, then falls back to full name
 * (column B / index 1) so a matric formatting mismatch still finds the email.
 *
 * Returns a diagnostic object so callers can tell WHY an email is unavailable:
 *   { email, matched, reason }
 *   - email:   the resolved address ("" if none)
 *   - matched: true if a student row was found (by matric or name)
 *   - reason:  "" on success, or one of:
 *              "no_matric"        no matric/name supplied
 *              "student_not_found" no matching row in the Students sheet
 *              "email_blank"      row found but the Email cell (col J) is empty
 *              "email_invalid"    row found but the Email value has no "@"
 */
function resolveStudentEmail(matric, studentName) {
  try {
    var wantMatric = String(matric || "").trim().toLowerCase();
    var wantName = String(studentName || "").trim().toLowerCase();
    if (!wantMatric && !wantName) {
      return { email: "", matched: false, reason: "no_matric" };
    }

    var sheet = getDbSpreadsheet().getSheetByName("Students");
    var values = sheet.getDataRange().getValues();

    var row = null;
    // Pass 1: match by matric (most reliable).
    if (wantMatric) {
      for (var i = 1; i < values.length; i++) {
        if (String(values[i][2]).trim().toLowerCase() === wantMatric) { row = values[i]; break; }
      }
    }
    // Pass 2: fall back to full-name match if matric didn't resolve.
    if (!row && wantName) {
      for (var j = 1; j < values.length; j++) {
        if (String(values[j][1]).trim().toLowerCase() === wantName) { row = values[j]; break; }
      }
    }

    if (!row) return { email: "", matched: false, reason: "student_not_found" };

    var email = String(row[9] || "").trim();
    if (!email) return { email: "", matched: true, reason: "email_blank" };
    if (email.indexOf("@") === -1) return { email: email, matched: true, reason: "email_invalid" };

    return { email: email, matched: true, reason: "" };
  } catch (err) {
    Logger.log("resolveStudentEmail failed: " + err);
    return { email: "", matched: false, reason: "student_not_found" };
  }
}

/**
 * Maps a resolveStudentEmail() outcome + send result into a human-readable
 * message for the UI toast.
 */
function describeEmailOutcome(reason, emailSent) {
  if (emailSent) return "Student emailed.";
  switch (reason) {
    case "no_matric":         return "Could not identify the student (no matric on the proposal).";
    case "student_not_found": return "No matching student found in the Students sheet — check the matric.";
    case "email_blank":       return "No email on file for this student (column J is blank).";
    case "email_invalid":     return "The student's email address is invalid (missing '@').";
    default:                  return "Email delivery failed — check mail authorization/quota in the execution log.";
  }
}

function updateStudentLastMeeting(studentId, meetingDate) {
  var sheet = getDbSpreadsheet().getSheetByName("Students");
  var values = sheet.getDataRange().getValues();
  for (var i = 1; i < values.length; i++) {
    if (String(values[i][0]) === String(studentId)) {
       // Column 8 = "Last Meeting" (1-indexed)
       sheet.getRange(i + 1, 8).setValue(meetingDate);
       sheet.getRange(i + 1, 8).setNumberFormat('@');
      break;
    }
  }
}

/**
 * Sets a student's approved research topic. Matches by matric when provided
 * (more reliable), otherwise by name. Records the approval timestamp in the
 * "Topic Approved Date" column. Returns the approval date string, or "".
 */
function updateStudentTopic(studentName, topicTitle, matric) {
  var sheet = getDbSpreadsheet().getSheetByName("Students");
  var values = sheet.getDataRange().getValues();
  var wantMatric = String(matric || "").trim().toLowerCase();
  var wantName = String(studentName || "").trim().toLowerCase();
  var approvedDate = Utilities.formatDate(new Date(), Session.getScriptTimeZone(), "dd MMM yyyy HH:mm");

  for (var i = 1; i < values.length; i++) {
    var rowMatric = String(values[i][2]).trim().toLowerCase();
    var rowName = String(values[i][1]).trim().toLowerCase();
    var match = wantMatric ? (rowMatric === wantMatric) : (rowName === wantName);
    if (match) {
      // 1-indexed: 5=Research Topic, 9=Status, 11=Topic Approved Date
      sheet.getRange(i + 1, 5).setValue(topicTitle);
      sheet.getRange(i + 1, 9).setValue("Active");
      sheet.getRange(i + 1, 11).setValue(approvedDate);
      return approvedDate;
    }
  }
  return "";
}

/* =========================================================================
 * ANALYTICS ENGINE
 * A single supervisor-only entry point (getAnalytics) that gathers every
 * datapoint in the portal and returns ONE JSON payload the client renders:
 *   - overview KPIs (cohort, progress, meetings, proposals)
 *   - stage funnel (how many students sit at each of the 11 stages)
 *   - meeting activity (per-month trend, by status, by mode)
 *   - bottlenecks (average days students spend per stage, from StageHistory)
 *   - risk flags (no recent meeting, stalled at a stage, oldest pending items)
 *   - computed insights (short, plain-text findings for the "Insights" panel)
 *
 * Read-only: never writes to the sheets. Guarded by checkSupervisorAuthorization
 * so only an authorized account OR the supervisor passphrase can call it.
 * ========================================================================= */

// Days without a logged meeting before a student is flagged "quiet".
var ANALYTICS_QUIET_DAYS = 30;
// Days at the same stage before a student is flagged "stalled".
var ANALYTICS_STALLED_DAYS = 45;

/**
 * Parses a value into a Date, tolerating ISO strings, "dd MMM yyyy [HH:mm]"
 * display strings, and real Date objects. Returns null when unparseable.
 */
function analyticsParseDate_(value) {
  if (!value) return null;
  if (value instanceof Date) return isNaN(value.getTime()) ? null : value;
  var s = String(value).trim();
  if (!s) return null;
  var d = new Date(s);
  if (!isNaN(d.getTime())) return d;
  // Try "dd MMM yyyy" / "dd MMM yyyy HH:mm" forms.
  var m = s.match(/^(\d{1,2})\s+([A-Za-z]{3,})\s+(\d{4})(?:\s+(\d{1,2}):(\d{2}))?/);
  if (m) {
    var months = { jan:0, feb:1, mar:2, apr:3, may:4, jun:5, jul:6, aug:7, sep:8, oct:9, nov:10, dec:11 };
    var mo = months[m[2].slice(0, 3).toLowerCase()];
    if (mo !== undefined) {
      var dt = new Date(parseInt(m[3],10), mo, parseInt(m[1],10),
                        m[4] ? parseInt(m[4],10) : 0, m[5] ? parseInt(m[5],10) : 0);
      if (!isNaN(dt.getTime())) return dt;
    }
  }
  return null;
}

// Whole-day difference between two dates (b - a). Returns null if either missing.
function analyticsDaysBetween_(a, b) {
  if (!a || !b) return null;
  return Math.round((b.getTime() - a.getTime()) / (1000 * 60 * 60 * 24));
}

// "YYYY-MM" bucket key for a date (used for the monthly trend).
function analyticsMonthKey_(d) {
  return d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2);
}

/**
 * SUPERVISOR-ONLY. Returns the full analytics payload for the portal.
 * @param {string} passphrase  optional supervisor passphrase (account also accepted)
 * @return {Object}  { success, generatedAt, overview, stageFunnel, meetings, bottlenecks, risks, insights }
 */
function getAnalytics(passphrase) {
  try {
    checkSupervisorAuthorization(passphrase); // account OR passphrase

    var now = new Date();
    var students = getStudentsList() || [];
    var logs = getAllMeetingLogs() || [];          // newest first
    var pendingProposals = getProposalsList() || []; // only PENDING
    var topicHistory = getTopicHistory("") || [];   // all rows
    var stageHistory = getStageHistory("") || [];   // all rows
    var progressRows = getResourceProgressAll() || []; // all completion submissions

    var totalStages = STAGES.length;

    /* ---------- OVERVIEW KPIs ---------- */
    var activeStudents = students.filter(function (s) {
      return String(s.status || "").toLowerCase() === "active";
    }).length;

    var avgProgress = students.length
      ? Math.round(students.reduce(function (sum, s) { return sum + (Number(s.progress) || 0); }, 0) / students.length)
      : 0;

    var approvedTopics = students.filter(function (s) {
      return String(s.topicApprovedDate || "").trim() !== "";
    }).length;

    var completedStudents = students.filter(function (s) {
      return (Number(s.stage) || 1) >= totalStages;
    }).length;

    var logsByStatus = { PENDING: 0, UNDER_REVIEW: 0, APPROVED: 0, REJECTED: 0 };
    var logsByMode = {};
    logs.forEach(function (l) {
      var st = String(l.status || "PENDING").toUpperCase();
      if (logsByStatus[st] === undefined) logsByStatus[st] = 0;
      logsByStatus[st]++;
      var mode = String(l.meetingMode || "Unspecified").trim() || "Unspecified";
      logsByMode[mode] = (logsByMode[mode] || 0) + 1;
    });

    var pendingLogs = logsByStatus.PENDING + logsByStatus.UNDER_REVIEW;

    // Meetings this month (by meeting date, falling back to submit timestamp).
    var thisMonthKey = analyticsMonthKey_(now);
    var meetingsThisMonth = 0;
    logs.forEach(function (l) {
      var d = analyticsParseDate_(l.meetingDate) || analyticsParseDate_(l.timestamp);
      if (d && analyticsMonthKey_(d) === thisMonthKey) meetingsThisMonth++;
    });

    var totalPointsAwarded = 0;
    var completedResources = 0;
    var pendingResourceApprovals = 0;
    progressRows.forEach(function (p) {
      if (p.status === RESOURCE_STATUS.APPROVED) {
        completedResources++;
        totalPointsAwarded += Number(p.points) || 0;
      } else if (p.status === RESOURCE_STATUS.PENDING_APPROVAL) {
        pendingResourceApprovals++;
      }
    });

    var overview = {
      totalStudents: students.length,
      activeStudents: activeStudents,
      completedStudents: completedStudents,
      avgProgress: avgProgress,
      approvedTopics: approvedTopics,
      pendingProposals: pendingProposals.length,
      totalMeetingLogs: logs.length,
      pendingLogs: pendingLogs,
      meetingsThisMonth: meetingsThisMonth,
      completedResources: completedResources,
      totalPointsAwarded: totalPointsAwarded,
      pendingResourceApprovals: pendingResourceApprovals
    };

    /* ---------- STAGE FUNNEL (students per stage) ---------- */
    var stageCounts = [];
    for (var s = 1; s <= totalStages; s++) {
      stageCounts.push({ stage: s, name: stageName(s), count: 0 });
    }
    students.forEach(function (stu) {
      var idx = (Number(stu.stage) || 1) - 1;
      if (idx < 0) idx = 0;
      if (idx >= totalStages) idx = totalStages - 1;
      stageCounts[idx].count++;
    });
    var maxStageCount = stageCounts.reduce(function (m, x) { return Math.max(m, x.count); }, 0);

    /* ---------- MEETING ACTIVITY: last 6 months trend ---------- */
    var monthOrder = [];
    var monthMap = {};
    for (var i = 5; i >= 0; i--) {
      var d = new Date(now.getFullYear(), now.getMonth() - i, 1);
      var key = analyticsMonthKey_(d);
      var label = Utilities.formatDate(d, Session.getScriptTimeZone(), "MMM yyyy");
      monthMap[key] = { key: key, label: label, count: 0 };
      monthOrder.push(key);
    }
    logs.forEach(function (l) {
      var d = analyticsParseDate_(l.meetingDate) || analyticsParseDate_(l.timestamp);
      if (!d) return;
      var key = analyticsMonthKey_(d);
      if (monthMap[key]) monthMap[key].count++;
    });
    var meetingsPerMonth = monthOrder.map(function (k) { return monthMap[k]; });
    var maxMonthCount = meetingsPerMonth.reduce(function (m, x) { return Math.max(m, x.count); }, 0);

    var meetings = {
      perMonth: meetingsPerMonth,
      maxMonthCount: maxMonthCount,
      byStatus: logsByStatus,
      byMode: Object.keys(logsByMode).map(function (k) { return { mode: k, count: logsByMode[k] }; })
                    .sort(function (a, b) { return b.count - a.count; })
    };

    /* ---------- BOTTLENECKS: avg days per stage (from StageHistory) ---------- */
    // Group ADVANCE events per student (chronological), then measure the gap
    // between consecutive stage entries. That gap is the time spent in the
    // earlier stage. Averaged across students, per stage.
    var perStudentEvents = {};
    stageHistory.forEach(function (h) {
      var d = analyticsParseDate_(h.timestamp);
      if (!d) return;
      var key = String(h.matric || h.studentName || "").toLowerCase();
      if (!key) return;
      (perStudentEvents[key] = perStudentEvents[key] || []).push({ stage: Number(h.stage) || 0, date: d });
    });

    var stageDurTotals = {}; // stage -> { totalDays, samples }
    Object.keys(perStudentEvents).forEach(function (key) {
      var evs = perStudentEvents[key].sort(function (a, b) { return a.date - b.date; });
      for (var j = 0; j < evs.length - 1; j++) {
        var fromStage = evs[j].stage;
        var days = analyticsDaysBetween_(evs[j].date, evs[j + 1].date);
        if (fromStage >= 1 && days !== null && days >= 0) {
          var t = stageDurTotals[fromStage] = stageDurTotals[fromStage] || { totalDays: 0, samples: 0 };
          t.totalDays += days;
          t.samples++;
        }
      }
    });

    var stageDurations = [];
    for (var sd = 1; sd <= totalStages; sd++) {
      var rec = stageDurTotals[sd];
      stageDurations.push({
        stage: sd,
        name: stageName(sd),
        avgDays: rec && rec.samples ? Math.round(rec.totalDays / rec.samples) : null,
        samples: rec ? rec.samples : 0
      });
    }
    var measured = stageDurations.filter(function (x) { return x.avgDays !== null; });
    var slowestStage = measured.slice().sort(function (a, b) { return b.avgDays - a.avgDays; })[0] || null;

    var bottlenecks = { stageDurations: stageDurations, slowestStage: slowestStage };

    /* ---------- RISK FLAGS ---------- */
    // Last meeting date per student (matric -> Date), from logs.
    var lastMeetingByMatric = {};
    logs.forEach(function (l) {
      var mm = String(l.studentNameMatric || "");
      var mt = (mm.match(/\(([^)]+)\)\s*$/) || [])[1];
      if (!mt) return;
      var key = mt.trim().toLowerCase();
      var d = analyticsParseDate_(l.meetingDate) || analyticsParseDate_(l.timestamp);
      if (!d) return;
      if (!lastMeetingByMatric[key] || d > lastMeetingByMatric[key]) lastMeetingByMatric[key] = d;
    });

    // Last stage-change date per student (matric -> Date), from StageHistory.
    var lastStageChangeByMatric = {};
    stageHistory.forEach(function (h) {
      var key = String(h.matric || "").trim().toLowerCase();
      if (!key) return;
      var d = analyticsParseDate_(h.timestamp);
      if (!d) return;
      if (!lastStageChangeByMatric[key] || d > lastStageChangeByMatric[key]) lastStageChangeByMatric[key] = d;
    });

    var quietStudents = [];   // no meeting logged in ANALYTICS_QUIET_DAYS
    var stalledStudents = [];  // same stage for ANALYTICS_STALLED_DAYS (not yet completed)
    students.forEach(function (stu) {
      var key = String(stu.matric || "").trim().toLowerCase();
      var lastMtg = lastMeetingByMatric[key] || analyticsParseDate_(stu.lastMeeting);
      var quietDays = lastMtg ? analyticsDaysBetween_(lastMtg, now) : null;
      if (quietDays === null || quietDays >= ANALYTICS_QUIET_DAYS) {
        quietStudents.push({
          name: stu.name, matric: stu.matric,
          daysSinceMeeting: quietDays, lastMeeting: lastMtg ? Utilities.formatDate(lastMtg, Session.getScriptTimeZone(), "dd MMM yyyy") : "None"
        });
      }
      var stg = Number(stu.stage) || 1;
      if (stg < totalStages) {
        var since = lastStageChangeByMatric[key] || analyticsParseDate_(stu.stageUpdated);
        var stalledDays = since ? analyticsDaysBetween_(since, now) : null;
        if (stalledDays !== null && stalledDays >= ANALYTICS_STALLED_DAYS) {
          stalledStudents.push({
            name: stu.name, matric: stu.matric,
            stage: stg, stageName: stageName(stg), daysAtStage: stalledDays
          });
        }
      }
    });
    quietStudents.sort(function (a, b) { return (b.daysSinceMeeting || 9999) - (a.daysSinceMeeting || 9999); });
    stalledStudents.sort(function (a, b) { return b.daysAtStage - a.daysAtStage; });

    // Oldest pending proposals (age in days).
    var oldestPendingProposals = pendingProposals.map(function (p) {
      var d = analyticsParseDate_(p.date);
      return { topic: p.topic, studentName: p.studentName, matric: p.matric,
               ageDays: d ? analyticsDaysBetween_(d, now) : null, date: p.date };
    }).sort(function (a, b) { return (b.ageDays || 0) - (a.ageDays || 0); }).slice(0, 5);

    var risks = {
      quietDaysThreshold: ANALYTICS_QUIET_DAYS,
      stalledDaysThreshold: ANALYTICS_STALLED_DAYS,
      quietStudents: quietStudents,
      stalledStudents: stalledStudents,
      oldestPendingProposals: oldestPendingProposals,
      logsAwaitingReview: pendingLogs
    };

    /* ---------- COMPUTED INSIGHTS (plain-text findings) ---------- */
    var insights = [];
    insights.push({
      type: "info",
      text: "Supervising " + overview.totalStudents + " student(s); cohort average progress is " + avgProgress + "%."
    });
    if (pendingLogs > 0) {
      insights.push({ type: pendingLogs >= 3 ? "warn" : "info",
        text: pendingLogs + " meeting log(s) await your review." });
    }
    if (overview.pendingProposals > 0) {
      insights.push({ type: overview.pendingProposals >= 3 ? "warn" : "info",
        text: overview.pendingProposals + " topic proposal(s) pending approval." });
      if (oldestPendingProposals[0] && oldestPendingProposals[0].ageDays !== null && oldestPendingProposals[0].ageDays >= 7) {
        insights.push({ type: "warn",
          text: "Oldest pending proposal has waited " + oldestPendingProposals[0].ageDays + " days (" + oldestPendingProposals[0].studentName + ")." });
      }
    }
    if (quietStudents.length > 0) {
      insights.push({ type: "warn",
        text: quietStudents.length + " student(s) have no meeting logged in the last " + ANALYTICS_QUIET_DAYS + " days." });
    }
    if (stalledStudents.length > 0) {
      insights.push({ type: "warn",
        text: stalledStudents.length + " student(s) have stayed at the same stage for " + ANALYTICS_STALLED_DAYS + "+ days." });
    }
    if (slowestStage && slowestStage.avgDays !== null) {
      insights.push({ type: "info",
        text: "Slowest stage so far: \"" + slowestStage.name + "\" at ~" + slowestStage.avgDays + " days on average." });
    }
    if (completedStudents > 0) {
      insights.push({ type: "success",
        text: completedStudents + " student(s) have reached the final stage (Sign-off)." });
    }
     if (meetingsThisMonth === 0) {
       insights.push({ type: "warn", text: "No meetings have been logged yet this month." });
     } else {
       insights.push({ type: "success", text: meetingsThisMonth + " meeting(s) logged this month." });
     }

     // Resource-completion insights.
     if (pendingResourceApprovals > 0) {
       insights.push({ type: pendingResourceApprovals >= 3 ? "warn" : "info",
         text: pendingResourceApprovals + " resource completion(s) await your review." });
     }
     if (completedResources > 0) {
       insights.push({ type: "success",
         text: completedResources + " resource(s) approved across the cohort (" + totalPointsAwarded + " pts total)." });
     }

    return {
      success: true,
      generatedAt: now.toISOString(),
      overview: overview,
      stageFunnel: { stages: stageCounts, maxCount: maxStageCount, totalStages: totalStages },
      meetings: meetings,
      bottlenecks: bottlenecks,
      risks: risks,
      insights: insights
    };
  } catch (err) {
    Logger.log("getAnalytics failed: " + err);
    return { success: false, message: err.toString() };
  }
}

/* =========================================================================
 * GOOGLE CHAT ANNOUNCEMENTS
 * Posts a features-update announcement to a Google Chat space via an incoming
 * webhook. The webhook URL is stored in Script Properties (never hard-coded),
 * so no secret lives in this source file.
 *
 * Setup (one time):
 *   1. In the target Google Chat space: + > Apps & integrations > Webhooks >
 *      add a webhook, then copy its URL.
 *   2. In the Apps Script editor run:  setChatWebhookUrl("PASTE_URL_HERE")
 *      (or use the "LASU Portal" menu item that prompts for it).
 *   3. Run postAnnouncementToChat() (or the menu item) to send the note.
 * ========================================================================= */

// Script Property key holding the Google Chat incoming webhook URL.
var CHAT_WEBHOOK_PROP = "CHAT_WEBHOOK_URL";

/**
 * Stores the Google Chat incoming webhook URL in Script Properties.
 * Run from the editor: setChatWebhookUrl("https://chat.googleapis.com/v1/spaces/.../messages?key=...&token=...")
 */
function setChatWebhookUrl(url) {
  var u = String(url || "").trim();
  if (!u) return "No URL provided. Usage: setChatWebhookUrl(\"WEBHOOK_URL\").";
  if (u.indexOf("chat.googleapis.com") === -1) {
    return "That doesn't look like a Google Chat webhook URL (expected 'chat.googleapis.com').";
  }
  PropertiesService.getScriptProperties().setProperty(CHAT_WEBHOOK_PROP, u);
  return "Google Chat webhook URL saved.";
}

function getChatWebhookUrl_() {
  return PropertiesService.getScriptProperties().getProperty(CHAT_WEBHOOK_PROP) || "";
}

/**
 * Menu-driven webhook setup: prompts for the URL so you don't have to edit code.
 */
function promptSetChatWebhookUrl() {
  try {
    var ui = SpreadsheetApp.getUi();
    var resp = ui.prompt("Set Google Chat webhook",
      "Paste the incoming webhook URL for the target Chat space:", ui.ButtonSet.OK_CANCEL);
    if (resp.getSelectedButton() !== ui.Button.OK) return;
    ui.alert(setChatWebhookUrl(resp.getResponseText()));
  } catch (e) {
    Logger.log("promptSetChatWebhookUrl: " + e);
  }
}

/**
 * The features-update announcement text (single source of truth). Google Chat
 * renders *bold* and hyphen bullets, so the copy uses that lightweight markup.
 */
function getFeaturesAnnouncementText() {
  return [
    "*TheOAsis (for LASU) UG-Research Supervision Portal — Full Features Update*",
    "",
    "TheOAsis (for LASU) UG-Research Supervision Portal now covers the entire supervision journey, end to end. Here's everything available:",
    "",
    "*━━━ 👨‍🏫 SUPERVISOR HUB (admin-only) ━━━*",
    "",
    "*📊 Analytics & Insights* — Cohort KPIs (students, average progress, meeting logs, items awaiting action), a stage funnel across all 11 research stages, 6-month meeting trends, logs by status and mode, stage bottlenecks (average days per stage), and automatic risk flags — quiet students, stalled students, oldest pending proposals — with plain-language insights.",
    "",
    "*🧑‍🎓 Supervisor Matrix & Cohort* — Full student roster with topics, phases, progress, and last session, synced live with Google Sheets.",
    "",
    "*✅ Topic Approvals* — Review, approve, or request revisions on student topic proposals.",
    "",
    "*📝 Log Supervision Sessions* — Record official feedback and advance students through research stages.",
    "",
    "*🎛️ Broadcast Graphics Studio* — One-click access for on-air/video graphics.",
    "",
    "*🗂️ Portal Database (Sheet)* — Direct link to the live Google Sheet backend.",
    "",
    "*━━━ 🎓 STUDENT PORTAL ━━━*",
    "",
    "*📖 My Research Portal* — Profile, active topic with approval status, current milestone, overall progress bar, and the full 11-stage research lifecycle roadmap. Quick actions to submit a topic proposal, log a progress session, and refresh for the latest supervisor updates.",
    "",
    "*💬 Meeting Logs* — Submit detailed session logs and track supervisor feedback and status (Pending / Under review / Approved / Rewrite requested).",
    "",
    "*📚 Learning Resources* — Current assignment videos, required worksheets, and prerequisite tutorials.",
    "",
    "*📁 Drive Folders* — One-click access to a personal project folder and the shared cohort drive.",
    "",
    "*━━━ 🤖 FOR EVERYONE ━━━*",
    "",
    "*Gemini AI Research Assistant* — Tailored to Journalism & Media Studies: framing analysis, content-analysis codebooks, Chapter 3 methodology, objectives/hypotheses, and APA 7th citations. Now auto-recovers during busy periods by cascading across free-tier models, and replies are cleanly formatted.",
    "",
    "Plus: dark/light mode, mobile-friendly navigation, secure role-based login (students never see supervisor tools), and email notifications for proposals, meeting-log updates, and stage changes.",
    "",
    "— Dr. Olasunkanmi Arowolo, Journalism & Media Studies, Lagos State University (LASU)"
  ].join("\n");
}

/**
 * Posts the features-update announcement to the configured Google Chat space.
 * Returns a short status string (also shown in an alert when run from the menu).
 */
function postAnnouncementToChat() {
  try {
    var url = getChatWebhookUrl_();
    if (!url) {
      var noUrl = "No Google Chat webhook set. Run setChatWebhookUrl(\"...\") or use the 'Set Google Chat webhook' menu item first.";
      try { SpreadsheetApp.getUi().alert(noUrl); } catch (e) {}
      return noUrl;
    }

    var payload = { text: getFeaturesAnnouncementText() };
    var res = UrlFetchApp.fetch(url, {
      method: "post",
      contentType: "application/json",
      payload: JSON.stringify(payload),
      muteHttpExceptions: true
    });

    var code = res.getResponseCode();
    var ok = (code >= 200 && code < 300);
    var msg = ok
      ? "Announcement posted to Google Chat."
      : "Failed to post (HTTP " + code + "): " + res.getContentText().slice(0, 300);
    try { SpreadsheetApp.getUi().alert(msg); } catch (e) {}
    Logger.log(msg);
    return msg;
  } catch (err) {
    Logger.log("postAnnouncementToChat failed: " + err);
    return "postAnnouncementToChat failed: " + err;
  }
}
