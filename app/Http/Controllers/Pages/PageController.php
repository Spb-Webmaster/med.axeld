<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class PageController extends Controller
{
    protected function pageSuffix(LengthAwarePaginator $items): string
    {
        $page = $items->currentPage();

        return $page > 1 ? ' — Страница ' . $page : '';
    }
}
