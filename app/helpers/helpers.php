<?php
// app/Helpers/helpers.php

if (! function_exists('th_sort')) {
    /**
     * Generate link header yang bisa di-click untuk sorting.
     */
    function th_sort(string $label, string $field): string
    {
        $currentField = request('sort_by');
        $currentOrder = request('sort_order') === 'asc' ? 'asc' : 'desc';
        $newOrder     = ($currentField === $field && $currentOrder === 'asc')
                        ? 'desc'
                        : 'asc';

        $qs = array_merge(request()->query(), [
            'sort_by'    => $field,
            'sort_order' => $newOrder,
        ]);

        $arrow = $currentField === $field
               ? ($currentOrder === 'asc' ? ' ↑' : ' ↓')
               : '';

        $url = url()->current() . '?' . http_build_query($qs);

        return '<a href="' . e($url) . '" class="font-medium hover:underline">'
             . e($label) . $arrow
             . '</a>';
    }
}
