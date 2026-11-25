<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Midwife;
use App\Support\DariDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/login — code + PIN. Binds device_id on first login.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'pin' => ['required', 'string', 'min:4', 'max:8'],
            'device_id' => ['nullable', 'string', 'max:120'],
        ]);

        $code = strtoupper(trim($data['code']));

        $midwife = Midwife::with('cluster')->where('code', $code)->first();

        if (! $midwife || ! Hash::check($data['pin'], $midwife->pin_hash)) {
            throw ValidationException::withMessages([
                'code' => ['کد قابله یا پین نادرست است. لطفاً دوباره تلاش کنید.'],
            ]);
        }

        if (! $midwife->active) {
            return response()->json([
                'success' => false,
                'message' => 'حساب شما غیرفعال شده است. با سوپروایزر تماس بگیرید.',
            ], 403);
        }

        // Device binding — one active device per midwife.
        $deviceId = $data['device_id'] ?? null;
        if ($deviceId !== null) {
            if ($midwife->device_id !== null && $midwife->device_id !== $deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'این حساب به دستگاه دیگری متصل است. برای تغییر دستگاه با سوپروایزر تماس بگیرید.',
                ], 409);
            }
            if ($midwife->device_id === null) {
                $midwife->device_id = $deviceId;
                $midwife->save();
            }
        }

        $token = $midwife->createToken('mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'خوش آمدید!',
            'data' => [
                'token' => $token,
                'midwife' => $this->midwifePayload($midwife),
                'cluster' => $this->clusterPayload($midwife),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'از حساب خارج شدید.',
        ]);
    }

    private function midwifePayload(Midwife $midwife): array
    {
        return [
            'id' => $midwife->id,
            'code' => DariDate::en($midwife->code),
            'full_name' => $midwife->full_name,
            'phone' => DariDate::en((string) $midwife->phone),
            'leave_balance' => $midwife->leave_balance,
            'initial' => mb_substr($midwife->full_name, 0, 1),
        ];
    }

    private function clusterPayload(Midwife $midwife): ?array
    {
        $cluster = $midwife->cluster;
        if (! $cluster) {
            return null;
        }

        return [
            'id' => $cluster->id,
            'code' => DariDate::en($cluster->code),
            'number' => DariDate::en((string) (int) preg_replace('/\D+/', '', $cluster->code)),
            'name' => $cluster->name,
            'village' => $cluster->village,
            'display' => $cluster->displayLabel(),
            'lat' => (float) $cluster->lat,
            'lng' => (float) $cluster->lng,
            'radius_m' => (int) $cluster->radius_m,
        ];
    }
}
