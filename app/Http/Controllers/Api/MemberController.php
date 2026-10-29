<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::latest()->paginate(10);

        return MemberResource::collection($members);
    }

    public function show(string $id)
    {
        $member = Member::findOrFail($id);

        return new MemberResource($member);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:20|unique:members,nim',
            'email' => 'required|email|max:255|unique:members,email',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $member = Member::create($validated);

        return (new MemberResource($member))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'nim' => 'sometimes|required|string|max:20|unique:members,nim,'.$id,
            'email' => 'sometimes|required|email|max:255|unique:members,email,'.$id,
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'sometimes|required|in:aktif,nonaktif',
        ]);

        $member->update($validated);

        return new MemberResource($member);
    }

    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return response()->json(['message' => 'Anggota berhasil dihapus.']);
    }
}
