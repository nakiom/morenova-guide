<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuggestionRequest;
use App\Models\Suggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuggestionController extends Controller
{
    public function index(): View
    {
        $suggestions = Suggestion::with(['step.pathology', 'pathology'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->get();

        return view('admin.suggestions.index', compact('suggestions'));
    }

    public function store(StoreSuggestionRequest $request): RedirectResponse
    {
        Suggestion::create($request->suggestionPayload());

        if (! $request->user()) {
            \App\Support\SuggestionCaptcha::refresh();
        }

        return redirect()->back()->with('success', __('messages.suggestion_sent'));
    }

    public function update(Request $request, Suggestion $suggestion): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $suggestion->update(['status' => $request->status]);

        return redirect()->route('admin.suggestions.index')
            ->with('success', __('messages.suggestion_status_updated'));
    }
}
