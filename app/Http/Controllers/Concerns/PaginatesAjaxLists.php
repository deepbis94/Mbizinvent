<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait PaginatesAjaxLists
{
    protected function listPage(Request $request): array
    {
        $perPage = max(1, (int) $request->input('perPage', 10));
        $currentPage = max(1, (int) $request->input('currentPage', 1));
        $isFragment = $request->filled('perPage')
            || $request->filled('currentPage')
            || $request->has('searchString');

        return [
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'skip' => ($currentPage - 1) * $perPage,
            'isFragment' => $isFragment,
            'search' => trim((string) $request->input('searchString', '')),
        ];
    }

    protected function pageCount(int $totalRecords, int $perPage): int
    {
        return max(1, (int) ceil($totalRecords / $perPage));
    }
}
