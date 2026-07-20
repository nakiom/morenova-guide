<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UpcomingVisitController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $period = $request->string('period', 'week')->toString();
        if (! in_array($period, ['week', 'month', 'all'], true)) {
            $period = 'week';
        }

        [$from, $to, $periodLabel] = $this->periodBounds($period);

        $query = Patient::query()
            ->forUser($user)
            ->withUpcomingVisit()
            ->with(['pathology.translations', 'user.center'])
            ->where(function ($q) use ($from, $to) {
                // Incluye vencidas (pendientes) + las del período.
                $q->whereDate('expected_next_visit_at', '<', $from->toDateString());

                $q->orWhere(function ($inner) use ($from, $to) {
                    $inner->whereDate('expected_next_visit_at', '>=', $from->toDateString());
                    if ($to) {
                        $inner->whereDate('expected_next_visit_at', '<=', $to->toDateString());
                    }
                });
            });

        $canFilterCenter = $user->isAdmin();
        $canFilterDoctor = $user->isAdmin() || ($user->center_id && ! $user->isSubUser());

        if ($canFilterCenter && $request->filled('center_id')) {
            $centerId = (int) $request->input('center_id');
            $query->whereHas('user', fn ($q) => $q->where('center_id', $centerId));
        }

        if ($canFilterDoctor && $request->filled('user_id')) {
            $doctorId = (int) $request->input('user_id');
            $query->where('user_id', $doctorId);
        }

        $patients = $query
            ->orderBy('expected_next_visit_at')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        $centers = $canFilterCenter
            ? Center::orderBy('name')->get(['id', 'name'])
            : collect();

        $doctors = $this->doctorsForFilters($user, $request->integer('center_id') ?: null);

        return view('clinical.upcoming.index', compact(
            'patients',
            'user',
            'period',
            'periodLabel',
            'from',
            'to',
            'centers',
            'doctors',
            'canFilterCenter',
            'canFilterDoctor',
        ));
    }

    /**
     * @return array{0: Carbon, 1: ?Carbon, 2: string}
     */
    private function periodBounds(string $period): array
    {
        $today = now()->startOfDay();

        return match ($period) {
            'month' => [
                $today->copy(),
                $today->copy()->addMonth()->endOfDay(),
                __('messages.period_month'),
            ],
            'all' => [
                $today->copy(),
                null,
                __('messages.period_all_upcoming'),
            ],
            default => [
                $today->copy(),
                $today->copy()->addDays(6)->endOfDay(),
                __('messages.period_week'),
            ],
        };
    }

    private function doctorsForFilters(User $user, ?int $centerId)
    {
        $query = User::query()
            ->where('is_active', true)
            ->orderBy('name');

        if ($user->isAdmin()) {
            if ($centerId) {
                $query->where('center_id', $centerId);
            }
        } elseif ($user->center_id && ! $user->isSubUser()) {
            $query->where('center_id', $user->center_id);
        } else {
            return collect([$user]);
        }

        return $query->get(['id', 'name', 'center_id']);
    }
}
