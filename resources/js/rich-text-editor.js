/**
 * Rich Text Editor Component
 * Vanilla contenteditable editor with toolbar, auto-grow, paste plain-text
 * enforcement, and whitelist enforcement for formatBlock commands.
 *
 * Isolated in its own module so it can later be swapped for TipTap without
 * touching the page controller.
 */
(function () {
  'use strict';

  if (typeof window !== 'undefined') {
    window.DefenseReadinessEditor = {
      init: initEditor,
      initAll: initAllEditors,
      getSanitizedCommandValue: function () { return null; }
    };
  }

  var ALLOWED_FORMATS = ['h2', 'h3', 'p', 'pre'];

  function initAllEditors(container) {
    container = container || document;
    var roots = container.querySelectorAll('[data-dr-editor]:not([data-dr-ready])');
    roots.forEach(initEditor);
  }

  function initEditor(root) {
    root.setAttribute('data-dr-ready', 'true');

    var input = root.querySelector('[data-dr-input]');
    var hiddenInput = root.querySelector('[data-dr-hidden-input]');
    var placeholder = root.getAttribute('data-dr-placeholder') || 'Start writing...';

    if (! input) return;

    input.setAttribute('contenteditable', 'true');
    input.setAttribute('spellcheck', 'true');
    input.setAttribute('autocomplete', 'off');
    input.setAttribute('autocorrect', 'off');
    input.setAttribute('autocapitalize', 'off');

    if (! input.textContent.trim() && ! input.innerHTML.trim()) {
      input.setAttribute('data-dr-empty', 'true');
    }

    // --- Toolbar ---
    var toolbar = root.querySelector('.dr-toolbar');
    if (toolbar) {
      var buttons = toolbar.querySelectorAll('[data-dr-cmd]');
      buttons.forEach(function (btn) {
        btn.addEventListener('mousedown', function (e) {
          e.preventDefault();
          var cmd = btn.getAttribute('data-dr-cmd');
          var val = btn.getAttribute('data-dr-value');

          if (cmd === 'undo' || cmd === 'redo') {
            document.execCommand(cmd, false, null);
          } else if (cmd === 'insertUnorderedList' || cmd === 'insertOrderedList') {
            document.execCommand(cmd, false, null);
          } else if (cmd === 'insertHTML') {
            var tagName = val || 'blockquote';
            document.execCommand('insertHTML', false, '<' + tagName + '>' + placeholder + '</' + tagName + '>');
            placeCaretAtEnd(input);
          } else if (cmd === 'bold' || cmd === 'italic' || cmd === 'underline' || cmd === 'strikethrough') {
            document.execCommand(cmd, false, null);
          } else if (cmd === 'formatBlock') {
            var fmt = val || 'p';
            if (ALLOWED_FORMATS.indexOf(fmt) !== -1) {
              document.execCommand(cmd, false, fmt);
            }
          } else {
            document.execCommand(cmd, false, val || null);
          }

          syncHiddenInput(input, hiddenInput);
          autoGrow(input);
          updatePlaceholder(input, placeholder);
        });
      });
    }

    // --- Auto-grow ---
    function autoGrow(el) {
      el.style.height = 'auto';
      el.style.height = el.scrollHeight + 'px';
      if (el.scrollHeight > window.innerHeight * 0.6) {
        el.style.maxHeight = '60vh';
        el.style.overflowY = 'auto';
      } else {
        el.style.maxHeight = '';
        el.style.overflowY = '';
      }
    }

    // --- Placeholder ---
    function updatePlaceholder(el, text) {
      if (! el.textContent.trim()) {
        el.setAttribute('data-dr-empty', 'true');
      } else {
        el.removeAttribute('data-dr-empty');
      }
    }

    // --- beforeinput: block disallowed formatBlock values ---
    input.addEventListener('beforeinput', function (e) {
      if (e.inputType === 'formatBlock' || e.inputType === 'formatInline' || e.inputType === 'insertHTML') {
        var cmd = e.inputType;
        var fmt = e.detail?.value || '';

        if (cmd === 'formatBlock' && ALLOWED_FORMATS.indexOf(fmt.toLowerCase()) === -1) {
          e.preventDefault();
          return false;
        }
      }
    });

    // --- Paste: plain text only ---
    input.addEventListener('paste', function (e) {
      e.preventDefault();
      var text = '';
      if (e.clipboardData && e.clipboardData.getData) {
        text = e.clipboardData.getData('text/plain');
      } else {
        text = window.clipboardData ? window.clipboardData.getData('text/plain') : '';
      }
      text = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
      document.execCommand('insertText', false, text);
    });

    // --- Input handler: auto-grow + placeholder + callback ---
    input.addEventListener('input', function () {
      autoGrow(input);
      updatePlaceholder(input, placeholder);
      syncHiddenInput(input, hiddenInput);
      if (typeof root._onContentChange === 'function') {
        root._onContentChange();
      }
    });

    // --- Focus / blur ---
    input.addEventListener('focus', function () {
      if (input.getAttribute('data-dr-empty') === 'true') {
        input.innerHTML = '';
        input.removeAttribute('data-dr-empty');
      }
    });

    input.addEventListener('blur', function () {
      updatePlaceholder(input, placeholder);
      syncHiddenInput(input, hiddenInput);
      if (typeof root._onBlur === 'function') {
        root._onBlur();
      }
    });

    function syncHiddenInput(from, to) {
      if (to) {
        to.value = from.innerHTML;
      }
    }

    function placeCaretAtEnd(el) {
      var range = document.createRange();
      var sel = window.getSelection();
      range.selectNodeContents(el);
      range.collapse(false);
      sel.removeAllRanges();
      sel.addRange(range);
      el.focus();
    }

    // Initial grow
    autoGrow(input);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      initAllEditors();
    });
  } else {
    initAllEditors();
  }
})();
