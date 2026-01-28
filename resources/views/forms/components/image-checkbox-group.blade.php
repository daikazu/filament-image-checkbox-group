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

@php
    $isLive = $isLive();
    $compactLabels = $getCompactLabels();
@endphp

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
            state: $wire.entangle('{{ $getStatePath() }}'),
            minSelect: {{ $getMinSelect() ?? 'null' }},
            maxSelect: {{ $getMaxSelect() ?? 'null' }},
            required: {{ $isRequired() ? 'true' : 'false' }},
            isLive: @js($isLive),
            init() {
                if (! Array.isArray(this.state)) {
                    this.state = [];
                }

                this.$watch('state', value => {
                    if (!Array.isArray(value)) {
                        this.state = [];
                        return;
                    }

                    if (this.maxSelect !== null && value.length > this.maxSelect) {
                        this.state = value.slice(0, this.maxSelect);
                    }

                    if (this.isLive) {
                        $wire.call('$refresh');
                    }
                });
            },

            getState() {
                return Array.isArray(this.state) ? this.state : [];
            },

            isSelected(value) {
                return this.getState().includes(value);
            },

            toggleSelection(value) {
                const currentState = this.getState();

                if (this.isSelected(value)) {
                    this.state = currentState.filter(item => item !== value);
                    return;
                }

                if (this.maxSelect !== null && currentState.length >= this.maxSelect) {
                    return;
                }

                this.state = [...currentState, value];
            },

            canAddMore() {
                return this.maxSelect === null || this.getState().length < this.maxSelect;
            },

            getSelectionText() {
                const count = this.getState().length;

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
            class="text-sm text-gray-500 dark:text-gray-400 flex justify-between items-center select-none"
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
            class="grid gap-4 items-stretch {{ $activeGridClasses }}"
            role="presentation"
        >
            @foreach ($getOptions() as $option)
                @php
                    $hasImage = !empty($option['image']);
                    $hasLabel = !empty($option['label']);
                    $displayLabel = $option['label'] ?? $option['value'];
                    $showImageInline = $hasImage && !$compactLabels;
                    $showCompactButton = $compactLabels || !$hasImage;
                    $showImagePopover = $compactLabels && $hasImage;
                @endphp

                {{-- Checkbox Button --}}
                <button
                    type="button"
                    x-data="{
                        value: @js($option['value']),
                        @if ($showImagePopover)
                        popoverOpen: false,
                        popoverPosition: 'top',
                        popoverTimeout: null,
                        showPopover() {
                            clearTimeout(this.popoverTimeout);
                            this.calculatePosition();
                            this.popoverOpen = true;
                        },
                        hidePopover() {
                            this.popoverTimeout = setTimeout(() => {
                                this.popoverOpen = false;
                            }, 100);
                        },
                        calculatePosition() {
                            const rect = this.$el.getBoundingClientRect();
                            const popoverHeight = 160;
                            const popoverWidth = 160;
                            const padding = 16;

                            const spaceAbove = rect.top;
                            const spaceBelow = window.innerHeight - rect.bottom;
                            const spaceLeft = rect.left;
                            const spaceRight = window.innerWidth - rect.right;

                            if (spaceAbove >= popoverHeight + padding) {
                                this.popoverPosition = 'top';
                            } else if (spaceBelow >= popoverHeight + padding) {
                                this.popoverPosition = 'bottom';
                            } else if (spaceRight >= popoverWidth + padding) {
                                this.popoverPosition = 'right';
                            } else if (spaceLeft >= popoverWidth + padding) {
                                this.popoverPosition = 'left';
                            } else {
                                this.popoverPosition = 'top';
                            }
                        },
                        @endif
                    }"
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
                    @class([
                        'relative h-full rounded-xl border-2 border-gray-200 dark:border-gray-600 transition-all duration-200 motion-reduce:transition-none flex flex-col focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 shadow-sm hover:shadow-md',
                        'overflow-hidden' => !$showImagePopover,
                        'overflow-visible' => $showImagePopover,
                    ])
                    x-on:click="toggleSelection(value)"
                    x-on:keydown.space.prevent="toggleSelection(value)"
                    x-on:keydown.enter.prevent="toggleSelection(value)"
                    @if ($showImagePopover)
                    x-on:mouseenter="showPopover()"
                    x-on:mouseleave="hidePopover()"
                    x-on:focusin="showPopover()"
                    x-on:focusout="hidePopover()"
                    @endif
                    tabindex="0"
                >
                    @if ($showImageInline)
                        {{-- Image variant (non-compact mode with image) --}}
                        <div
                            class="relative w-full aspect-square overflow-hidden bg-gray-100 dark:bg-gray-800"
                            role="presentation"
                        >
                            <img
                                src="{{ $option['image'] }}"
                                alt=""
                                aria-hidden="true"
                                class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105 motion-reduce:transition-none"
                                loading="lazy"
                            />
                            <div
                                x-show="isSelected(value)"
                                x-cloak
                                class="absolute inset-0 bg-primary-500/5 transition-opacity duration-200 motion-reduce:transition-none"
                                aria-hidden="true"
                            ></div>
                        </div>

                        {{-- Accessible label for image variant (visible on hover/focus, always available to screen readers) --}}
                        <div
                            class="p-3 text-center flex-grow flex flex-col justify-center min-h-[3rem] absolute inset-0 bg-black/60 z-10 transition-opacity duration-200 motion-reduce:transition-none"
                            x-bind:class="{
                                'opacity-0 group-hover:opacity-100 group-focus:opacity-100 group-focus-within:opacity-100': true
                            }"
                            id="{{ $getId() }}-{{ $loop->index }}-label"
                        >
                            <span class="text-xs sm:text-sm font-semibold text-white line-clamp-2">
                                {{ $displayLabel }}
                            </span>
                            {{-- Screen reader only status --}}
                            <span class="sr-only" x-text="isSelected(value) ? ', selected' : ', not selected'"></span>
                        </div>
                    @endif

                    @if ($showCompactButton)
                        {{-- Compact button variant --}}
                        <div
                            @class([
                                'w-full flex-1 flex items-center justify-center transition-colors duration-200 motion-reduce:transition-none rounded-lg',
                                'aspect-square p-4' => !$compactLabels,
                                'py-3 px-4' => $compactLabels,
                            ])
                            x-bind:class="{
                                'bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/20': isSelected(value),
                                'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700': !isSelected(value)
                            }"
                            id="{{ $getId() }}-{{ $loop->index }}-label"
                        >
                            <span
                                @class([
                                    'font-semibold text-center transition-colors duration-200 motion-reduce:transition-none',
                                    'text-sm line-clamp-3' => !$compactLabels,
                                    'text-sm' => $compactLabels,
                                ])
                                x-bind:class="{
                                    'text-primary-700 dark:text-primary-300': isSelected(value),
                                    'text-gray-700 dark:text-gray-200 group-hover:text-primary-600 dark:group-hover:text-primary-400': !isSelected(value)
                                }"
                            >
                                {{ $displayLabel }}
                            </span>
                            {{-- Screen reader only status --}}
                            <span class="sr-only" x-text="isSelected(value) ? ', selected' : ', not selected'"></span>
                        </div>

                        @if ($showImagePopover)
                            {{-- Image popover for compact mode --}}
                            <div
                                x-show="popoverOpen"
                                x-cloak
                                x-transition:enter="transition ease-out duration-150 motion-reduce:duration-0"
                                x-transition:enter-start="opacity-0 scale-95 motion-reduce:scale-100"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100 motion-reduce:duration-0"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95 motion-reduce:scale-100"
                                x-bind:class="{
                                    'bottom-full left-1/2 -translate-x-1/2 mb-2': popoverPosition === 'top',
                                    'top-full left-1/2 -translate-x-1/2 mt-2': popoverPosition === 'bottom',
                                    'left-full top-1/2 -translate-y-1/2 ml-2': popoverPosition === 'right',
                                    'right-full top-1/2 -translate-y-1/2 mr-2': popoverPosition === 'left'
                                }"
                                class="absolute z-50 w-40 h-40 rounded-lg shadow-xl overflow-hidden ring-1 ring-gray-900/10 dark:ring-white/10 bg-white dark:bg-gray-800 pointer-events-none"
                                aria-hidden="true"
                                role="tooltip"
                            >
                                <img
                                    src="{{ $option['image'] }}"
                                    alt=""
                                    class="w-full h-full object-cover"
                                />
                                {{-- Popover arrow --}}
                                <div
                                    x-bind:class="{
                                        'bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 rotate-45': popoverPosition === 'top',
                                        'top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 rotate-45': popoverPosition === 'bottom',
                                        'left-0 top-1/2 -translate-y-1/2 -translate-x-1/2 rotate-45': popoverPosition === 'right',
                                        'right-0 top-1/2 -translate-y-1/2 translate-x-1/2 rotate-45': popoverPosition === 'left'
                                    }"
                                    class="absolute w-3 h-3 bg-white dark:bg-gray-800 ring-1 ring-gray-900/10 dark:ring-white/10"
                                    aria-hidden="true"
                                ></div>
                            </div>
                        @endif
                    @endif

                    {{-- Selection checkmark icon --}}
                    <div
                        x-show="isSelected(value)"
                        x-cloak
                        class="absolute top-2 right-2 bg-primary-500 text-white rounded-full p-1 shadow-sm ring-2 ring-white dark:ring-gray-900 z-20"
                        aria-hidden="true"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" role="presentation">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    {{-- Disabled overlay - show when maxed out --}}
                    <div
                        x-show="!isSelected(value) && !canAddMore()"
                        x-cloak
                        class="absolute inset-0 bg-gray-100/90 dark:bg-gray-800/90 backdrop-blur-[1px] flex items-center justify-center transition-opacity duration-200 motion-reduce:transition-none"
                        aria-hidden="true"
                    >
                        <span
                            class="text-xs font-medium text-gray-600 dark:text-gray-300 px-2 py-1 bg-white/80 dark:bg-gray-700/80 rounded-full shadow-sm border border-gray-200 dark:border-gray-600"
                            role="status"
                        >
                            Max selected
                        </span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
</x-dynamic-component>
