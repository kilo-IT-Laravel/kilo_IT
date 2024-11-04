<?php

namespace App\pagination;

class Paginating
{
    public function metadata($datas)
    {
        return [
             
           
                'totalPages' => $datas->lastPage(),
                'hasNextPage' => $datas->hasMorePages(), 
                'pageSize' => $datas->perPage(),
                'current_page' => $datas->currentPage(),
                'totalItems' => $datas->total(), 
            
        ];
    }

}
