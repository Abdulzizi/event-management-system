<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\EventRequest;
use App\Http\Resources\Event\EventResource;
use App\Http\Traits\CanLoadRelationship;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use CanLoadRelationship;

    private $relations = ['user', 'atendees', 'atendees.user'];


    public function index()
    {
        $query = $this->loadRelationShip(Event::query(), $this->relations);

        $page = request('page', 1);
        $perPage = request('perPage', 10);

        $event = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        return response()->success([
            'list' => EventResource::collection($event),
            'meta' => [
                'total' => $event->total(),
                'last_page' => $event->lastPage(),
                'current_page' => $event->currentPage(),
                'per_page' => $event->perPage(),
            ],
        ], 'List Event berhasil ditemukan');
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

        $this->loadRelationShip($event);

        return response()->success($event, 'Event berhasil dibuat');
    }

    public function show(string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->failed('Event tidak ditemukan');
        }

        $this->loadRelationShip($event);

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
            return response()->failed('Event tidak ditemukan', 404);
        }

        if (Gate::denies('update-event', $event)) {
            return response()->json([
                'status_code' => 403,
                'message' => 'Anda tidak memiliki akses untuk merubah event ini'
            ], 403);
        }

        $event->update($payload);

        $this->loadRelationShip($event);

        return response()->success($event, 'Event berhasil diupdate');
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