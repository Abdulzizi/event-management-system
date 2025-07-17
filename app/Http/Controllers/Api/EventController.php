<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventRequest;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $page = request('page', 1);
        $perPage = request('perPage', 15);

        return response()->json([
            'success' => true,
            'data' => Event::paginate($perPage, ['*'], 'page', $page)
        ]);
    }

    public function store(EventRequest $request)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['user_id', 'name', 'description', 'start_date', 'end_date']);

        $event = Event::create($payload);

        return response()->success($event, 'Event berhasil dibuat');
    }

    public function show(string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->failed('Event tidak ditemukan');
        }

        return response()->success($event, 'Event berhasil ditemukan');
    }

    public function update(EventRequest $request, string $id)
    {
        /**
         * Menampilkan pesan error ketika validasi gagal
         * pengaturan validasi bisa dilihat pada class app/Http/request/User/CreateRequest
         */
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['user_id', 'name', 'description', 'start_date', 'end_date']);
        $event = Event::find($id);

        if (!$event) {
            return response()->failed('Event tidak ditemukan');
        }

        $event->update($payload);

        return response()->success($event, 'Event berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->failed('Event tidak ditemukan');
        }

        $event->delete();

        return response()->success(null, 'Event berhasil dihapus');
    }
}