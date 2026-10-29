<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'member_id' => $this->member_id,
            'user_id' => $this->user_id,
            'member' => [
                'id' => $this->member?->id,
                'nama' => $this->member?->nama,
                'nim' => $this->member?->nim,
                'email' => $this->member?->email,
            ],
            'petugas' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
            'books' => $this->loanItems->map(function ($item) {
                return [
                    'id' => $item->book?->id,
                    'judul' => $item->book?->judul,
                    'penulis' => $item->book?->penulis,
                    'penerbit' => $item->book?->penerbit,
                    'tahun_terbit' => $item->book?->tahun_terbit,
                ];
            }),
            'tanggal_pinjam' => $this->tanggal_pinjam,
            'tanggal_kembali' => $this->tanggal_kembali,
            'tanggal_dikembalikan' => $this->tanggal_dikembalikan,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
