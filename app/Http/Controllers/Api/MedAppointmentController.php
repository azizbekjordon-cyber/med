<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorAppointment;
use App\Models\Med;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedAppointmentController extends Controller
{
    /**
     * Display a listing of appointments for a med card.
     */
    public function index(string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $appointments = $med->appointments()
            ->with(['doctor:id,name,specialty', 'clinic:id,name,address'])
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $appointments,
        ]);
    }

    /**
     * Check doctor available time slots and queue for a given date.
     */
    public function slots(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $doctor = User::where('id', $request->input('doctor_id'))
            ->where('role', 'doctor')
            ->with('clinic')
            ->firstOrFail();

        $date = $request->input('date');

        $standardSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '12:00', '12:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
        ];

        $booked = DoctorAppointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('queue_number', 'appointment_time')
            ->toArray();

        $slots = [];
        foreach ($standardSlots as $time) {
            $isBooked = isset($booked[$time]);
            $slots[] = [
                'time' => $time,
                'available' => ! $isBooked,
                'queue_number' => $isBooked ? $booked[$time] : null,
            ];
        }

        $nextQueueNumber = count($booked) + 1;

        return response()->json([
            'status' => 'success',
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialty' => $doctor->specialty ?? 'Mutaxassis',
                'clinic_name' => $doctor->clinic?->name ?? 'Markaziy Shifoxona',
                'room_number' => ($doctor->id * 102).' - xona',
            ],
            'date' => $date,
            'total_booked' => count($booked),
            'next_queue_number' => $nextQueueNumber,
            'slots' => $slots,
        ]);
    }

    /**
     * Book an appointment via API.
     */
    public function book(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'med_id' => 'required|exists:meds,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string|max:10',
            'reason' => 'nullable|string|max:500',
        ]);

        $med = Med::with('user')->findOrFail($validated['med_id']);
        $doctor = User::where('id', $validated['doctor_id'])
            ->where('role', 'doctor')
            ->firstOrFail();

        $existing = DoctorAppointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ushbu vaqt allaqachon boshqa bemor tomonidan band qilingan.',
            ], 422);
        }

        $currentCount = DoctorAppointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('status', '!=', 'cancelled')
            ->count();

        $queueNumber = $currentCount + 1;
        $ticketNumber = 'NAV-'.date('Y').'-'.str_pad((string) $queueNumber, 3, '0', STR_PAD_LEFT);
        $roomNumber = ($doctor->id * 102).' - xona';

        $appointment = DoctorAppointment::create([
            'med_id' => $med->id,
            'patient_id' => $med->user_id,
            'doctor_id' => $doctor->id,
            'clinic_id' => $doctor->clinic_id ?? 1,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'queue_number' => $queueNumber,
            'ticket_number' => $ticketNumber,
            'room_number' => $roomNumber,
            'reason' => $validated['reason'] ?? 'Shifokor ko\'rigi va maslahati',
            'status' => 'pending',
        ]);

        $med->logAccess(auth()->id(), 'online_appointment_booked_api', [
            'ticket_number' => $ticketNumber,
            'doctor' => $doctor->name,
            'date' => $validated['appointment_date'],
            'time' => $validated['appointment_time'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Navbat muvaffaqiyatli olindi.',
            'data' => $appointment->load(['doctor', 'clinic']),
        ], 201);
    }

    /**
     * Cancel an appointment via API.
     */
    public function cancel(int $id): JsonResponse
    {
        $appointment = DoctorAppointment::findOrFail($id);
        $appointment->cancel();

        return response()->json([
            'status' => 'success',
            'message' => 'Navbat bekor qilindi.',
            'data' => $appointment,
        ]);
    }
}
