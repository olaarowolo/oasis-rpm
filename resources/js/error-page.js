/**
 * Error page behaviour: countdown redirect with a cancel option, copy to
 * clipboard for the support reference, and the optional error report form.
 * Everything degrades gracefully: with JS disabled the page still offers a
 * plain link back and a working form submission.
 */
(function () {
    'use strict';

    var countdownRoot = document.querySelector('[data-error-countdown-root]');
    var countdownLabel = document.querySelector('[data-error-countdown]');
    var cancelButton = document.querySelector('[data-error-cancel]');
    var ring = document.querySelector('[data-error-ring]');
    var returnLink = document.querySelector('[data-error-return]');

    function startCountdown() {
        if (!countdownRoot || !returnLink) {
            return;
        }

        var seconds = parseInt(countdownRoot.getAttribute('data-error-seconds') || '0', 10);
        if (!seconds || seconds < 1) {
            return;
        }

        var target = returnLink.getAttribute('href');
        var remaining = seconds;
        var circumference = ring ? parseFloat(ring.getAttribute('stroke-dasharray') || '0') : 0;
        var reduceMotion = window.matchMedia
            && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var frameId = null;
        var start = null;
        var cancelled = false;

        function render(value) {
            if (countdownLabel) {
                countdownLabel.textContent = String(value);
            }

            if (ring && circumference) {
                ring.setAttribute('stroke-dashoffset', String(circumference * (1 - value / seconds)));
            }
        }

        function finish() {
            if (cancelled) {
                return;
            }

            if (frameId !== null) {
                window.cancelAnimationFrame(frameId);
            }

            if (countdownLabel) {
                countdownLabel.textContent = '0';
            }

            window.location.assign(target);
        }

        function tick(timestamp) {
            if (cancelled) {
                return;
            }

            if (start === null) {
                start = timestamp;
            }

            var elapsed = (timestamp - start) / 1000;
            remaining = Math.max(0, seconds - elapsed);
            render(remaining);

            if (remaining <= 0) {
                finish();

                return;
            }

            frameId = window.requestAnimationFrame(tick);
        }

        function cancel() {
            cancelled = true;

            if (frameId !== null) {
                window.cancelAnimationFrame(frameId);
            }

            if (intervalId !== null) {
                window.clearInterval(intervalId);
            }

            countdownRoot.classList.add('opacity-60');
            countdownRoot.setAttribute('data-error-cancelled', 'true');

            var text = countdownRoot.querySelector('[data-error-countdown]');
            var message = text && text.parentElement;

            if (message) {
                message.textContent = 'Take your time. Use the buttons below when you are ready.';
            }
        }

        var intervalId = null;

        if (cancelButton) {
            cancelButton.addEventListener('click', cancel);
        }

        if (reduceMotion) {
            intervalId = window.setInterval(function () {
                remaining -= 1;
                render(remaining);

                if (remaining <= 0) {
                    window.clearInterval(intervalId);
                    finish();
                }
            }, 1000);

            return;
        }

        frameId = window.requestAnimationFrame(tick);
    }

    function setupCopy() {
        var copyButton = document.querySelector('[data-error-copy]');
        var copyLabel = document.querySelector('[data-error-copy-label]');
        var copyStatus = document.querySelector('[data-error-copy-status]');

        if (!copyButton) {
            return;
        }

        copyButton.addEventListener('click', function () {
            var value = copyButton.getAttribute('data-error-copy') || '';
            var fallback = function () {
                var input = document.createElement('textarea');
                input.value = value;
                input.setAttribute('readonly', 'readonly');
                input.style.position = 'fixed';
                input.style.opacity = '0';
                document.body.appendChild(input);
                input.select();

                try {
                    document.execCommand('copy');
                } catch (error) {
                    /* clipboard unavailable: the code is visible on the page */
                }

                document.body.removeChild(input);
            };

            var announce = function (message) {
                if (copyLabel) {
                    copyLabel.textContent = message;
                }

                if (copyStatus) {
                    copyStatus.textContent = message;
                }

                window.setTimeout(function () {
                    if (copyLabel) {
                        copyLabel.textContent = 'Copy reference';
                    }
                }, 2500);
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(value).then(function () {
                    announce('Copied');
                }, function () {
                    fallback();
                    announce('Copied');
                });

                return;
            }

            fallback();
            announce('Copied');
        });
    }

    function setupReportForm() {
        var form = document.querySelector('[data-error-report-form]');

        if (!form) {
            return;
        }

        var status = document.querySelector('[data-error-report-status]');
        var submit = document.querySelector('[data-error-report-submit]');
        var label = document.querySelector('[data-error-report-label]');
        var note = document.querySelector('[data-error-report-note]');

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            if (submit) {
                submit.disabled = true;
            }

            if (label) {
                label.textContent = 'Sending…';
            }

            if (status) {
                status.textContent = 'Sending your report…';
            }

            window.fetch(form.getAttribute('action'), {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            })
                .then(function (response) {
                    return response.json().then(function (payload) {
                        return { ok: response.ok, payload: payload };
                    });
                })
                .then(function (result) {
                    if (result.ok && result.payload && result.payload.success) {
                        if (status) {
                            status.textContent = result.payload.message || 'Sent. Our team will take a look.';
                        }

                        if (label) {
                            label.textContent = 'Report sent';
                        }

                        if (note) {
                            note.value = '';
                            note.disabled = true;
                        }

                        return;
                    }

                    throw new Error(
                        (result.payload && result.payload.message) || 'We could not send that report.'
                    );
                })
                .catch(function (error) {
                    if (status) {
                        status.textContent = (error && error.message) || 'We could not send that report.';
                    }

                    if (label) {
                        label.textContent = 'Try again';
                    }

                    if (submit) {
                        submit.disabled = false;
                    }
                });
        });
    }

    function ready() {
        startCountdown();
        setupCopy();
        setupReportForm();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ready);
    } else {
        ready();
    }
})();
