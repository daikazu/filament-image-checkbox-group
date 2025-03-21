<?php

return [
    /*
   |--------------------------------------------------------------------------
   | Default Grid Columns
   |--------------------------------------------------------------------------
   |
   | This option controls the default number of grid columns to use for the
   | image checkbox group component. You can override this on a per-component
   | basis using the gridColumns() method.
   |
   | Values must be between 2 and 12 for compatibility with Tailwind's grid system.
   | The component will automatically clamp values outside this range.
   |
   */
    'default_grid_columns' => 4,

    /*
    |--------------------------------------------------------------------------
    | Default Min Select
    |--------------------------------------------------------------------------
    |
    | This option controls the default minimum number of options that must be
    | selected when the component is required. null means at least 1 when
    | required. You can override this on a per-component basis using the
    | minSelect() method.
    |
    */
    'default_min_select' => null,

    /*
    |--------------------------------------------------------------------------
    | Default Max Select
    |--------------------------------------------------------------------------
    |
    | This option controls the default maximum number of options that can be
    | selected. null means no limit. You can override this on a per-component
    | basis using the maxSelect() method.
    |
    */
    'default_max_select' => null,
];
