<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Events\TransferRequestApproved;
use App\Events\TransferRequestRejected;
use App\Http\Controllers\Controller;
use App\Models\ResultTransferRequest;
use App\Services\ResultTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class AdminResultTransferController extends Controller
{
    public function __construct(
        private readonly ResultTransferService $transferService
    ) {}

    /**
     * Показать список всех запросов на перенос результатов.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $perPage = min((int) $request->query('per_page', 20), 50);

        $requests = $this->transferService->getRequestsForAdmin($status, $perPage);

        $stats = [
            'pending' => ResultTransferRequest::pending()->count(),
            'approved' => ResultTransferRequest::approved()->count(),
            'rejected' => ResultTransferRequest::rejected()->count(),
            'total' => ResultTransferRequest::count(),
        ];

        return view('admin.transfer-requests.index', compact('requests', 'stats', 'status'));
    }

    /**
     * Показать детали конкретного запроса на перенос результатов.
     */
    public function show(int $id): View
    {
        $transferRequest = ResultTransferRequest::with([
            'user:id,name,email',
            'sourceAthlete:id,admin_full_name,ironman_number,country_iso',
            'sourceAthlete.raceResults:id,user_profile_id,race_date,location,race_type,swim_time,t1_time,bike_time,t2_time,run_time,total_time,overall_position,age_group_position',
            'reviewedBy:id,name',
        ])->findOrFail($id);

        // Подсчитать результаты по типам гонок для атлета-донора
        $raceCounts = $transferRequest->sourceAthlete->raceResults
            ->groupBy(fn ($result) => $result->race_type->value)
            ->map(fn ($items) => $items->count());

        return view('admin.transfer-requests.show', compact('transferRequest', 'raceCounts'));
    }

    /**
     * Одобрить запрос на перенос результатов.
     */
    public function approve(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $request->validate([
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $transferRequest = ResultTransferRequest::findOrFail($id);

        if (!$transferRequest->canBeApproved()) {
            $message = 'Запрос не может быть одобрен. Проверьте статус запроса и атлета-донора.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        try {
            $approvedRequest = $this->transferService->approveRequest(
                $transferRequest,
                $request->user(),
                $request->input('comment')
            );

            // Dispatch event for FCM notification
            event(new TransferRequestApproved($approvedRequest));

            $message = 'Запрос на перенос результатов успешно одобрен.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'reload' => true,
                ]);
            }

            return back()->with('success', $message);
        } catch (InvalidArgumentException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Отклонить запрос на перенос результатов.
     */
    public function reject(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $transferRequest = ResultTransferRequest::findOrFail($id);

        if (!$transferRequest->canBeRejected()) {
            $message = 'Запрос не может быть отклонён.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        try {
            $rejectedRequest = $this->transferService->rejectRequest(
                $transferRequest,
                $request->user(),
                $request->input('comment')
            );

            // Dispatch event for FCM notification
            event(new TransferRequestRejected($rejectedRequest));

            $message = 'Запрос на перенос результатов отклонён.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'reload' => true,
                ]);
            }

            return back()->with('success', $message);
        } catch (InvalidArgumentException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Получить статистику запросов (для AJAX).
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'pending' => ResultTransferRequest::pending()->count(),
            'approved' => ResultTransferRequest::approved()->count(),
            'rejected' => ResultTransferRequest::rejected()->count(),
            'total' => ResultTransferRequest::count(),
        ];

        return response()->json(['data' => $stats]);
    }
}