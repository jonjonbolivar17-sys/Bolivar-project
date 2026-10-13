<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncidentCategory;
use App\Models\SystemLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IncidentCategoryController extends Controller
{
    public function index(): View
    {
        $categories = IncidentCategory::query()
            ->withCount('incidentReports')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:incident_categories,name'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $category = IncidentCategory::query()->create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'is_active' => true,
        ]);

        SystemLog::query()->create([
            'user_id' => $request->user()->id,
            'action' => 'incident_category_created',
            'description' => "Created incident category {$category->name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Incident category created.');
    }

    public function edit(IncidentCategory $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, IncidentCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('incident_categories', 'name')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        SystemLog::query()->create([
            'user_id' => $request->user()->id,
            'action' => 'incident_category_updated',
            'description' => "Updated incident category {$category->name}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Incident category updated.');
    }

    public function destroy(Request $request, IncidentCategory $category): RedirectResponse
    {
        if ($category->incidentReports()->exists()) {
            return back()->with('error', 'This category is used by reports. Deactivate it instead of deleting it.');
        }

        SystemLog::query()->create([
            'user_id' => $request->user()->id,
            'action' => 'incident_category_deleted',
            'description' => "Deleted incident category {$category->name}.",
            'ip_address' => $request->ip(),
        ]);

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Incident category deleted.');
    }
}
