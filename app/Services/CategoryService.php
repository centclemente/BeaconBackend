<?php

namespace App\Services;
use App\Models\Category;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\CategoryResource;

class CategoryService
{
    use ApiResponse;
    public function getAll($data)
    {
        $status = $data['status'] ?? null;
        $pagination = $data['pagination'] ?? true;

        $category = Category::when($status == 'inactive', function ($query) {
            $query->onlyTrashed();
        })
            ->useFilters()
            ->dynamicPaginate();

        if(!$pagination){
            CategoryResource::collection($category);
        }else{
            $category=CategoryResource::collection($category);
        }
        return $category;
    }


    

    public function create(array $data)
    {

        $category = Category::create([
            'name' => $data['name'],
            'system_id' => $data['system_id']?? null,
        ]);

        return $category;

    }

    public function update(int $id, array $data)
    {
        $category = Category::find($id);

         if (!$category) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'Category']));
        }

        $category->update([
            'name' => $data['name'],
        ]);
        $category->load('systems');

        return $category;
    }

    public function toggleArchived(int $id)
    {
        $category = Category::withTrashed()->find($id);

        $trashed = $category->trashed();

        if (!$trashed) {
            $category->delete();
        } else {
            $category->restore();
        }

        return $category;
    }
}