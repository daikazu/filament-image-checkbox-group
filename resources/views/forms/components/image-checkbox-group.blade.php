{{--
    Tailwind Classes:
    grid-cols-1 grid-cols-2 grid-cols-3 grid-cols-4 grid-cols-5 grid-cols-6
    grid-cols-7 grid-cols-8 grid-cols-9 grid-cols-10 grid-cols-11 grid-cols-12
    sm:grid-cols-1 sm:grid-cols-2 sm:grid-cols-3 sm:grid-cols-4 sm:grid-cols-5 sm:grid-cols-6
    sm:grid-cols-7 sm:grid-cols-8 sm:grid-cols-9 sm:grid-cols-10 sm:grid-cols-11 sm:grid-cols-12
    md:grid-cols-1 md:grid-cols-2 md:grid-cols-3 md:grid-cols-4 md:grid-cols-5 md:grid-cols-6
    md:grid-cols-7 md:grid-cols-8 md:grid-cols-9 md:grid-cols-10 md:grid-cols-11 md:grid-cols-12
    lg:grid-cols-1 lg:grid-cols-2 lg:grid-cols-3 lg:grid-cols-4 lg:grid-cols-5 lg:grid-cols-6
    lg:grid-cols-7 lg:grid-cols-8 lg:grid-cols-9 lg:grid-cols-10 lg:grid-cols-11 lg:grid-cols-12
    xl:grid-cols-1 xl:grid-cols-2 xl:grid-cols-3 xl:grid-cols-4 xl:grid-cols-5 xl:grid-cols-6
    xl:grid-cols-7 xl:grid-cols-8 xl:grid-cols-9 xl:grid-cols-10 xl:grid-cols-11 xl:grid-cols-12
    2xl:grid-cols-1 2xl:grid-cols-2 2xl:grid-cols-3 2xl:grid-cols-4 2xl:grid-cols-5 2xl:grid-cols-6
    2xl:grid-cols-7 2xl:grid-cols-8 2xl:grid-cols-9 2xl:grid-cols-10 2xl:grid-cols-11 2xl:grid-cols-12
--}}
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'){{ $isLive() ? '.live' : '.defer' }},
            minSelect: {{ $getMinSelect() ?? 'null' }},
            maxSelect: {{ $getMaxSelect() ?? 'null' }},
            required: {{ $isRequired() ? 'true' : 'false' }},

            init() {
                if (! Array.isArray(this.state)) {
                    this.state = [];
                }

                this.$watch('state', value => {
                    if (this.maxSelect !== null && value.length > this.maxSelect) {
                        this.state = value.slice(0, this.maxSelect);
                    }
                });
            },

            isSelected(value) {
                return this.state.includes(value);
            },

            toggleSelection(value) {
                if (this.isSelected(value)) {
                    this.state = this.state.filter(item => item !== value);
                    return;
                }

                if (this.maxSelect !== null && this.state.length >= this.maxSelect) {
                    return;
                }

                this.state = [...this.state, value];
            },

            canAddMore() {
                return this.maxSelect === null || this.state.length < this.maxSelect;
            },

            getSelectionText() {
                const count = this.state.length;

                if (count === 0) {
                    return 'None selected';
                }

                if (this.maxSelect === null) {
                    return `${count} selected`;
                }

                return `${count} of ${this.maxSelect} selected`;
            },

            getRequirementText() {
                if (!this.required) {
                    return 'Optional selection';
                }

                if (this.minSelect !== null && this.maxSelect !== null) {
                    return `Select ${this.minSelect} to ${this.maxSelect}`;
                }

                if (this.minSelect !== null) {
                    return `Select at least ${this.minSelect}`;
                }

                if (this.maxSelect !== null) {
                    return `Select up to ${this.maxSelect}`;
                }

                return 'Select at least one';
            }
        }"
        {{
            $attributes
                ->merge($getExtraAttributes())
                ->class(['filament-forms-image-checkbox-group-component space-y-2'])
                ->merge([
                    'role' => 'group',
                    'aria-label' => $getLabel(),
                    'aria-required' => $isRequired() ? 'true' : 'false',
                ])
         }}
    >
        <div
            class="text-sm text-gray-500 dark:text-gray-400 flex justify-between items-center"
            id="{{ $getId() }}-description"
        >
            <div x-text="getRequirementText()" aria-live="polite"></div>
            <div x-text="getSelectionText()" aria-live="polite"></div>
        </div>

        @php
            $columns = $getGridColumns();
            $activeClasses = [];

            // Add default (mobile-first) columns
            $activeClasses[] = 'grid-cols-' . $columns['default'];

            // Add responsive breakpoints
            foreach (['sm', 'md', 'lg', 'xl', '2xl'] as $breakpoint) {
                if (isset($columns[$breakpoint])) {
                    $activeClasses[] = $breakpoint . ':grid-cols-' . $columns[$breakpoint];
                }
            }

            $activeGridClasses = implode(' ', $activeClasses);
        @endphp

        <div
            class="grid gap-4 {{ $activeGridClasses }}"
            role="presentation"
        >
            @foreach ($getOptions() as $option)
                <button
                    type="button"
                    x-data="{ value: @js($option['value']) }"
                    x-bind:class="{
                        'ring-2 ring-primary-500 dark:ring-primary-500': isSelected(value),
                        'bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600': !isSelected(value),
                        'bg-primary-50 dark:bg-primary-900/20': isSelected(value),
                        'cursor-not-allowed opacity-70': !isSelected(value) && !canAddMore(),
                        'hover:border-primary-500 dark:hover:border-primary-500': canAddMore() || isSelected(value),
                        'group': true
                    }"
                    x-bind:disabled="!isSelected(value) && !canAddMore()"
                    x-bind:aria-checked="isSelected(value).toString()"
                    x-bind:aria-disabled="(!isSelected(value) && !canAddMore()).toString()"
                    aria-labelledby="{{ $getId() }}-{{ $loop->index }}-label"
                    aria-describedby="{{ $getId() }}-description"
                    role="checkbox"
                    class="relative h-full rounded-xl border-2 border-gray-200 dark:border-gray-600 transition-all duration-200 overflow-hidden flex flex-col focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 shadow-sm hover:shadow-md"
                    x-on:click="toggleSelection(value)"
                    x-on:keydown.space.prevent="toggleSelection(value)"
                    x-on:keydown.enter.prevent="toggleSelection(value)"
                    tabindex="0"
                >
                    @if ($option['image'])
                        <div
                            class="relative w-full aspect-square overflow-hidden bg-gray-100 dark:bg-gray-800"
                            role="presentation"
                        >
                            <img
                                src="{{ $option['image'] }}"
                                alt="{{ $option['label'] ?? $option['value'] }}"
                                class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105"
                                loading="lazy"
                            />
                            <div
                                x-show="isSelected(value)"
                                class="absolute inset-0 bg-primary-500/5 transition-opacity duration-200"
                                aria-hidden="true"
                            ></div>
                        </div>
                    @endif

                    @if ($option['label'])
                        <div
                            class="p-3 text-center flex-grow flex flex-col justify-center min-h-[3rem] opacity-0 group-hover:opacity-100 group-focus:opacity-100 absolute inset-0 bg-black/60 transition-opacity duration-200 z-10"
                            id="{{ $getId() }}-{{ $loop->index }}-label"
                        >
                            <span class="text-xs sm:text-sm font-semibold text-white line-clamp-2">
                                {{ $option['label'] }}
                            </span>
                        </div>
                    @endif

                    {{-- selection icon --}}
                    <div
                        x-show="isSelected(value)"
                        class="absolute top-2 right-2 bg-primary-500 text-white rounded-full p-1 shadow-sm ring-2 ring-white dark:ring-gray-900 z-20"
                        aria-hidden="true"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" role="presentation">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <!-- Disabled overlay - show when maxed out -->
                    <div
                        x-show="!isSelected(value) && !canAddMore()"
                        class="absolute inset-0 bg-gray-100/90 dark:bg-gray-800/90 backdrop-blur-[1px] flex items-center justify-center transition-opacity duration-200"
                        aria-hidden="true"
                    >
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300 px-2 py-1 bg-white/80 dark:bg-gray-700/80 rounded-full shadow-sm border border-gray-200 dark:border-gray-600">
                            Max selected
                        </span>
                    </div>
                </button>
            @endforeach
        </div>

        @if (!$isDisabled())
            <input
                type="hidden"
                x-model="state"
            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
            aria-hidden="true"
            />
        @endif
    </div>
</x-dynamic-component>







<?php
