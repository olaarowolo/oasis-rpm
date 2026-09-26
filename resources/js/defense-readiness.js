/**
 * Defense Readiness Manuscript Form — Page Controller
 *
 * Orchestrates the student-side manuscript readiness form: hydration from the
 * API, collapsible section cards, autosave with debounce + optimistic
 * concurrency, submit-for-review, conditions acknowledgment, custom sections,
 * and the Submit-for-Defense gate.
 *
 * Depends on rich-text-editor.js (auto-initialises on data-dr-editor roots).
 */
(function () {
  'use strict';

  var CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  var API = (window.APP_API || '/').replace(/\/+$/, '') || '';
  var sectionToExpand = new URLSearchParams(window.location.search).get('section');

  var STATE = {
    document: null,
    sections: [],
    checklist: null,
    autosaveTimer: null,
    autosaveRetries: 0,
    maxRetries: 3,
  };

  // ─── Utilities ─────────────────────────────────────────────────────────

  function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function formatTime(date) {
    return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  function formatDate(date) {
    return new Date(date).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
  }

  function showToast(message, type) {
    type = type || 'success';
    var container = document.getElementById('toast-container');
    if (!container) return;
    var colors = { success: 'bg-emerald-600', error: 'bg-rose-600', info: 'bg-academic-700' };
    var icons = { success: 'fa-circle-check', error: 'fa-circle-exclamation', info: 'fa-circle-info' };
    var toast = document.createElement('div');
    var cls = colors[type] || colors.info;
    var icon = icons[type] || icons.info;
    toast.className = cls + ' text-white px-4 py-2.5 rounded-xl shadow-xl text-xs font-semibold flex items-center gap-2 fade-in pointer-events-auto';
    toast.innerHTML = '<i class="fa-solid ' + icon + '"></i><span>' + escapeHtml(message) + '</span>';
    container.appendChild(toast);
    setTimeout(function () {
      toast.style.transition = 'opacity 0.3s ease';
      toast.style.opacity = '0';
      setTimeout(function () { toast.remove(); }, 300);
    }, 3500);
  }

  function apiFetch(url, options) {
    var defaults = {
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF,
      },
    };
    if (options && options.body !== undefined) {
      defaults.headers['Content-Type'] = 'application/json';
      options.body = JSON.stringify(options.body);
    }
    var merged = Object.assign({}, defaults, options || {});
    return fetch(url, merged)
      .then(function (res) { return res.json().then(function (json) { return { ok: res.ok, status: res.status, json: json }; }); });
  }

  // ─── Hydration ─────────────────────────────────────────────────────────

  function loadDocument() {
    var container = document.getElementById('manuscript-form-container');
    if (!container) return;

    apiFetch(API + '/student/defense-readiness')
      .then(function (resp) {
        if (!resp.ok) {
          throw new Error(resp.json?.message || 'Failed to load manuscript data');
        }
        var data = resp.json.data;
        STATE.document = data.document;
        STATE.sections = data.sections;
        STATE.checklist = data.checklist;

        updateStatusBar(data.document.status);
        renderSettingsBar(data.document);
        renderProgressRail(data.sections);
        renderSections(data.sections, data.document);
        updateSubmitDefenseButton(data.document.status, data.checklist.defense_score);
        updateSettingsLockState(data.document.settings_locked);

        container.classList.remove('hidden');
        initConflictModal();

        // Auto-expand from ?section= query param
        if (sectionToExpand) {
          setTimeout(function () {
            var card = document.querySelector('[data-section-card="' + sectionToExpand + '"]');
            if (card) {
              expandSection(card);
              card.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          }, 200);
        } else {
          // Expand the first draft/revision section by default
          var firstEditable = document.querySelector('[data-section-status="draft"], [data-section-status="revision_requested"]');
          if (firstEditable) {
            expandSection(firstEditable.closest('[data-section-card]'));
          }
        }
      })
      .catch(function (err) {
        showToast(err.message || 'Could not load manuscript data', 'error');
      });
  }

  // ─── Status & Settings ─────────────────────────────────────────────────

  var STATUS_BANNER = {
    draft: { label: 'Draft', cls: 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300' },
    in_review: { label: 'Awaiting Review', cls: 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' },
    in_revision: { label: 'Revision Requested', cls: 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' },
    halted: { label: 'Halted', cls: 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' },
    completed: { label: 'Complete', cls: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' },
  };

  var SECTION_STATUS_LABEL = {
    locked: 'Locked',
    draft: 'Draft',
    submitted: 'In Review',
    accepted: 'Accepted',
    conditional: 'Conditionally Approved',
    revision_requested: 'Revision Requested',
    rejected: 'Rejected',
  };

  var SECTION_STATUS_CLS = {
    locked: 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',
    draft: 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
    submitted: 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
    accepted: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
    conditional: 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
    revision_requested: 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
  };

  function updateStatusBar(status) {
    var banner = document.getElementById('document-status-banner');
    if (!banner) return;
    var meta = STATUS_BANNER[status] || STATUS_BANNER.draft;
    banner.textContent = meta.label;
    banner.className = 'px-3 py-1.5 rounded-full text-xs font-semibold ' + meta.cls;
  }

  function updateSettingsLockState(locked) {
    var note = document.getElementById('settings-locked-note');
    var bar = document.getElementById('settings-bar');
    if (!note || !bar) return;

    if (locked) {
      note.classList.remove('hidden');
      var selects = bar.querySelectorAll('select, input[type="checkbox"]');
      selects.forEach(function (el) { el.disabled = true; });
    }
  }

  function renderSettingsBar(document) {
    var approachSelect = document.getElementById('study-approach');
    var primaryCheckbox = document.getElementById('primary-data-collection');

    if (approachSelect && document.study_approach) {
      approachSelect.value = document.study_approach;
    }
    if (primaryCheckbox) {
      primaryCheckbox.checked = document.primary_data_collection;
    }

    if (approachSelect) {
      approachSelect.removeEventListener('change', onSettingsChange);
      approachSelect.addEventListener('change', onSettingsChange);
    }
    if (primaryCheckbox) {
      primaryCheckbox.removeEventListener('change', onSettingsChange);
      primaryCheckbox.addEventListener('change', onSettingsChange);
    }
  }

  function onSettingsChange() {
    var approach = document.getElementById('study-approach').value;
    var primary = document.getElementById('primary-data-collection').checked;

    apiFetch(API + '/student/defense-readiness/settings', {
      method: 'PATCH',
      body: { study_approach: approach || null, primary_data_collection: primary },
    }).then(function (resp) {
      if (resp.ok) {
        showToast('Settings updated', 'success');
      } else {
        showToast(resp.json?.message || 'Could not update settings', 'error');
      }
    });
  }

  // ─── Progress Rail ─────────────────────────────────────────────────────

  function renderProgressRail(sections) {
    var rail = document.getElementById('progress-rail');
    if (!rail) return;

    var visibleSections = sections.filter(function (s) { return !s.is_group; });
    var html = '';

    visibleSections.forEach(function (s, i) {
      var isCurrent = s.status === 'draft' || s.status === 'revision_requested';
      var label = SECTION_STATUS_LABEL[s.status] || s.status;
      var cls = SECTION_STATUS_CLS[s.status] || '';
      var isComplete = s.status === 'accepted' || s.status === 'conditional';

      html += '<div class="flex items-center gap-1">';
      if (i > 0) {
        html += '<div class="w-8 h-px bg-slate-300 dark:bg-slate-600"></div>';
      }
      html += '<div class="flex items-center justify-center w-6 h-6 rounded-full text-[9px] font-bold ' + cls + '" title="' + escapeHtml(s.title) + '">' + escapeHtml(isComplete ? '✓' : s.position) + '</div>';
      html += '<span class="text-[10px] text-slate-500 dark:text-slate-400 truncate max-w-24" title="' + escapeHtml(s.title) + '">' + escapeHtml(s.title) + '</span>';
      html += '</div>';
    });

    rail.innerHTML = '<div class="flex items-center gap-2">' + html + '</div>';
  }

  // ─── Section Cards ─────────────────────────────────────────────────────

  function renderSections(sections, document) {
    var container = document.getElementById('sections-container');
    if (!container) return;

    // Build a tree: group section + its children
    var topLevel = sections.filter(function (s) { return !s.parent_id; });
    var byParent = {};
    sections.filter(function (s) { return s.parent_id; }).forEach(function (s) {
      (byParent[s.parent_id] = byParent[s.parent_id] || []).push(s);
    });

    var html = '';
    topLevel.forEach(function (s, i) {
      html += renderSectionCard(s, document, i, topLevel.length, byParent[s.id] || [], sections);
    });

    container.innerHTML = html;

    // Initialize editors on the newly rendered cards
    if (window.DefenseReadinessEditor && window.DefenseReadinessEditor.initAll) {
      window.DefenseReadinessEditor.initAll(container);
    }

    // Wire up event handlers
    wireSectionEvents();
  }

  function renderSectionCard(section, document, index, total, children, allSections) {
    var isGroup = section.kind === 'group';
    var isLast = index === total - 1;
    var isHalted = document.status === 'halted';
    var isEditable = section.unlocked && section.status === 'draft';
    var isRevision = section.unlocked && section.status === 'revision_requested';
    var canEdit = isEditable || isRevision;
    var canSubmit = canEdit && section.content && section.content.trim() !== '';
    var isConditional = section.status === 'conditional';

    var statusCls = SECTION_STATUS_CLS[section.status] || '';
    var statusLabel = SECTION_STATUS_LABEL[section.status] || section.status;

    var targetText = '';
    if (section.target_min_words !== null && section.target_max_words !== null) {
      targetText = section.target_min_words + '–' + section.target_max_words;
    } else if (section.word_count_label) {
      targetText = section.word_count_label;
    }

    var wordCountHtml = targetText
      ? '<span class="text-xs text-slate-500 dark:text-slate-400">' + section.word_count + ' / ' + targetText + ' words</span>'
      : '<span class="text-xs text-slate-500 dark:text-slate-400">' + section.word_count + ' words</span>';

    var guidanceHtml = '';
    if (section.guidance) {
      guidanceHtml = '<div class="mt-2 text-xs text-slate-500 dark:text-slate-400 guidance-text hidden">' + escapeHtml(section.guidance) + '</div>';
    }

    var contentHtml = '';
    if (!isGroup) {
      contentHtml = renderEditor(section, document, canEdit && !isHalted);
    }

    // Children (methodology sub-sections)
    var childrenHtml = '';
    if (children && children.length) {
      children.sort(function (a, b) { return a.position - b.position; });
      childrenHtml = '<div class="ml-0 sm:ml-6 border-l-2 border-slate-200 dark:border-slate-700 pl-0 sm:pl-4 mt-2 space-y-2">';
      children.forEach(function (child, ci) {
        childrenHtml += renderChildCard(child, document, isHalted);
      });
      childrenHtml += '</div>';
    }

    var lockedNote = '';
    if (!section.unlocked) {
      lockedNote = '<div class="mt-2 text-xs text-slate-500 dark:text-slate-400 italic">Complete the previous section to unlock</div>';
    }

    var submitNote = '';
    if (isHalted) {
      submitNote = '<div class="mt-2 text-xs text-red-500 dark:text-red-400">Editing is disabled — the manuscript is halted</div>';
    }

    var conditionalBox = '';
    if (isConditional) {
      var conditions = section.reviews
        .filter(function (r) { return r.action === 'conditional'; })
        .map(function (r) { return r.conditions; })
        .filter(Boolean);
      var latestConditions = conditions.length ? conditions[conditions.length - 1] : '';

      var ackHtml = section.conditions_acknowledged_at
        ? '<span class="text-xs text-emerald-600 dark:text-emerald-400">✓ Conditions addressed on ' + formatDate(section.conditions_acknowledged_at) + '</span>'
        : '<button data-acknowledge-conditions data-section-id="' + section.id + '" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-semibold">I have addressed these conditions</button>';

      conditionalBox = '<div class="mt-2 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">' +
        '<div class="font-semibold text-xs text-blue-800 dark:text-blue-300 mb-1">Pinned Conditions</div>' +
        '<div class="text-xs text-blue-700 dark:text-blue-400">' + escapeHtml(latestConditions) + '</div>' +
        ackHtml +
        '</div>';
    }

    var groupBadge = isGroup ? '<span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">Group</span>' : '';

    return '<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden" data-section-card="' + section.id + '" data-section-status="' + section.status + '" data-section-unlocked="' + (section.unlocked ? 'true' : 'false') + '" data-section-group="' + (isGroup ? 'true' : 'false') + '">' +
      '<div class="p-4 border-b border-slate-200 dark:border-slate-700 cursor-pointer" onclick="toggleSection(this)" data-section-header>' +
        '<div class="flex items-center justify-between gap-3">' +
          '<div class="flex items-center gap-3">' +
            '<i class="fa-solid fa-chevron-down text-slate-400 dark:text-slate-500 transition-transform duration-200" style="font-size: 0.75rem;" data-chevron></i>' +
            '<h3 class="font-semibold text-slate-900 dark:text-white">' + escapeHtml(section.title) + '</h3>' +
            groupBadge +
          '</div>' +
          '<span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold ' + statusCls + '">' + escapeHtml(statusLabel) + '</span>' +
        '</div>' +
        '<div class="mt-1 flex items-center justify-between text-xs">' +
          wordCountHtml +
          '<button type="button" class="text-slate-400 hover:text-slate-600" onclick="toggleGuidance(this)" data-guidance-toggle><i class="fa-solid fa-info-circle"></i> Guidance</button>' +
        '</div>' +
        guidanceHtml +
      '</div>' +
      '<div class="section-body hidden" data-section-body>' +
        (lockedNote + contentHtml + conditionalBox + submitNote + childrenHtml) +
      '</div>' +
      (canSubmit ? '<div class="p-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/20 flex items-center justify-between">' +
        '<div id="save-indicator-' + section.id + '" class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2"><i class="fa-solid fa-clock"></i> Idle</div>' +
        '<button data-submit-section data-section-id="' + section.id + '" class="px-4 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl text-xs font-semibold">Submit for Review</button>' +
        '</div>' : '') +
    '</div>';
  }

  function renderChildCard(section, document, isHalted) {
    var isEditChild = section.unlocked && (section.status === 'draft' || section.status === 'revision_requested');
    var canSubmitChild = isEditChild && section.content && section.content.trim() !== '';
    var isConditional = section.status === 'conditional';
    var statusCls = SECTION_STATUS_CLS[section.status] || '';
    var statusLabel = SECTION_STATUS_LABEL[section.status] || section.status;

    var wordCountHtml = (section.target_min_words !== null && section.target_max_words !== null)
      ? '<span class="text-xs text-slate-500 dark:text-slate-400">' + section.word_count + ' / ' + section.target_min_words + '–' + section.target_max_words + ' words</span>'
      : '<span class="text-xs text-slate-500 dark:text-slate-400">' + section.word_count + ' words</span>';

    var guidanceHtml = section.guidance
      ? '<div class="mt-2 text-xs text-slate-500 dark:text-slate-400 guidance-text hidden">' + escapeHtml(section.guidance) + '</div>'
      : '';

    var contentHtml = renderEditor(section, document, isEditChild && !isHalted);

    var conditionalBox = '';
    if (isConditional) {
      var conditions = section.reviews
        .filter(function (r) { return r.action === 'conditional'; })
        .map(function (r) { return r.conditions; })
        .filter(Boolean);
      var latestConditions = conditions.length ? conditions[conditions.length - 1] : '';
      var ackHtml = section.conditions_acknowledged_at
        ? '<span class="text-xs text-emerald-600 dark:text-emerald-400">✓ Conditions addressed on ' + formatDate(section.conditions_acknowledged_at) + '</span>'
        : '<button data-acknowledge-conditions data-section-id="' + section.id + '" class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-xs">Acknowledge</button>';

      conditionalBox = '<div class="mt-2 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">' +
        '<div class="font-semibold text-xs text-blue-800 dark:text-blue-300 mb-1">Pinned Conditions</div>' +
        '<div class="text-xs text-blue-700 dark:text-blue-400">' + escapeHtml(latestConditions) + '</div>' +
        ackHtml +
        '</div>';
    }

    var lockedNote = !section.unlocked
      ? '<div class="mt-1 text-xs text-slate-500 dark:text-slate-400 italic">Complete the previous section to unlock</div>'
      : '';

    return '<div class="bg-slate-50 dark:bg-slate-900/30 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden" data-child-card data-section-id="' + section.id + '">' +
      '<div class="p-3 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">' +
        '<div class="flex items-center gap-2">' +
          '<span class="text-xs font-semibold text-slate-700 dark:text-slate-300">' + escapeHtml(section.title) + '</span>' +
          '<span class="px-2 py-0.5 rounded-full text-[9px] font-bold ' + statusCls + '">' + escapeHtml(statusLabel) + '</span>' +
        '</div>' +
        wordCountHtml +
      '</div>' +
      '<div class="p-3 section-body" data-section-body>' +
        (guidanceHtml + lockedNote + contentHtml + conditionalBox) +
      '</div>' +
      (canSubmitChild ? '<div class="p-2 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">' +
        '<div id="save-indicator-' + section.id + '" class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2"><i class="fa-solid fa-clock"></i> Idle</div>' +
        '<button data-submit-section data-section-id="' + section.id + '" class="px-3 py-1 bg-academic-700 hover:bg-academic-800 text-white rounded-lg text-xs font-semibold">Submit for Review</button>' +
        '</div>' : '') +
    '</div>';
  }

  function renderEditor(section, document, editable) {
    var value = escapeHtml(section.content || '');
    var placeholder = 'Write your content here...';
    var targetInfo = '';
    if (section.target_min_words !== null && section.target_max_words !== null) {
      targetInfo = 'Target: ' + section.target_min_words + '–' + section.target_max_words + ' words';
    } else if (section.word_count_label) {
      targetInfo = section.word_count_label;
    }

    var toolbarClass = editable ? '' : ' opacity-50';

    return '<div class="dr-editor-wrapper" data-dr-editor-root data-section-id="' + section.id + '" data-section-status="' + section.status + '">' +
      '<div class="dr-toolbar flex flex-wrap gap-1 p-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-t-xl' + toolbarClass + '">' +
        '<button type="button" class="dr-btn" data-dr-cmd="bold" title="Bold" aria-label="Bold"><i class="fa-solid fa-bold"></i></button>' +
        '<button type="button" class="dr-btn" data-dr-cmd="italic" title="Italic" aria-label="Italic"><i class="fa-solid fa-italic"></i></button>' +
        '<button type="button" class="dr-btn" data-dr-cmd="underline" title="Underline" aria-label="Underline"><i class="fa-solid fa-underline"></i></button>' +
        '<button type="button" class="dr-btn" data-dr-cmd="strikethrough" title="Strikethrough" aria-label="Strikethrough"><i class="fa-solid fa-strikethrough"></i></button>' +
        '<span class="dr-separator w-px h-5 bg-slate-300 dark:bg-slate-600 self-center mx-1"></span>' +
        '<button type="button" class="dr-btn" data-dr-cmd="formatBlock" data-dr-value="h2" title="Heading 2" aria-label="Heading 2"><i class="fa-solid fa-heading"></i></button>' +
        '<button type="button" class="dr-btn" data-dr-cmd="formatBlock" data-dr-value="h3" title="Heading 3" aria-label="Heading 3"><i class="fa-solid fa-heading"></i><sub style="font-size:8px;">3</sub></button>' +
        '<button type="button" class="dr-btn" data-dr-cmd="formatBlock" data-dr-value="p" title="Paragraph" aria-label="Paragraph"><i class="fa-solid fa-paragraph"></i></button>' +
        '<span class="dr-separator w-px h-5 bg-slate-300 dark:bg-slate-600 self-center mx-1"></span>' +
        '<button type="button" class="dr-btn" data-dr-cmd="insertHTML" data-dr-value="<blockquote>" title="Quote" aria-label="Insert quote"><i class="fa-solid fa-quote-left"></i></button>' +
        '<button type="button" class="dr-btn" data-dr-cmd="insertUnorderedList" title="Bullet list" aria-label="Bullet list"><i class="fa-solid fa-list-ul"></i></button>' +
        '<button type="button" class="dr-btn" data-dr-cmd="insertOrderedList" title="Numbered list" aria-label="Numbered list"><i class="fa-solid fa-list-ol"></i></button>' +
        '<button type="button" class="dr-btn" data-dr-cmd="undo" title="Undo" aria-label="Undo"><i class="fa-solid fa-undo"></i></button>' +
        '<button type="button" class="dr-btn" data-dr-cmd="redo" title="Redo" aria-label="Redo"><i class="fa-solid fa-redo"></i></button>' +
      '</div>' +
      '<div class="dr-input" data-dr-input contenteditable="' + (editable ? 'true' : 'false') + '" data-dr-placeholder="Write your content here..." style="min-height:120px;max-height:60vh;overflow-y:auto;padding:12px 14px;border:1px solid #cbd5e1 dark:border-slate-600;border-t:0;border-b-left-radius:0;border-b-right-radius:0;">' + (value || '<br>') + '</div>' +
      '<div class="px-3 py-1.5 text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/30 border-t border-slate-200 dark:border-slate-700">' + (targetInfo || '') + '</div>' +
      '<input type="hidden" name="content" data-dr-hidden-input value="' + value + '">' +
    '</div>';
  }

  // ─── Section Interactions ────────────────────────────────────────────────

  function wireSectionEvents() {
    // Accordion toggle
    var headers = document.querySelectorAll('[data-section-header]');
    headers.forEach(function (h) {
      h.removeEventListener('click', onHeaderClick);
      h.addEventListener('click', onHeaderClick);
    });

    // Guidance toggle
    var guidanceToggles = document.querySelectorAll('[data-guidance-toggle]');
    guidanceToggles.forEach(function (btn) {
      btn.removeEventListener('click', onGuidanceClick);
      btn.addEventListener('click', onGuidanceClick);
    });

    // Submit buttons
    var submitButtons = document.querySelectorAll('[data-submit-section]');
    submitButtons.forEach(function (btn) {
      btn.removeEventListener('click', onSubmitClick);
      btn.addEventListener('click', onSubmitClick);
    });

    // Acknowledge conditions
    var ackButtons = document.querySelectorAll('[data-acknowledge-conditions]');
    ackButtons.forEach(function (btn) {
      btn.removeEventListener('click', onAckClick);
      btn.addEventListener('click', onAckClick);
    });

    // Setup autosave on editors
    var editorRoots = document.querySelectorAll('[data-dr-editor-root]');
    editorRoots.forEach(function (root) {
      setupAutosave(root);
    });
  }

  function onHeaderClick(e) {
    e.preventDefault();
    var card = this.closest('[data-section-card]');
    if (card) toggleSection(card);
  }

  function onGuidanceClick(e) {
    e.stopPropagation();
    var card = this.closest('[data-section-card], [data-child-card]');
    if (!card) return;
    var guidance = card.querySelector('.guidance-text');
    if (guidance) guidance.classList.toggle('hidden');
  }

  function toggleSection(card) {
    var body = card.querySelector('[data-section-body]');
    var chevron = card.querySelector('[data-chevron]');
    if (!body) return;
    var expanded = body.classList.contains('hidden');
    body.classList.toggle('hidden', !expanded);
    if (expanded) body.classList.remove('hidden');
    else body.classList.add('hidden');
    if (chevron) chevron.style.transform = expanded ? 'rotate(180deg)' : 'rotate(0deg)';
  }

  function expandSection(card) {
    var body = card.querySelector('[data-section-body]');
    var chevron = card.querySelector('[data-chevron]');
    if (body) body.classList.remove('hidden');
    if (chevron) chevron.style.transform = 'rotate(180deg)';
  }

  // ─── Autosave ──────────────────────────────────────────────────────────

  function setupAutosave(root) {
    var sectionId = root.getAttribute('data-section-id');
    var input = root.querySelector('[data-dr-input]');
    var indicator = document.getElementById('save-indicator-' + sectionId);
    var lastSavedAt = null;

    if (!input || !indicator) return;

    function setIndicator(state, text) {
      if (!indicator) return;
      indicator.innerHTML = '';
      var icon = {
        idle: 'fa-clock',
        saving: 'fa-spinner fa-spin',
        saved: 'fa-check',
        error: 'fa-circle-exclamation',
      }[state] || 'fa-clock';
      indicator.innerHTML = '<i class="fa-solid ' + icon + '"></i> ' + text;
    }

    function doSave(forceLastSaved) {
      var content = input.innerHTML;
      setIndicator('saving', 'Saving...');
      STATE.autosaveRetries = 0;

      apiFetch(API + '/student/defense-readiness/sections/' + sectionId + '/content', {
        method: 'PUT',
        body: { content: content, last_saved_at: forceLastSaved ? null : lastSavedAt },
      }).then(function (resp) {
        if (resp.status === 409) {
          setIndicator('error', 'Conflict');
          showConflictModal(sectionId, resp.json.server_updated_at || null);
          return;
        }
        if (!resp.ok) {
          throw new Error(resp.json?.message || 'Save failed');
        }
        lastSavedAt = resp.json.data.saved_at;
        setIndicator('saved', 'Saved ' + formatTime(new Date(lastSavedAt)));
        setTimeout(function () { setIndicator('idle', 'Idle'); }, 3000);
      }).catch(function (err) {
        STATE.autosaveRetries++;
        if (STATE.autosaveRetries <= 3) {
          var delay = Math.pow(2, STATE.autosaveRetries) * 1000;
          setIndicator('error', 'Not saved — retrying (' + STATE.autosaveRetries + ')');
          setTimeout(function () { doSave(forceLastSaved); }, delay);
        } else {
          setIndicator('error', 'Not saved — retry');
        }
      });
    }

    // Debounce 1.5s
    input.addEventListener('input', function () {
      clearTimeout(STATE.autosaveTimer);
      STATE.autosaveTimer = setTimeout(function () { doSave(false); }, 1500);
    });

    // Flush on blur
    input.addEventListener('blur', function () {
      clearTimeout(STATE.autosaveTimer);
      doSave(false);
    });

    // Flush on visibility change / beforeunload
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) doSave(false);
    });

    window.addEventListener('beforeunload', function (e) {
      var content = input.innerHTML;
      var data = new FormData();
      data.append('content', content);
      data.append('last_saved_at', lastSavedAt || '');
      navigator.sendBeacon(API + '/student/defense-readiness/sections/' + sectionId + '/content', data);
      // Note: sendBeacon doesn't send CSRF by default; for true last-save safety
      // the server should also accept this. For now, rely on blur + interval save.
    });

    root._onContentChange = function () {
      // Update hidden input for form-based submission
      var hidden = root.querySelector('[data-dr-hidden-input]');
      if (hidden) hidden.value = input.innerHTML;
    };
  }

  // ─── Conflict Modal ──────────────────────────────────────────────────────

  var conflictState = { sectionId: null, serverUpdatedAt: null };

  function initConflictModal() {
    // Inject conflict modal HTML
    var existing = document.getElementById('conflict-modal');
    if (existing) return;

    var modal = document.createElement('div');
    modal.id = 'conflict-modal';
    modal.className = 'fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4';
    modal.innerHTML =
      '<div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700">' +
      '<div class="flex items-start gap-3">' +
        '<i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>' +
        '<div class="flex-1">' +
          '<h3 class="font-bold text-slate-900 dark:text-white">Conflict detected</h3>' +
          '<p class="mt-1 text-sm text-slate-600 dark:text-slate-300">This section was modified in another tab.</p>' +
          '<p class="mt-1 text-xs text-slate-500 dark:text-slate-400" id="conflict-time">Server updated just now.</p>' +
        '</div>' +
      '</div>' +
      '<div class="mt-4 flex gap-2 justify-end">' +
        '<button onclick="resolveConflict(true)" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold">Reload</button>' +
        '<button onclick="resolveConflict(false)" class="px-4 py-2 bg-academic-700 hover:bg-academic-800 text-white rounded-xl font-semibold">Overwrite</button>' +
      '</div>' +
      '</div>';
    document.body.appendChild(modal);
  }

  function showConflictModal(sectionId, serverUpdatedAt) {
    conflictState.sectionId = sectionId;
    conflictState.serverUpdatedAt = serverUpdatedAt;

    var modal = document.getElementById('conflict-modal');
    var timeEl = document.getElementById('conflict-time');
    if (timeEl && serverUpdatedAt) {
      timeEl.textContent = 'Server updated: ' + formatDate(serverUpdatedAt);
    }
    if (modal) modal.classList.remove('hidden');
  }

  window.resolveConflict = function (reload) {
    var modal = document.getElementById('conflict-modal');
    if (modal) modal.classList.add('hidden');

    if (reload) {
      window.location.reload();
    } else {
      // Overwrite: re-send with last_saved_at = null
      var input = document.querySelector('[data-dr-editor-root][data-section-id="' + conflictState.sectionId + '"] [data-dr-input]');
      if (input) {
        var content = input.innerHTML;
        apiFetch(API + '/student/defense-readiness/sections/' + conflictState.sectionId + '/content', {
          method: 'PUT',
          body: { content: content, last_saved_at: null },
        }).then(function (resp) {
          if (resp.ok) {
            showToast('Content overwritten successfully', 'success');
          } else {
            showToast('Overwrite failed', 'error');
          }
        });
      }
    }
  };

  // ─── Submit Section ────────────────────────────────────────────────────

  function onSubmitClick(e) {
    var btn = e.currentTarget;
    var sectionId = btn.getAttribute('data-section-id');

    if (!confirm('Submit this section for supervisor review? You will not be able to edit it again until the supervisor responds.')) {
      return;
    }

    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

    apiFetch(API + '/student/defense-readiness/sections/' + sectionId + '/submit', {
      method: 'POST',
    }).then(function (resp) {
      if (resp.ok) {
        showToast('Section submitted for review', 'success');
      } else {
        showToast(resp.json?.message || 'Could not submit section', 'error');
      }
    }).catch(function (err) {
      showToast(err.message || 'Submission failed', 'error');
    }).finally(function () {
      btn.disabled = false;
      btn.innerHTML = original;
    });
  }

  // ─── Acknowledge Conditions ────────────────────────────────────────────

  function onAckClick(e) {
    var btn = e.currentTarget;
    var sectionId = btn.getAttribute('data-section-id');

    apiFetch(API + '/student/defense-readiness/sections/' + sectionId + '/acknowledge-conditions', {
      method: 'POST',
    }).then(function (resp) {
      if (resp.ok) {
        showToast('Conditions acknowledged', 'success');
        btn.disabled = true;
        btn.textContent = 'Conditions addressed';
      } else {
        showToast(resp.json?.message || 'Could not acknowledge conditions', 'error');
      }
    });
  }

  // ─── Submit for Defense ────────────────────────────────────────────────

  function updateSubmitDefenseButton(documentStatus, score) {
    var btn = document.getElementById('submit-for-defense-btn');
    var gateText = document.getElementById('defense-gate-text');
    if (!btn) return;

    var isCompleted = documentStatus === 'completed';
    var scoreOk = score >= 70;

    if (isCompleted && scoreOk) {
      btn.disabled = false;
      if (gateText) gateText.textContent = 'All sections accepted and readiness score is ' + score + '%';
    } else {
      btn.disabled = true;
      var reasons = [];
      if (!isCompleted) reasons.push('all sections must be accepted');
      if (!scoreOk) reasons.push('readiness score must be at least 70% (currently ' + score + '%)');
      if (gateText) gateText.textContent = 'Requires: ' + reasons.join('; ');
    }
  }

  // ─── Global functions for inline handlers ──────────────────────────────

  window.toggleSection = toggleSection;
  window.toggleGuidance = onGuidanceClick;
  window.loadManuscripts = function () {}; // no-op placeholder used by supervisor index template

  // ─── Init ──────────────────────────────────────────────────────────────

  document.addEventListener('DOMContentLoaded', function () {
    loadDocument();
  });
})();
