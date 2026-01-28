<?php

use Daikazu\FilamentImageCheckboxGroup\Forms\Components\ImageCheckboxGroup;

describe('ImageCheckboxGroup', function () {

    describe('options', function () {

        it('formats simple string options correctly', function () {
            $component = ImageCheckboxGroup::make('test')
                ->options([
                    'option1' => 'Label 1',
                    'option2' => 'Label 2',
                ]);

            $options = $component->getOptions();

            expect($options)->toHaveCount(2);
            expect($options[0])->toBe([
                'value' => 'option1',
                'label' => 'Label 1',
                'image' => null,
            ]);
            expect($options[1])->toBe([
                'value' => 'option2',
                'label' => 'Label 2',
                'image' => null,
            ]);
        });

        it('formats array options with image and label correctly', function () {
            $component = ImageCheckboxGroup::make('test')
                ->options([
                    'cat' => [
                        'label' => 'Cat',
                        'image' => '/images/cat.jpg',
                    ],
                    'dog' => [
                        'label' => 'Dog',
                        'image' => '/images/dog.jpg',
                    ],
                ]);

            $options = $component->getOptions();

            expect($options)->toHaveCount(2);
            expect($options[0])->toBe([
                'value' => 'cat',
                'label' => 'Cat',
                'image' => '/images/cat.jpg',
            ]);
            expect($options[1])->toBe([
                'value' => 'dog',
                'label' => 'Dog',
                'image' => '/images/dog.jpg',
            ]);
        });

        it('handles options with missing label or image', function () {
            $component = ImageCheckboxGroup::make('test')
                ->options([
                    'partial' => [
                        'image' => '/images/partial.jpg',
                    ],
                ]);

            $options = $component->getOptions();

            expect($options[0])->toBe([
                'value' => 'partial',
                'label' => null,
                'image' => '/images/partial.jpg',
            ]);
        });

        it('handles empty options array', function () {
            $component = ImageCheckboxGroup::make('test')
                ->options([]);

            expect($component->getOptions())->toBe([]);
        });

    });

    describe('minSelect and maxSelect', function () {

        it('returns null when min/max not set and no config default', function () {
            $component = ImageCheckboxGroup::make('test');

            expect($component->getMinSelect())->toBeNull();
            expect($component->getMaxSelect())->toBeNull();
        });

        it('returns the configured minSelect value', function () {
            $component = ImageCheckboxGroup::make('test')
                ->minSelect(2);

            expect($component->getMinSelect())->toBe(2);
        });

        it('returns the configured maxSelect value', function () {
            $component = ImageCheckboxGroup::make('test')
                ->maxSelect(5);

            expect($component->getMaxSelect())->toBe(5);
        });

        it('supports closure for minSelect', function () {
            $component = ImageCheckboxGroup::make('test')
                ->minSelect(fn () => 3);

            expect($component->getMinSelect())->toBe(3);
        });

        it('supports closure for maxSelect', function () {
            $component = ImageCheckboxGroup::make('test')
                ->maxSelect(fn () => 10);

            expect($component->getMaxSelect())->toBe(10);
        });

    });

    describe('gridColumns', function () {

        it('returns default grid columns when not configured', function () {
            $component = ImageCheckboxGroup::make('test');

            expect($component->getGridColumns())->toBe([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
            ]);
        });

        it('converts integer to responsive grid columns', function () {
            $component = ImageCheckboxGroup::make('test')
                ->gridColumns(6);

            expect($component->getGridColumns())->toBe([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 6,
            ]);
        });

        it('accepts custom breakpoint array', function () {
            $component = ImageCheckboxGroup::make('test')
                ->gridColumns([
                    'default' => 2,
                    'sm' => 3,
                    'md' => 4,
                    'lg' => 5,
                    'xl' => 6,
                ]);

            expect($component->getGridColumns())->toBe([
                'default' => 2,
                'sm' => 3,
                'md' => 4,
                'lg' => 5,
                'xl' => 6,
            ]);
        });

        it('clamps column values between 1 and 12', function () {
            $component = ImageCheckboxGroup::make('test')
                ->gridColumns([
                    'default' => 0,
                    'sm' => 15,
                    'md' => -5,
                ]);

            $columns = $component->getGridColumns();

            expect($columns['default'])->toBe(1);
            expect($columns['sm'])->toBe(12);
            expect($columns['md'])->toBe(1);
        });

        it('ignores invalid breakpoints', function () {
            $component = ImageCheckboxGroup::make('test')
                ->gridColumns([
                    'default' => 2,
                    'invalid' => 5,
                    'sm' => 3,
                ]);

            $columns = $component->getGridColumns();

            expect($columns)->not->toHaveKey('invalid');
            expect($columns)->toHaveKey('default');
            expect($columns)->toHaveKey('sm');
        });

        it('supports closure for gridColumns', function () {
            $component = ImageCheckboxGroup::make('test')
                ->gridColumns(fn () => 4);

            expect($component->getGridColumns())->toBe([
                'default' => 1,
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
            ]);
        });

    });

    describe('fluent interface', function () {

        it('supports method chaining', function () {
            $component = ImageCheckboxGroup::make('test')
                ->options(['a' => 'A', 'b' => 'B'])
                ->minSelect(1)
                ->maxSelect(2)
                ->gridColumns(4);

            expect($component)->toBeInstanceOf(ImageCheckboxGroup::class);
            expect($component->getOptions())->toHaveCount(2);
            expect($component->getMinSelect())->toBe(1);
            expect($component->getMaxSelect())->toBe(2);
        });

    });

    describe('component configuration', function () {

        it('has correct view path', function () {
            $component = ImageCheckboxGroup::make('test');

            $reflection = new ReflectionClass($component);
            $property = $reflection->getProperty('view');
            $property->setAccessible(true);

            expect($property->getValue($component))
                ->toBe('filament-image-checkbox-group::forms.components.image-checkbox-group');
        });

        it('sets default empty array state', function () {
            $component = ImageCheckboxGroup::make('test');

            expect($component->getDefaultState())->toBe([]);
        });

        it('can set required', function () {
            $component = ImageCheckboxGroup::make('test')
                ->required();

            expect($component->isRequired())->toBeTrue();
        });

        it('can set label', function () {
            $component = ImageCheckboxGroup::make('test')
                ->label('Select Items');

            expect($component->getLabel())->toBe('Select Items');
        });

        it('uses name as default label when not set', function () {
            $component = ImageCheckboxGroup::make('test_field');

            expect($component->getLabel())->toBe('Test field');
        });

    });

    describe('options with closures', function () {

        it('evaluates closure for options', function () {
            $component = ImageCheckboxGroup::make('test')
                ->options(fn () => [
                    'dynamic1' => 'Dynamic Option 1',
                    'dynamic2' => 'Dynamic Option 2',
                ]);

            $options = $component->getOptions();

            expect($options)->toHaveCount(2);
            expect($options[0]['value'])->toBe('dynamic1');
            expect($options[1]['value'])->toBe('dynamic2');
        });

    });

    describe('numeric option keys', function () {

        it('handles numeric keys correctly', function () {
            $component = ImageCheckboxGroup::make('test')
                ->options([
                    1 => 'One',
                    2 => 'Two',
                    3 => 'Three',
                ]);

            $options = $component->getOptions();

            expect($options)->toHaveCount(3);
            expect($options[0]['value'])->toBe(1);
            expect($options[1]['value'])->toBe(2);
            expect($options[2]['value'])->toBe(3);
        });

        it('handles mixed key types', function () {
            $component = ImageCheckboxGroup::make('test')
                ->options([
                    'string_key' => 'String',
                    123 => 'Numeric',
                ]);

            $options = $component->getOptions();

            expect($options[0]['value'])->toBe('string_key');
            expect($options[1]['value'])->toBe(123);
        });

    });

    describe('compactLabels', function () {

        it('returns false by default', function () {
            $component = ImageCheckboxGroup::make('test');

            expect($component->getCompactLabels())->toBeFalse();
        });

        it('can be set to true', function () {
            $component = ImageCheckboxGroup::make('test')
                ->compactLabels();

            expect($component->getCompactLabels())->toBeTrue();
        });

        it('can be set to false explicitly', function () {
            $component = ImageCheckboxGroup::make('test')
                ->compactLabels(false);

            expect($component->getCompactLabels())->toBeFalse();
        });

        it('supports closure', function () {
            $component = ImageCheckboxGroup::make('test')
                ->compactLabels(fn () => true);

            expect($component->getCompactLabels())->toBeTrue();
        });

        it('supports method chaining', function () {
            $component = ImageCheckboxGroup::make('test')
                ->options(['a' => 'A', 'b' => 'B'])
                ->compactLabels()
                ->gridColumns(4)
                ->minSelect(1);

            expect($component)->toBeInstanceOf(ImageCheckboxGroup::class);
            expect($component->getCompactLabels())->toBeTrue();
            expect($component->getOptions())->toHaveCount(2);
            expect($component->getMinSelect())->toBe(1);
        });

    });

});
