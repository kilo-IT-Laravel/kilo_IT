<?php

namespace App\pagination;

class Paginating
{
    public function metadata($datas)
    {
        return [
            'current_page' => $datas->currentPage(),
            'page_size' => $datas->perPage(),
            'total_items' => $datas->total(),
            'total_pages' => $datas->lastPage(),
            'has_next' => $datas->hasMorePages(),
            'has_prev' => !$datas->onFirstPage(),
        ];
    }
}
