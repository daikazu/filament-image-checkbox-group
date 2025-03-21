<?php

namespace Daikazu\FilamentImageCheckboxGroup\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class ImageCheckboxGroup extends Field
{
    protected string $view = 'filament-image-checkbox-group::forms.components.image-checkbox-group';

    protected array|Closure $options = [];

    protected int|Closure|null $minSelect = null;

    protected int|Closure|null $maxSelect = null;

    protected int|array|Closure|null $gridColumns = null;

    public function options(array|Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function minSelect(int|Closure|null $minSelect): static
    {
        $this->minSelect = $minSelect;

        return $this;
    }

    public function maxSelect(int|Closure|null $maxSelect): static
    {
        $this->maxSelect = $maxSelect;

        return $this;
    }

    public function gridColumns(int|array|Closure $columns): static
    {
        $this->gridColumns = $columns;

        return $this;
    }

    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

        $formattedOptions = [];

        foreach ($options as $value => $option) {
            if (is_array($option)) {
                $formattedOptions[] = [
                    'value' => $value,
                    'label' => $option['label'] ?? null,
                    'image' => $option['image'] ?? null,
                ];

                continue;
            }

            $formattedOptions[] = [
                'value' => $value,
                'label' => $option,
                'image' => null,
            ];
        }

        return $formattedOptions;
    }

    public function getMinSelect(): ?int
    {
        return $this->evaluate($this->minSelect) ?? config('filament-image-checkbox-group.default_min_select');
    }

    public function getMaxSelect(): ?int
    {
        return $this->evaluate($this->maxSelect) ?? config('filament-image-checkbox-group.default_max_select');
    }

    public function getGridColumns(): array
    {
        $columns = $this->evaluate($this->gridColumns);

        if ($columns === null) {
            return [
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
            ];
        }

        // If columns is an integer, convert to responsive array
        if (is_int($columns) || is_numeric($columns)) {
            $columns = (int) $columns;

            return [
                'default' => 1,
                'sm' => min($columns, 2),
                'md' => min($columns, 3),
                'lg' => min($columns, $columns),
            ];
        }

        $validBreakpoints = ['default', 'sm', 'md', 'lg', 'xl', '2xl'];
        $sanitizedColumns = [];

        // Ensure default is set
        $sanitizedColumns['default'] = max(1, min(12, (int) ($columns['default'] ?? 1)));

        foreach ($validBreakpoints as $breakpoint) {
            if ($breakpoint !== 'default' && isset($columns[$breakpoint])) {
                $sanitizedColumns[$breakpoint] = max(1, min(12, (int) $columns[$breakpoint]));
            }
        }

        return $sanitizedColumns;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(function (ImageCheckboxGroup $component, $state): void {
            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->dehydrateStateUsing(function ($state) {
            if (! is_array($state)) {
                return [];
            }

            return $state;
        });

        // Always ensure it's an array
        $this->rule('array');

        // Apply min/max validation only when required
        $this->rule(function (ImageCheckboxGroup $component): ?string {
            if (! $component->isRequired()) {
                return null;
            }

            $min = $component->getMinSelect();

            if ($min !== null) {
                return "min:{$min}";
            }

            return 'min:1'; // Default minimum of 1 when required
        });

        // Always apply max validation if set
        $this->rule(function (ImageCheckboxGroup $component): ?string {
            $max = $component->getMaxSelect();

            if ($max !== null) {
                return "max:{$max}";
            }

            return null;
        });
    }
}
