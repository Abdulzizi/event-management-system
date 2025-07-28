<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventRequest;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $query = Event::query();
        $relations = ['user', 'atendees', 'atendees.user'];

        foreach ($relations as $relation) {
            $query->when(
                $this->relationShouldLoad($relation),
                fn($q) => $q->with($relation)
            );
        }

        $page = request('page', 1);
        $perPage = request('perPage', 10);

        $event = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        return response()->success([
            'list' => EventResource::collection($event),
            'meta' => [
                // 'total' => $event->total(),
                // 'per_page' => $event->perPage(),
                // 'current_page' => $event->currentPage(),
                // 'last_page' => $event->lastPage(),
                // 'from' => $event->firstItem(),
                // 'to' => $event->lastItem(),
                // 'has_more_pages' => $event->hasMorePages(),
                'total' => $event->total(),
                'last_page' => $event->lastPage(),
                'current_page' => $event->currentPage(),
                'per_page' => $event->perPage(),
            ],
        ], 'List Event berhasil ditemukan');
    }

    protected function relationShouldLoad(string $relation): bool
    {
        $include = request()->query('include');

        if (!$include) {
            return false;
        }

        $relations = array_map('trim', explode(',', $include));

        return in_array($relation, $relations);
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

        $payload = $request->only([
            'user_id',
            'name',
            'description',
            'start_date',
            'end_date'
        ]);

        $event = Event::create($payload);

        return response()->success($event, 'Event berhasil dibuat');
    }

    public function show(string $id)
    {
        $event = Event::with('user')->find($id);

        if (!$event) {
            return response()->failed('Event tidak ditemukan');
        }

        return response()->success(new EventResource($event), 'Event berhasil ditemukan');
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

        $payload = $request->only([
            'user_id',
            'name',
            'description',
            'start_date',
            'end_date'
        ]);
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