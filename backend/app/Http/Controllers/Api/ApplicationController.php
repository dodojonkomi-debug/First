<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    // Рӯйхати аризаҳо (фильтрация аз рӯи нақш)
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Application::with(['school.district.region', 'user', 'documents']);

        switch ($user->role) {
            case 'superadmin':
                // Ҳама аризаҳо
                break;
            case 'admin_region':
                $query->whereHas('school.district', function ($q) use ($user) {
                    $q->where('region_id', $user->region_id);
                });
                break;
            case 'admin_district':
                $query->whereHas('school', function ($q) use ($user) {
                    $q->where('district_id', $user->district_id);
                });
                break;
            case 'admin_school':
                $query->where('school_id', $user->school_id);
                break;
            case 'parent':
                $query->where('user_id', $user->id);
                break;
        }

        // Филтрация аз рӯи статус
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ҷустуҷӯ аз рӯи код
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_code', 'like', "%{$search}%")
                  ->orWhere('child_first_name', 'like', "%{$search}%")
                  ->orWhere('child_last_name', 'like', "%{$search}%");
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($applications);
    }

    // Сохтани аризаи нав
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'child_first_name' => 'required|string|max:100',
            'child_last_name' => 'required|string|max:100',
            'child_middle_name' => 'nullable|string|max:100',
            'child_birth_date' => 'required|date',
            'child_gender' => 'required|in:male,female',
            'grade' => 'required|in:0,1',
            'parent_first_name' => 'required|string|max:100',
            'parent_last_name' => 'required|string|max:100',
            'parent_id_number' => 'required|string|max:50',
            'parent_phone' => 'required|string|max:20',
            'parent_email' => 'nullable|email',
            'residence_region_id' => 'required|exists:regions,id',
            'residence_district_id' => 'required|exists:districts,id',
            'residence_address' => 'required|string|max:500',
        ]);

        // Тафтиши санаи қабул (1 август – 1 сентябр)
        $now = now();
        $admissionStart = $now->copy()->setMonth(8)->setDay(1)->startOfDay();
        $admissionEnd = $now->copy()->setMonth(9)->setDay(1)->endOfDay();

        // Барои тест ин тафтишро комент мекунем
        // if ($now->lt($admissionStart) || $now->gt($admissionEnd)) {
        //     return response()->json(['message' => 'Санаи қабул: 1 август – 1 сентябр'], 422);
        // }

        $application = Application::create([
            ...$request->only([
                'school_id', 'child_first_name', 'child_last_name', 'child_middle_name',
                'child_birth_date', 'child_gender', 'grade',
                'parent_first_name', 'parent_last_name', 'parent_id_number',
                'parent_phone', 'parent_email',
                'residence_region_id', 'residence_district_id', 'residence_address',
            ]),
            'user_id' => $request->user()->id,
            'application_code' => Application::generateCode(),
            'status' => 'pending',
        ]);

        return response()->json([
            'data' => $application->load(['school.district.region', 'documents']),
            'message' => 'Ариза бо муваффақият сабт шуд.',
            'code' => $application->application_code,
        ], 201);
    }

    // Намоиш
    public function show(Request $request, Application $application): JsonResponse
    {
        $user = $request->user();

        if (!$user->canViewApplication($application)) {
            return response()->json(['message' => 'Дастрасӣ манъ аст.'], 403);
        }

        return response()->json([
            'data' => $application->load(['school.district.region', 'user', 'documents', 'reviewer']),
        ]);
    }

    // Боргузории ҳуҷҷат
    public function uploadDocument(Request $request, Application $application): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:birth_certificate,parent_id,medical_form,vaccination_card,residence_certificate',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // макс 10MB
        ]);

        $user = $request->user();
        if ($application->user_id !== $user->id && !$user->isSuperAdmin()) {
            return response()->json(['message' => 'Дастрасӣ манъ аст.'], 403);
        }

        $file = $request->file('file');
        $path = $file->store("documents/{$application->id}", 'public');

        // Нест кардани ҳуҷҷати қаблии ҳамин навъ
        $existing = $application->documents()->where('type', $request->type)->first();
        if ($existing) {
            Storage::disk('public')->delete($existing->file_path);
            $existing->delete();
        }

        $document = Document::create([
            'application_id' => $application->id,
            'type' => $request->type,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        // Навсозии чеклист
        $checklistField = 'has_' . $request->type;
        $application->update([$checklistField => true]);

        return response()->json(['data' => $document], 201);
    }

    // Тағйири статус (барои маъмурон)
    public function updateStatus(Request $request, Application $application): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:review,approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        $user = $request->user();

        if (!$user->canViewApplication($application)) {
            return response()->json(['message' => 'Дастрасӣ манъ аст.'], 403);
        }

        if ($user->role === 'parent') {
            return response()->json(['message' => 'Волидайн наметавонад статусро тағйир диҳад.'], 403);
        }

        $application->update([
            'status' => $request->status,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
        ]);

        return response()->json([
            'data' => $application->load(['school.district.region', 'documents', 'reviewer']),
            'message' => 'Статуси ариза тағйир ёфт.',
        ]);
    }

    // Тафтиши статус аз рӯи код (публикӣ)
    public function checkStatus(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $application = Application::where('application_code', $request->code)
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Ариза ёфт нашуд.'], 404);
        }

        return response()->json([
            'data' => [
                'application_code' => $application->application_code,
                'child_name' => $application->child_first_name . ' ' . $application->child_last_name,
                'school' => $application->school->name,
                'grade' => $application->grade,
                'status' => $application->status,
                'rejection_reason' => $application->rejection_reason,
                'submitted_at' => $application->created_at->format('Y-m-d H:i'),
                'reviewed_at' => $application->reviewed_at?->format('Y-m-d H:i'),
            ],
        ]);
    }

    // Статистика
    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Application::query();

        switch ($user->role) {
            case 'admin_region':
                $query->whereHas('school.district', fn($q) => $q->where('region_id', $user->region_id));
                break;
            case 'admin_district':
                $query->whereHas('school', fn($q) => $q->where('district_id', $user->district_id));
                break;
            case 'admin_school':
                $query->where('school_id', $user->school_id);
                break;
        }

        return response()->json([
            'data' => [
                'total' => $query->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'review' => (clone $query)->where('status', 'review')->count(),
                'approved' => (clone $query)->where('status', 'approved')->count(),
                'rejected' => (clone $query)->where('status', 'rejected')->count(),
            ],
        ]);
    }
}
