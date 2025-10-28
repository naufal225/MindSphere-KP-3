<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChallengeRequest;
use App\Http\Services\ChallengeService;
use App\Models\Category;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    public function __construct(private ChallengeService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $challenges = $this->service->getAll($request, 10);
            $stats = $this->service->getChallengeStats();
            $categories = Category::all();
            $challengeTypes = \App\Enums\ChallengeType::cases();

            return view('admin.challenges.index', compact('challenges', 'stats', 'categories', 'challengeTypes'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $categories = Category::all();
            return view('admin.challenges.create', compact('categories'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function store(ChallengeRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            $this->service->create($data);
            return redirect()->route('admin.challenges.index')->with('success', 'Tantangan berhasil dibuat.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $result = $this->service->findById($id);
            return view('admin.challenges.show', [
                'challenge' => $result['challenge'],
                'participants' => $result['participants']
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            $categories = Category::all();

            $challenge = $this->service->findById($id)['challenge'];
            return view('admin.challenges.edit', compact('challenge', 'categories'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(ChallengeRequest $request, string $id): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['updated_by'] = auth()->id();
            $this->service->update($id, $data);
            return redirect()->route('admin.challenges.index')->with('success', 'Tantangan berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->service->delete($id);
            return redirect()->route('admin.challenges.index')->with('success', 'Tantangan berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
