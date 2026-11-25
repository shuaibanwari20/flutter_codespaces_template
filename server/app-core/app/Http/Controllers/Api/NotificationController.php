<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Support\DariDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** GET /api/notifications — targeted notifications + read state. */
    public function index(Request $request): JsonResponse
    {
        $midwife = $request->user();

        $notifications = Notification::query()
            ->where(function ($q) use ($midwife) {
                $q->where('target', 'all')
                    ->orWhere(function ($q2) use ($midwife) {
                        $q2->where('target', 'cluster')->where('cluster_id', $midwife->cluster_id);
                    })
                    ->orWhere(function ($q2) use ($midwife) {
                        $q2->where('target', 'midwife')->where('midwife_id', $midwife->id);
                    });
            })
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($n) use ($midwife) {
                $read = $n->readers()
                    ->where('midwife_id', $midwife->id)
                    ->whereNotNull('notification_reads.read_at')
                    ->exists();

                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'body' => $n->body,
                    'target' => $n->target,
                    'is_management' => $n->target === 'all',
                    'date' => DariDate::full($n->created_at),
                    'time' => DariDate::time24($n->created_at),
                    'read' => $read,
                ];
            });

        $unread = $notifications->where('read', false)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread' => $unread,
                'notifications' => $notifications->values(),
            ],
        ]);
    }

    /** POST /api/notifications/{id}/read */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $midwife = $request->user();
        $notification = Notification::findOrFail($id);

        if (! $notification->isRelevantFor($midwife)) {
            return response()->json(['success' => false, 'message' => 'اعلان یافت نشد.'], 404);
        }

        \DB::table('notification_reads')->updateOrInsert(
            ['notification_id' => $id, 'midwife_id' => $midwife->id],
            ['read_at' => now()]
        );

        return response()->json(['success' => true, 'message' => 'خوانده شد.']);
    }
}
