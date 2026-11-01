<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Tool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function tools()
    {
        return $this->listing(Tool::class, 'tools');
    }

    public function quizzes()
    {
        return $this->listing(Quiz::class, 'quizzes');
    }

    public function updateTools(Request $request)
    {
        $this->apply($request, Tool::class);

        return redirect()->route('admin.tools.index')->with('status', '工具的排序与状态已保存。');
    }

    public function updateQuizzes(Request $request)
    {
        $this->apply($request, Quiz::class);

        return redirect()->route('admin.quizzes.index')->with('status', '测试的排序与状态已保存。');
    }

    private function listing(string $model, string $kind)
    {
        return view('admin.catalog', [
            'kind' => $kind,
            'items' => $model::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    private function apply(Request $request, string $model): void
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['array'],
            'items.*.sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'items.*.is_active' => ['required', 'boolean'],
        ]);

        foreach ($data['items'] as $id => $row) {
            /** @var Model $record */
            $record = $model::find((int) $id);

            if (! $record) {
                continue;
            }

            $record->update([
                'sort_order' => (int) $row['sort_order'],
                'is_active' => (bool) $row['is_active'],
            ]);
        }
    }
}
