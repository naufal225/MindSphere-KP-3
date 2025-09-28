<?php

namespace App\Http\Services;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

class SchoolClassService
{
    public function getAll($search = null)
    {
        $query = SchoolClass::with('teacher');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->latest()->paginate(10);
    }

    public function create(array $data)
    {
        if (!empty($data['teacher_id'])) {
            $teacher = User::where('id', $data['teacher_id'])->where('role', 'guru')->first();
            if (!$teacher) {
                throw new InvalidArgumentException('Guru tidak ditemukan.');
            }
        }

        return SchoolClass::create($data);
    }

    public function findById($id)
    {
        $class = SchoolClass::with(['teacher', 'students'])->find($id);

        if (!$class) {
            throw new ModelNotFoundException("Kelas dengan ID {$id} tidak ditemukan.");
        }

        return $class;
    }

    public function update($id, array $data)
    {
        $class = $this->findById($id);

        if (!empty($data['teacher_id'])) {
            $teacher = User::where('id', $data['teacher_id'])->where('role', 'guru')->first();
            if (!$teacher) {
                throw new InvalidArgumentException('Guru tidak ditemukan.');
            }
        }

        $class->update($data);
        return $class;
    }

    public function delete($id)
    {
        $class = $this->findById($id);
        $class->delete();
    }
}
