@props(['text', 'position' => 'top'])

<div x-data="tooltip('{{ $text }}', '{{ $position }}')"
     @mouseenter="mouseenter()"
     @mouseleave="mouseleave()"
     class="tooltip-container inline-block">

    {{-- Trigger Element --}}
    <span {{ $attributes->merge(['class' => 'cursor-help']) }}>
        {{ $slot }}
    </span>

    {{-- Tooltip --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="tooltip"
         :class="{
             'bottom-full mb-2': '{{ $position }}' === 'top',
             'top-full mt-2': '{{ $position }}' === 'bottom',
             'right-full mr-2': '{{ $position }}' === 'left',
             'left-full ml-2': '{{ $position }}' === 'right'
         }"
         x-cloak>
        <span x-text="text"></span>
    </div>
</div>
