<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgressRequest;
use App\Models\Category;
use App\Models\Progress;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $totalProgress = Progress::count();
        $completedProgress = Progress::where('status', 'done')->count();

        $progressPercentage = ($completedProgress / $totalProgress) * 100;

        return $this->responseSuccess(__('messages.display', ['module' => 'Progress']), [
            'total' => $totalProgress,
            'completed' => $completedProgress,
            'pending' => $totalProgress - $completedProgress,
            'holding' => Progress::where('status', 'hold')->count(),
            'percentage' => round($progressPercentage) . '%',
        ]);
    }



    public function store(ProgressRequest $request)
    {
        $validated = $request->validated();
        $createdProgress = [];
        $duplicates = [];

        foreach ($validated['categories'] as $category) {
            $categoryRecord = Category::where('name', $category['categoryName'])->first();

            foreach ($category['progress'] as $progressData) {

                $exists = Progress::where('system_id', $validated['system_id'])
                    ->where('description', $progressData['description'])
                    ->where('raised_date', $progressData['raised_date'])
                    ->exists();
                if ($exists) {
                    $duplicates[] = $progressData['description'];

                }

                if (count($duplicates) > 0) {
                    return $this->responseUnprocessable('Duplicate progress entries found: ' . implode(', ', $duplicates));
                }
                $progress = Progress::create([
                    'system_id' => $validated['system_id'],
                    'category_id' => $categoryRecord->id,
                    'description' => $progressData['description'],
                    'raised_date' => $progressData['raised_date'],
                    'target_date' => $progressData['target_date'] ?? null,
                    'end_date' => $progressData['end_date'] ?? null,
                    'status' => $progressData['status'] ?? 'pending',
                    'remarks' => $progressData['remarks'] ?? null,
                ]);

                $createdProgress[] = $progress;
            }
        }

        return $this->responseSuccess(__('messages.created', ['module' => 'Progress']), $createdProgress);
    }

    public function show($id)

    {
        $progress= Progress::where('system_id', $id)->count();

        if ($progress === 0) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'Progress']));
        }

        $doneProgress = Progress::where('system_id', $id)->where('status', 'done')->count();

        $progressPercentage = ($doneProgress / $progress) * 100;

        return $this->responseSuccess(__('messages.display', ['module' => 'Progress']), [
            'total' => $progress,
            'completed' => $doneProgress,
            'Remaining' => $progress - $doneProgress,
            'holding' => Progress::where('system_id', $id)->where('status', 'hold')->count(),
            'percentage' => round($progressPercentage) . '%',
        ]);


    }

    public function update(Request $request, $id)
    {
        $progress = Progress::find($id);

        if (!$progress) {
            return $this->responseNotFound(__('messages.not_found', ['module' => 'Progress']));
        }

        $request->validate([
            'status' => 'required|in:done,pending,hold',
        ]);

        $progress->status = $request->input('status');
        $progress->save();

        return $this->responseSuccess(__('messages.updated', ['module' => 'Progress']), $progress);
    }

}
