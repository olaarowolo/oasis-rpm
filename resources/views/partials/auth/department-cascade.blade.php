@php
    $dropdownType = $dropdownType ?? 'department';
    $universityCode = $universityCode ?? '';
    $hasStructured = $hasStructured ?? false;
    $selectedDepartment = $selectedDepartment ?? '';
    $inputName = $inputName ?? 'department';
    $required = $required ?? false;
    $cascadeId = 'department-cascade-' . $dropdownType;
@endphp

<div id="{{ $cascadeId }}" class="department-cascade-container"
     data-has-structured="{{ $hasStructured ? 'true' : 'false' }}"
     data-university-code="{{ $universityCode }}">
    
    {{-- Free-text fallback (shown when not structured or JS fails) --}}
    <div class="department-free-text {{ $hasStructured ? 'hidden' : '' }}">
        <input type="text" name="{{ $inputName }}" value="{{ $selectedDepartment }}" {{ $required ? 'required' : '' }}
               class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200"
               placeholder="Enter department name">
    </div>

    {{-- Structured cascade wrapper (shown via JS for structured universities) --}}
    <div class="department-cascade-wrapper hidden space-y-3">
        {{-- Faculty/School Select --}}
        <div>
            <label class="block text-sm font-medium text-slate-700">Faculty / School</label>
            <div class="relative mt-1.5">
                <select id="department-faculty-{{ $dropdownType }}"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200 appearance-none pr-10"
                        onchange="onFacultyChange('{{ $dropdownType }})"
                        aria-label="Select faculty or school">
                    <option value="">Select faculty or school</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fa-solid fa-chevron-down text-slate-400"></i>
                </div>
            </div>
        </div>

        {{-- Department Select --}}
        <div id="department-department-wrapper-{{ $dropdownType }}">
            <label class="block text-sm font-medium text-slate-700">Department</label>
            <div class="relative mt-1.5">
                <select id="department-department-{{ $dropdownType }}"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200 appearance-none pr-10 hidden"
                        onchange="onDepartmentChange('{{ $dropdownType }})"
                        aria-label="Select department">
                    <option value="">Select department</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fa-solid fa-chevron-down text-slate-400"></i>
                </div>
            </div>
        </div>

        {{-- Hidden input that actually gets submitted --}}
        <input type="hidden" id="department-hidden-{{ $dropdownType }}" name="{{ $inputName }}" value="{{ $selectedDepartment }}" {{ $required ? 'required' : '' }}>
    </div>
</div>

<script>
    // Initialize department cascade when this partial loads
    document.addEventListener('DOMContentLoaded', function () {
        initDepartmentCascade('{{ $dropdownType }}', '{{ $universityCode }}');
    });

    // Also handle if the partial is loaded after DOMContentLoaded (e.g., via AJAX)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initDepartmentCascade('{{ $dropdownType }}', '{{ $universityCode }}');
        });
    } else {
        initDepartmentCascade('{{ $dropdownType }}', '{{ $universityCode }}');
    }
</script>