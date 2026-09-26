@props([
    'sectionId',
    'name' => 'content',
    'value' => '',
    'placeholder' => 'Start writing...',
    'disabled' => false,
    'minWords' => null,
    'maxWords' => null,
    'locked' => false,
])

@php
    $disabled = $disabled || $locked;
@endphp

<div {{ $attributes->merge(['class' => 'dr-editor']) }} data-dr-editor data-section-id="{{ $sectionId }}">
    <div class="dr-toolbar flex flex-wrap gap-1 p-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-t-xl">
        <button type="button" class="dr-btn" data-dr-cmd="bold" title="Bold" aria-label="Bold"><i class="fa-solid fa-bold"></i></button>
        <button type="button" class="dr-btn" data-dr-cmd="italic" title="Italic" aria-label="Italic"><i class="fa-solid fa-italic"></i></button>
        <button type="button" class="dr-btn" data-dr-cmd="underline" title="Underline" aria-label="Underline"><i class="fa-solid fa-underline"></i></button>
        <button type="button" class="dr-btn" data-dr-cmd="strikethrough" title="Strikethrough" aria-label="Strikethrough"><i class="fa-solid fa-strikethrough"></i></button>
        <span class="dr-separator w-px h-5 bg-slate-300 dark:bg-slate-600 self-center mx-1"></span>
        <button type="button" class="dr-btn" data-dr-cmd="formatBlock" data-dr-value="h2" title="Heading 2" aria-label="Heading 2"><i class="fa-solid fa-heading"></i></button>
        <button type="button" class="dr-btn" data-dr-cmd="formatBlock" data-dr-value="h3" title="Heading 3" aria-label="Heading 3"><i class="fa-solid fa-heading"></i><sub style="font-size:8px;">3</sub></button>
        <button type="button" class="dr-btn" data-dr-cmd="formatBlock" data-dr-value="p" title="Paragraph" aria-label="Paragraph"><i class="fa-solid fa-paragraph"></i></button>
        <span class="dr-separator w-px h-5 bg-slate-300 dark:bg-slate-600 self-center mx-1"></span>
        <button type="button" class="dr-btn" data-dr-cmd="insertHTML" data-dr-value="<blockquote>" title="Quote" aria-label="Insert quote"><i class="fa-solid fa-quote-left"></i></button>
        <button type="button" class="dr-btn" data-dr-cmd="insertUnorderedList" title="Bullet list" aria-label="Bullet list"><i class="fa-solid fa-list-ul"></i></button>
        <button type="button" class="dr-btn" data-dr-cmd="insertOrderedList" title="Numbered list" aria-label="Numbered list"><i class="fa-solid fa-list-ol"></i></button>
        <button type="button" class="dr-btn" data-dr-cmd="undo" title="Undo" aria-label="Undo"><i class="fa-solid fa-undo"></i></button>
        <button type="button" class="dr-btn" data-dr-cmd="redo" title="Redo" aria-label="Redo"><i class="fa-solid fa-redo"></i></button>
    </div>

    <div class="dr-input"
         data-dr-input
         contenteditable="{{ $disabled ? 'false' : 'true' }}"
         data-dr-placeholder="{{ $placeholder }}"
         style="min-height:120px;max-height:60vh;overflow-y:auto;padding:12px 14px;border:1px solid #cbd5e1;dark:border-slate-600;border-t:0;border-b-left-radius:0;border-b-right-radius:0;">
        @if($value)
            {!! $value !!}
        @else
            <!-- placeholder managed by JS -->
        @endif
    </div>

    @if (! $disabled)
    <input type="hidden" name="{{ $name }}" data-dr-hidden-input value="">
    @endif
</div>

@if ($minWords !== null || $maxWords !== null)
<div class="dr-wordcount hidden absolute top-0 right-0 text-xs text-slate-400 dark:text-slate-500">
    @if ($minWords !== null && $maxWords !== null)
        Target: {{ $minWords }}–{{ $maxWords }} words
    @elseif ($minWords !== null)
        Minimum: {{ $minWords }} words
    @elseif ($maxWords !== null)
        Maximum: {{ $maxWords }} words
    @endif
</div>
@endif
