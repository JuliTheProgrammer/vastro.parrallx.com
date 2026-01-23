<?php

namespace App\Http\Controllers\Vault;

use App\Actions\VaultAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vault\VaultStoreRequest;
use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class VaultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vaults = Vault::all();

        return Inertia::render('vaults/index', compact('vaults'));
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

        app(VaultAction::class)->createVault($vaultName, $vaultLocation, $wormProtection, $encryption, $deleteProtection);

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
