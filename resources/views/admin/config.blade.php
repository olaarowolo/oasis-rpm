@extends('layouts.admin')

@section('title', 'System Configuration | TheOAsis Research Supervision System')

@section('content')
    <section class="rounded-3xl p-6 sm:p-8 text-white shadow-xl bg-gradient-to-br from-slate-950 via-academic-900 to-academic-800 relative overflow-hidden">
      <div class="absolute inset-0 opacity-25 bg-[radial-gradient(circle_at_top_right,_rgba(245,158,11,0.55),_transparent_30%),radial-gradient(circle_at_bottom_left,_rgba(56,189,248,0.35),_transparent_28%)]"></div>
      <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="max-w-2xl space-y-3">
          <p class="text-xs uppercase tracking-[0.3em] text-amber-300 font-semibold">Tools & system</p>
          <h2 class="text-3xl sm:text-4xl font-black leading-tight">Keep Gemini, email, and platform settings in sync with the current university scope.</h2>
          <p class="text-slate-300 text-sm sm:text-base">Each card writes directly to the existing admin configuration API, so changes persist without leaving this page.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold">{{ $configCount ?? 0 }} saved keys</span>
          <span class="px-3 py-1.5 rounded-full bg-white/10 border border-white/15 text-xs font-semibold">{{ $currentUniversity->name ?? 'No university selected' }}</span>
        </div>
      </div>
    </section>

    <section class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-4">
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
          <h3 class="font-bold text-lg text-slate-900 dark:text-white">Configuration scope</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400">Select the university whose settings you want to edit.</p>
        </div>
        <div class="flex items-center gap-3">
          <select id="university-switcher" class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-sm">
            @foreach($universities as $university)
              <option value="{{ $university->id }}" @selected((string) $selectedUniversityId === (string) $university->id)>{{ $university->name }} ({{ $university->code }})</option>
            @endforeach
          </select>
          <button type="button" onclick="switchUniversity()" class="px-4 py-2.5 rounded-xl bg-academic-700 hover:bg-academic-800 text-white text-sm font-semibold">Load</button>
        </div>
      </div>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-4" id="general-settings">
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">General and platform</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">App identity and operational defaults.</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-academic-100 dark:bg-academic-900/30 text-academic-700 dark:text-academic-300 flex items-center justify-center">
            <i class="fa-solid fa-sliders"></i>
          </div>
        </div>

        <form class="config-form space-y-3" data-config-group="general">
          <label class="block text-sm">
            <span class="font-semibold text-slate-700 dark:text-slate-300">App name</span>
            <input name="app_name" value="{{ $configMap['app_name'] ?? config('app.name') }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
          </label>
          <label class="block text-sm">
            <span class="font-semibold text-slate-700 dark:text-slate-300">Default university code</span>
            <input name="default_university_code" value="{{ $configMap['default_university_code'] ?? ($currentUniversity->code ?? 'LASU') }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
          </label>
          <div class="flex justify-end">
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold">Save general settings</button>
          </div>
        </form>
      </article>

      <article id="ai-settings" class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Gemini Assistant</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">AI model, temperature, and token settings.</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 flex items-center justify-center">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
          </div>
        </div>

        <form class="config-form space-y-3" data-config-group="ai">
          <label class="block text-sm">
            <span class="font-semibold text-slate-700 dark:text-slate-300">Primary model</span>
            <input name="gemini_model" value="{{ $configMap['gemini_model'] ?? 'gemini-1.5-pro' }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
          </label>
          <label class="block text-sm">
            <span class="font-semibold text-slate-700 dark:text-slate-300">Fallback model</span>
            <input name="gemini_fallback_model" value="{{ $configMap['gemini_fallback_model'] ?? 'gemini-1.5-flash' }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
          </label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="block text-sm">
              <span class="font-semibold text-slate-700 dark:text-slate-300">Temperature</span>
              <input name="gemini_temperature" type="number" step="0.1" value="{{ $configMap['gemini_temperature'] ?? 0.7 }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
            </label>
            <label class="block text-sm">
              <span class="font-semibold text-slate-700 dark:text-slate-300">Max tokens</span>
              <input name="gemini_max_tokens" type="number" value="{{ $configMap['gemini_max_tokens'] ?? 2048 }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
            </label>
          </div>
          <div class="flex items-center gap-2 text-sm">
            <input id="ai_assistant_enabled" name="ai_assistant_enabled" type="checkbox" value="1" class="rounded border-slate-300 text-academic-700 focus:ring-academic-700" @checked(($configMap['ai_assistant_enabled'] ?? '1') === '1' || ($configMap['ai_assistant_enabled'] ?? true) === true)>
            <label for="ai_assistant_enabled" class="font-semibold text-slate-700 dark:text-slate-300">Enable Gemini Assistant</label>
          </div>
          <div class="flex justify-end">
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-semibold">Save AI settings</button>
          </div>
        </form>
      </article>

      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Email and notifications</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Outbound mail and external notification hooks.</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 flex items-center justify-center">
            <i class="fa-solid fa-envelope"></i>
          </div>
        </div>

        <form class="config-form space-y-3" data-config-group="email">
          <label class="block text-sm">
            <span class="font-semibold text-slate-700 dark:text-slate-300">Mail host</span>
            <input name="mail_host" value="{{ $configMap['mail_host'] ?? config('mail.mailers.smtp.host') }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
          </label>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="block text-sm">
              <span class="font-semibold text-slate-700 dark:text-slate-300">Mail port</span>
              <input name="mail_port" type="number" value="{{ $configMap['mail_port'] ?? config('mail.mailers.smtp.port') }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
            </label>
            <label class="block text-sm">
              <span class="font-semibold text-slate-700 dark:text-slate-300">Mail username</span>
              <input name="mail_username" value="{{ $configMap['mail_username'] ?? config('mail.mailers.smtp.username') }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
            </label>
          </div>
          <label class="block text-sm">
            <span class="font-semibold text-slate-700 dark:text-slate-300">From address</span>
            <input name="mail_from_address" type="email" value="{{ $configMap['mail_from_address'] ?? config('mail.from.address') }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
          </label>
          <label class="block text-sm">
            <span class="font-semibold text-slate-700 dark:text-slate-300">Google Chat webhook</span>
            <input name="google_chat_webhook_url" value="{{ $configMap['google_chat_webhook_url'] ?? ($currentUniversity->google_chat_webhook_url ?? '') }}" class="mt-1 w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5">
          </label>
          <div class="flex items-center gap-2 text-sm">
            <input id="google_chat_enabled" name="google_chat_enabled" type="checkbox" value="1" class="rounded border-slate-300 text-academic-700 focus:ring-academic-700" @checked(($configMap['google_chat_enabled'] ?? '1') === '1' || ($configMap['google_chat_enabled'] ?? true) === true)>
            <label for="google_chat_enabled" class="font-semibold text-slate-700 dark:text-slate-300">Enable Google Chat alerts</label>
          </div>
          <div class="flex justify-end">
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold">Save notification settings</button>
          </div>
        </form>
      </article>

      <article class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="font-bold text-lg text-slate-900 dark:text-white">Current keys</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Saved configuration values for this university scope.</p>
          </div>
          <div class="w-11 h-11 rounded-xl bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 flex items-center justify-center">
            <i class="fa-solid fa-database"></i>
          </div>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
          <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/40 text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
              <tr>
                <th class="px-4 py-3">Key</th>
                <th class="px-4 py-3">Value</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
              @forelse($configEntries as $config)
                <tr>
                  <td class="px-4 py-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $config->config_key }}</td>
                  <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300 break-all">{{ is_array($configMap[$config->config_key] ?? null) ? json_encode($configMap[$config->config_key]) : ($configMap[$config->config_key] ?? '') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">No configuration has been saved for this university yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </article>
    </section>

    <p id="config-feedback" class="hidden rounded-2xl border px-4 py-3 text-sm"></p>

  <script>
    const csrfToken = @json(csrf_token());
    const selectedUniversityId = @json($selectedUniversityId);

    async function apiRequest(path, options = {}) {
      const response = await fetch(path, {
        method: options.method || 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          ...(options.headers || {}),
        },
        credentials: 'same-origin',
        body: options.body ? JSON.stringify(options.body) : undefined,
      });

      const payload = await response.json().catch(() => ({}));
      if (!response.ok || payload.success === false) {
        throw new Error(payload.message || 'Request failed');
      }
      return payload;
    }

    function switchUniversity() {
      const universityId = document.getElementById('university-switcher').value;
      const query = universityId ? ('?university_id=' + encodeURIComponent(universityId)) : '';
      window.location.href = '{{ route('admin.config') }}' + query;
    }

    document.querySelectorAll('.config-form').forEach(function (form) {
      form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const feedback = document.getElementById('config-feedback');
        const formData = new FormData(form);
        const entries = Array.from(formData.entries());
        const configPairs = entries.filter(function ([key]) {
          return key !== 'ai_assistant_enabled' && key !== 'google_chat_enabled';
        });

        try {
          for (const [key, value] of configPairs) {
            await apiRequest('/api/admin/config', {
              method: 'POST',
              body: {
                university_id: selectedUniversityId,
                config_key: key,
                config_value: value,
              }
            });
          }

          const booleanKeys = ['ai_assistant_enabled', 'google_chat_enabled'];
          for (const key of booleanKeys) {
            const checkbox = form.querySelector('[name="' + key + '"]');
            if (!checkbox) {
              continue;
            }

            await apiRequest('/api/admin/config', {
              method: 'POST',
              body: {
                university_id: selectedUniversityId,
                config_key: key,
                config_value: checkbox.checked ? '1' : '0',
                data_type: 'boolean',
              }
            });
          }

          feedback.textContent = 'Configuration saved successfully.';
          feedback.className = 'rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/30 dark:bg-emerald-900/20 dark:text-emerald-300 px-4 py-3 text-sm';
          feedback.classList.remove('hidden');
          window.setTimeout(function () { window.location.reload(); }, 600);
        } catch (error) {
          feedback.textContent = error.message || 'Unable to save configuration.';
          feedback.className = 'rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/30 dark:bg-rose-900/20 dark:text-rose-300 px-4 py-3 text-sm';
          feedback.classList.remove('hidden');
        }
      });
    });
  </script>
@endsection