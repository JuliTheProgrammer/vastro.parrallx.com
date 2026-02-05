<?php

namespace App\Http\Controllers\Vault;

use App\Actions\VaultAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vault\VaultStoreRequest;
use App\Models\Location;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

class VaultController extends Controller
{
    /**
     * Display a listing of the resource. This is for users of the application only
     */
    public function index(Request $request): Response
    {
        $vaults = $request->user()
            ?->vaults()
            ->with('location')
            ->get()
            ->map(function (Vault $vault): array {
                return [
                    ...$vault->toArray(),
                    'location' => $vault->location?->name,
                ];
            }) ?? collect();
        $locations = Location::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('vaults/index', compact('vaults', 'locations'));
    }

    public function indexSharedVault(Request $request)
    {
        abort_if(! $request->hasValidRelativeSignature());

        $validated = $request->validate([
            'vault_id' => 'required',
        ]);

        $vault = Vault::find($validated['vault_id']);

        return Inertia::render('vaults/sharedVaults', compact('vault'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VaultStoreRequest $request)
    {
        ray('Controller Reached');
        $vaultData = $request->validated();

        $vaultName = Arr::get($vaultData, 'name');
        $vaultLocation = Arr::get($vaultData, 'region');
        $wormProtection = Arr::get($vaultData, 'wormProtection');
        $encryption = Arr::get($vaultData, 'encryption');
        $deleteProtection = Arr::get($vaultData, 'deleteProtection');

        app(VaultAction::class)->createVault($vaultName, $vaultLocation, $wormProtection, $deleteProtection);

        return redirect()
            ->route('vaults.index')
            ->with('success', 'Vault created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vault $vault)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vault $vault)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vault $vault)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vault $vault)
    {
        //
    }
}
