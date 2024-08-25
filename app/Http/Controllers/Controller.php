<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Prepare the query with search and filter conditions.
     *
     * @param Request $request
     * @param Builder $query
     * @return Builder
     */
    protected function prepSubject(Request $request, Builder $query): Builder
    {
        $model = $query->getModel();

        // Handle search functionality
        if ($request->has('search')) {
            $searchValue = '%' . $request->input('search') . '%';

            // Get searchable fields from model
            $searchKeys = method_exists($model, 'getSearchable') ? $model->getSearchable() : [];

            if (!empty($searchKeys)) {
                $query->where(function ($query) use ($searchValue, $searchKeys) {
                    foreach ($searchKeys as $key) {
                        $query->orWhere($key, 'like', $searchValue);
                    }
                });
            }
        }

        // Handling filtering functionality for model defined filterable columns
        if ($request->has('filter')) {
            $filters = $request->input('filter');

            // Get filterable fields from model
            $filterableKeys = method_exists($model, 'getFilterable') ? $model->getFilterable() : [];

            foreach ($filters as $field => $value) {
                if (in_array($field, $filterableKeys)) {
                    $query->where($field, $value);
                }

                /**
                 * due to time shortage i am directly adding the category filtering
                 * without making it dynamic
                 */
                if ($field === 'category') {

                    $categoryId = Category::where('slug', $value)
                        ->pluck('id')
                        ->first();
                    if ($categoryId) {
                        $query->where('category_id', $categoryId);
                    }
                }
            }
        }

        return $query;
    }

    /**
     * Paginate the results of a query.
     *
     * @param Builder $query
     * @param int $perPage
     * @return mixed
     */
    protected function paginate(Builder $query, int $perPage = 30)
    {
        // Fetch paginated results
        return $query->paginate($perPage)->appends(['per_page' => $perPage]);
    }

    /**
     * Run the index functionality with pagination and search.
     *
     * @param Request $request
     * @param Builder $query
     * @param string $collectionName
     * @return mixed
     * @throws Exception
     */
    protected function runIndex(Request $request, $query, $collection_name)
    {
        // Prepare the query with filtering and searching
        $query = $this->prepSubject($request, $query);

        // Paginate the results
        $response = $query
            ->paginate($request->input('per_page', 30))
            ->appends(['per_page' => $request->input('per_page', 30)]);

        return new $collection_name($response);
    }
}
